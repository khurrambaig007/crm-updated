<?php

namespace Tests\Feature;

use App\Models\CompanyProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\Concerns\CreatesCompanyProfile;
use Tests\TestCase;

class CompanyProfileGateTest extends TestCase
{
    use CreatesCompanyProfile;
    use RefreshDatabase;

    private function superAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole(Role::firstOrCreate(['name' => config('system.super_admin_role')]));

        return $user;
    }

    public function test_missing_fields_reports_each_mandatory_field(): void
    {
        $this->assertSame(['Company Name', 'Logo', 'Email'], CompanyProfile::missingFields());

        CompanyProfile::create(['name' => 'Acme', 'logo' => 'company-profile/logo.png']);
        $this->assertSame(['Email'], CompanyProfile::missingFields());

        CompanyProfile::query()->update(['emails' => []]);
        $this->assertSame(['Email'], CompanyProfile::missingFields());
    }

    public function test_incomplete_profile_blocks_the_dashboard(): void
    {
        $this->actingAs($this->superAdmin())
            ->get(route('dashboard'))
            ->assertRedirect(route('company-profile.index'))
            ->assertSessionHas('error');
    }

    public function test_super_admin_is_also_blocked(): void
    {
        $user = $this->superAdmin();

        $this->assertTrue($user->isSuperAdmin());

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('company-profile.index'));
    }

    public function test_a_complete_profile_releases_every_screen(): void
    {
        $this->seedCompleteCompanyProfile();

        $this->actingAs($this->superAdmin())
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_a_logo_only_profile_stays_locked(): void
    {
        CompanyProfile::create(['name' => 'Acme', 'logo' => 'company-profile/logo.png']);

        $this->actingAs($this->superAdmin())
            ->get(route('dashboard'))
            ->assertRedirect(route('company-profile.index'));
    }

    public function test_the_profile_screen_stays_reachable_while_incomplete(): void
    {
        $this->actingAs($this->superAdmin())
            ->get(route('company-profile.index'))
            ->assertOk();
    }

    public function test_the_banner_names_the_missing_fields(): void
    {
        $this->actingAs($this->superAdmin())
            ->get(route('company-profile.index'))
            ->assertOk()
            ->assertSee('Complete your company profile to continue')
            ->assertSee('Company Name, Logo, Email');
    }

    public function test_the_banner_disappears_once_the_profile_is_complete(): void
    {
        $this->seedCompleteCompanyProfile();

        $this->actingAs($this->superAdmin())
            ->get(route('company-profile.index'))
            ->assertOk()
            ->assertDontSee('Complete your company profile to continue');
    }

    public function test_logout_and_personal_account_chrome_stay_reachable(): void
    {
        $user = $this->superAdmin();

        $this->actingAs($user)->get(route('profile.edit'))->assertOk();
        $this->actingAs($user)->patch(route('theme.update'), ['theme' => 'dark'])->assertRedirect();
        $this->actingAs($user)->post(route('logout'))->assertRedirect('/');
    }

    public function test_guests_are_sent_to_login_not_the_profile_editor(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_a_user_who_cannot_open_the_profile_screen_is_not_locked_out(): void
    {
        // A future role without company_profiles.view would have no way to fix the
        // profile, so the gate stands aside rather than trapping them.
        $user = User::factory()->create();

        $this->assertFalse($user->isSuperAdmin());

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertStatus(403);
    }
}
