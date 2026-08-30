<?php

namespace Tests\Feature;

use App\Http\Middleware\PermissionMiddleware;
use App\Models\ContainerPurchase;
use App\Models\User;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;

class ContainerPurchaseStoreUpdateTest extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedLookups();

        // Disable auth + permission middleware so the request reaches the
        // controller. We then actAs a user so views that call
        // auth()->user()->... don't blow up.
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
            [
                'name' => 'Test Supplier',
                'address' => '123 Test St',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        \DB::table('pols')->updateOrInsert(
            ['id' => 1],
            [
                'port_code' => 'TP',
                'city' => 'Testport',
                'country' => 'Testland',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        \DB::table('agents')->updateOrInsert(
            ['id' => 1],
            ['code' => 'AG-TEST', 'name' => 'Test Agent', 'created_at' => now(), 'updated_at' => now()]
        );
    }

    public function test_store_then_update_round_trip_persists_every_field(): void
    {
        $payload = [
            'trans_no' => 'VRM0000000001',
            'date' => '2026-08-20',
            'normal_purchase' => 'normal',
            'supplier_id' => '1',
            'port_id' => '1',
            'handling_id' => '1',
            'expected_delivery' => '2026-08-25',
            'release_no' => 'REL-001',
            'currency' => 'USD',
            'currency_code' => 'USD',
            'rate' => '1.25',
            'principal' => '1',
        ];

        $createResponse = $this->post(route('container-purchases.store'), $payload);

        $createResponse->assertRedirect();
        $this->assertSame(1, ContainerPurchase::count(), 'record should be created');

        $record = ContainerPurchase::first();
        $this->assertNotNull($record, 'record fetched from DB');

        foreach ($payload as $field => $expected) {
            $actual = $record->{$field};
            if (in_array($field, ['date', 'expected_delivery'], true)) {
                $this->assertSame($expected, $actual?->format('Y-m-d'), "create: {$field}");
            } elseif (str_ends_with($field, '_id')) {
                $this->assertSame((int) $expected, (int) $actual, "create: {$field}");
            } else {
                $this->assertSame((string) $expected, (string) $actual, "create: {$field}");
            }
        }

        $createResponse->assertSessionHas('status');

        $updatePayload = $payload;
        $updatePayload['trans_no'] = 'VRM0000000001';
        $updatePayload['date'] = '2026-09-01';
        $updatePayload['normal_purchase'] = 'lease';
        $updatePayload['supplier_id'] = '1';
        $updatePayload['port_id'] = '1';
        $updatePayload['handling_id'] = '1';
        $updatePayload['expected_delivery'] = '2026-09-20';
        $updatePayload['release_no'] = 'REL-002';
        $updatePayload['currency'] = 'EUR';
        $updatePayload['currency_code'] = 'EUR';
        $updatePayload['rate'] = '0.95';
        $updatePayload['principal'] = '2';

        $updateResponse = $this->patch(
            route('container-purchases.update', $record),
            $updatePayload
        );

        $updateResponse->assertRedirect();
        $this->assertSame(1, ContainerPurchase::count(), 'update must not create a new row');

        $record->refresh();

        foreach ($updatePayload as $field => $expected) {
            $actual = $record->{$field};
            if (in_array($field, ['date', 'expected_delivery'], true)) {
                $this->assertSame($expected, $actual?->format('Y-m-d'), "update: {$field}");
            } elseif (str_ends_with($field, '_id')) {
                $this->assertSame((int) $expected, (int) $actual, "update: {$field}");
            } else {
                $this->assertSame((string) $expected, (string) $actual, "update: {$field}");
            }
        }

        $updateResponse->assertSessionHas('status');

        $editResponse = $this->get(route('container-purchases.edit', $record));
        $editResponse->assertOk();
        $editResponse->assertSee('REL-002');
        $editResponse->assertSee('VRM0000000001');
    }

    public function test_edit_page_does_not_nest_delete_form_inside_main_form(): void
    {
        $record = ContainerPurchase::create([
            'trans_no' => 'VRM0000000001',
            'currency_code' => 'USD',
            'release_no' => 'REL-001',
        ]);

        $editResponse = $this->get(route('container-purchases.edit', $record));
        $editResponse->assertOk();

        $this->assertSame(1, ContainerPurchase::count(), 'rendering the edit page must not delete records');

        $html = $editResponse->getContent();

        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$html, LIBXML_NOERROR);

        $xpath = new \DOMXPath($dom);

        // The main save form must route to UPDATE with a single PATCH verb.
        $patchInputs = $xpath->query('//form[@id="cp-form"]//input[@name="_method"][@value="PATCH"]');
        $this->assertSame(1, $patchInputs->length, 'cp-form must contain exactly one _method=PATCH');

        // Regression: a DELETE verb leaked into cp-form would hijack Save into
        // destroy (deleting the record). The delete form must be a standalone
        // sibling form routed to DESTROY, not nested inside cp-form.
        $deleteInside = $xpath->query('//form[@id="cp-form"]//input[@name="_method"][@value="DELETE"]');
        $this->assertSame(0, $deleteInside->length, 'cp-form must NOT contain a _method=DELETE input');

        $deleteForm = $xpath->query('//form[not(@id="cp-form")][input[@name="_method"][@value="DELETE"]]');
        $this->assertSame(1, $deleteForm->length, 'a standalone DELETE form must exist');
        $this->assertStringContainsString(
            route('container-purchases.destroy', $record),
            $deleteForm->item(0)?->getAttribute('action'),
            'the DELETE form must post to the destroy route'
        );
    }

    public function test_validation_rejects_bad_data(): void
    {
        $this->post(route('container-purchases.store'), [
            'trans_no' => str_repeat('A', 200),
            'date' => 'not-a-date',
            'expected_delivery' => 'not-a-date',
            'rate' => 'not-a-number',
            'normal_purchase' => 'bogus',
        ])->assertSessionHasErrors(['trans_no', 'date', 'expected_delivery', 'rate', 'normal_purchase']);
    }
}
