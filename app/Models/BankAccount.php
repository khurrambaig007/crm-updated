<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['bank', 'beneficiary_name', 'bank_name', 'account', 'iban', 'swift', 'address', 'custom_fields'])]
final class BankAccount extends Model
{
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'custom_fields' => 'array',
        ];
    }

    /**
     * The only bank account in the system, or an unsaved instance when none exists yet.
     */
    public static function current(): self
    {
        return self::query()->first() ?? new self;
    }

    /**
     * Whether a bank account record has been created yet.
     *
     * Named hasAccount() rather than exists() so it is never confused with
     * Eloquent's $model->exists property.
     */
    public static function hasAccount(): bool
    {
        return self::query()->exists();
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
