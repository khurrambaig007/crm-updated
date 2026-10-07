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
     * Text used for bank-account dropdown options across the app.
     *
     * Must match between the server-rendered selects and the
     * bank-accounts.quick-create JSON label so option lists never drift.
     */
    public function optionLabel(): string
    {
        return trim(sprintf('%s — %s (%s)', $this->bank ?: $this->bank_name, $this->beneficiary_name, $this->account));
    }

    /**
     * The first bank account row, or an unsaved instance when none exists yet.
     *
     * Fallback used by the sales invoice PDF until the invoice itself references a
     * specific bank account via sales_invoices.bank_account_id.
     */
    public static function current(): self
    {
        return self::query()->first() ?? new self;
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
