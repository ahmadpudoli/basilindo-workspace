<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['email_verified_at'] = now();

        return $data;
    }

    protected function afterCreate(): void
    {
        app(\App\Services\Audit\AuditLogger::class)->record('user.created', $this->record, null, [
            'created_user_id' => $this->record->getKey(),
        ]);
    }
}
