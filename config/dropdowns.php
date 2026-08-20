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

];
