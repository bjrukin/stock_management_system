<?php

namespace Tests\Unit;

use App\Models\FiscalYear;
use App\Models\DocumentSequence;
use App\Services\DocumentSequenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentSequenceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        FiscalYear::create([
            'name' => '2081/82',
            'start_date_ad' => '2024-07-16',
            'start_date_bs' => '2081-04-01',
            'end_date_ad' => '2025-07-15',
            'end_date_bs' => '2082-03-31',
            'is_current' => true,
            'is_closed' => false,
        ]);
    }

    public function test_generates_sequential_gapless_numbers(): void
    {
        $first = DocumentSequenceService::getNextNumber('INV', null, 'INV-8182-', 5);
        $this->assertEquals('INV-8182-00001', $first);

        $second = DocumentSequenceService::getNextNumber('INV', null, 'INV-8182-', 5);
        $this->assertEquals('INV-8182-00002', $second);

        $third = DocumentSequenceService::getNextNumber('INV', null, 'INV-8182-', 5);
        $this->assertEquals('INV-8182-00003', $third);
    }

    public function test_previews_next_number_without_incrementing(): void
    {
        $preview1 = DocumentSequenceService::previewNextNumber('PO');
        $this->assertEquals('PO-00001', $preview1);

        $actual = DocumentSequenceService::getNextNumber('PO');
        $this->assertEquals('PO-00001', $actual);

        $preview2 = DocumentSequenceService::previewNextNumber('PO');
        $this->assertEquals('PO-00002', $preview2);
    }
}
