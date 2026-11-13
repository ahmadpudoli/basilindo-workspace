<?php

namespace App\Modules\HRIS\Application\Services;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClaimRequestForm extends FormRequest
{
    public function authorize(): bool { return true; }
    protected function prepareForValidation(): void
    {
        $items = $this->input('items');
        if (is_array($items)) {
            foreach ($items as $index => $item) {
                if (! is_array($item) || ! is_string($item['amount'] ?? null)) continue;
                $amount = trim($item['amount']);
                if (str_contains($amount, ',')) {
                    if (preg_match('/^\d{1,3}(?:\.\d{3})*(?:,\d{1,2})?$/', $amount) || preg_match('/^\d+(?:,\d{1,2})?$/', $amount)) {
                        $items[$index]['amount'] = str_replace(',', '.', str_replace('.', '', $amount));
                    }
                } elseif (preg_match('/^\d{1,3}(?:\.\d{3})+$/', $amount)) {
                    $items[$index]['amount'] = str_replace('.', '', $amount);
                }
            }
            $this->merge(['items' => $items]);
        }

        if (! $this->filled('items') && $this->filled('category')) {
            $this->merge(['items' => [[
                'category' => $this->input('category'),
                'description' => $this->input('purpose'),
                'currency' => $this->input('currency', 'IDR'),
                'amount' => $this->input('amount'),
                'attachment_reference' => $this->input('supporting_document_id'),
            ]]]);
        }
    }

    public function rules(): array
    {
        return [
            'action' => ['nullable', 'in:draft,submit'],
            'request_number' => ['nullable', 'string', 'regex:/^HRIS-CLAIM-[0-9]{8}-[A-Z0-9]{10}$/'],
            'employee_id' => ['nullable', 'string', 'size:26'],
            'type' => ['required', 'in:reimbursement,cash_advance'],
            'expense_scope' => ['required', 'in:internal,external_project'],
            'work_reference_type' => ['required_if:expense_scope,external_project', 'in:project,opportunity,none'],
            'department' => ['nullable', 'string', 'max:150'],
            'project_id' => ['nullable', 'integer'],
            'opportunity_id' => ['nullable', 'string', 'max:36', 'uuid'],
            'client_id' => ['nullable', 'integer'],
            'po_number' => ['nullable', 'string', 'max:100'],
            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*.id' => ['nullable', 'string', 'size:26'],
            'items.*.receipt_date' => ['required', 'date'],
            'items.*.activity_date' => ['nullable', 'date'],
            'items.*.expense_category_id' => ['required', 'string', 'size:26', 'exists:hris.expense_categories,id'],
            'items.*.description' => ['required', 'string', 'max:2000'],
            'items.*.currency' => ['required', 'in:IDR,USD,SGD'],
            'items.*.amount' => ['required', 'numeric', 'decimal:0,2', 'min:0.01'],
            'items.*.attachment_reference' => ['nullable', 'string', 'max:255'],
            'items.*.attachment_token' => ['nullable', 'string', 'size:26', 'regex:/^[0-9A-HJKMNP-TV-Z]{26}$/i'],
            'items.*.remove_attachment' => ['nullable', 'boolean'],
            'category' => ['nullable', 'string', 'max:100'],
            'purpose' => ['nullable', 'string', 'max:2000'],
            'amount' => ['nullable', 'numeric', 'min:0.01'],
            'currency' => ['nullable', 'in:IDR,USD,SGD'],
            'supporting_document_id' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $currencies = collect($this->input('items', []))->pluck('currency')->filter()->unique();
            if ($currencies->count() > 1) $validator->errors()->add('items', 'Satu pengajuan hanya dapat menggunakan satu mata uang.');

            if ($this->input('expense_scope') === 'external_project' && ! in_array($this->input('work_reference_type'), ['project', 'opportunity', 'none'], true)) {
                $validator->errors()->add('work_reference_type', 'Pilih jenis referensi pekerjaan.');
            }
            if ($this->input('expense_scope') === 'external_project' && $this->input('work_reference_type') === 'project' && blank($this->input('project_id'))) {
                $validator->errors()->add('project_id', 'Project wajib dipilih.');
            }
            if ($this->input('expense_scope') === 'external_project' && $this->input('work_reference_type') === 'opportunity' && blank($this->input('opportunity_id'))) {
                $validator->errors()->add('opportunity_id', 'Opportunity wajib dipilih.');
            }

            $claim = $this->route('claim');
            $existingItems = $claim instanceof \App\Modules\HRIS\Domain\Models\Claim
                ? $claim->expenseItems()->orderBy('line_number')->get()
                : collect();
            $existingItemIds = $existingItems->pluck('id')->map(fn ($id): string => (string) $id)->all();
            $usedTokens = [];
            $totalAttachmentBytes = 0;
            foreach ((array) $this->input('items', []) as $index => $item) {
                $itemId = is_array($item) ? trim((string) ($item['id'] ?? '')) : '';
                if ($itemId !== '' && ! in_array($itemId, $existingItemIds, true)) {
                    $validator->errors()->add("items.{$index}.id", 'Rincian biaya tidak sesuai dengan pengajuan ini.');
                    continue;
                }
                $token = is_array($item) ? trim((string) ($item['attachment_token'] ?? '')) : '';
                if ($token === '') {
                    if (is_array($item) && filter_var($item['remove_attachment'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
                        $validator->errors()->add("items.{$index}.attachment_token", 'Lampirkan bukti nota pengganti sebelum menyimpan rincian.');
                        continue;
                    }
                    $existingKey = $itemId !== '' ? $existingItems->firstWhere('id', $itemId)?->attachment_object_key : null;
                    $disk = Storage::disk((string) config('hris.claim_attachment_disk', 's3'));
                    if (blank($existingKey) || ! $disk->exists((string) $existingKey)) {
                        $validator->errors()->add("items.{$index}.attachment_token", 'Bukti nota wajib diunggah untuk setiap rincian.');
                    } else {
                        $totalAttachmentBytes += $disk->size((string) $existingKey);
                    }
                    continue;
                }

                if (! Str::isUlid($token)) {
                    $validator->errors()->add("items.{$index}.attachment_token", 'Token file bukti tidak valid. Silakan pilih ulang file.');
                    continue;
                }

                if (isset($usedTokens[$token])) {
                    $validator->errors()->add("items.{$index}.attachment_token", 'Satu file bukti tidak dapat digunakan pada lebih dari satu rincian.');
                    continue;
                }
                $usedTokens[$token] = true;
                $temporaryKey = 'hris-claim-tmp/'.(string) $this->user()->getKey().'/'.$token;
                $disk = Storage::disk((string) config('hris.claim_attachment_disk', 's3'));
                if (! $disk->exists($temporaryKey)) {
                    $validator->errors()->add("items.{$index}.attachment_token", 'File bukti sementara sudah kedaluwarsa. Silakan pilih ulang file.');
                } else {
                    $totalAttachmentBytes += $disk->size($temporaryKey);
                }
            }
            if ($totalAttachmentBytes > 50 * 1024 * 1024) {
                $validator->errors()->add('items', 'Total ukuran seluruh bukti nota maksimal 50 MB per pengajuan.');
            }
        });
    }
}
