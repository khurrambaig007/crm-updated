<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\Party;
use App\Models\SalesInvoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\Concerns\CreatesCompanyProfile;
use Tests\TestCase;

class BankAccountQuickCreateTest extends TestCase
{
    use CreatesCompanyProfile;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedCompleteCompanyProfile();
    }

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
            'bank_name' => 'Habib Bank Limited',
            'account' => '1234567890123',
            'iban' => 'PK36SCBL0000001123456702',
            'swift' => 'HABBKPKK',
        ], $overrides);
    }

    private function createInvoiceViaForm(User $user, Party $party): SalesInvoice
    {
        $this->actingAs($user)
            ->post(route('sales-invoices.store'), [
                'currency_code' => 'PKR',
                'status' => 'unpaid',
                'party_id' => $party->id,
                'invoice_date' => '2026-10-05',
                'due_date' => '2026-10-20',
                'our_reference' => 'REF-1122',
                'customer_contact' => 'Accounts Payable',
                'remarks' => 'Container sale',
                'vat_rate' => '0',
                'details' => [['description' => 'Container sale', 'container_number' => 'SCSU1234567', 'amount' => '100000']],
            ])
            ->assertRedirect();

        return SalesInvoice::firstOrFail();
    }

    public function test_quick_create_stores_account_and_returns_json(): void
    {
        $response = $this->actingAs($this->superAdmin())
            ->post(route('bank-accounts.quick-create'), $this->validPayload())
            ->assertStatus(201)
            ->assertJsonStructure(['id', 'label']);

        $account = BankAccount::firstOrFail();
        $this->assertSame($account->id, $response->json('id'));
        $this->assertSame($account->optionLabel(), $response->json('label'));
        $this->assertSame('HBL — Acme Logistics (Pvt) Ltd (1234567890123)', $response->json('label'));
    }

    public function test_quick_create_requires_the_add_permission(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::findOrCreate('sales_invoices.edit'));

        $this->actingAs($user)
            ->post(route('bank-accounts.quick-create'), $this->validPayload())
            ->assertForbidden();

        $this->assertSame(0, BankAccount::count());
    }

    public function test_quick_create_returns_json_422_on_validation_errors(): void
    {
        $response = $this->actingAs($this->superAdmin())
            ->post(route('bank-accounts.quick-create'), ['bank' => 'HBL'])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Validation failed.');

        $errors = $response->json('errors');
        $this->assertArrayHasKey('beneficiary_name', $errors);
        $this->assertArrayHasKey('account', $errors);

        $this->assertSame(0, BankAccount::count());
    }

    public function test_sales_invoice_screen_shows_the_quick_create_button_for_permitted_users(): void
    {
        $party = Party::create(['name' => 'UNR Test Logistics', 'address' => 'Karachi', 'email' => 'billing@unr.test', 'phone_No' => '+92 300 5551234']);
        $invoice = $this->createInvoiceViaForm($this->superAdmin(), $party);

        $this->actingAs($this->superAdmin())
            ->get(route('sales-invoices.edit', $invoice))
            ->assertOk()
            ->assertSee('data-modal-target="bank-account-quick-create-modal"', false)
            ->assertSee('id="bank-account-quick-create-modal"', false);
    }

    public function test_sales_invoice_screen_hides_the_quick_create_button_without_add_permission(): void
    {
        $editor = User::factory()->create();
        $editor->givePermissionTo(Permission::findOrCreate('sales_invoices.edit'));

        $party = Party::create(['name' => 'UNR Test Logistics', 'address' => 'Karachi', 'email' => 'billing@unr.test', 'phone_No' => '+92 300 5551234']);
        $invoice = $this->createInvoiceViaForm($this->superAdmin(), $party);

        $this->actingAs($editor)
            ->get(route('sales-invoices.edit', $invoice))
            ->assertOk()
            ->assertDontSee('data-modal-target="bank-account-quick-create-modal"', false)
            ->assertDontSee('id="bank-account-quick-create-modal"', false);
    }
}
