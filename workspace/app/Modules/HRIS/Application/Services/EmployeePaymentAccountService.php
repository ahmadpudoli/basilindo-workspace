<?php

namespace App\Modules\HRIS\Application\Services;

use App\Modules\Core\Application\Services\Audit\AuditLogger;
use App\Modules\HRIS\Domain\Models\Employee;
use App\Modules\HRIS\Domain\Models\EmployeePaymentAccount;
use Core\Models\User;
use Illuminate\Support\Facades\DB;

class EmployeePaymentAccountService
{
    public function __construct(private readonly AuditLogger $audit) {}

    public function replace(Employee $employee, string $accountType, array $data, User $actor): EmployeePaymentAccount
    {
        $version = DB::connection('hris')->transaction(function () use ($employee, $accountType, $data, $actor) {
            $employee->paymentAccounts()->where('account_type', $accountType)->where('is_current', true)->update(['is_current' => false, 'replaced_at' => now(), 'updated_at' => now()]);
            return $employee->paymentAccounts()->create($data + ['company_id' => $employee->company_id, 'account_type' => $accountType, 'is_current' => true, 'created_by' => $actor->getKey()]);
        });

        $this->audit->record('employee_payment_account.updated', $employee, (int) $employee->company_id, ['account_type' => $accountType, 'version_id' => $version->getKey()]);
        return $version;
    }
}
