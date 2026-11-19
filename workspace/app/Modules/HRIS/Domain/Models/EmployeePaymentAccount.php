<?php

namespace App\Modules\HRIS\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmployeePaymentAccount extends Model
{
    protected $connection = 'hris';
    protected $table = 'employee_payment_accounts';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['employee_id', 'company_id', 'account_type', 'bank_id', 'bank_code', 'bank_name', 'account_holder', 'account_number', 'is_current', 'created_by', 'replaced_at'];

    protected function casts(): array
    {
        return ['account_holder' => 'encrypted', 'account_number' => 'encrypted', 'is_current' => 'boolean', 'replaced_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(fn (self $account) => $account->id ??= (string) Str::ulid());
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function bank()
    {
        return $this->belongsTo(\Core\Models\Bank::class, 'bank_id');
    }

    public function maskedNumber(): string
    {
        $number = (string) $this->account_number;
        return str_repeat('•', max(0, mb_strlen($number) - 4)).mb_substr($number, -4);
    }
}
