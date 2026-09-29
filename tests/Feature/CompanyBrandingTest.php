<?php

namespace Tests\Feature;

use App\Models\CompanyProfile;
use App\Models\ContainerReleaseOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\Concerns\CreatesCompanyProfile;
use Tests\TestCase;

class CompanyBrandingTest extends TestCase
{
    use CreatesCompanyProfile;
    use RefreshDatabase;

    private function superAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole(Role::firstOrCreate(['name' => config('system.super_admin_role')]));

        return $user;
    }

    public function test_the_sidebar_brands_with_the_company_name_and_subtitle(): void
    {
        $this->seedCompleteCompanyProfile([
            'name' => 'Apex MeriTime',
            'subtitle' => 'Shipping Line',
        ]);

        $this->actingAs($this->superAdmin())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Apex MeriTime')
            ->assertSee('Shipping Line');
    }

    public function test_the_sidebar_renders_the_uploaded_logo(): void
    {
        Storage::fake('public');

        $this->seedCompleteCompanyProfile(['name' => 'Apex MeriTime']);

        $this->actingAs($this->superAdmin())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(Storage::disk('public')->url('company-profile/test-logo.png'), false);
    }

    public function test_the_favicon_is_cache_busted_so_a_logo_swap_takes_effect(): void
    {
        Storage::fake('public');

        $profile = $this->seedCompleteCompanyProfile();

        $this->assertStringContainsString(
            '?v='.$profile->updated_at->getTimestamp(),
            $profile->faviconUrl(),
        );

        $this->actingAs($this->superAdmin())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee($profile->faviconUrl(), false);
    }

    public function test_the_page_title_is_suffixed_with_the_company_name(): void
    {
        $this->seedCompleteCompanyProfile(['name' => 'Apex MeriTime']);

        $this->actingAs($this->superAdmin())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Dashboard · Apex MeriTime', false);
    }

    public function test_branding_falls_back_to_the_app_name_when_no_profile_exists(): void
    {
        // No profile seeded, so the fresh-install path is exercised.
        $this->assertSame(0, CompanyProfile::count());

        $this->assertSame(config('app.name'), CompanyProfile::current()->displayName());
        $this->assertNull(CompanyProfile::current()->logoUrl());
        $this->assertNull(CompanyProfile::current()->faviconUrl());

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('<title>'.config('app.name').'</title>', false);
    }

    public function test_the_svg_brand_mark_is_used_when_no_logo_is_stored(): void
    {
        $profile = CompanyProfile::create([
            'name' => 'Logo-less Co',
            'emails' => ['info@logo-less.test'],
        ]);

        $this->assertNull($profile->logoUrl());
        $this->assertNull($profile->faviconUrl());

        // A profile with no logo is locked, but the layout must still render.
        $this->actingAs($this->superAdmin())
            ->get(route('company-profile.index'))
            ->assertOk()
            ->assertSee('Logo-less Co')
            ->assertSee('href="'.asset('favicon.ico').'"', false);
    }

    public function test_the_login_screen_brands_with_the_company_identity(): void
    {
        Storage::fake('public');

        $this->seedCompleteCompanyProfile([
            'name' => 'Apex MeriTime',
            'subtitle' => 'Shipping Line',
            'message' => 'Your gateway to the sea.',
        ]);

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('<title>Apex MeriTime</title>', false)
            ->assertSee('Apex MeriTime')
            ->assertSee('Shipping Line')
            ->assertSee('Your gateway to the sea.')
            ->assertSee(Storage::disk('public')->url('company-profile/test-logo.png'), false);
    }

    public function test_the_login_screen_keeps_the_default_copy_without_a_message(): void
    {
        $this->seedCompleteCompanyProfile();

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Move cargo with confidence.');
    }

    public function test_the_login_form_shows_the_company_logo_above_the_welcome_heading(): void
    {
        Storage::fake('public');

        $this->seedCompleteCompanyProfile(['name' => 'Apex MeriTime']);

        $html = $this->get(route('login'))->assertOk()->getContent();

        $this->assertStringContainsString(Storage::disk('public')->url('company-profile/test-logo.png'), $html);

        $logo = strpos($html, 'company-profile/test-logo.png');
        $heading = strpos($html, 'Welcome back');
        $subheading = strpos($html, 'Sign in to continue to your workspace.');

        $this->assertIsInt($logo);
        $this->assertIsInt($heading);
        $this->assertIsInt($subheading);
        $this->assertLessThan($heading, $logo, 'The login logo should render above the welcome heading.');
        $this->assertLessThan($subheading, $heading);
    }

    public function test_the_login_form_omits_the_logo_when_none_is_stored(): void
    {
        CompanyProfile::create([
            'name' => 'Logo-less Co',
            'emails' => ['info@logo-less.test'],
        ]);

        $html = $this->get(route('login'))->assertOk()->getContent();

        $this->assertStringContainsString('Welcome back', $html);
        $this->assertStringNotContainsString('<img', $html);
    }

    public function test_the_pdf_route_returns_a_pdf_while_branding_is_configured(): void
    {
        $this->seedCompleteCompanyProfile(['name' => 'Apex MeriTime']);

        $cro = ContainerReleaseOrder::create(['booking_no' => 'BR-1']);

        $response = $this->actingAs($this->superAdmin())
            ->get(route('container-release-orders.pdf', $cro));

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');

        // dompdf compresses its content streams, so the brand text is not readable in
        // the raw bytes. The header markup is asserted separately below.
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_the_pdf_header_markup_is_branded(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('company-profile/test-logo.png', 'fake-image-bytes');

        $profile = $this->seedCompleteCompanyProfile([
            'name' => 'Brand Co Ltd',
            'website' => 'https://apex.test',
            'pic_number' => '+1 555 0100',
        ]);

        $html = view('container-release-orders.pdf', [
            'cro' => new ContainerReleaseOrder(['booking_no' => 'BR-1']),
            'brandName' => $profile->displayName(),
            'brandLogoPath' => $profile->logoPath(),
            'brandLogoSize' => $profile->logoDisplaySize(320, 150),
            'brandContact' => array_filter([
                $profile->website,
                $profile->primaryEmail(),
                $profile->pic_number,
            ]),
        ])->render();

        $this->assertStringContainsString('Brand Co Ltd', $html);
        $this->assertStringContainsString('https://apex.test', $html);
        $this->assertStringContainsString('+1 555 0100', $html);

        // Proves the header is driven by the profile rather than config('app.name'),
        // which happens to be set to a different value in this environment.
        $this->assertStringNotContainsString(config('app.name'), $html);
    }

    public function test_the_pdf_header_omits_the_logo_when_the_file_is_missing(): void
    {
        // Storage::fake means the path is returned but no file exists on disk, which
        // is exactly the state a deleted logo leaves behind.
        Storage::fake('public');

        $profile = $this->seedCompleteCompanyProfile(['name' => 'Apex MeriTime']);

        $html = view('container-release-orders.pdf', [
            'cro' => new ContainerReleaseOrder(['booking_no' => 'BR-1']),
            'brandName' => $profile->displayName(),
            'brandLogoPath' => $profile->logoPath(),
            'brandLogoSize' => $profile->logoDisplaySize(320, 150),
            'brandContact' => [],
        ])->render();

        $this->assertStringContainsString('Apex MeriTime', $html);
        $this->assertStringNotContainsString('<img', $html);
    }

    public function test_the_pdf_logo_is_right_aligned_and_large(): void
    {
        Storage::fake('public');

        $profile = $this->seedCompleteCompanyProfile(['name' => 'Apex MeriTime']);
        $this->writePng(Storage::disk('public')->path($profile->logo), 600, 400);

        $html = view('container-release-orders.pdf', [
            'cro' => new ContainerReleaseOrder(['booking_no' => 'BR-1']),
            'brandName' => $profile->displayName(),
            'brandLogoPath' => $profile->logoPath(),
            'brandLogoSize' => $profile->logoDisplaySize(320, 150),
            'brandContact' => [],
        ])->render();

        // Right aligned: the logo lives in the right-hand, right-aligned cell.
        $this->assertStringContainsString('class="header-mark"', $html);
        $this->assertStringContainsString('align="right"', $html);
        $this->assertStringContainsString('td.header-mark { text-align: right; }', $html);

        // Large: explicit pixel dimensions, because max-* only ever shrinks an image.
        $this->assertMatchesRegularExpression('/<img[^>]*\swidth="\d+"[^>]*\sheight="\d+"/', $html);
        $this->assertStringNotContainsString('max-height', $html);

        // It must render inside the mark cell, after the brand cell.
        $this->assertLessThan(
            strpos($html, 'class="brand-logo"'),
            strpos($html, 'class="header-brand"'),
            'The logo should sit in the right-hand header cell.',
        );
    }

    public function test_the_pdf_overrides_the_dompdf_default_page_margin(): void
    {
        // dompdf's built-in stylesheet injects `@page { margin: 1.2cm }`, which stacks
        // on top of the body margin. Without an explicit override the real edge
        // distance becomes 1.2cm + the body margin, wasting a lot of the page.
        $html = view('container-release-orders.pdf', [
            'cro' => new ContainerReleaseOrder(['booking_no' => 'BR-1']),
            'brandName' => 'Apex MeriTime',
            'brandLogoPath' => null,
            'brandLogoSize' => null,
            'brandContact' => [],
        ])->render();

        $this->assertStringContainsString('@page { margin: 0.7cm; }', $html);

        // The body must not add a second margin on top of @page.
        $this->assertMatchesRegularExpression('/body\s*\{[^}]*margin:\s*0\s*;/', $html);
        $this->assertStringNotContainsString('margin: 24px', $html);
    }

    public function test_logo_display_size_scales_to_the_box_and_preserves_the_ratio(): void
    {
        Storage::fake('public');

        $profile = $this->seedCompleteCompanyProfile(['logo' => 'company-profile/portrait.png']);

        $this->writePng(Storage::disk('public')->path('company-profile/portrait.png'), 1800, 2170);
        $this->assertSame(['width' => 1800, 'height' => 2170], $profile->logoDimensions());

        // A tall logo is limited by height, and is scaled UP relative to a naive
        // max-height rule, which would have left it at 83x100.
        $size = $profile->logoDisplaySize(320, 150);
        $this->assertSame(150, $size['height']);
        $this->assertSame(124, $size['width']);
        $this->assertEqualsWithDelta(1800 / 2170, $size['width'] / $size['height'], 0.02);

        // A wide logo is limited by width instead.
        $this->writePng(Storage::disk('public')->path('company-profile/wide.png'), 2000, 250);
        $profile->logo = 'company-profile/wide.png';
        $size = $profile->logoDisplaySize(320, 150);
        $this->assertSame(320, $size['width']);
        $this->assertSame(40, $size['height']);

        // A tiny logo is not blown up into a blur (capped at 4x).
        $this->writePng(Storage::disk('public')->path('company-profile/tiny.png'), 50, 20);
        $profile->logo = 'company-profile/tiny.png';
        $size = $profile->logoDisplaySize(320, 150);
        $this->assertSame(200, $size['width']);
        $this->assertSame(80, $size['height']);
    }

    public function test_logo_display_size_is_null_without_a_readable_file(): void
    {
        Storage::fake('public');

        $this->assertNull(CompanyProfile::current()->logoDisplaySize(320, 150));

        $profile = $this->seedCompleteCompanyProfile();
        Storage::disk('public')->put($profile->logo, 'not-an-image');

        $this->assertNull($profile->logoDisplaySize(320, 150));
    }

    /**
     * Write a real PNG of the given size so getimagesize() can read it back.
     *
     * Storage::fake() only creates the disk root, so the nested logo directory has
     * to be made explicitly before imagepng() can open the file.
     *
     * @return array{width: int, height: int}
     */
    private function writePng(string $path, int $width, int $height): array
    {
        File::ensureDirectoryExists(dirname($path));

        $image = imagecreatetruecolor($width, $height);
        imagefill($image, 0, 0, imagecolorallocate($image, 20, 20, 20));
        imagepng($image, $path);
        imagedestroy($image);

        return ['width' => $width, 'height' => $height];
    }

    public function test_display_helpers_fall_back_to_config(): void
    {
        $profile = CompanyProfile::create([
            'name' => 'Apex MeriTime',
            'emails' => ['first@apex.test', 'second@apex.test'],
        ]);

        $this->assertSame('Apex MeriTime', $profile->displayName());
        $this->assertSame(config('app.subtitle'), $profile->displaySubtitle());
        $this->assertSame('first@apex.test', $profile->primaryEmail());
    }

    public function test_the_profile_subtitle_persists(): void
    {
        $this->seedCompleteCompanyProfile();

        $this->actingAs($this->superAdmin())
            ->patch(route('company-profile.update'), [
                'name' => 'Apex MeriTime',
                'subtitle' => 'Shipping Line',
                'emails' => ['info@apex.test'],
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame('Shipping Line', CompanyProfile::current()->subtitle);
    }
}
