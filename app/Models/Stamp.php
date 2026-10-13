<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * A reusable stamp image for the System Operations > Stamps Info screen.
 *
 * The image is a path on the public disk rather than a blob, matching how
 * CompanyProfile stores its logo, so both share the same resolution helpers.
 */
#[Fillable(['name', 'image'])]
final class Stamp extends Model
{
    use HasFactory;

    /**
     * Publicly reachable URL of the stamp image, or null when none is stored.
     */
    public function imageUrl(): ?string
    {
        return filled($this->image)
            ? Storage::disk('public')->url($this->image)
            : null;
    }

    /**
     * Absolute on-disk path of the stamp image.
     */
    public function imagePath(): ?string
    {
        return filled($this->image)
            ? Storage::disk('public')->path($this->image)
            : null;
    }
}
