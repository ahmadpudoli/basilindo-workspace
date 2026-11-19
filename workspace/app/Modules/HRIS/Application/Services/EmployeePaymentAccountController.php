<?php

namespace App\Modules\HRIS\Application\Services;

use App\Http\Controllers\Controller;
use App\Modules\HRIS\Domain\Models\Employee;
use App\Modules\HRIS\Domain\Models\EmployeePaymentAccount;
use Core\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class EmployeePaymentAccountController extends Controller
{
    private const TYPES = ['payroll', 'reimbursement'];

    public function show(Employee $employee)
    {
        $accounts = [];
        foreach (self::TYPES as $type) {
            $ability = $type === 'payroll' ? 'viewPayrollAccount' : 'viewReimbursementAccount';
            $updateAbility = $type === 'payroll' ? 'updatePayrollAccount' : 'updateReimbursementAccount';
            if (Gate::allows($ability, $employee) || Gate::allows($updateAbility, $employee)) {
                $accounts[$type] = $employee->paymentAccounts()->with('bank')->where('account_type', $type)->orderByDesc('created_at')->get();
            }
        }
        abort_if($accounts === [], 403);
        $canEnrichReimbursement = Gate::allows('viewPayrollAccount', $employee)
            && Gate::allows('updateReimbursementAccount', $employee);
        $banks = Bank::query()->active()->orderBy('name')->orderBy('bank_code')->get();

        return view('modules.hris-employee-payment-accounts', compact('employee', 'accounts', 'canEnrichReimbursement', 'banks'));
    }

    public function update(Request $request, Employee $employee, string $accountType, EmployeePaymentAccountService $service)
    {
        abort_unless(in_array($accountType, self::TYPES, true), 404);
        $ability = $accountType === 'payroll' ? 'updatePayrollAccount' : 'updateReimbursementAccount';
        Gate::authorize($ability, $employee);
        $isReplacement = $employee->paymentAccounts()
            ->where('account_type', $accountType)
            ->where('is_current', true)
            ->exists();
        $rules = [
            'bank_id' => ['required', 'string', Rule::exists('core.banks', 'id')->where(fn ($query) => $query->where('status', 'active'))],
            'account_holder' => ['required', 'string', 'max:160'],
            'account_number' => ['required', 'string', 'max:80', 'regex:/^[0-9 .-]+$/'],
        ];
        if ($isReplacement) {
            $rules['password'] = ['required', 'string', 'current_password:web'];
        }
        $data = $request->validate([
            ...$rules,
        ], [
            'bank_id.required' => 'Pilih nama bank dari daftar master.',
            'bank_id.exists' => 'Bank yang dipilih tidak terdaftar atau sudah nonaktif.',
            'account_holder.required' => 'Nama pemilik rekening wajib diisi.',
            'account_holder.max' => 'Nama pemilik rekening maksimal 160 karakter.',
            'account_number.required' => 'Nomor rekening wajib diisi.',
            'account_number.regex' => 'Nomor rekening hanya boleh berisi angka, spasi, titik, atau tanda hubung.',
            'account_number.max' => 'Nomor rekening maksimal 80 karakter.',
            'password.required' => 'Masukkan password akun untuk mengganti rekening aktif.',
            'password.current_password' => 'Password akun tidak sesuai. Silakan coba kembali.',
        ], [
            'bank_id' => 'nama bank',
            'account_holder' => 'nama pemilik rekening',
            'account_number' => 'nomor rekening',
        ]);
        unset($data['password']);

        $digits = preg_replace('/\D+/', '', $data['account_number']);
        Validator::make(['account_number' => $digits], ['account_number' => ['required', 'digits_between:6,34']], [
            'account_number.digits_between' => 'Nomor rekening harus memiliki 6 sampai 34 digit.',
        ], ['account_number' => 'nomor rekening'])->validate();
        $data['account_number'] = $digits;
        $bank = Bank::query()->active()->findOrFail($data['bank_id']);
        $data['bank_name'] = $bank->name;
        $data['bank_code'] = $bank->bank_code;

        $service->replace($employee, $accountType, $data, $request->user());
        return back()->with('status', 'Data rekening berhasil diperbarui.');
    }

    public function reveal(Request $request, Employee $employee, EmployeePaymentAccount $paymentAccount)
    {
        $accountType = $paymentAccount->account_type;
        abort_unless(in_array($accountType, self::TYPES, true), 404);

        $ability = $accountType === 'payroll' ? 'viewPayrollAccount' : 'viewReimbursementAccount';
        Gate::authorize($ability, $employee);
        abort_unless((string) $paymentAccount->employee_id === (string) $employee->getKey(), 404);

        $request->validate([
            'password' => ['required', 'string', 'current_password:web'],
        ], [
            'password.required' => 'Masukkan password akun Anda.',
            'password.current_password' => 'Password akun tidak sesuai.',
        ]);

        return response()->json(['account_number' => $paymentAccount->account_number])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, private')
            ->header('Pragma', 'no-cache');
    }

    public function enrichFromPayroll(Request $request, Employee $employee)
    {
        Gate::authorize('viewPayrollAccount', $employee);
        Gate::authorize('updateReimbursementAccount', $employee);
        $request->validate([
            'password' => ['required', 'string', 'current_password:web'],
        ], [
            'password.required' => 'Masukkan password akun Anda.',
            'password.current_password' => 'Password akun tidak sesuai.',
        ]);

        $source = $employee->paymentAccounts()
            ->with('bank')
            ->where('account_type', 'payroll')
            ->where('is_current', true)
            ->firstOrFail();

        abort_unless($source->bank_id && $source->bank?->status === 'active', 422);

        return response()->json([
            'bank_id' => $source->bank_id,
            'account_holder' => $source->account_holder,
            'account_number' => $source->account_number,
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, private')
            ->header('Pragma', 'no-cache');
    }
}
