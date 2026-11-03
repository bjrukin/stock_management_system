<?php

namespace Tests\Unit;

use App\Models\AuditLog;
use App\Services\AuditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_audit_log_entry(): void
    {
        $role = \App\Models\Role::create(['name' => 'Admin']);
        $admin = \App\Models\Admin::create([
            'role_id' => $role->id,
            'name' => 'Admin User',
            'username' => 'admin_test',
            'status' => 1,
        ]);

        $log = AuditService::log(
            event: 'created',
            model: null,
            oldValues: null,
            newValues: ['name' => 'Central Hub'],
            userType: 'admin',
            userId: $admin->id
        );

        $this->assertDatabaseHas('audit_logs', [
            'id' => $log->id,
            'event' => 'created',
            'user_id' => $admin->id,
        ]);
        $this->assertEquals(['name' => 'Central Hub'], $log->new_values);
    }

    public function test_audit_log_cannot_be_updated(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Audit logs are append-only and cannot be updated.');

        $log = AuditService::log(
            event: 'posted',
            model: null,
            oldValues: null,
            newValues: ['status' => 'posted']
        );

        $log->event = 'tampered';
        $log->save();
    }

    public function test_audit_log_cannot_be_deleted(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Audit logs are append-only and cannot be deleted.');

        $log = AuditService::log(
            event: 'posted',
            model: null,
            oldValues: null,
            newValues: ['status' => 'posted']
        );

        $log->delete();
    }
}
