<?php

namespace Tests\Feature;

use App\Http\Middleware\PermissionMiddleware;
use App\Models\User;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ContainerPurchaseChildStoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedLookups();

        $this->withoutMiddleware([Authenticate::class, PermissionMiddleware::class]);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
        $this->actingAs($user);
    }

    public function test_child_store_endpoints_persist_rows(): void
    {
        $parentId = $this->createParentPurchase();
        $invoiceId = $this->createInvoiceViaEndpoint();

        $modelResponse = $this->postJson(route('container-purchases.models.store'), [
            'container_purchase_detail_id' => $parentId,
            'container_size_id' => 1,
            'container_type_id' => 1,
            'container_kind_id' => 1,
            'quantity' => 2,
            'amount' => 2000,
            'rate' => 1,
        ]);
        $modelResponse->assertStatus(201)->assertJson(['message' => 'Purchase saved successfully.']);
        $this->assertDatabaseHas('container_purchase_models', [
            'container_purchase_detail_id' => $parentId,
            'container_size_id' => 1,
            'container_type_id' => 1,
            'container_kind_id' => 1,
            'quantity' => '2',
            'amount' => 2000,
        ]);

        $releaseResponse = $this->postJson(route('container-purchases.releases.store'), [
            'container_purchase_detail_id' => $parentId,
            'container_number' => 'CNTR-001',
            'container_size' => 1,
            'container_type' => 1,
            'container_kind' => 1,
            'm_f_year' => '2020',
            'rate' => 1,
            'remarks' => 'Test release',
            'original_container_number' => 'CNTR-000',
        ]);
        $releaseResponse->assertStatus(201)->assertJson(['message' => 'Release saved successfully.']);
        $this->assertDatabaseHas('container_purchase_releases', [
            'container_purchase_detail_id' => $parentId,
            'container_number' => 'CNTR-001',
            'container_size' => 1,
            'container_type' => 1,
            'container_kind' => 1,
            'm_f_year' => '2020',
            'rate' => 1,
        ]);

        $debitResponse = $this->postJson(route('container-purchases.debits.store'), [
            'container_purchase_detail_id' => $parentId,
            'doc_no' => 'DN-1',
            'invoice_id' => $invoiceId,
            'settlement_type_id' => 1,
            'payment_agent_id' => 1,
            'amount' => 50,
            'supplier_id' => 1,
            'location_id' => 1,
            'sub_company_id' => 1,
            'currency' => 'USD',
            'currency_code' => 'USD',
            'currency_exchange_rate' => '1',
            'total_amount' => 50,
        ]);
        $debitResponse->assertStatus(201)->assertJson(['message' => 'Debit note saved successfully.']);
        $this->assertDatabaseHas('debite_notes', [
            'container_purchase_detail_id' => $parentId,
            'doc_no' => 'DN-1',
            'invoice_id' => $invoiceId,
            'currency_id' => 1,
            'currency_code' => 'USD',
            'amount' => 50,
        ]);

        $poCancelResponse = $this->postJson(route('container-purchases.po-cancels.store'), [
            'doc_no' => 100,
            'trans_no' => 200,
            'transaction_date' => '2026-08-20',
        ]);
        $poCancelResponse->assertStatus(201)->assertJson(['message' => 'PO cancel saved successfully.']);
        $poCancel = \DB::table('po_cancels')->first();
        $this->assertSame(100, (int) $poCancel->doc_no, 'doc_no stored as integer');
        $this->assertSame(200, (int) $poCancel->trans_no, 'trans_no stored as integer');
        $this->assertSame('2026-08-20', substr((string) $poCancel->transaction_date, 0, 10), 'transaction date persisted');
    }

    public function test_invoice_store_resolves_currency_id_and_links_parent(): void
    {
        $parentId = $this->createParentPurchase();

        $invoiceResponse = $this->postJson(route('container-purchases.invoices.store'), [
            'container_purchase_detail_id' => $parentId,
            'doc_no' => 'DOC-1',
            'invoice_no' => 'INV-1',
            'invoice_date' => '2026-08-20',
            'settlement_type_id' => 1,
            'payment_agent_id' => 1,
            'amount' => 100,
            'supplier_id' => 1,
            'location_id' => 1,
            'sub_company_id' => 1,
            'currency' => 'USD',
            'currency_code' => 'USD',
            'currency_exchange_rate' => '1',
            'total_amount' => 100,
        ]);
        $invoiceResponse->assertStatus(201)->assertJson(['message' => 'Invoice saved successfully.']);
        $this->assertSame(1, \DB::table('invoices')->count(), 'invoice row persisted');
        $this->assertDatabaseHas('invoices', [
            'invoice_no' => 'INV-1',
            'currency_id' => 1,
            'currency_code' => 'USD',
            'container_purchase_detail_id' => $parentId,
            'total_amount' => 100,
        ]);
    }

    public function test_child_store_rejects_bad_data(): void
    {
        $this->postJson(route('container-purchases.models.store'), [
            'container_size_id' => 9999,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['container_size_id', 'container_type_id', 'container_kind_id']);

        $this->postJson(route('container-purchases.invoices.store'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['doc_no', 'invoice_no', 'invoice_date', 'settlement_type_id', 'payment_agent_id', 'amount', 'supplier_id', 'location_id', 'sub_company_id', 'currency']);

        $this->postJson(route('container-purchases.releases.store'), [
            'container_size' => 9999,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['container_size']);

        $this->postJson(route('container-purchases.debits.store'), [
            'invoice_id' => 9999,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['doc_no', 'invoice_id', 'settlement_type_id', 'payment_agent_id', 'amount', 'supplier_id', 'location_id', 'sub_company_id', 'currency']);

        $this->postJson(route('container-purchases.po-cancels.store'), [
            'doc_no' => 'not-a-number',
            'trans_no' => 'not-a-number',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['doc_no', 'trans_no', 'transaction_date']);
    }

    public function test_child_screens_render_forms_with_store_urls_and_csrf(): void
    {
        $expected = [
            'container-purchases.models' => ['form' => 'cp-purchase-form', 'route' => 'container-purchases.models.store', 'hasParent' => true],
            'container-purchases.invoices' => ['form' => 'cp-invoice-form', 'route' => 'container-purchases.invoices.store', 'hasParent' => true],
            'container-purchases.releases' => ['form' => 'cp-release-form', 'route' => 'container-purchases.releases.store', 'hasParent' => true],
            'container-purchases.debits' => ['form' => 'cp-debit-form', 'route' => 'container-purchases.debits.store', 'hasParent' => true],
            'container-purchases.po-cancels' => ['form' => 'cp-po-cancel-form', 'route' => 'container-purchases.po-cancels.store', 'hasParent' => false],
        ];

        foreach ($expected as $screenRoute => $config) {
            $response = $this->get(route($screenRoute));
            $response->assertOk();

            $dom = new \DOMDocument;
            @$dom->loadHTML('<?xml encoding="UTF-8">'.$response->getContent(), LIBXML_NOERROR);
            $xpath = new \DOMXPath($dom);

            $form = $xpath->query('//form[@id="'.$config['form'].'"]');
            $this->assertSame(1, $form->length, "{$config['form']} must exist on the {$screenRoute} screen");
            $this->assertSame(
                route($config['route']),
                $form->item(0)?->getAttribute('data-submit-url'),
                "{$config['form']} must carry the store URL for AJAX submission"
            );

            $tokens = $xpath->query('//form[@id="'.$config['form'].'"]//input[@name="_token"]');
            $this->assertSame(1, $tokens->length, "{$config['form']} must include a CSRF token so the browser POST is not rejected");

            $parentField = $xpath->query('//form[@id="'.$config['form'].'"]//input[@name="container_purchase_detail_id"]');
            if ($config['hasParent']) {
                $this->assertSame(1, $parentField->length, "{$config['form']} must carry the parent purchase id");
            } else {
                $this->assertSame(0, $parentField->length, 'po_cancels has no parent FK so no hidden parent field');
            }
        }
    }

    public function test_child_update_endpoints_persist_changes(): void
    {
        $parentId = $this->createParentPurchase();
        $invoiceId = $this->createInvoiceViaEndpoint();

        $modelId = \DB::table('container_purchase_models')->insertGetId([
            'container_size_id' => 1,
            'container_type_id' => 1,
            'container_kind_id' => 1,
            'quantity' => 2,
            'amount' => 2000,
            'rate' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->patchJson(route('container-purchases.models.update', $modelId), [
            'container_purchase_detail_id' => $parentId,
            'container_size_id' => 1,
            'container_type_id' => 1,
            'container_kind_id' => 1,
            'quantity' => 5,
            'amount' => 5000,
            'rate' => 2,
        ])->assertOk()->assertJson(['message' => 'Purchase updated successfully.']);
        $this->assertDatabaseHas('container_purchase_models', [
            'id' => $modelId,
            'container_purchase_detail_id' => $parentId,
            'quantity' => '5',
            'amount' => 5000,
            'rate' => 2,
        ]);

        $releaseId = \DB::table('container_purchase_releases')->insertGetId([
            'container_number' => 'CNTR-OLD',
            'container_size' => 1,
            'container_type' => 1,
            'container_kind' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->patchJson(route('container-purchases.releases.update', $releaseId), [
            'container_purchase_detail_id' => $parentId,
            'container_number' => 'CNTR-UPD',
            'container_size' => 1,
            'container_type' => 1,
            'container_kind' => 1,
            'm_f_year' => '2021',
            'rate' => 2,
            'remarks' => 'Updated release',
            'original_container_number' => 'CNTR-ORIG',
        ])->assertOk()->assertJson(['message' => 'Release updated successfully.']);
        $this->assertDatabaseHas('container_purchase_releases', [
            'id' => $releaseId,
            'container_number' => 'CNTR-UPD',
            'container_purchase_detail_id' => $parentId,
            'original_container_number' => 'CNTR-ORIG',
        ]);

        $this->patchJson(route('container-purchases.invoices.update', $invoiceId), [
            'container_purchase_detail_id' => $parentId,
            'doc_no' => 'DOC-UPD',
            'invoice_no' => 'INV-UPD',
            'invoice_date' => '2026-08-21',
            'settlement_type_id' => 1,
            'payment_agent_id' => 1,
            'amount' => 150,
            'supplier_id' => 1,
            'location_id' => 1,
            'sub_company_id' => 1,
            'currency' => 'USD',
            'currency_code' => 'USD',
            'currency_exchange_rate' => '1',
            'total_amount' => 150,
        ])->assertOk()->assertJson(['message' => 'Invoice updated successfully.']);
        $this->assertDatabaseHas('invoices', [
            'id' => $invoiceId,
            'invoice_no' => 'INV-UPD',
            'amount' => 150,
            'currency_id' => 1,
        ]);

        $debitId = \DB::table('debite_notes')->insertGetId([
            'doc_no' => 'DN-OLD',
            'invoice_id' => $invoiceId,
            'settlement_type_id' => 1,
            'payment_agent_id' => 1,
            'currency_id' => 1,
            'amount' => 50,
            'supplier_id' => 1,
            'location_id' => 1,
            'sub_company_id' => 1,
            'currency_code' => 'USD',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->patchJson(route('container-purchases.debits.update', $debitId), [
            'container_purchase_detail_id' => $parentId,
            'doc_no' => 'DN-UPD',
            'invoice_id' => $invoiceId,
            'settlement_type_id' => 1,
            'payment_agent_id' => 1,
            'amount' => 60,
            'supplier_id' => 1,
            'location_id' => 1,
            'sub_company_id' => 1,
            'currency' => 'USD',
            'currency_code' => 'USD',
            'currency_exchange_rate' => '1',
            'total_amount' => 60,
        ])->assertOk()->assertJson(['message' => 'Debit note updated successfully.']);
        $this->assertDatabaseHas('debite_notes', [
            'id' => $debitId,
            'doc_no' => 'DN-UPD',
            'amount' => 60,
        ]);

        $poCancelId = \DB::table('po_cancels')->insertGetId([
            'doc_no' => 100,
            'trans_no' => 200,
            'transaction_date' => now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->patchJson(route('container-purchases.po-cancels.update', $poCancelId), [
            'doc_no' => 101,
            'trans_no' => 201,
            'transaction_date' => '2026-08-22',
        ])->assertOk()->assertJson(['message' => 'PO cancel updated successfully.']);
        $poCancel = \DB::table('po_cancels')->find($poCancelId);
        $this->assertSame(101, (int) $poCancel->doc_no);
        $this->assertSame(201, (int) $poCancel->trans_no);
        $this->assertSame('2026-08-22', substr((string) $poCancel->transaction_date, 0, 10));
    }

    public function test_child_update_rejects_bad_data(): void
    {
        $modelId = \DB::table('container_purchase_models')->insertGetId([
            'container_size_id' => 1,
            'container_type_id' => 1,
            'container_kind_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->patchJson(route('container-purchases.models.update', $modelId), [
            'container_size_id' => 9999,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['container_size_id', 'container_type_id', 'container_kind_id']);
    }

    public function test_child_destroy_endpoints_remove_rows(): void
    {
        $parentId = $this->createParentPurchase();
        $invoiceId = $this->createInvoiceViaEndpoint();

        $modelId = \DB::table('container_purchase_models')->insertGetId([
            'container_size_id' => 1,
            'container_type_id' => 1,
            'container_kind_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->deleteJson(route('container-purchases.models.destroy', $modelId))
            ->assertOk()->assertJson(['message' => 'Purchase deleted successfully.']);
        $this->assertDatabaseMissing('container_purchase_models', ['id' => $modelId]);

        $releaseId = \DB::table('container_purchase_releases')->insertGetId([
            'container_number' => 'CNTR-001',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->deleteJson(route('container-purchases.releases.destroy', $releaseId))
            ->assertOk()->assertJson(['message' => 'Release deleted successfully.']);
        $this->assertDatabaseMissing('container_purchase_releases', ['id' => $releaseId]);

        $debitId = \DB::table('debite_notes')->insertGetId([
            'doc_no' => 'DN-1',
            'invoice_id' => $invoiceId,
            'settlement_type_id' => 1,
            'payment_agent_id' => 1,
            'currency_id' => 1,
            'amount' => 50,
            'supplier_id' => 1,
            'location_id' => 1,
            'sub_company_id' => 1,
            'currency_code' => 'USD',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->deleteJson(route('container-purchases.debits.destroy', $debitId))
            ->assertOk()->assertJson(['message' => 'Debit note deleted successfully.']);
        $this->assertDatabaseMissing('debite_notes', ['id' => $debitId]);

        $this->deleteJson(route('container-purchases.invoices.destroy', $invoiceId))
            ->assertOk()->assertJson(['message' => 'Invoice deleted successfully.']);
        $this->assertDatabaseMissing('invoices', ['id' => $invoiceId]);

        $poCancelId = \DB::table('po_cancels')->insertGetId([
            'doc_no' => 100,
            'trans_no' => 200,
            'transaction_date' => now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->deleteJson(route('container-purchases.po-cancels.destroy', $poCancelId))
            ->assertOk()->assertJson(['message' => 'PO cancel deleted successfully.']);
        $this->assertDatabaseMissing('po_cancels', ['id' => $poCancelId]);

        $this->assertSame(1, \DB::table('container_purchases')->where('id', $parentId)->count(), 'parent purchase is preserved');
    }

    protected function seedLookups(): void
    {
        \DB::table('suppliers')->updateOrInsert(
            ['id' => 1],
            ['name' => 'Test Supplier', 'address' => '123 Test St', 'created_at' => now(), 'updated_at' => now()]
        );

        \DB::table('pols')->updateOrInsert(
            ['id' => 1],
            ['port_code' => 'TP', 'city' => 'Testport', 'country' => 'Testland', 'created_at' => now(), 'updated_at' => now()]
        );

        \DB::table('agents')->updateOrInsert(
            ['id' => 1],
            ['code' => 'AG-TEST', 'name' => 'Test Agent', 'created_at' => now(), 'updated_at' => now()]
        );

        \DB::table('container_sizes')->updateOrInsert(
            ['id' => 1],
            ['size' => '20', 'created_at' => now(), 'updated_at' => now()]
        );

        \DB::table('container_types')->updateOrInsert(
            ['id' => 1],
            ['name' => 'GP', 'created_at' => now(), 'updated_at' => now()]
        );

        \DB::table('container_kinds')->updateOrInsert(
            ['id' => 1],
            ['name' => 'Standard', 'created_at' => now(), 'updated_at' => now()]
        );

        \DB::table('settlement_types')->updateOrInsert(
            ['id' => 1],
            ['name' => 'Cash', 'number' => '001', 'created_at' => now(), 'updated_at' => now()]
        );

        \DB::table('sub_companies')->updateOrInsert(
            ['id' => 1],
            ['name' => 'Sub Co', 'created_at' => now(), 'updated_at' => now()]
        );

        \DB::table('currencies')->updateOrInsert(
            ['id' => 1],
            ['exchange_rate_date' => now()->toDateString(), 'exchange_rate' => json_encode(['USD' => 1]), 'created_at' => now(), 'updated_at' => now()]
        );
    }

    private function createParentPurchase(): int
    {
        return \DB::table('container_purchases')->insertGetId([
            'trans_no' => 'VRM0000000001',
            'date' => now()->toDateString(),
            'currency_code' => 'USD',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createInvoiceViaEndpoint(): int
    {
        $this->postJson(route('container-purchases.invoices.store'), [
            'doc_no' => 'DOC-1',
            'invoice_no' => 'INV-1',
            'invoice_date' => '2026-08-20',
            'settlement_type_id' => 1,
            'payment_agent_id' => 1,
            'amount' => 100,
            'supplier_id' => 1,
            'location_id' => 1,
            'sub_company_id' => 1,
            'currency' => 'USD',
            'currency_code' => 'USD',
        ])->assertStatus(201);

        return (int) \DB::table('invoices')->orderByDesc('id')->value('id');
    }
}
