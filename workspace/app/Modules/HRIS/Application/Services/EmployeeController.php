<?php

namespace App\Modules\HRIS\Application\Services;

use App\Http\Controllers\Controller;
use App\Modules\HRIS\Domain\Models\Employee;
use Core\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Employee::class);
        $companies = Company::query()->operationalFor($request->user())->orderBy('name')->get();
        $companyIds = array_values(array_filter(array_map('intval', (array) $request->input('company_ids', []))));
        $perPage = in_array($request->integer('limit'), [10, 25, 50, 100], true) ? $request->integer('limit') : 10;
        $sort = in_array($request->string('sort')->toString(), ['full_name', 'employee_number', 'company_id', 'status', 'created_at'], true) ? $request->string('sort')->toString() : 'created_at';
        $direction = $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc';
        $employees = Employee::query()->visibleTo($request->user())->with(['company', 'manager'])->withCount(['leaveRequests', 'subordinates'])->when($request->q, fn ($q, $value) => $q->where(fn ($query) => $query->where('full_name', 'ilike', "%{$value}%")->orWhere('employee_number', 'ilike', "%{$value}%")))->when($companyIds !== [], fn ($q) => $q->whereIn('company_id', $companyIds))->when(in_array($request->status, ['active', 'inactive', 'archived'], true), fn ($q) => $q->where('status', $request->status))->orderBy($sort, $direction)->paginate($perPage)->withQueryString();
        return view('modules.hris-employees-enhanced', compact('employees', 'companies', 'companyIds', 'sort', 'direction', 'perPage'));
    }

    public function create(Request $request): View { Gate::authorize('create', Employee::class); return view('modules.hris-employee-form-enhanced', ['employee' => new Employee(['status' => 'active']), 'companies' => Company::query()->operationalFor($request->user())->orderBy('name')->get(), 'managers' => Employee::query()->visibleTo($request->user())->where('status', 'active')->orderBy('full_name')->get()]); }

    public function store(EmployeeRequest $request): RedirectResponse
    {
        Gate::authorize('create', Employee::class);
        $data = $request->validated();
        abort_unless($request->user()->hasRole('super_admin') || $request->user()->companies()->whereKey($data['company_id'])->exists(), 403);
        $employee = Employee::create($data);
        return redirect()->route('workspace.hris.employees')->with('status', "Karyawan {$employee->full_name} berhasil dibuat.");
    }

    public function edit(Request $request, Employee $employee): View
    {
        Gate::authorize('update', $employee);
        return view('modules.hris-employee-form-enhanced', ['employee' => $employee, 'companies' => Company::query()->operationalFor($request->user())->orderBy('name')->get(), 'managers' => Employee::query()->visibleTo($request->user())->where('status', 'active')->where('id', '!=', $employee->getKey())->orderBy('full_name')->get()]);
    }

    public function update(EmployeeRequest $request, Employee $employee): RedirectResponse
    {
        Gate::authorize('update', $employee);
        $data = $request->validated();
        abort_unless($request->user()->hasRole('super_admin') || $request->user()->companies()->whereKey($data['company_id'])->exists(), 403);
        $employee->update($data);
        return redirect()->route('workspace.hris.employees')->with('status', "Karyawan {$employee->full_name} berhasil diperbarui.");
    }

    public function destroy(Request $request, Employee $employee): RedirectResponse
    {
        Gate::authorize('delete', $employee);
        if ($employee->leaveRequests()->exists() || $employee->subordinates()->exists()) {
            $employee->update(['status' => 'archived']);
            return back()->with('status', "Karyawan {$employee->full_name} memiliki relasi dan dipindahkan ke arsip.");
        }
        $employee->delete();
        return back()->with('status', "Karyawan {$employee->full_name} berhasil dihapus.");
    }
}
