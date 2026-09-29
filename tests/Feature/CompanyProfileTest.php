<?php

namespace Tests\Feature;

use App\Models\CompanyProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\Concerns\CreatesCompanyProfile;
use Tests\TestCase;

class CompanyProfileTest extends TestCase
{
    use CreatesCompanyProfile;
    use RefreshDatabase;

    private function superAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole(Role::firstOrCreate(['name' => config('system.super_admin_role')]));

        return $user;
    }

    /**
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Acme Logistics',
            'website' => 'https://acme.test',
            'number' => 'ACME-001',
            'emails' => ['info@acme.test'],
            'pic_name' => 'Ada Lovelace',
            'pic_email' => 'ada@acme.test',
            'pic_number' => '+1 555 0100',
            'message' => 'Please quote by email.',
        ], $overrides);
    }

    public function test_user_can_view_the_company_profile_form(): void
    {
        $this->actingAs($this->superAdmin())
            ->get(route('company-profile.index'))
            ->assertOk()
            ->assertSee('Company Profile');
    }

    public function test_user_can_create_the_company_profile(): void
    {
        Storage::fake('public');

        $this->actingAs($this->superAdmin())
            ->post(route('company-profile.store'), $this->validPayload([
                'logo' => UploadedFile::fake()->image('logo.png'),
                'custom_fields' => [['key' => 'Tax ID', 'value' => 'TX-99']],
            ]))
            ->assertRedirect(route('company-profile.index'))
            ->assertSessionHas('status');

        $profile = CompanyProfile::current();

        $this->assertSame('Acme Logistics', $profile->name);
        $this->assertSame(['info@acme.test'], $profile->emails);
        $this->assertSame([['key' => 'Tax ID', 'value' => 'TX-99']], $profile->custom_fields);
        Storage::disk('public')->assertExists($profile->logo);
    }

    public function test_creating_a_profile_requires_a_name_logo_and_an_email(): void
    {
        Storage::fake('public');

        $this->actingAs($this->superAdmin())
            ->from(route('company-profile.index'))
            ->post(route('company-profile.store'), ['emails' => ['']])
            ->assertSessionHasErrors(['name', 'logo', 'emails']);

        $this->assertSame(0, CompanyProfile::count());
    }

    public function test_blank_email_rows_do_not_block_saving_the_real_ones(): void
    {
        $this->seedCompleteCompanyProfile();

        $this->actingAs($this->superAdmin())
            ->patch(route('company-profile.update'), $this->validPayload([
                'emails' => ['', 'info@acme.test', '  '],
            ]))
            ->assertSessionHasNoErrors();

        $this->assertSame(['info@acme.test'], CompanyProfile::current()->emails);
    }

    public function test_user_can_update_the_company_profile(): void
    {
        $this->seedCompleteCompanyProfile(['name' => 'Old Name']);

        $this->actingAs($this->superAdmin())
            ->patch(route('company-profile.update'), $this->validPayload(['name' => 'New Name']))
            ->assertRedirect(route('company-profile.index'))
            ->assertSessionHas('status');

        $profile = CompanyProfile::current();

        $this->assertSame('New Name', $profile->name);
        $this->assertSame('company-profile/test-logo.png', $profile->logo);
    }

    public function test_blank_email_rows_and_half_filled_custom_fields_are_discarded(): void
    {
        $this->seedCompleteCompanyProfile();

        $this->actingAs($this->superAdmin())
            ->patch(route('company-profile.update'), $this->validPayload([
                'emails' => ['  first@acme.test  ', '   ', 'second@acme.test'],
                'custom_fields' => [
                    ['key' => 'Tax ID', 'value' => 'TX-99'],
                    ['key' => '', 'value' => 'orphan value'],
                    ['key' => 'orphan key', 'value' => ''],
                    ['key' => '  ', 'value' => '  '],
                ],
            ]))
            ->assertSessionHasNoErrors();

        $profile = CompanyProfile::current();

        $this->assertSame(['first@acme.test', 'second@acme.test'], $profile->emails);
        $this->assertSame([['key' => 'Tax ID', 'value' => 'TX-99']], $profile->custom_fields);
    }

    public function test_a_second_submission_never_creates_a_duplicate_profile(): void
    {
        Storage::fake('public');
        $this->seedCompleteCompanyProfile();

        $this->actingAs($this->superAdmin())
            ->post(route('company-profile.store'), $this->validPayload([
                'logo' => UploadedFile::fake()->image('logo.png'),
            ]))
            ->assertRedirect(route('company-profile.index'))
            ->assertSessionHas('error');

        $this->assertSame(1, CompanyProfile::count());
    }

    public function test_uploading_a_new_logo_replaces_and_deletes_the_old_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('company-profile/old.png', 'old');

        $this->seedCompleteCompanyProfile(['logo' => 'company-profile/old.png']);

        $this->actingAs($this->superAdmin())
            ->patch(route('company-profile.update'), $this->validPayload([
                'logo' => UploadedFile::fake()->image('new.png'),
            ]))
            ->assertSessionHasNoErrors();

        $profile = CompanyProfile::current();

        $this->assertNotSame('company-profile/old.png', $profile->logo);
        Storage::disk('public')->assertMissing('company-profile/old.png');
        Storage::disk('public')->assertExists($profile->logo);
    }

    public function test_the_logo_can_be_removed_but_only_by_uploading_a_replacement_next_time(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('company-profile/old.png', 'old');

        $this->seedCompleteCompanyProfile(['logo' => 'company-profile/old.png']);

        $this->actingAs($this->superAdmin())
            ->patch(route('company-profile.update'), $this->validPayload(['remove_logo' => '1']))
            ->assertSessionHasNoErrors();

        $this->assertNull(CompanyProfile::current()->logo);
        Storage::disk('public')->assertMissing('company-profile/old.png');

        $this->actingAs($this->superAdmin())
            ->from(route('company-profile.index'))
            ->patch(route('company-profile.update'), $this->validPayload())
            ->assertSessionHasErrors('logo');
    }

    public function test_the_logo_must_be_an_image(): void
    {
        Storage::fake('public');

        $this->actingAs($this->superAdmin())
            ->from(route('company-profile.index'))
            ->post(route('company-profile.store'), $this->validPayload([
                'logo' => UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf'),
            ]))
            ->assertSessionHasErrors('logo');
    }
}
