<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\CompanyProfile;
use App\Models\Currency;
use App\Models\Party;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceDetail;
use App\Models\User;
use App\Services\ExchangeRateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\Concerns\CreatesCompanyProfile;
use Tests\TestCase;

class SalesInvoiceTest extends TestCase
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

    private function customer(array $attributes = []): Party
    {
        return Party::create(array_merge([
            'name' => 'UNR Test Logistics',
            'address' => 'Karachi, Pakistan',
            'email' => 'billing@unr.test',
            'phone_No' => '+92 300 5551234',
        ], $attributes));
    }

    /** @return array<string, mixed> */
    private function validPayload(Party $party, array $overrides = []): array
    {
        return array_merge([
            'currency_code' => 'PKR',
            'status' => 'unpaid',
            'party_id' => $party->id,
            'invoice_date' => '2026-10-05',
            'due_date' => '2026-10-20',
            'our_reference' => 'REF-1122',
            'customer_contact' => 'Accounts Payable',
            'remarks' => 'Container sale',
            'vat_rate' => '10',
            'details' => [
                ['description' => '20ft container sale', 'container_number' => 'SCSU1234567', 'amount' => '200000'],
                ['description' => 'Delivery charge', 'container_number' => '', 'amount' => '45000'],
            ],
        ], $overrides);
    }

    public function test_user_can_create_an_invoice_with_generated_number_and_server_calculated_totals(): void
    {
        $party = $this->customer();

        $this->actingAs($this->superAdmin())
            ->post(route('sales-invoices.store'), $this->validPayload($party))
            ->assertRedirect(route('sales-invoices.edit', 1))
            ->assertSessionHas('status');

        $invoice = SalesInvoice::with('details')->firstOrFail();
        $this->assertSame('APX00000001', $invoice->invoice_number);
        $this->assertSame(2, $invoice->details->count());
        $this->assertSame('245000.00', $invoice->subtotal);
        $this->assertSame('24500.00', $invoice->vat_amount);
        $this->assertSame('269500.00', $invoice->total_amount);
        $this->assertSame('SCSU1234567', $invoice->details->first()->container_number);
    }

    public function test_user_can_view_the_invoice_form(): void
    {
        $this->customer();

        $this->actingAs($this->superAdmin())
            ->get(route('sales-invoices.create'))
            ->assertOk()
            ->assertSee('Sales Invoice')
            ->assertSee('Add Line')
            ->assertSee('UNR Test Logistics');
    }

    public function test_user_can_update_invoice_lines_and_totals(): void
    {
        $party = $this->customer();
        $user = $this->superAdmin();
        $this->actingAs($user)->post(route('sales-invoices.store'), $this->validPayload($party));
        $first = SalesInvoice::firstOrFail();

        $this->actingAs($user)
            ->patch(route('sales-invoices.update', $first), $this->validPayload($party, [
                'vat_rate' => '5',
                'details' => [['description' => 'Updated container charge', 'container_number' => 'ABCU7654321', 'amount' => '100.50']],
            ]))
            ->assertRedirect(route('sales-invoices.edit', $first));

        $first->refresh();
        $this->assertSame('100.50', $first->subtotal);
        $this->assertSame('5.03', $first->vat_amount);
        $this->assertSame('105.53', $first->total_amount);
        $this->assertSame('Updated container charge', $first->details()->firstOrFail()->description);
    }

    public function test_invoice_requires_a_valid_customer_and_non_blank_detail_line(): void
    {
        $party = $this->customer();

        $this->actingAs($this->superAdmin())
            ->from(route('sales-invoices.create'))
            ->post(route('sales-invoices.store'), $this->validPayload($party, [
                'party_id' => 999999,
                'vat_rate' => '3.333',
                'details' => [['description' => '', 'container_number' => '', 'amount' => '1.234']],
            ]))
            ->assertSessionHasErrors(['party_id', 'vat_rate', 'details.0.description', 'details.0.amount']);

        $this->assertSame(0, SalesInvoice::count());
        $this->assertSame(0, SalesInvoiceDetail::count());
    }

    public function test_due_date_must_not_precede_the_invoice_date(): void
    {
        $party = $this->customer();

        $this->actingAs($this->superAdmin())
            ->from(route('sales-invoices.create'))
            ->post(route('sales-invoices.store'), $this->validPayload($party, [
                'invoice_date' => '2026-10-20',
                'due_date' => '2026-10-05',
            ]))
            ->assertSessionHasErrors(['due_date']);

        $this->assertSame(0, SalesInvoice::count());
    }

    public function test_invoice_date_and_due_date_may_be_omitted_or_equal(): void
    {
        $party = $this->customer();
        $user = $this->superAdmin();

        $this->actingAs($user)
            ->post(route('sales-invoices.store'), $this->validPayload($party, [
                'invoice_date' => '2026-10-20',
                'due_date' => '2026-10-20',
            ]))
            ->assertSessionHasNoErrors();

        $this->actingAs($user)
            ->post(route('sales-invoices.store'), $this->validPayload($party, [
                'invoice_date' => null,
                'due_date' => null,
            ]))
            ->assertSessionHasNoErrors();

        $this->assertSame(2, SalesInvoice::count());
    }

    public function test_amount_labels_track_the_selected_currency(): void
    {
        Currency::create([
            'exchange_rate_date' => '2026-10-04',
            'exchange_rate' => json_encode(['USD' => 1, 'AED' => 3.67]),
        ]);
        $party = $this->customer();

        $this->actingAs($this->superAdmin())
            ->post(route('sales-invoices.store'), $this->validPayload($party, ['currency_code' => 'AED']));

        $invoice = SalesInvoice::firstOrFail();

        $this->actingAs($this->superAdmin())
            ->get(route('sales-invoices.edit', $invoice))
            ->assertOk()
            ->assertSee('js-amount-label', false)
            ->assertSee('Amount (AED)');
    }

    public function test_index_filters_by_status(): void
    {
        $party = $this->customer();
        $user = $this->superAdmin();
        $this->actingAs($user)->post(route('sales-invoices.store'), $this->validPayload($party, ['status' => 'paid']));
        $this->actingAs($user)->post(route('sales-invoices.store'), $this->validPayload($party, ['status' => 'unpaid']));

        // "unpaid" contains "paid", so a substring column search cannot separate
        // the two statuses. The search pane sends searchPanes[<column index>].
        $paidOnly = $this->actingAs($user)->get(route('sales-invoices.data', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'columns' => [
                ['data' => 'invoice_number', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => 'customer', 'searchable' => 'false', 'orderable' => 'false'],
                ['data' => 'bank', 'searchable' => 'false', 'orderable' => 'false'],
                ['data' => 'invoice_date', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => 'currency_code', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => 'total_amount', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => 'status', 'searchable' => 'false', 'orderable' => 'true'],
                ['data' => 'actions', 'searchable' => 'false', 'orderable' => 'false'],
            ],
            'searchPanes' => [6 => ['paid']],
        ]), ['X-Requested-With' => 'XMLHttpRequest']);

        $paidOnly->assertOk()
            ->assertJsonPath('recordsFiltered', 1)
            ->assertJsonPath('data.0.status', 'paid')
            ->assertJsonPath('searchPanes.options.6.unpaid', 'Unpaid');

        $this->assertStringContainsString('Paid', $paidOnly->json('data')[0]['status_badge']);

        $this->actingAs($user)->get(route('sales-invoices.data', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'searchPanes' => [6 => ['']],
        ]), ['X-Requested-With' => 'XMLHttpRequest'])->assertOk()
            ->assertJsonPath('recordsFiltered', 2);
    }

    public function test_invoice_number_is_stamped_on_create_without_an_extra_update(): void
    {
        $party = $this->customer();

        $this->actingAs($this->superAdmin())
            ->post(route('sales-invoices.store'), $this->validPayload($party));

        $invoice = SalesInvoice::firstOrFail();
        $this->assertSame('APX00000001', $invoice->invoice_number);
        $this->assertTrue($invoice->created_at->equalTo($invoice->updated_at));
    }

    public function test_create_screen_defaults_the_invoice_date_to_today(): void
    {
        $this->customer();

        $this->actingAs($this->superAdmin())
            ->get(route('sales-invoices.create'))
            ->assertOk()
            ->assertSee(now()->format('Y-m-d'));
    }

    public function test_company_profile_can_save_invoice_address_and_payment_instructions(): void
    {
        $company = CompanyProfile::current();

        $this->actingAs($this->superAdmin())
            ->patch(route('company-profile.update'), [
                'name' => $company->name,
                'emails' => $company->emails,
                'billing_address' => 'Office address, Karachi',
                'invoice_payment_instructions' => 'Please include the invoice number in your remittance.',
            ])
            ->assertRedirect(route('company-profile.index'))
            ->assertSessionHasNoErrors();

        $company->refresh();
        $this->assertSame('Office address, Karachi', $company->billing_address);
        $this->assertSame('Please include the invoice number in your remittance.', $company->invoice_payment_instructions);
    }

    public function test_invoice_numbers_are_unique_and_details_delete_with_the_invoice(): void
    {
        $party = $this->customer();
        $user = $this->superAdmin();
        $this->actingAs($user)->post(route('sales-invoices.store'), $this->validPayload($party));
        $first = SalesInvoice::firstOrFail();
        $this->actingAs($user)->post(route('sales-invoices.store'), $this->validPayload($party));
        $second = SalesInvoice::latest('id')->firstOrFail();

        $this->assertNotSame($first->invoice_number, $second->invoice_number);
        $this->actingAs($user)->delete(route('sales-invoices.destroy', $first))->assertRedirect(route('sales-invoices.index'));
        $this->assertSame(0, SalesInvoiceDetail::where('sales_invoice_id', $first->id)->count());
    }

    public function test_invoice_pdf_uses_company_customer_and_bank_account_details(): void
    {
        CompanyProfile::current()->update([
            'name' => 'AMK Test Logistics',
            'billing_address' => 'Business District, Karachi',
            'invoice_payment_instructions' => 'Please reference the invoice number with payment.',
        ]);
        BankAccount::create([
            'bank_name' => 'Mashreq Bank',
            'beneficiary_name' => 'Test Beneficiary',
            'account' => '1234567890',
            'iban' => 'PK00TEST0000001234567890',
        ]);
        $party = $this->customer();
        $this->actingAs($this->superAdmin())->post(route('sales-invoices.store'), $this->validPayload($party));

        $invoice = SalesInvoice::with(['party', 'details'])->firstOrFail();
        $this->view('sales-invoices.pdf', [
            'invoice' => $invoice,
            'company' => CompanyProfile::current(),
            'bankAccount' => BankAccount::current(),
            'brandLogoPath' => CompanyProfile::current()->logoPath(),
            'brandLogoSize' => CompanyProfile::current()->logoDisplaySize(240, 125),
        ])
            ->assertSee('AMK Test Logistics')
            ->assertSee('Business District, Karachi')
            ->assertSee('UNR Test Logistics')
            ->assertSee('SCSU1234567')
            ->assertSee('269,500.00')
            ->assertSee('Mashreq Bank')
            ->assertSee('PK00TEST0000001234567890')
            ->assertSee('Please reference the invoice number with payment.');

        $response = $this->actingAs($this->superAdmin())->get(route('sales-invoices.pdf', $invoice));
        $response->assertOk()->assertHeader('content-type', 'application/pdf')->assertSee('%PDF', false);
        $this->assertStringContainsString('APX00000001.pdf', $response->headers->get('content-disposition'));

        $viewed = $this->actingAs($this->superAdmin())->get(route('sales-invoices.pdf-view', $invoice));
        $viewed->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('inline', $viewed->headers->get('content-disposition'));
        $this->assertStringContainsString('APX00000001.pdf', $viewed->headers->get('content-disposition'));
    }

    public function test_pdf_download_succeeds_without_optional_company_and_bank_values(): void
    {
        $party = $this->customer();
        $invoice = SalesInvoice::create(['invoice_number' => 'APX00000042', 'party_id' => $party->id]);
        $invoice->details()->create(['description' => 'Container hire', 'amount' => '125.00']);

        $this->actingAs($this->superAdmin())
            ->get(route('sales-invoices.pdf', $invoice))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_index_renders_datatable_and_data_endpoint_returns_json(): void
    {
        $party = $this->customer();
        $user = $this->superAdmin();
        $this->actingAs($user)->post(route('sales-invoices.store'), $this->validPayload($party, ['status' => 'paid']));

        $index = $this->actingAs($user)
            ->get(route('sales-invoices.index'))
            ->assertOk()
            ->assertSee('Sales Invoices')
            ->assertSee('+ Create Invoice');

        // The status pane is only usable if the client bundle ships the
        // SearchPanes extension and the column carries its options.
        $html = $index->getContent();
        $this->assertStringContainsString('searchPanes', $html);
        $this->assertStringContainsString('unpaid', $html);
        $this->assertStringContainsString('paid', $html);
        $this->assertStringContainsString('status_badge', $html);

        $response = $this->actingAs($user)
            ->get(route('sales-invoices.data', ['draw' => 1, 'start' => 0, 'length' => 10]), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertJsonStructure(['draw', 'recordsTotal', 'recordsFiltered', 'data']);

        $row = collect($response->json('data'))->firstOrFail();
        $this->assertSame('APX00000001', $row['invoice_number']);
        $this->assertSame('UNR Test Logistics', $row['customer']);
        $this->assertSame('PKR 269,500.00', $row['total_amount']);
        $this->assertStringContainsString('Paid', $row['status_badge']);
        $this->assertStringContainsString((string) route('sales-invoices.pdf-view', 1), $row['actions']);
    }

    public function test_currency_codes_come_from_the_latest_exchange_rate_snapshot(): void
    {
        Currency::create([
            'exchange_rate_date' => '2026-10-04',
            'exchange_rate' => json_encode(['USD' => 1, 'AED' => 3.67, 'EUR' => 0.92]),
        ]);

        $this->actingAs($this->superAdmin())
            ->get(route('sales-invoices.create'))
            ->assertOk()
            ->assertSee('AED')
            ->assertSee('EUR');

        $codes = ExchangeRateService::availableCodes();
        $this->assertSame(['USD', 'AED', 'EUR'], $codes);
    }

    public function test_invoice_can_be_stored_and_displayed_in_a_different_currency(): void
    {
        Currency::create([
            'exchange_rate_date' => '2026-10-04',
            'exchange_rate' => json_encode(['USD' => 1, 'AED' => 3.67]),
        ]);
        $party = $this->customer();

        $this->actingAs($this->superAdmin())
            ->post(route('sales-invoices.store'), $this->validPayload($party, ['currency_code' => 'AED']))
            ->assertSessionHasNoErrors();

        $invoice = SalesInvoice::firstOrFail();
        $this->assertSame('AED', $invoice->currency_code);

        $this->actingAs($this->superAdmin())
            ->get(route('sales-invoices.edit', $invoice))
            ->assertOk()
            ->assertSee('Amount (AED)')
            ->assertSee('Total AED');
    }

    public function test_currency_must_be_an_available_code_and_status_must_be_known(): void
    {
        $party = $this->customer();

        $this->actingAs($this->superAdmin())
            ->post(route('sales-invoices.store'), $this->validPayload($party, [
                'currency_code' => 'XXX',
                'status' => 'maybe',
            ]))
            ->assertSessionHasErrors(['currency_code', 'status']);

        $this->assertSame(0, SalesInvoice::count());
    }

    public function test_invoice_stores_the_chosen_bank_account(): void
    {
        $party = $this->customer();
        $first = BankAccount::create(['bank' => 'HBL', 'beneficiary_name' => 'First Beneficiary', 'account' => '111']);
        $chosen = BankAccount::create(['bank' => 'MCB', 'beneficiary_name' => 'Second Beneficiary', 'account' => '222']);

        $this->actingAs($this->superAdmin())
            ->post(route('sales-invoices.store'), $this->validPayload($party, ['bank_account_id' => $chosen->id]))
            ->assertSessionHasNoErrors();

        $invoice = SalesInvoice::with('bankAccount')->firstOrFail();
        $this->assertSame($chosen->id, $invoice->bank_account_id);
        $this->assertSame($chosen->beneficiary_name, $invoice->bankAccount->beneficiary_name);

        $this->actingAs($this->superAdmin())
            ->get(route('sales-invoices.edit', $invoice))
            ->assertOk()
            ->assertSee('Second Beneficiary');
    }

    public function test_bank_account_id_must_reference_a_saved_account(): void
    {
        $party = $this->customer();

        $this->actingAs($this->superAdmin())
            ->post(route('sales-invoices.store'), $this->validPayload($party, ['bank_account_id' => 999999]))
            ->assertSessionHasErrors(['bank_account_id']);

        $this->assertSame(0, SalesInvoice::count());
    }

    public function test_pdf_prints_the_invoices_chosen_bank_account(): void
    {
        BankAccount::create([
            'bank_name' => 'Mashreq Bank',
            'beneficiary_name' => 'Test Beneficiary',
            'account' => '1234567890',
            'iban' => 'PK00TEST0000001234567890',
        ]);
        $chosen = BankAccount::create([
            'bank' => 'MCB',
            'bank_name' => 'MCB Bank',
            'beneficiary_name' => 'Chosen Beneficiary',
            'account' => '99999',
            'iban' => 'PK00CHOSEN0000009999999999',
        ]);
        $party = $this->customer();
        $this->actingAs($this->superAdmin())->post(route('sales-invoices.store'), $this->validPayload($party, ['bank_account_id' => $chosen->id]));

        $invoice = SalesInvoice::with(['party', 'details', 'bankAccount'])->firstOrFail();

        $this->view('sales-invoices.pdf', [
            'invoice' => $invoice,
            'company' => CompanyProfile::current(),
            'bankAccount' => $invoice->bankAccount,
            'brandLogoPath' => CompanyProfile::current()->logoPath(),
            'brandLogoSize' => CompanyProfile::current()->logoDisplaySize(240, 125),
        ])
            ->assertSee('Chosen Beneficiary')
            ->assertSee('PK00CHOSEN0000009999999999')
            ->assertDontSee('Test Beneficiary');

        $this->actingAs($this->superAdmin())
            ->get(route('sales-invoices.pdf', $invoice))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }
}
