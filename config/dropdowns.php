<?php

/*
|--------------------------------------------------------------------------
| Dropdown Values
|--------------------------------------------------------------------------
|
| Free-text dropdown values used across the application that do NOT have
| a dedicated Laravel model. Convention: flat keys with format
| "{screen}.{field}" so they can be looked up via
| config('dropdowns.container_activities_activity').
|
*/

return [

    // Container Activity screen
    'container_activities_activity' => [
        'Arrived at up country',
        'Container exchange in',
        'Container exchange out',
        'Container sales',
        'Container sold',
        'Damaged',
        'Departure from up Country',
        'Destuffing',
        'Discharge Empty',
        'Discharge Empty Transhipment',
        'Discharge Full',
        'Discharge Full Transhipment',
        'Empty Movement',
        'Empty Receive',
        'Empty Reject from Shipper',
        'Import Empty',
        'Import Full',
        'Lease In/ One Way leased in',
        'Lease Out/one way leased out',
        'Lease Rental In',
        'Lease Rental Out',
        'Leased In',
        'Leased In Internal',
        'Leased Out',
        'Leased Out Internal',
        'Load Empty',
        'Load Empty Transhipment',
        'Loaded Full',
        'Loaded Full Transhipment',
        'Off Hire',
        'On Hire',
        'Purchase',
        'Receive From Consignee',
        'Receive From Shipper',
        'Received From Overseas Agent',
        'Received From Up Country',
        'Received In Afghanistan',
        'REturn to Overseas Agent',
        'Sent for Repair',
        'Sent to Afghanistan',
        'Sent to Consignee',
        'Sent to Shipper',
        'Sent to UpCountry',
        'Shift from Other Yard',
        'Shift to Other Yard',
        'Stack Out',
        'Swap Damage Full At Transhipment',
        'Transfer Agent',
        'Transfer Empty',
    ],

    'container_activities_cargo_type' => [
        'General',
        'Reefer',
        'Hazmat',
        'OOG',
        'Bulk',
        'Liquid',
    ],

    'container_activities_status' => [
        'NORMAL DAMAGE',
        'DIRTY',
        'DIRTY/DAMAGE',
        'FLOOR SWEEP',
        'HEAVY DAMAGE',
        'NET WEAR AND TEA',
        'OILY/DIRTY',
        'OK',
    ],

    'bookings' => [
        'non_dg' => [
            0 => 'NON DG',
            1 => 'DG',
        ],
        'cntr_owner' => [
            1 => 'Shipper',
            2 => 'Carrier',
            3 => 'Consignee',
        ],
        'freight_type' => [
            1 => 'Regular',
            2 => 'Zero',
            3 => 'Negative',
        ],
        'freight_type_sub' => [
            1 => 'Sub to Both THC',
            2 => 'FRT Incl LTHC Sub to TDHC',
            3 => 'FRT Incl DTHC Sub to LTHC',
            4 => 'FRT Incl LTHC & DTHC',
        ],
        'detention_currency' => [
            'USD' => 'USD',
            'EUR' => 'EUR',
            'GBP' => 'GBP',
            'AED' => 'AED',
            'SGD' => 'SGD',
            'INR' => 'INR',
            'PKR' => 'PKR',
            'AFN' => 'AFN',
        ],
        'approval_status' => [
            1 => 'Draft',
            2 => 'RFA',
            3 => 'Cancelled',
            4 => 'Approved',
        ],
        'booking_prefix' => [
            'AMS' => 'AMS',
        ],
        'reporting_prefix' => [
            'B' => 'B',
        ],
    ],

    // BL Info screen
    'bl_info' => [
        'booking_si_status' => [
            1 => 'Draft',
            2 => 'Final',
        ],
        'booking_info_carrier' => [
            1 => 'Cntr Owner',
            2 => 'Principal',
            3 => 'Shipper Carrier',
            4 => 'Agent',
        ],
    ],

    // Container Release Order screen
    'container_release_orders' => [
        'cntr_owner' => [
            1 => 'Shipper',
            2 => 'Carrier',
            3 => 'Consignee',
        ],
        'dg_status' => [
            0 => 'NON DG',
            1 => 'DG',
        ],
    ],

    // Container Purchase screen
    'container_purchases' => [
        'purchase_type' => [
            'normal' => 'Normal Purchase',
            'lease' => 'Lease Purchase',
            'exchange' => 'Exchange',
        ],
        // TODO: replace with meaningful Principal labels if available (value => label)
        'principal' => [
            1 => '1',
            2 => '2',
            3 => '3',
            4 => '4',
            5 => '5',
            6 => '6',
            7 => '7',
            8 => '8',
            9 => '9',
            10 => '10',
        ],
    ],

    // Bank Account screen
    'bank_accounts' => [
        // Keyed by the bank's common abbreviation (the value stored on the record);
        // the label is the full legal name. Edit this list to add or remove banks.
        'bank' => [
            // Conventional banks
            'ABL' => 'Allied Bank Limited',
            'AKBL' => 'Askari Bank Limited',
            'BAHL' => 'Bank Al Habib Limited',
            'ALFALAH' => 'Bank Alfalah Limited',
            'BOC' => 'Bank of China Limited (Pakistan)',
            'BOK' => 'The Bank of Khyber',
            'BOP' => 'The Bank of Punjab',
            'CITI' => 'Citibank N.A.',
            'DB' => 'Deutsche Bank AG',
            'FAYSAL' => 'Faysal Bank Limited',
            'FWBL' => 'First Women Bank Limited',
            'HBL' => 'Habib Bank Limited',
            'HMB' => 'Habib Metropolitan Bank Limited',
            'ICBC' => 'Industrial and Commercial Bank of China Limited',
            'JSBL' => 'JS Bank Limited',
            'MCB' => 'MCB Bank Limited',
            'NBP' => 'National Bank of Pakistan',
            'SAMBA' => 'Samba Bank Limited',
            'SCB' => 'Standard Chartered Bank (Pakistan) Limited',
            'SILK' => 'Silkbank Limited',
            'SINDH' => 'Sindh Bank Limited',
            'SONERI' => 'Soneri Bank Limited',
            'SUMMIT' => 'Summit Bank Limited',
            'UBL' => 'United Bank Limited',

            // Islamic banks
            'ALBARAKA' => 'Al Baraka Bank (Pakistan) Limited',
            'BIPL' => 'BankIslami Pakistan Limited',
            'DIBPL' => 'Dubai Islamic Bank Pakistan Limited',
            'MCBISLAMIC' => 'MCB Islamic Bank Limited',
            'MEZAN' => 'Meezan Bank Limited',

            // Digital banks
            'NAYAPAY' => 'NayaPay',
            'SADAPAY' => 'SadaPay',
        ],
    ],

    // Sales Invoice screen
    'sales_invoices' => [
        'status' => [
            'unpaid' => 'Unpaid',
            'paid' => 'Paid',
        ],
    ],

];
