<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Application\Services\Audit\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class AccountPasswordController extends Controller
{
    public function edit(): RedirectResponse
    {
        return redirect()->route('workspace.account.settings', ['section' => 'password']);
    }

    public function update(Request $request, AuditLogger $audit): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string', 'current_password:web'],
            'new_password' => ['required', 'string', Password::min(8), 'confirmed', 'different:current_password'],
        ], [
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak sama.',
            'new_password.different' => 'Password baru harus berbeda dari password saat ini.',
        ], [
            'current_password' => 'password saat ini',
            'new_password' => 'password baru',
        ]);

        $user = $request->user();
        $user->forceFill(['password' => $validated['new_password']])->save();

        $revokedOtherSessions = false;
        if (config('session.driver') === 'database') {
            $sessionConnection = config('session.connection') ?: config('database.default');
            DB::connection($sessionConnection)
                ->table(config('session.table', 'sessions'))
                ->where('user_id', $user->getKey())
                ->where('id', '!=', $request->session()->getId())
                ->delete();
            $revokedOtherSessions = true;
        }

        $request->session()->regenerate();
        $audit->record('auth.password_changed', $user, metadata: [
            'other_database_sessions_revoked' => $revokedOtherSessions,
        ]);

        return redirect()->route('workspace.account.settings', ['section' => 'password'])->with('status', 'Password berhasil diperbarui.');
    }
}
