<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Log an auditable event to the append-only ledger.
     */
    public static function log(
        string $event,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $userType = 'admin',
        ?int $userId = null
    ): AuditLog {
        if ($userId === null && Auth::guard('admin')->check()) {
            $userId = Auth::guard('admin')->id();
        }

        return AuditLog::create([
            'user_id' => $userId,
            'user_type' => $userType ?? 'admin',
            'auditable_type' => $model ? get_class($model) : 'System',
            'auditable_id' => $model?->getKey(),
            'event' => $event,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'created_at' => now(),
        ]);
    }
}
