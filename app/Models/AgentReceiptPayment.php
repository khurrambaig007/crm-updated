<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['transaction_no', 'receipt_date', 'agent', 'mode', 'sub_mode', 'currency', 'exchange_rate', 'remarks', 'act_mode', 'bank_charges', 'ac_code', 'cheque_no', 'cheque_date', 'gain_loss', 'total_amount', 'total_amount_1', 'approved_by', 'approved_on'])]
final class AgentReceiptPayment extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'receipt_date' => 'date',
            'cheque_date' => 'date',
        ];
    }

    public function agentName(): ?string
    {
        return $this->agent;
    }
}
