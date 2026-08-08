<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['vessel_name', 'voyage_number'])]
class VesselVoyage extends Model {}
