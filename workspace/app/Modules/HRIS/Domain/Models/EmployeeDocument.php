<?php

namespace App\Modules\HRIS\Domain\Models;

use Core\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class EmployeeDocument extends Model
{
    use SoftDeletes;

    protected $connection = 'hris';
    protected $table = 'employee_documents';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'document_number', 'employee_id', 'company_id', 'type', 'title', 'original_filename',
        'object_key', 'disk', 'mime_type', 'file_size', 'checksum',
        'effective_from', 'expires_at', 'uploaded_by',
    ];

    protected function casts(): array
    {
        return ['effective_from' => 'date', 'expires_at' => 'date'];
    }

    protected static function booted(): void
    {
        static::creating(function (self $document): void {
            $document->id ??= (string) Str::ulid();
            $document->document_number ??= self::generateDocumentNumber();
        });
    }

    public function employee() { return $this->belongsTo(Employee::class); }
    public function uploader() { return $this->belongsTo(User::class, 'uploaded_by'); }

    public static function generateDocumentNumber(): string
    {
        do {
            $number = 'HRIS-'.now()->format('Ymd').'-'.Str::upper(substr((string) Str::ulid(), -10));
        } while (static::query()->where('document_number', $number)->exists());

        return $number;
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'employment_contract' => 'Kontrak kerja',
            'contract_addendum' => 'Addendum kontrak',
            'appointment_letter' => 'Surat pengangkatan',
            'identity_document' => 'Dokumen identitas',
            default => 'Dokumen lainnya',
        };
    }
}
