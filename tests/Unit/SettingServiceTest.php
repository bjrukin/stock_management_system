<?php

namespace Tests\Unit;

use App\Services\SettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_persists_and_retrieves_settings(): void
    {
        SettingService::set('test_company_name', 'Himalayan Pharma', 'company', 'string');
        $this->assertEquals('Himalayan Pharma', SettingService::get('test_company_name'));
    }

    public function test_returns_fallback_default_when_not_set(): void
    {
        $this->assertEquals('fallback_val', SettingService::get('non_existent_key', 'fallback_val'));
    }

    public function test_handles_boolean_and_integer_types(): void
    {
        SettingService::set('test_enforce_fefo', 'true', 'inventory', 'boolean');
        $this->assertTrue(SettingService::get('test_enforce_fefo'));

        SettingService::set('test_alert_days', '45', 'inventory', 'integer');
        $this->assertSame(45, SettingService::get('test_alert_days'));
    }

    public function test_all_by_group(): void
    {
        SettingService::set('g_key1', 'val1', 'group_a');
        SettingService::set('g_key2', 'val2', 'group_a');
        SettingService::set('g_key3', 'val3', 'group_b');

        $groupA = SettingService::allByGroup('group_a');
        $this->assertCount(2, $groupA);
        $this->assertEquals('val1', $groupA['g_key1']);
        $this->assertEquals('val2', $groupA['g_key2']);
    }
}
