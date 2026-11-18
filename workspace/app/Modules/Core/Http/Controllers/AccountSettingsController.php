<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Core\Models\Company;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountSettingsController extends Controller
{
    public function index(Request $request, ?string $section = null): View
    {
        $section ??= 'summary';
        abort_unless(in_array($section, ['summary', 'password', 'notifications'], true), 404);

        $categories = config('notifications.email.preference_categories', []);
        $user = $request->user();
        $preferences = array_replace(array_fill_keys(array_keys($categories), true), (array) $user->email_notification_preferences);
        $companies = $user->hasRole('super_admin')
            ? Company::query()->orderBy('name')->get(['id', 'name', 'code', 'entity_type', 'status'])
            : $user->companies()->orderBy('name')->get(['companies.id', 'companies.name', 'companies.code', 'companies.entity_type', 'companies.status']);

        return view('modules.core.account-settings', [
            'section' => $section,
            'user' => $user,
            'companies' => $companies,
            'emailPreferenceCategories' => $categories,
            'emailPreferences' => $preferences,
        ]);
    }
}
