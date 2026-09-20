<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Carrier;
use App\Models\Charge;
use App\Models\Commodity;
use App\Models\ContainerKind;
use App\Models\ContainerSize;
use App\Models\ContainerType;
use App\Models\Currency;
use App\Models\Investor;
use App\Models\Party;
use App\Models\Pod;
use App\Models\Pol;
use App\Models\SettlementType;
use App\Models\ShipperBp;
use App\Models\SlotTerm;
use App\Models\SubCompany;
use App\Models\Supplier;
use App\Models\VesselVoyage;
use Illuminate\Database\Seeder;

class SystemOperationsSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedContainerSizes();
        $this->seedCarriers();
        $this->seedContainerTypes();
        $this->seedContainerKinds();
        $this->seedCommodities();
        $this->seedVesselVoyages();
        $this->seedCharges();
        $this->seedCurrencies();
        $this->seedSlotTerms();
        $this->seedInvestors();
        $this->seedSettlementTypes();
        $this->seedSubCompanies();
        $this->seedPols();
        $this->seedPods();
        $this->seedAgents();
        $this->seedParties();
        $this->seedShipperBps();
        $this->seedSuppliers();
    }

    private function seedContainerSizes(): void
    {
        $sizes = ['20ft', '40ft', '40HC', '45ft', '20RF', '40RF', '20OT', '40OT', '20FR', '40FR'];

        foreach ($sizes as $size) {
            ContainerSize::create(['size' => $size]);
        }
    }

    private function seedCarriers(): void
    {
        $carriers = [
            ['name' => 'Maersk Line', 'code' => 'MSK01', 'description' => 'Global container shipping company'],
            ['name' => 'MSC Mediterranean', 'code' => 'MSC02', 'description' => 'Mediterranean Shipping Company'],
            ['name' => 'CMA CGM', 'code' => 'CMA03', 'description' => 'French container transportation and shipping'],
            ['name' => 'COSCO Shipping', 'code' => 'COS04', 'description' => 'China Ocean Shipping Company'],
            ['name' => 'Hapag-Lloyd', 'code' => 'HPL05', 'description' => 'German international shipping company'],
            ['name' => 'ONE Line', 'code' => 'ONE06', 'description' => 'Ocean Network Express'],
            ['name' => 'Evergreen Marine', 'code' => 'EVR07', 'description' => 'Taiwanese shipping and transportation'],
            ['name' => 'HMM', 'code' => 'HMM08', 'description' => 'Hyundai Merchant Marine'],
            ['name' => 'Yang Ming', 'code' => 'YML09', 'description' => 'Taiwanese container shipping company'],
            ['name' => 'ZIM Integrated', 'code' => 'ZIM10', 'description' => 'Israeli international cargo shipping'],
        ];

        foreach ($carriers as $carrier) {
            Carrier::create($carrier);
        }
    }

    private function seedContainerTypes(): void
    {
        $types = ['Dry', 'Reefer', 'Open Top', 'Flat Rack', 'Tank', 'Bulk', 'Ventilated', 'Insulated', 'Platform', 'Half Height'];

        foreach ($types as $type) {
            ContainerType::create(['name' => $type]);
        }
    }

    private function seedContainerKinds(): void
    {
        $kinds = ['Standard', 'High Cube', 'Double Door', 'Side Door', 'Open Side', 'Hard Top', 'FlexiTank', 'Garment', 'Pallet Wide', 'Swap Body'];

        foreach ($kinds as $kind) {
            ContainerKind::create(['name' => $kind]);
        }
    }

    private function seedCommodities(): void
    {
        $commodities = ['COM-1001', 'COM-1002', 'COM-1003', 'COM-1004', 'COM-1005', 'COM-1006', 'COM-1007', 'COM-1008', 'COM-1009', 'COM-1010'];

        foreach ($commodities as $commodity) {
            Commodity::create(['commodity_number' => $commodity]);
        }
    }

    private function seedVesselVoyages(): void
    {
        $vessels = [
            ['vessel_name' => 'MV Ocean Star', 'voyage_number' => '101A'],
            ['vessel_name' => 'MV Pacific Wave', 'voyage_number' => '202B'],
            ['vessel_name' => 'MV Atlantic Wind', 'voyage_number' => '303C'],
            ['vessel_name' => 'MV Gulf Stream', 'voyage_number' => '404D'],
            ['vessel_name' => 'MV Sea Breeze', 'voyage_number' => '505E'],
            ['vessel_name' => 'MV Horizon', 'voyage_number' => '606F'],
            ['vessel_name' => 'MV Northern Light', 'voyage_number' => '707G'],
            ['vessel_name' => 'MV Southern Cross', 'voyage_number' => '808H'],
            ['vessel_name' => 'MV Eastern Pearl', 'voyage_number' => '909I'],
            ['vessel_name' => 'MV Western Glory', 'voyage_number' => '010J'],
        ];

        foreach ($vessels as $vessel) {
            VesselVoyage::create($vessel);
        }
    }

    private function seedCharges(): void
    {
        $charges = ['Freight', 'Handling', 'Documentation', 'THC', 'BAF', 'CAF', 'ISPS', 'Seal Fee', 'Customs Clearance', 'Storage'];

        foreach ($charges as $charge) {
            Charge::create(['name' => $charge]);
        }
    }

    private function seedCurrencies(): void
    {
        $baseRates = [
            'PKR' => 278.50,
            'INR' => 83.20,
            'USD' => 1.00,
            'MYR' => 4.68,
            'AED' => 3.67,
            'SAR' => 3.75,
            'CNY' => 7.24,
        ];

        for ($i = 0; $i < 10; $i++) {
            $date = now()->subDays($i);
            $rates = [];

            foreach ($baseRates as $code => $rate) {
                $rates[$code] = round($rate + (mt_rand(-50, 50) / 1000), 4);
            }

            Currency::create([
                'exchange_rate_date' => $date->toDateString(),
                'exchange_rate' => json_encode($rates),
            ]);
        }
    }

    private function seedSlotTerms(): void
    {
        $terms = ['CY-CY', 'CFS-CFS', 'Door-Door', 'CY-CFS', 'CFS-CY', 'Door-CY', 'CY-Door', 'Door-CFS', 'CFS-Door', 'FI-FO'];

        foreach ($terms as $term) {
            SlotTerm::create(['term' => $term]);
        }
    }

    private function seedInvestors(): void
    {
        $investors = [
            ['name' => 'Global Trade Partners', 'contact_number' => '+1-555-0101', 'email' => 'info@globaltrade.com'],
            ['name' => 'Pacific Ventures Ltd', 'contact_number' => '+1-555-0102', 'email' => 'contact@pacificventures.com'],
            ['name' => 'Atlantic Holdings Inc', 'contact_number' => '+1-555-0103', 'email' => 'hello@atlanticholdings.com'],
            ['name' => 'Maritime Capital Group', 'contact_number' => '+1-555-0104', 'email' => 'invest@maritimecapital.com'],
            ['name' => 'Oceanic Investments', 'contact_number' => '+1-555-0105', 'email' => 'info@oceanicinvest.com'],
            ['name' => 'Portside Equity', 'contact_number' => '+1-555-0106', 'email' => 'contact@portsideequity.com'],
            ['name' => 'Harbor Financial', 'contact_number' => '+1-555-0107', 'email' => 'info@harborfinancial.com'],
            ['name' => 'Trade Winds Capital', 'contact_number' => '+1-555-0108', 'email' => 'hello@tradewindscapital.com'],
            ['name' => 'Seafarer Partners', 'contact_number' => '+1-555-0109', 'email' => 'contact@seafarerpartners.com'],
            ['name' => 'Anchor Investments', 'contact_number' => '+1-555-0110', 'email' => 'info@anchorinvestments.com'],
        ];

        foreach ($investors as $investor) {
            Investor::create($investor);
        }
    }

    private function seedSettlementTypes(): void
    {
        $types = [
            ['name' => 'Cash', 'number' => 'ST-001'],
            ['name' => 'Credit 30 Days', 'number' => 'ST-002'],
            ['name' => 'Credit 60 Days', 'number' => 'ST-003'],
            ['name' => 'Credit 90 Days', 'number' => 'ST-004'],
            ['name' => 'Advance Payment', 'number' => 'ST-005'],
            ['name' => 'LC at Sight', 'number' => 'ST-006'],
            ['name' => 'LC 30 Days', 'number' => 'ST-007'],
            ['name' => 'LC 60 Days', 'number' => 'ST-008'],
            ['name' => 'LC 90 Days', 'number' => 'ST-009'],
            ['name' => 'CAD', 'number' => 'ST-010'],
        ];

        foreach ($types as $type) {
            SettlementType::create($type);
        }
    }

    private function seedSubCompanies(): void
    {
        $companies = [
            ['name' => 'Logistics Pro LLC', 'email' => 'info@logisticspro.com', 'contact' => '+1-555-0201'],
            ['name' => 'Cargo Express Inc', 'email' => 'hello@cargoexpress.com', 'contact' => '+1-555-0202'],
            ['name' => 'Freight Masters Ltd', 'email' => 'contact@freightmasters.com', 'contact' => '+1-555-0203'],
            ['name' => 'ShipRight Corp', 'email' => 'info@shipright.com', 'contact' => '+1-555-0204'],
            ['name' => 'TransGlobal Services', 'email' => 'hello@transglobal.com', 'contact' => '+1-555-0205'],
            ['name' => 'PortLink Solutions', 'email' => 'contact@portlink.com', 'contact' => '+1-555-0206'],
            ['name' => 'ContainerHub Ltd', 'email' => 'info@containerhub.com', 'contact' => '+1-555-0207'],
            ['name' => 'MarineLogistics Co', 'email' => 'hello@marinelogistics.com', 'contact' => '+1-555-0208'],
            ['name' => 'OceanBridge Inc', 'email' => 'contact@oceanbridge.com', 'contact' => '+1-555-0209'],
            ['name' => 'TradeRoute Partners', 'email' => 'info@traderoute.com', 'contact' => '+1-555-0210'],
        ];

        foreach ($companies as $company) {
            SubCompany::create($company);
        }
    }

    private function seedPols(): void
    {
        $ports = [
            ['city' => 'Dubai', 'country' => 'UAE', 'port_code' => 'DXB'],
            ['city' => 'Singapore', 'country' => 'Singapore', 'port_code' => 'SIN'],
            ['city' => 'Shanghai', 'country' => 'China', 'port_code' => 'SHA'],
            ['city' => 'Rotterdam', 'country' => 'Netherlands', 'port_code' => 'RTM'],
            ['city' => 'Hamburg', 'country' => 'Germany', 'port_code' => 'HAM'],
            ['city' => 'Los Angeles', 'country' => 'USA', 'port_code' => 'LAX'],
            ['city' => 'Mumbai', 'country' => 'India', 'port_code' => 'BOM'],
            ['city' => 'Colombo', 'country' => 'Sri Lanka', 'port_code' => 'CMB'],
            ['city' => 'Karachi', 'country' => 'Pakistan', 'port_code' => 'KHI'],
            ['city' => 'Jeddah', 'country' => 'Saudi Arabia', 'port_code' => 'JED'],
        ];

        $containerSizeIds = ContainerSize::pluck('id')->toArray();

        foreach ($ports as $port) {
            $port['container_size_id'] = $containerSizeIds[array_rand($containerSizeIds)];
            Pol::create($port);
        }
    }

    private function seedPods(): void
    {
        foreach (Pol::orderBy('id')->get() as $pol) {
            Pod::updateOrCreate(
                ['city' => $pol->city],
                ['country' => $pol->country, 'location_code' => $pol->port_code],
            );
        }
    }

    private function seedAgents(): void
    {
        $polIds = Pol::pluck('id')->toArray();

        $agents = [
            ['name' => 'Global Shipping Agency', 'code' => 'AG-1001'],
            ['name' => 'Maritime Services Ltd', 'code' => 'AG-1002'],
            ['name' => 'Ocean Freight Agents', 'code' => 'AG-1003'],
            ['name' => 'Portside Logistics', 'code' => 'AG-1004'],
            ['name' => 'Cargo Link Agency', 'code' => 'AG-1005'],
            ['name' => 'TransMarine Agency', 'code' => 'AG-1006'],
            ['name' => 'SeaBridge Agents', 'code' => 'AG-1007'],
            ['name' => 'Harbor Services Co', 'code' => 'AG-1008'],
            ['name' => 'TradeFlow Agency', 'code' => 'AG-1009'],
            ['name' => 'Anchor Shipping Agency', 'code' => 'AG-1010'],
        ];

        foreach ($agents as $agent) {
            $agent['pol_id'] = $polIds[array_rand($polIds)];
            $agent['amount'] = round(mt_rand(10000, 500000) / 100, 2);
            Agent::create($agent);
        }
    }

    private function seedParties(): void
    {
        $agentIds = Agent::pluck('id')->toArray();
        $types = ['Shipper', 'Consignee', 'Notify', 'Forwarder'];
        $lineTypes = ['FCL', 'LCL', 'Bulk', 'Break Bulk'];

        $parties = [
            ['name' => 'Al-Futtaim Logistics', 'code' => 'PA-2001'],
            ['name' => 'DP World FZE', 'code' => 'PA-2002'],
            ['name' => 'Agility Logistics', 'code' => 'PA-2003'],
            ['name' => 'Gulf Agency Company', 'code' => 'PA-2004'],
            ['name' => 'Kuehne + Nagel', 'code' => 'PA-2005'],
            ['name' => 'DHL Global Forwarding', 'code' => 'PA-2006'],
            ['name' => 'DB Schenker', 'code' => 'PA-2007'],
            ['name' => 'Panalpina World Transport', 'code' => 'PA-2008'],
            ['name' => 'Expeditors International', 'code' => 'PA-2009'],
            ['name' => 'CEVA Logistics', 'code' => 'PA-2010'],
        ];

        foreach ($parties as $party) {
            $party['agent_id'] = $agentIds[array_rand($agentIds)];
            $party['email'] = strtolower(str_replace(' ', '', $party['name'])).'@example.com';
            $party['address'] = 'P.O. Box '.mt_rand(10000, 99999).', '.$party['name'].' Building';
            $party['phone_No'] = '+971-4-'.mt_rand(1000000, 9999999);
            $party['website'] = 'https://www.'.strtolower(str_replace(' ', '', $party['name'])).'.com';
            $party['type'] = $types[array_rand($types)];
            $party['line_type'] = $lineTypes[array_rand($lineTypes)];
            Party::create($party);
        }
    }

    private function seedShipperBps(): void
    {
        $agentIds = Agent::pluck('id')->toArray();
        $portIds = Pol::pluck('id')->toArray();
        $types = ['Importer', 'Exporter', 'Trader', 'Manufacturer'];

        $shippers = [
            ['name' => 'Emirates Trading Co', 'code' => 'BP-3001'],
            ['name' => 'Gulf Exporters Ltd', 'code' => 'BP-3002'],
            ['name' => 'Arabian Supplies FZE', 'code' => 'BP-3003'],
            ['name' => 'Desert Rose Trading', 'code' => 'BP-3004'],
            ['name' => 'Pearl Import Export', 'code' => 'BP-3005'],
            ['name' => 'Falcon Trade Group', 'code' => 'BP-3006'],
            ['name' => 'Oasis Commerce LLC', 'code' => 'BP-3007'],
            ['name' => 'Cedar Global Trade', 'code' => 'BP-3008'],
            ['name' => 'Palm Shipping Co', 'code' => 'BP-3009'],
            ['name' => 'Horizon Trade Links', 'code' => 'BP-3010'],
        ];

        foreach ($shippers as $shipper) {
            $shipper['agent_id'] = $agentIds[array_rand($agentIds)];
            $shipper['port_id'] = $portIds[array_rand($portIds)];
            $shipper['type'] = $types[array_rand($types)];
            $shipper['tax_id'] = 'TAX-'.mt_rand(100000, 999999);
            $shipper['phone_no'] = '+971-4-'.mt_rand(1000000, 9999999);
            $shipper['web'] = 'https://www.'.strtolower(str_replace(' ', '', $shipper['name'])).'.com';
            $shipper['address'] = 'P.O. Box '.mt_rand(10000, 99999).', '.$shipper['name'].' Building';
            $shipper['email'] = strtolower(str_replace(' ', '', $shipper['name'])).'@example.com';
            $shipper['fax'] = '+971-4-'.mt_rand(1000000, 9999999);
            $shipper['shipper'] = (bool) mt_rand(0, 1);
            $shipper['ca'] = (bool) mt_rand(0, 1);
            $shipper['consignee'] = (bool) mt_rand(0, 1);
            ShipperBp::create($shipper);
        }
    }

    private function seedSuppliers(): void
    {
        $portIds = Pol::pluck('id')->toArray();

        $suppliers = [
            ['name' => 'Container Depot LLC', 'email' => 'info@containerdepot.com', 'contact' => '+971-4-3001001', 'address' => 'Jebel Ali Free Zone, Dubai'],
            ['name' => 'Port Equipment Supply', 'email' => 'sales@portequipment.com', 'contact' => '+971-4-3001002', 'address' => 'Al Quoz Industrial Area, Dubai'],
            ['name' => 'Marine Parts Co', 'email' => 'orders@marineparts.com', 'contact' => '+971-4-3001003', 'address' => 'Port Rashid, Dubai'],
            ['name' => 'Ship Chandlers Ltd', 'email' => 'info@shipchandlers.com', 'contact' => '+971-4-3001004', 'address' => 'Deira, Dubai'],
            ['name' => 'Cargo Gear Suppliers', 'email' => 'sales@cargogear.com', 'contact' => '+971-4-3001005', 'address' => 'Al Aweer, Dubai'],
            ['name' => 'Reefer Services Co', 'email' => 'service@reeferservices.com', 'contact' => '+971-4-3001006', 'address' => 'Jebel Ali Industrial, Dubai'],
            ['name' => 'Container Repair Hub', 'email' => 'repair@containerrepair.com', 'contact' => '+971-4-3001007', 'address' => 'Mina Jebel Ali, Dubai'],
            ['name' => 'Lashing Materials Inc', 'email' => 'info@lashingmaterials.com', 'contact' => '+971-4-3001008', 'address' => 'Al Hamriya Port, Dubai'],
            ['name' => 'Warehouse Solutions', 'email' => 'sales@warehousesolutions.com', 'contact' => '+971-4-3001009', 'address' => 'Dubai Investment Park, Dubai'],
            ['name' => 'Forklift Trading Co', 'email' => 'info@forklifttrading.com', 'contact' => '+971-4-3001010', 'address' => 'Ras Al Khor, Dubai'],
        ];

        foreach ($suppliers as $supplier) {
            $supplier['location_id'] = $portIds[array_rand($portIds)];
            Supplier::create($supplier);
        }
    }
}
