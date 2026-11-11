<?php

namespace App\Modules\CRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CRM\Domain\Models\CrmAccount;
use App\Modules\CRM\Domain\Models\CrmActivity;
use App\Modules\CRM\Domain\Models\CrmOpportunity;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;

class CrmDashboardController extends Controller
{
    public function __invoke(): View
    {
        Gate::authorize('viewAny', CrmAccount::class);
        return view('modules.crm.dashboard', [
            'stats' => [
                ['label' => 'Account', 'value' => CrmAccount::query()->count()],
                ['label' => 'Pipeline Sales', 'value' => CrmOpportunity::query()->count()],
                ['label' => 'Aktivitas', 'value' => CrmActivity::query()->count()],
            ],
            'accounts' => CrmAccount::query()->with('partyCompany')->latest()->limit(10)->get(),
        ]);
    }
}
