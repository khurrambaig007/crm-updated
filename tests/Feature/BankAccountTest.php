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

    public function test_user_can_view_the_bank_accounts_index(): void
    {
        $this->seedCompleteCompanyProfile();

        $this->actingAs($this->superAdmin())
            ->get(route('bank-accounts.index'))
            ->assertOk()
            ->assertSee('Bank Accounts');
    }

    public function test_index_lists_all_created_bank_accounts(): void
    {
        $this->seedCompleteCompanyProfile();

        $first = BankAccount::create($this->validPayload());
        $second = BankAccount::create($this->validPayload(['bank' => 'MCB', 'beneficiary_name' => 'Mashreq Trading LLC']));

        // Rows arrive via a server-side DataTable ajax call, not the initial HTML.
        $rows = collect($this->actingAs($this->superAdmin())
            ->get(route('bank-accounts.index', ['draw' => 1, 'start' => 0, 'length' => 10]), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->json('data'));

        $this->assertCount(2, $rows);
        $this->assertTrue(
            $rows->pluck('beneficiary_name')->sort()->values()->all()
            === collect([$first->beneficiary_name, $second->beneficiary_name])->sort()->values()->all()
        );

        $this->assertSame(2, BankAccount::count());
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

        $account = BankAccount::query()->sole();

        $this->assertSame('HBL', $account->bank);
        $this->assertSame('Acme Logistics (Pvt) Ltd', $account->beneficiary_name);
        $this->assertSame('1234567890123', $account->account);
        $this->assertSame([['key' => 'Branch Code', 'value' => '0123']], $account->custom_fields);
    }

    public function test_multiple_bank_accounts_can_exist(): void
    {
        $this->seedCompleteCompanyProfile();

        $response = $this->actingAs($this->superAdmin());

        foreach ([$this->validPayload(), $this->validPayload(['beneficiary_name' => 'Mashreq Trading LLC'])] as $payload) {
            $response->post(route('bank-accounts.store'), $payload)
                ->assertRedirect(route('bank-accounts.index'))
                ->assertSessionHas('status');
        }

        $this->assertSame(2, BankAccount::count());
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
        $account = BankAccount::create($this->validPayload());

        $this->actingAs($this->superAdmin())
            ->patch(route('bank-accounts.update', $account), $this->validPayload(['account' => '9999999999999']))
            ->assertRedirect(route('bank-accounts.index'))
            ->assertSessionHas('status');

        $this->assertSame('9999999999999', $account->refresh()->account);
        $this->assertSame(1, BankAccount::count());
    }

    public function test_updating_an_unknown_bank_account_returns_404(): void
    {
        $this->seedCompleteCompanyProfile();

        $this->actingAs($this->superAdmin())
            ->patch(route('bank-accounts.update', 999), $this->validPayload())
            ->assertNotFound();
    }

    public function test_edit_page_shows_the_bound_record(): void
    {
        $this->seedCompleteCompanyProfile();

        $other = BankAccount::create($this->validPayload());
        $account = BankAccount::create($this->validPayload(['beneficiary_name' => 'Mashreq Trading LLC']));

        $this->actingAs($this->superAdmin())
            ->get(route('bank-accounts.edit', $account))
            ->assertOk()
            ->assertSee('Mashreq Trading LLC');
    }

    public function test_user_can_delete_a_bank_account(): void
    {
        $this->seedCompleteCompanyProfile();
        $account = BankAccount::create($this->validPayload());

        $this->actingAs($this->superAdmin())
            ->delete(route('bank-accounts.destroy', $account))
            ->assertRedirect(route('bank-accounts.index'))
            ->assertSessionHas('status');

        $this->assertSame(0, BankAccount::count());
    }

    public function test_half_filled_custom_fields_are_discarded(): void
    {
        $this->seedCompleteCompanyProfile();
        $account = BankAccount::create($this->validPayload());

        $this->actingAs($this->superAdmin())
            ->patch(route('bank-accounts.update', $account), $this->validPayload([
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
            $account->refresh()->custom_fields
        );
    }

    public function test_viewing_the_bank_accounts_requires_permission(): void
    {
        $this->seedCompleteCompanyProfile();

        $this->actingAs(User::factory()->create())
            ->get(route('bank-accounts.index'))
            ->assertForbidden();
    }

    public function test_creating_and_deleting_bank_accounts_requires_permission(): void
    {
        $this->seedCompleteCompanyProfile();

        $this->actingAs(User::factory()->create())
            ->get(route('bank-accounts.create'))
            ->assertForbidden();

        $account = BankAccount::create($this->validPayload());

        $this->actingAs(User::factory()->create())
            ->delete(route('bank-accounts.destroy', $account))
            ->assertForbidden();

        $this->assertSame(1, BankAccount::count());
    }
}
