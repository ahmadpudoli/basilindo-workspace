<?php

namespace App\Filament\Resources;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCrmOpportunities extends ListRecords
{
    protected static string $resource = CrmOpportunityResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()->label('Opportunity baru')]; }
}
