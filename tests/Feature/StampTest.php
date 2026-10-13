<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Middleware\PermissionMiddleware;
use App\Models\Stamp;
use App\Models\User;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreatesCompanyProfile;

class StampTest extends BaseTestCase
{
    use CreatesCompanyProfile;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Every screen is gated by EnsureCompanyProfileIsComplete, and the
        // stamp files live on the public disk, so both are faked up front.
        Storage::fake('public');
        $this->seedCompleteCompanyProfile();

        $this->withoutMiddleware([Authenticate::class, PermissionMiddleware::class]);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user);
    }

    public function test_index_lists_stamps(): void
    {
        Stamp::create(['name' => 'Company Stamp', 'image' => 'stamps/company.png']);

        $response = $this->get(route('stamps.index'));

        $response->assertOk();
        $response->assertSee('Stamps Info');
        $response->assertSee('id="stamps-table"', escape: false);
    }

    public function test_index_answers_the_datatable_ajax_request(): void
    {
        Stamp::create(['name' => 'Company Stamp', 'image' => 'stamps/company.png']);

        $response = $this->get(route('stamps.index'), ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertOk();
        $response->assertJsonPath('data.0.name', 'Company Stamp');
    }

    public function test_store_saves_the_name_and_stores_the_image_on_the_public_disk(): void
    {
        $response = $this->post(route('stamps.store'), [
            'name' => 'Company Stamp',
            'image' => UploadedFile::fake()->image('company.png', 120, 80),
        ]);

        $response->assertRedirect(route('stamps.index'));
        $response->assertSessionHas('status');

        $stamp = Stamp::sole();

        $this->assertSame('Company Stamp', $stamp->name);
        $this->assertNotNull($stamp->image);
        $this->assertStringStartsWith('stamps/', $stamp->image);
        Storage::disk('public')->assertExists($stamp->image);
    }

    public function test_store_requires_a_name_and_an_image(): void
    {
        // A valid image with a blank name only complains about the name.
        $this->post(route('stamps.store'), [
            'name' => '',
            'image' => UploadedFile::fake()->image('company.png'),
        ])->assertSessionHasErrors(['name']);

        $this->post(route('stamps.store'), ['name' => 'No image'])->assertSessionHasErrors(['image']);

        $this->assertSame(0, Stamp::count());
    }

    public function test_store_rejects_a_non_image_upload(): void
    {
        $this->post(route('stamps.store'), [
            'name' => 'Company Stamp',
            'image' => UploadedFile::fake()->create('notes.pdf', 64, 'application/pdf'),
        ])->assertSessionHasErrors(['image']);

        $this->assertSame(0, Stamp::count());
    }

    public function test_edit_screen_shows_the_current_stamp(): void
    {
        $stamp = Stamp::create(['name' => 'Company Stamp', 'image' => 'stamps/company.png']);
        Storage::disk('public')->put('stamps/company.png', 'image-bytes');

        $response = $this->get(route('stamps.edit', $stamp));

        $response->assertOk();
        $response->assertSee('Edit Stamp');
        $response->assertSee('name="remove_image"', escape: false);
    }

    public function test_update_renames_without_touching_the_image(): void
    {
        $stamp = Stamp::create(['name' => 'Old Name', 'image' => 'stamps/company.png']);
        Storage::disk('public')->put('stamps/company.png', 'image-bytes');

        $this->patch(route('stamps.update', $stamp), ['name' => 'New Name'])
            ->assertRedirect(route('stamps.index'));

        // Leaving the file input empty is what keeps the stored image.
        $this->assertSame('New Name', $stamp->refresh()->name);
        $this->assertSame('stamps/company.png', $stamp->image);
        Storage::disk('public')->assertExists('stamps/company.png');
    }

    public function test_update_replaces_the_image_and_deletes_the_old_file(): void
    {
        $stamp = Stamp::create(['name' => 'Company Stamp', 'image' => 'stamps/old.png']);
        Storage::disk('public')->put('stamps/old.png', 'old-bytes');

        $this->patch(route('stamps.update', $stamp), [
            'name' => 'Company Stamp',
            'image' => UploadedFile::fake()->image('new.png', 120, 80),
        ])->assertRedirect(route('stamps.index'));

        $stamp->refresh();

        $this->assertNotSame('stamps/old.png', $stamp->image);
        Storage::disk('public')->assertMissing('stamps/old.png');
        Storage::disk('public')->assertExists($stamp->image);
    }

    public function test_update_can_remove_the_stamp(): void
    {
        $stamp = Stamp::create(['name' => 'Company Stamp', 'image' => 'stamps/company.png']);
        Storage::disk('public')->put('stamps/company.png', 'image-bytes');

        $this->patch(route('stamps.update', $stamp), [
            'name' => 'Company Stamp',
            'remove_image' => '1',
        ])->assertRedirect(route('stamps.index'));

        $this->assertNull($stamp->refresh()->image);
        Storage::disk('public')->assertMissing('stamps/company.png');
    }

    public function test_destroy_deletes_the_row_and_its_file(): void
    {
        $stamp = Stamp::create(['name' => 'Company Stamp', 'image' => 'stamps/company.png']);
        Storage::disk('public')->put('stamps/company.png', 'image-bytes');

        $this->delete(route('stamps.destroy', $stamp))
            ->assertRedirect(route('stamps.index'));

        $this->assertSame(0, Stamp::count());
        Storage::disk('public')->assertMissing('stamps/company.png');
    }

    public function test_every_action_is_gated_by_its_own_permission(): void
    {
        // The PermissionMiddleware short-circuit is bypassed in setUp, so assert
        // on the gate each route is actually wired with instead.
        $routes = collect(Route::getRoutes()->getRoutes())->keyBy(
            fn ($route): string => $route->methods()[0].'|'.$route->uri()
        );

        $gate = function (string $method, string $uri) use ($routes): ?string {
            $route = $routes->get($method.'|'.$uri);

            if ($route === null) {
                return null;
            }

            foreach ($route->gatherMiddleware() as $middleware) {
                if (str_starts_with($middleware, 'permission:')) {
                    return $middleware;
                }
            }

            return null;
        };

        $this->assertSame('permission:stamps.view', $gate('GET', 'system-operations/stamps'));
        $this->assertSame('permission:stamps.add', $gate('GET', 'system-operations/stamps/create'));
        $this->assertSame('permission:stamps.add', $gate('POST', 'system-operations/stamps'));
        $this->assertSame('permission:stamps.edit', $gate('GET', 'system-operations/stamps/{stamp}/edit'));
        $this->assertSame('permission:stamps.edit', $gate('PATCH', 'system-operations/stamps/{stamp}'));
        $this->assertSame('permission:stamps.delete', $gate('DELETE', 'system-operations/stamps/{stamp}'));
    }

    public function test_model_resolves_the_image_url_and_path(): void
    {
        $stamp = Stamp::create(['name' => 'Company Stamp', 'image' => 'stamps/company.png']);

        $this->assertStringEndsWith('stamps/company.png', (string) $stamp->imageUrl());
        $this->assertFileDoesNotExist($stamp->imagePath());

        $blank = new Stamp;
        $this->assertNull($blank->imageUrl());
        $this->assertNull($blank->imagePath());
    }
}
