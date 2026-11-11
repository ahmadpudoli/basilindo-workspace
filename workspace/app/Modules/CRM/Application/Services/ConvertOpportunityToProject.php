<?php

namespace App\Modules\CRM\Application\Services;

use App\Modules\CRM\Domain\Models\CrmOpportunity;
use App\Modules\CRM\Domain\Models\CrmPipelineStage;
use Core\Models\Project;
use Throwable;

final class ConvertOpportunityToProject
{
    public function handle(CrmOpportunity $opportunity, string $projectName, ?string $description = null, $startDate = null, $endDate = null): Project
    {
        if ($opportunity->project_id !== null) {
            throw new \LogicException('Opportunity ini sudah memiliki project.');
        }

        $project = null;

        try {
            $project = Project::create([
                'company_id' => $opportunity->account->company_id,
                'name' => $projectName,
                'description' => $description,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            $project->members()->syncWithoutDetaching([$opportunity->owner_user_id ?: auth()->id()]);
            $wonStage = CrmPipelineStage::query()->where('is_won', true)->orderBy('sequence')->first();
            $opportunity->forceFill(['project_id' => $project->getKey(), 'stage' => $wonStage?->code ?: 'won', 'stage_id' => $wonStage?->getKey(), 'won_at' => $opportunity->won_at ?: now()])->save();
        } catch (Throwable $exception) {
            $project?->delete();
            throw $exception;
        }

        return $project;
    }
}
