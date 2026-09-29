<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'subtitle', 'logo', 'website', 'number', 'emails', 'pic_name', 'pic_email', 'pic_number', 'custom_fields', 'message'])]
final class CompanyProfile extends Model
{
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'emails' => 'array',
            'custom_fields' => 'array',
        ];
    }

    /**
     * The only profile in the system, or an unsaved instance when none exists yet.
     */
    public static function current(): self
    {
        return self::query()->first() ?? new self;
    }

    /**
     * Publicly reachable URL of the uploaded logo, or null when none is stored.
     */
    public function logoUrl(): ?string
    {
        return filled($this->logo)
            ? Storage::disk('public')->url($this->logo)
            : null;
    }

    /**
     * Absolute on-disk path of the uploaded logo.
     *
     * Used by dompdf, which cannot fetch a URL unless remote loading is enabled, but
     * can read any file inside its chroot (base_path()).
     */
    public function logoPath(): ?string
    {
        return filled($this->logo)
            ? Storage::disk('public')->path($this->logo)
            : null;
    }

    /**
     * Intrinsic pixel dimensions of the logo, or null when it is absent or unreadable.
     *
     * @return array{width: int, height: int}|null
     */
    public function logoDimensions(): ?array
    {
        $path = $this->logoPath();

        if (blank($path) || ! is_file($path)) {
            return null;
        }

        $size = @getimagesize($path);

        if ($size === false || $size[0] < 1 || $size[1] < 1) {
            return null;
        }

        return ['width' => $size[0], 'height' => $size[1]];
    }

    /**
     * Logo dimensions scaled to fit a box while preserving the aspect ratio.
     *
     * CSS max-width/max-height alone is not enough: those only ever shrink an image,
     * so a small logo stays small and a tall logo collapses to a sliver. Computing
     * the size explicitly means the logo renders at a predictable scale in output
     * that does not support responsive units, such as a dompdf document.
     *
     * $maxUpscale guards against blowing a low-resolution logo up into a blur.
     *
     * @return array{width: int, height: int}|null
     */
    public function logoDisplaySize(int $maxWidth, int $maxHeight, float $maxUpscale = 4.0): ?array
    {
        $dimensions = $this->logoDimensions();

        if ($dimensions === null) {
            return null;
        }

        $scale = min(
            $maxWidth / $dimensions['width'],
            $maxHeight / $dimensions['height'],
            $maxUpscale,
        );

        return [
            'width' => max(1, (int) round($dimensions['width'] * $scale)),
            'height' => max(1, (int) round($dimensions['height'] * $scale)),
        ];
    }

    /**
     * Logo URL to use as the browser icon.
     *
     * Browsers cache favicons far more aggressively than page assets, so the
     * updated_at timestamp is appended as a cache buster to make a logo change
     * actually take effect.
     */
    public function faviconUrl(): ?string
    {
        if (blank($this->logo)) {
            return null;
        }

        $version = $this->updated_at?->getTimestamp();

        return $this->logoUrl().($version ? '?v='.$version : '');
    }

    /**
     * Company name to brand the application with, falling back to APP_NAME so a
     * fresh install with no profile yet still renders sensibly.
     */
    public function displayName(): string
    {
        return filled($this->name)
            ? $this->name
            : (string) config('app.name', 'Laravel');
    }

    /**
     * Tagline shown under the company name, falling back to APP_SUBTITLE so the
     * existing env-configured subtitle keeps working until a profile overrides it.
     */
    public function displaySubtitle(): ?string
    {
        return filled($this->subtitle)
            ? $this->subtitle
            : config('app.subtitle');
    }

    /**
     * First email address, used where a single contact address is needed.
     */
    public function primaryEmail(): ?string
    {
        return collect($this->emails)->first();
    }

    /**
     * Whether a profile record has been created yet.
     *
     * Named hasProfile() rather than exists() so it is never confused with
     * Eloquent's $model->exists property.
     */
    public static function hasProfile(): bool
    {
        return self::query()->exists();
    }

    /**
     * Human-readable labels of the mandatory fields that are still missing.
     *
     * A profile is only considered complete once the company name, the logo and at
     * least one email address are all present. Used both by the
     * EnsureCompanyProfileIsComplete middleware and the app-layout banner so the
     * lock and its explanation can never drift apart.
     *
     * @return array<int, string>
     */
    public static function missingFields(): array
    {
        return self::current()->missingFieldsOn();
    }

    /**
     * Instance form of missingFields().
     *
     * Lets a layout that already loaded the profile for branding read the missing
     * list from the same instance rather than paying for a second query through the
     * static accessor.
     *
     * @return array<int, string>
     */
    public function missingFieldsOn(): array
    {
        $missing = [];

        if (blank($this->name)) {
            $missing[] = 'Company Name';
        }

        if (blank($this->logo)) {
            $missing[] = 'Logo';
        }

        if (collect($this->emails)->filter()->isEmpty()) {
            $missing[] = 'Email';
        }

        return $missing;
    }

    /**
     * Strip blank rows that the repeatable email / custom field inputs always submit.
     *
     * @param  array<int, mixed>  $values
     * @return array<int, string>
     */
    public static function normalizeEmails(array $values): array
    {
        return array_values(array_filter(
            array_map(fn ($value) => is_string($value) ? trim($value) : '', $values),
            fn (string $value) => $value !== '',
        ));
    }

    /**
     * Drop custom field pairs where either half was left blank.
     *
     * @param  array<int, mixed>  $items
     * @return array<int, array{key: string, value: string}>
     */
    public static function normalizeCustomFields(array $items): array
    {
        $normalized = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $key = is_string($item['key'] ?? null) ? trim($item['key']) : '';
            $value = is_string($item['value'] ?? null) ? trim($item['value']) : '';

            if ($key === '' || $value === '') {
                continue;
            }

            $normalized[] = ['key' => $key, 'value' => $value];
        }

        return $normalized;
    }
}
