<?php

/*
|--------------------------------------------------------------------------
| Dropdown Values
|--------------------------------------------------------------------------
|
| Free-text dropdown values used across the application that do NOT have
| a dedicated Laravel model. Convention: flat keys with format
| "{screen}.{field}" so they can be looked up via
| config('dropdowns.container_activities.activity').
|
*/

return [

    // Container Activity screen
    'container_activities.activity' => [
        'In Transit',
        'Delivered',
        'Empty Return',
        'Discharged',
        'Loaded',
        'Gate In',
        'Gate Out',
        'Customs Hold',
        'Released',
    ],

];
