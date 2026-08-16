<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['transaction_no', 'receipt_date', 'agent', 'mode', 'sub_mode', 'currency', 'exchange_rate', 'remarks', 'act_mode', 'bank_charges', 'ac_code', 'cheque_no', 'cheque_date', 'gain_loss', 'total_amount', 'total_amount_1', 'approved_by', 'approved_on'])]
class AgentReceiptPayment extends Model
{
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
