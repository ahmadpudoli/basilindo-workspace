<?php
namespace App\Filament\Resources\ApplicationAccesses\Pages;
use App\Filament\Resources\ApplicationAccesses\ApplicationAccessResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
class ListApplicationAccesses extends ListRecords { protected static string $resource = ApplicationAccessResource::class; protected function getHeaderActions(): array { return [CreateAction::make()]; } }
