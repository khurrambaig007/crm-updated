<?php

namespace Tests\Feature;

use App\Models\Pod;
use App\Models\Pol;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

class SystemOperationsSeederTest extends BaseTestCase
{
    use RefreshDatabase;

    public function test_seed_pods_mirrors_pols(): void
    {
        $this->seed();

        $this->assertSame(10, Pol::count(), 'pols should be seeded');
        $this->assertSame(10, Pod::count(), 'pods should be seeded');

        $pods = Pod::query()
            ->join('pols', 'pols.city', '=', 'pods.city')
            ->orderBy('pods.id')
            ->get(['pods.id', 'pods.city', 'pods.location_code', 'pols.port_code', 'pols.id as pol_id']);

        foreach ($pods as $pod) {
            $this->assertSame($pod->location_code, $pod->port_code, "location_code mirrors port_code for {$pod->city}");
            $this->assertSame($pod->id, $pod->pol_id, "pod id aligns with pol id for {$pod->city}");
        }
    }
}
