<?php

namespace Tests\Unit;

use App\Helpers\BsDateHelper;
use Tests\TestCase;

class BsDateHelperTest extends TestCase
{
    public function test_converts_ad_to_bs_accurately(): void
    {
        $bsDate = BsDateHelper::toBs('2026-10-18');
        $this->assertEquals('2083-07-01', $bsDate);
    }

    public function test_converts_bs_to_ad_accurately(): void
    {
        $adDate = BsDateHelper::toAd('2083-07-01');
        $this->assertEquals('2026-10-18', $adDate);
    }

    public function test_handles_null_or_empty_dates_safely(): void
    {
        $this->assertNull(BsDateHelper::toBs(null));
        $this->assertNull(BsDateHelper::toBs(''));
        $this->assertNull(BsDateHelper::toAd(null));
        $this->assertNull(BsDateHelper::toAd(''));
    }

    public function test_validates_bs_date_format(): void
    {
        $this->assertTrue(BsDateHelper::isValidBs('2081-04-01'));
        $this->assertFalse(BsDateHelper::isValidBs('invalid-date'));
        $this->assertFalse(BsDateHelper::isValidBs(null));
    }

    public function test_helper_functions_work(): void
    {
        $this->assertEquals('2083-07-01', nepali_date('2026-10-18'));
        $this->assertEquals('2026-10-18', english_date('2083-07-01'));
    }

    public function test_bcmath_money_helpers(): void
    {
        $formatted = bc_money_format('1234.5678');
        $this->assertEquals('1,234.56', $formatted);

        $tax = bc_calc_tax('100.00', '13.00');
        $this->assertEquals('13.00', $tax);
    }
}
