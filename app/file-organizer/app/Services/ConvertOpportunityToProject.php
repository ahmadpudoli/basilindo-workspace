<?php

namespace App\Services;

use App\Models\CrmOpportunity;
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
            $opportunity->forceFill(['project_id' => $project->getKey(), 'stage' => 'won'])->save();
        } catch (Throwable $exception) {
            $project?->delete();
            throw $exception;
        }

        return $project;
    }
}
