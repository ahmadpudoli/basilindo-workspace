<?php

namespace App\Modules\HRIS\Application\Services;

use App\Http\Controllers\Controller;
use App\Modules\Core\Application\Services\Audit\AuditLogger;
use App\Modules\HRIS\Domain\Models\HrisExpenseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class HrisExpenseCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize($request, 'view_hris_expense_categories');
        $perPage = in_array($request->integer('per_page', 10), [10, 25, 50, 100], true) ? $request->integer('per_page', 10) : 10;
        $sort = in_array($request->string('sort')->toString(), ['name', 'sort_order', 'is_active', 'created_at'], true) ? $request->string('sort')->toString() : 'sort_order';
        $direction = $request->string('direction')->toString() === 'desc' ? 'desc' : 'asc';
        $status = in_array($request->string('status')->toString(), ['active', 'archived', 'all'], true) ? $request->string('status')->toString() : 'active';
        $categories = HrisExpenseCategory::query()
            ->when($request->filled('q'), fn ($query) => $query->where('name', 'ilike', '%'.trim($request->string('q')->toString()).'%'))
            ->when($status === 'active', fn ($query) => $query->where('is_active', true)->whereNull('archived_at'))
            ->when($status === 'archived', fn ($query) => $query->where(fn ($nested) => $nested->where('is_active', false)->orWhereNotNull('archived_at')))
            ->orderBy($sort, $direction)->orderBy('name')->paginate($perPage)->withQueryString();

        $editing = null;
        if ($request->filled('edit')) $editing = HrisExpenseCategory::query()->findOrFail($request->string('edit')->toString());

        return view('modules.hris-expense-categories', compact('categories', 'editing', 'perPage', 'sort', 'direction', 'status'));
    }

    public function store(Request $request, AuditLogger $audit): RedirectResponse
    {
        $this->authorize($request, 'create_hris_expense_categories');
        $data = $this->validated($request);
        $this->ensureUniqueName($data['name']);
        $category = HrisExpenseCategory::create($data + ['is_active' => true]);
        $audit->record('hris.expense_category.created', $category, null, ['category_id' => $category->getKey()]);

        return redirect()->route('workspace.settings.hris.expense-categories')->with('status', 'Kategori biaya berhasil ditambahkan.');
    }

    public function update(Request $request, HrisExpenseCategory $category, AuditLogger $audit): RedirectResponse
    {
        $this->authorize($request, 'update_hris_expense_categories');
        $data = $this->validated($request);
        $this->ensureUniqueName($data['name'], $category);
        $before = $category->only(['name', 'description', 'sort_order', 'is_active']);
        $category->update($data);
        $audit->recordChange('hris.expense_category.updated', $category, $before, $category->fresh()->only(array_keys($before)));

        return redirect()->route('workspace.settings.hris.expense-categories')->with('status', 'Kategori biaya berhasil diperbarui.');
    }

    public function archive(Request $request, HrisExpenseCategory $category, AuditLogger $audit): RedirectResponse
    {
        $this->authorize($request, 'delete_hris_expense_categories');
        $category->update(['is_active' => false, 'archived_at' => now(), 'archived_by' => $request->user()->getKey()]);
        $audit->record('hris.expense_category.archived', $category, null, ['category_id' => $category->getKey()]);

        return back()->with('status', 'Kategori biaya diarsipkan dan tidak tersedia untuk pengajuan baru.');
    }

    public function restore(Request $request, HrisExpenseCategory $category, AuditLogger $audit): RedirectResponse
    {
        $this->authorize($request, 'delete_hris_expense_categories');
        $this->ensureUniqueName($category->name, $category);
        $category->update(['is_active' => true, 'archived_at' => null, 'archived_by' => null]);
        $audit->record('hris.expense_category.restored', $category, null, ['category_id' => $category->getKey()]);

        return back()->with('status', 'Kategori biaya dipulihkan.');
    }

    private function authorize(Request $request, string $permission): void
    {
        $user = $request->user();
        abort_unless($user->hasRole('super_admin') || $user->can($permission), 403);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);
        $data['name'] = trim($data['name']);
        if ($data['name'] === '') throw ValidationException::withMessages(['name' => 'Nama kategori wajib diisi.']);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }

    private function ensureUniqueName(string $name, ?HrisExpenseCategory $ignore = null): void
    {
        $duplicate = HrisExpenseCategory::query()->whereRaw('LOWER(name) = ?', [mb_strtolower(trim($name))])
            ->when($ignore, fn ($query) => $query->whereKeyNot($ignore->getKey()))->exists();
        if ($duplicate) throw ValidationException::withMessages(['name' => 'Nama kategori sudah digunakan.']);
    }
}
