<?php

namespace Tests\Unit;

use App\Services\ReportAnalyticsService;
use PHPUnit\Framework\TestCase;

class ReportAnalyticsServiceTest extends TestCase
{
    public function test_it_builds_one_month_sheet_with_chapter_rows_and_question_columns(): void
    {
        $sheets = (new ReportAnalyticsService)->buildQuestionAnalysisSheets([
            [
                'year' => 2026,
                'month' => 1,
                'chapter_id' => 10,
                'chapter_name' => 'Alpha',
                'answers' => [
                    [
                        'question_id' => 100,
                        'question_label' => 'Item 1',
                        'question_order' => 1,
                        'value' => 'Yes',
                    ],
                    [
                        'question_id' => 101,
                        'question_label' => 'Item 2',
                        'question_order' => 2,
                        'value' => 'No',
                    ],
                ],
            ],
            [
                'year' => 2026,
                'month' => 2,
                'chapter_id' => 10,
                'chapter_name' => 'Alpha',
                'answers' => [
                    [
                        'question_id' => 100,
                        'question_label' => 'Item 1',
                        'question_order' => 1,
                        'value' => 'Maybe',
                    ],
                ],
            ],
        ]);

        $this->assertSame(['Jan 2026', 'Feb 2026'], array_keys($sheets));
        $this->assertSame(['Chapter', 'Item 1', 'Item 2'], $sheets['Jan 2026']['headers']);
        $this->assertSame('Alpha', $sheets['Jan 2026']['rows'][0]['Chapter']);
        $this->assertSame('Yes', $sheets['Jan 2026']['rows'][0]['Item 1']);
        $this->assertSame('No', $sheets['Jan 2026']['rows'][0]['Item 2']);
        $this->assertSame('Alpha', $sheets['Feb 2026']['rows'][0]['Chapter']);
        $this->assertSame('Maybe', $sheets['Feb 2026']['rows'][0]['Item 1']);
        $this->assertSame('-', $sheets['Feb 2026']['rows'][0]['Item 2']);
    }

    public function test_it_converts_legacy_month_columns_into_month_sheets(): void
    {
        $sheets = (new ReportAnalyticsService)->splitQuestionAnalysisByMonth([
            'headers' => ['Chapter', 'Item', 'Jan 2026', 'Feb 2026'],
            'rows' => [
                [
                    'Chapter' => 'Alpha',
                    'Item' => 'Item 1',
                    'Jan 2026' => '10',
                    'Feb 2026' => '12',
                ],
                [
                    'Chapter' => 'Alpha',
                    'Item' => 'Item 2',
                    'Jan 2026' => 'Yes',
                    'Feb 2026' => 'No',
                ],
            ],
        ]);

        $this->assertSame(['Jan 2026', 'Feb 2026'], array_keys($sheets));
        $this->assertSame(['Chapter', 'Item 1', 'Item 2'], $sheets['Jan 2026']['headers']);
        $this->assertSame('10', $sheets['Jan 2026']['rows'][0]['Item 1']);
        $this->assertSame('Yes', $sheets['Jan 2026']['rows'][0]['Item 2']);
        $this->assertSame('12', $sheets['Feb 2026']['rows'][0]['Item 1']);
        $this->assertSame('No', $sheets['Feb 2026']['rows'][0]['Item 2']);
    }

    public function test_it_provides_a_valid_empty_sheet_when_no_data_exists(): void
    {
        $sheets = (new ReportAnalyticsService)->buildQuestionAnalysisSheets([]);

        $this->assertSame(['No Data'], array_keys($sheets));
        $this->assertSame(['Chapter'], $sheets['No Data']['headers']);
    }

    public function test_it_matches_report_months_against_the_requested_date_range(): void
    {
        $service = new ReportAnalyticsService;

        $this->assertTrue($service->reportMonthMatchesFilters(2026, 7, '2026-07-01', '2026-07-31'));
        $this->assertTrue($service->reportMonthMatchesFilters(2026, 7, '2026-06-15', '2026-07-10'));
        $this->assertFalse($service->reportMonthMatchesFilters(2026, 6, '2026-07-01', '2026-07-31'));
        $this->assertFalse($service->reportMonthMatchesFilters(2026, 8, '2026-07-01', '2026-07-31'));
    }
}
