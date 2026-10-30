<?php

use App\Modules\HRIS\Domain\Models\Employee;
use App\Modules\HRIS\Domain\Models\EmployeeDocument;
use Core\Models\Company;
use Core\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('uploads an employee document to the private HRIS prefix', function () {
    Storage::fake('s3');
    config(['filesystems.default' => 's3']);
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Basilindo HRIS', 'code' => 'HRIS-'.Str::random(8)]);
    $user->companies()->attach($company);
    $user->givePermissionTo(['view_employee_documents', 'upload_employee_documents']);
    $employee = Employee::create([
        'company_id' => $company->id,
        'user_id' => $user->id,
        'employee_number' => 'EMP-'.Str::random(8),
        'full_name' => 'Ahmad Pudoli',
        'status' => 'active',
    ]);

    $response = $this->actingAs($user)->post(route('workspace.hris.employee-documents.store'), [
        'employee_id' => $employee->id,
        'type' => 'employment_contract',
        'title' => 'Kontrak kerja 2026',
        'file' => UploadedFile::fake()->create('kontrak.pdf', 100, 'application/pdf'),
        'effective_from' => '2026-01-01',
    ]);

    $response->assertRedirect(route('workspace.hris.employee-documents'));
    $document = EmployeeDocument::query()->firstOrFail();
    expect($document->object_key)->toStartWith('hris/employee-documents/'.$employee->id.'/');
    Storage::disk('s3')->assertExists($document->object_key);
});

it('does not expose another company employee document', function () {
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Basilindo HRIS', 'code' => 'HRIS-'.Str::random(8)]);
    $otherCompany = Company::create(['name' => 'Perusahaan lain', 'code' => 'OTH-'.Str::random(8)]);
    $user->companies()->attach($company);
    $user->givePermissionTo('view_employee_documents');
    $employee = Employee::create([
        'company_id' => $otherCompany->id,
        'employee_number' => 'EMP-'.Str::random(8),
        'full_name' => 'Karyawan lain',
        'status' => 'active',
    ]);
    $document = EmployeeDocument::create([
        'employee_id' => $employee->id,
        'company_id' => $otherCompany->id,
        'type' => 'other',
        'title' => 'Rahasia',
        'original_filename' => 'rahasia.pdf',
        'object_key' => 'hris/employee-documents/'.$employee->id.'/rahasia.pdf',
        'disk' => 's3',
        'mime_type' => 'application/pdf',
        'file_size' => 10,
    ]);

    $this->actingAs($user)->get(route('workspace.hris.employee-documents.download', $document))->assertForbidden();
});
