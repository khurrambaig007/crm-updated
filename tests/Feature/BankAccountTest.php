<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\Concerns\CreatesCompanyProfile;
use Tests\TestCase;

class BankAccountTest extends TestCase
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
            'bank' => 'HBL',
            'beneficiary_name' => 'Acme Logistics (Pvt) Ltd',
            'bank_name' => 'Habib Bank Limited - Clifton Branch',
            'account' => '1234567890123',
            'iban' => 'PK36SCBL0000001123456702',
            'swift' => 'HABBKPKK',
            'address' => 'Clifton, Karachi',
        ], $overrides);
    }

    public function test_user_can_view_the_bank_account_form(): void
    {
        $this->seedCompleteCompanyProfile();

        $this->actingAs($this->superAdmin())
            ->get(route('bank-accounts.index'))
            ->assertOk()
            ->assertSee('Bank Account');
    }

    public function test_user_can_create_a_bank_account(): void
    {
        $this->seedCompleteCompanyProfile();

        $this->actingAs($this->superAdmin())
            ->post(route('bank-accounts.store'), $this->validPayload([
                'custom_fields' => [['key' => 'Branch Code', 'value' => '0123']],
            ]))
            ->assertRedirect(route('bank-accounts.index'))
            ->assertSessionHas('status');

        $account = BankAccount::current();

        $this->assertSame('HBL', $account->bank);
        $this->assertSame('Acme Logistics (Pvt) Ltd', $account->beneficiary_name);
        $this->assertSame('1234567890123', $account->account);
        $this->assertSame([['key' => 'Branch Code', 'value' => '0123']], $account->custom_fields);
    }

    public function test_creating_a_bank_account_requires_a_beneficiary_and_account(): void
    {
        $this->seedCompleteCompanyProfile();

        $this->actingAs($this->superAdmin())
            ->from(route('bank-accounts.index'))
            ->post(route('bank-accounts.store'), [])
            ->assertSessionHasErrors(['beneficiary_name', 'account']);

        $this->assertSame(0, BankAccount::count());
    }

    public function test_user_can_update_the_bank_account(): void
    {
        $this->seedCompleteCompanyProfile();
        BankAccount::create($this->validPayload());

        $this->actingAs($this->superAdmin())
            ->patch(route('bank-accounts.update'), $this->validPayload(['account' => '9999999999999']))
            ->assertRedirect(route('bank-accounts.index'))
            ->assertSessionHas('status');

        $this->assertSame('9999999999999', BankAccount::current()->account);
    }

    public function test_a_second_submission_never_creates_a_duplicate_account(): void
    {
        $this->seedCompleteCompanyProfile();
        BankAccount::create($this->validPayload());

        $this->actingAs($this->superAdmin())
            ->post(route('bank-accounts.store'), $this->validPayload())
            ->assertRedirect(route('bank-accounts.index'))
            ->assertSessionHas('error');

        $this->assertSame(1, BankAccount::count());
    }

    public function test_updating_without_an_existing_account_redirects_with_an_error(): void
    {
        $this->seedCompleteCompanyProfile();

        $this->actingAs($this->superAdmin())
            ->patch(route('bank-accounts.update'), $this->validPayload())
            ->assertRedirect(route('bank-accounts.index'))
            ->assertSessionHas('error');

        $this->assertSame(0, BankAccount::count());
    }

    public function test_half_filled_custom_fields_are_discarded(): void
    {
        $this->seedCompleteCompanyProfile();
        BankAccount::create($this->validPayload());

        $this->actingAs($this->superAdmin())
            ->patch(route('bank-accounts.update'), $this->validPayload([
                'custom_fields' => [
                    ['key' => 'Branch Code', 'value' => '0123'],
                    ['key' => '', 'value' => 'orphan value'],
                    ['key' => 'orphan key', 'value' => ''],
                    ['key' => '  ', 'value' => '  '],
                ],
            ]))
            ->assertSessionHasNoErrors();

        $this->assertSame(
            [['key' => 'Branch Code', 'value' => '0123']],
            BankAccount::current()->custom_fields
        );
    }

    public function test_viewing_the_bank_account_requires_permission(): void
    {
        $this->seedCompleteCompanyProfile();

        $this->actingAs(User::factory()->create())
            ->get(route('bank-accounts.index'))
            ->assertForbidden();
    }
}
