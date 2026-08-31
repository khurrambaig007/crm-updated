<?php

namespace Tests\Feature;

use App\Http\Middleware\PermissionMiddleware;
use App\Models\User;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ContainerPurchaseChildTablesTest extends TestCase
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

    public function test_child_datatables_return_json(): void
    {
        $this->assertOkServerSide(route('container-purchases.models-data'));
        $this->assertOkServerSide(route('container-purchases.invoice-data'));
        $this->assertOkServerSide(route('container-purchases.releases-data'));
        $this->assertOkServerSide(route('container-purchases.debit-data'));
        $this->assertOkServerSide(route('container-purchases.po-cancel-data'));
    }

    public function test_child_datatables_render_child_rows_with_relations(): void
    {
        \DB::table('container_purchases')->insert([
            'id' => 1,
            'trans_no' => 'VRM0000000001',
            'date' => now()->toDateString(),
            'currency_code' => 'USD',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('container_purchase_models')->insert([
            'container_size_id' => 1,
            'container_type_id' => 1,
            'container_kind_id' => 1,
            'quantity' => 2,
            'amount' => 2000,
            'rate' => 1,
            'container_purchase_detail_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('container_purchase_invoices')->insert([
            'doc_no' => 'DOC-1',
            'invoice_no' => 'INV-1',
            'invoice_date' => now(),
            'settlement_type_id' => 1,
            'payment_agent_id' => 1,
            'currency_id' => 1,
            'amount' => 100,
            'supplier_id' => 1,
            'location_id' => 1,
            'sub_company_id' => 1,
            'currency_code' => 'USD',
            'total_amount' => 100,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('container_purchase_releases')->insert([
            'container_number' => 'CNTR-001',
            'container_size' => 1,
            'container_type' => 1,
            'container_kind' => 1,
            'm_f_year' => '2020',
            'rate' => 1,
            'remarks' => 'Test remarks',
            'container_purchase_detail_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('debite_notes')->insert([
            'doc_no' => 'DN-1',
            'invoice_id' => 1,
            'settlement_type_id' => 1,
            'payment_agent_id' => 1,
            'currency_id' => 1,
            'amount' => 50,
            'supplier_id' => 1,
            'location_id' => 1,
            'sub_company_id' => 1,
            'currency_code' => 'USD',
            'total_amount' => 50,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('po_cancels')->insert([
            'invoice_id' => 1,
            'container_purchase_detail_id' => 1,
            'transaction_date' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $models = $this->get(route('container-purchases.models-data'))->json('data');
        $this->assertCount(1, $models);
        $this->assertSame('20', $models[0]['container_size'], 'size relation resolves to label');
        $this->assertSame('VRM0000000001', $models[0]['trans_no'], 'container purchase relation resolves to trans_no');
        $this->assertSame('2000', (string) $models[0]['amount'], 'amount column renders');

        $invoices = $this->get(route('container-purchases.invoice-data'))->json('data');
        $this->assertCount(1, $invoices);
        $this->assertSame('Cash', $invoices[0]['settlement_type'], 'settlement_type relation resolves');
        $this->assertSame('Test Supplier', $invoices[0]['supplier'], 'supplier relation resolves');
        $this->assertSame('Testport', $invoices[0]['location'], 'location relation resolves');
        $this->assertSame('Sub Co', $invoices[0]['sub_company'], 'sub_company relation resolves');
        $this->assertSame('USD (@1)', $invoices[0]['currency'], 'currency_code renders with rate');
        $this->assertSame('100', (string) $invoices[0]['total_amount'], 'total_amount column renders');

        $releases = $this->get(route('container-purchases.releases-data'))->json('data');
        $this->assertCount(1, $releases);
        $this->assertSame('20', $releases[0]['container_size'], 'release size relation resolves');
        $this->assertSame('CNTR-001', $releases[0]['container_number']);
        $this->assertSame('VRM0000000001', $releases[0]['trans_no'], 'transaction no resolves from container purchase');
        $this->assertSame('Test remarks', $releases[0]['remarks'], 'remarks column renders');

        $debits = $this->get(route('container-purchases.debit-data'))->json('data');
        $this->assertCount(1, $debits);
        $this->assertSame('Cash', $debits[0]['settlement_type'], 'debit settlement_type relation resolves');

        $poCancels = $this->get(route('container-purchases.po-cancel-data'))->json('data');
        $this->assertCount(1, $poCancels);
        $this->assertSame('INV-1', $poCancels[0]['invoice_no'], 'invoice relation resolves to invoice_no');
        $this->assertSame('VRM0000000001', $poCancels[0]['trans_no'], 'transaction no resolves from container purchase');
    }

    public function test_transactions_for_purchase_returns_partial_matches(): void
    {
        $base = ['date' => now()->toDateString(), 'currency_code' => 'USD', 'created_at' => now(), 'updated_at' => now()];

        \DB::table('container_purchases')->insert([
            ['id' => 1, 'trans_no' => 'VRM0000000001'] + $base,
            ['id' => 2, 'trans_no' => 'VRM0000000011'] + $base,
            ['id' => 3, 'trans_no' => 'ABC0000000005'] + $base,
        ]);

        $response = $this->getJson(route('container-purchases.transactions-for-purchase').'?q=0001');
        $response->assertOk();
        $results = $response->json('results');
        $this->assertCount(1, $results, 'partial match on last digits returns exactly the matching trans_no');
        $this->assertSame('VRM0000000001', $results[0]['text']);

        $matchAll = $this->getJson(route('container-purchases.transactions-for-purchase').'?q=VRM0000');
        $matchAll->assertOk();
        $this->assertCount(2, $matchAll->json('results'), 'substring matches all trans_no containing the term');

        $none = $this->getJson(route('container-purchases.transactions-for-purchase').'?q=zzzz');
        $none->assertOk();
        $this->assertSame([], $none->json('results'), 'no match returns empty results');

        $empty = $this->getJson(route('container-purchases.transactions-for-purchase').'?q=');
        $empty->assertOk();
        $this->assertCount(3, $empty->json('results'), 'empty query returns the recent transactions');
        $this->assertSame('ABC0000000005', $empty->json('results.0.text'), 'recent transactions are returned newest id first');
    }

    public function test_transactions_for_purchase_paginates(): void
    {
        $rows = [];
        for ($i = 1; $i <= 60; $i++) {
            $rows[] = [
                'id' => $i,
                'trans_no' => 'VRM'.str_pad((string) $i, 10, '0', STR_PAD_LEFT),
                'date' => now()->toDateString(),
                'currency_code' => 'USD',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        \DB::table('container_purchases')->insert($rows);

        $page1 = $this->getJson(route('container-purchases.transactions-for-purchase').'?q=VRM000000&page=1');
        $page1->assertOk();
        $this->assertCount(50, $page1->json('results'), 'first page returns a full page');
        $this->assertTrue($page1->json('pagination.more'), 'more must be true while a next page exists');
        $this->assertSame('VRM0000000001', $page1->json('results.0.text'), 'results remain ordered by trans_no');

        $page2 = $this->getJson(route('container-purchases.transactions-for-purchase').'?q=VRM000000&page=2');
        $page2->assertOk();
        $this->assertCount(10, $page2->json('results'), 'second page returns the remainder');
        $this->assertFalse($page2->json('pagination.more'), 'more must be false on the last page');
        $this->assertSame('VRM0000000051', $page2->json('results.0.text'));
    }

    protected function assertOkServerSide(string $url): void
    {
        $response = $this->call('GET', $url, ['draw' => 1, 'start' => 0, 'length' => 10], [], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertOk();
        $this->assertIsArray($response->json(), 'server-side endpoint must return JSON');
        $this->assertArrayHasKey('recordsTotal', $response->json(), 'response must include DataTables metadata');
    }
}
