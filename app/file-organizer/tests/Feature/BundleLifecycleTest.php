<?php

use App\Jobs\PurgeExpiredBundles;
use App\Models\Bundle;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('purges expired bundle objects and marks the bundle expired', function () {
    Storage::fake('s3');
    $company = Company::create(['name' => 'Basilindo', 'code' => 'BAS']);
    $user = User::factory()->create();
    Storage::disk('s3')->put('bundles/expired.zip', 'zip');
    $bundle = Bundle::create(['company_id' => $company->id, 'requested_by' => $user->id, 'status' => 'completed', 'disk' => 's3', 'object_key' => 'bundles/expired.zip', 'document_count' => 1, 'expires_at' => now()->subMinute(), 'completed_at' => now()->subHour()]);

    app(PurgeExpiredBundles::class)->handle();

    expect($bundle->fresh()->status)->toBe('expired')
        ->and(Storage::disk('s3')->exists('bundles/expired.zip'))->toBeFalse();
});
