<?php

namespace Tests\Feature;

use App\Services\GrowthAnalysisService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class GrowthAnalysisServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');

        Schema::create('chapters', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->unsignedBigInteger('field_id'); $table->unsignedBigInteger('zone_id');
        });
        Schema::create('fields', function (Blueprint $table) { $table->id(); $table->string('name'); });
        Schema::create('zones', function (Blueprint $table) { $table->id(); $table->string('name'); });
        Schema::create('stakeholder_report_questions', function (Blueprint $table) {
            $table->id(); $table->string('slug'); $table->string('label'); $table->string('type');
            $table->boolean('is_quantifiable'); $table->integer('status'); $table->string('module_type');
            $table->string('section_id')->nullable(); $table->integer('order'); $table->text('options')->nullable();
        });
        Schema::create('stakeholder_reports', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('chapter_id'); $table->integer('year'); $table->integer('month');
            $table->boolean('edit_mode'); $table->boolean('school_not_in_session');
        });
        Schema::create('stakeholder_report_answers', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('report_id'); $table->unsignedBigInteger('question_id');
            $table->text('answer_value')->nullable(); $table->integer('answer_quantity')->nullable();
        });
    }

    public function test_out_of_session_campus_does_not_reduce_zone_average_or_appear_as_zero(): void
    {
        DB::table('fields')->insert(['id' => 1, 'name' => 'Field A']);
        DB::table('zones')->insert(['id' => 1, 'name' => 'Zone A']);
        DB::table('chapters')->insert([
            ['id' => 1, 'name' => 'Campus A', 'field_id' => 1, 'zone_id' => 1],
            ['id' => 2, 'name' => 'Campus B', 'field_id' => 1, 'zone_id' => 1],
        ]);
        DB::table('stakeholder_report_questions')->insert([
            'id' => 10, 'slug' => 'highest_attendance', 'label' => 'Highest attendance', 'type' => 'number',
            'is_quantifiable' => 1, 'status' => 1, 'module_type' => 'report', 'order' => 1,
        ]);
        DB::table('stakeholder_reports')->insert([
            ['id' => 1, 'chapter_id' => 1, 'year' => 2026, 'month' => 1, 'edit_mode' => 0, 'school_not_in_session' => 0],
            ['id' => 2, 'chapter_id' => 2, 'year' => 2026, 'month' => 1, 'edit_mode' => 0, 'school_not_in_session' => 0],
            ['id' => 3, 'chapter_id' => 1, 'year' => 2026, 'month' => 2, 'edit_mode' => 0, 'school_not_in_session' => 0],
            ['id' => 4, 'chapter_id' => 2, 'year' => 2026, 'month' => 2, 'edit_mode' => 0, 'school_not_in_session' => 1],
        ]);
        DB::table('stakeholder_report_answers')->insert([
            ['report_id' => 1, 'question_id' => 10, 'answer_value' => '10'],
            ['report_id' => 2, 'question_id' => 10, 'answer_value' => '20'],
            ['report_id' => 3, 'question_id' => 10, 'answer_value' => '30'],
            ['report_id' => 4, 'question_id' => 10, 'answer_value' => '999'],
        ]);

        $service = new GrowthAnalysisService;
        $scope = ['chapterIds' => collect([1, 2])];
        $zone = $service->build(Request::create('/', 'GET', [
            'level' => 'zone', 'measure_id' => 10, 'from_month' => '2026-01', 'to_month' => '2026-02',
        ]), $scope, $service->measures());
        $this->assertSame([15.0, 30.0], $zone['series'][0]['values']);
        $this->assertSame([2, 1], $zone['series'][0]['contributors']);
        $this->assertSame(15.0, $zone['series'][0]['previous']);
        $this->assertSame(30.0, $zone['series'][0]['latest']);
        $this->assertSame('growing', $zone['series'][0]['status']);

        $zoneWithMissingLastMonth = $service->build(Request::create('/', 'GET', [
            'level' => 'zone', 'measure_id' => 10, 'from_month' => '2026-01', 'to_month' => '2026-03',
        ]), $scope, $service->measures());
        $this->assertSame([15.0, 30.0, null], $zoneWithMissingLastMonth['series'][0]['values']);
        $this->assertSame(15.0, $zoneWithMissingLastMonth['series'][0]['previous']);
        $this->assertSame('2026-01', $zoneWithMissingLastMonth['series'][0]['previous_month']);
        $this->assertSame(30.0, $zoneWithMissingLastMonth['series'][0]['latest']);
        $this->assertSame('2026-02', $zoneWithMissingLastMonth['series'][0]['latest_month']);
        $this->assertSame('growing', $zoneWithMissingLastMonth['series'][0]['status']);

        $chapters = $service->build(Request::create('/', 'GET', [
            'level' => 'chapter', 'measure_id' => 10, 'from_month' => '2026-01', 'to_month' => '2026-03',
        ]), $scope, $service->measures());
        $this->assertSame([20.0, null, null], $chapters['series'][1]['values']);
        $this->assertSame('insufficient', $chapters['series'][1]['status']);

        DB::table('stakeholder_reports')->insert([
            'id' => 5, 'chapter_id' => 1, 'year' => 2026, 'month' => 3,
            'edit_mode' => 0, 'school_not_in_session' => 0,
        ]);
        DB::table('stakeholder_report_answers')->insert([
            'report_id' => 5, 'question_id' => 10, 'answer_value' => '35',
        ]);
        $threeMonths = $service->build(Request::create('/', 'GET', [
            'level' => 'zone', 'measure_id' => 10, 'from_month' => '2026-01', 'to_month' => '2026-03',
        ]), $scope, $service->measures());
        $this->assertSame(30.0, $threeMonths['series'][0]['previous']);
        $this->assertSame(35.0, $threeMonths['series'][0]['latest']);
        $this->assertSame(5.0, $threeMonths['series'][0]['change']);

        DB::table('fields')->insert(['id' => 2, 'name' => 'Field B']);
        DB::table('zones')->insert(['id' => 2, 'name' => 'Zone B']);
        DB::table('chapters')->insert(['id' => 3, 'name' => 'Campus C', 'field_id' => 2, 'zone_id' => 2]);
        DB::table('stakeholder_reports')->insert([
            ['id' => 6, 'chapter_id' => 3, 'year' => 2026, 'month' => 1, 'edit_mode' => 0, 'school_not_in_session' => 0],
            ['id' => 7, 'chapter_id' => 3, 'year' => 2026, 'month' => 2, 'edit_mode' => 0, 'school_not_in_session' => 0],
        ]);
        DB::table('stakeholder_report_answers')->insert([
            ['report_id' => 6, 'question_id' => 10, 'answer_value' => '100'],
            ['report_id' => 7, 'question_id' => 10, 'answer_value' => '200'],
        ]);
        $fields = $service->build(Request::create('/', 'GET', [
            'level' => 'field', 'measure_id' => 10, 'from_month' => '2026-01', 'to_month' => '2026-03',
        ]), ['chapterIds' => collect([1, 2, 3])], $service->measures());
        $this->assertSame(['Field A', 'Field B'], array_column($fields['series'], 'name'));
        $this->assertSame([15.0, 30.0, 35.0], $fields['series'][0]['values']);
        $this->assertSame([100.0, 200.0, null], $fields['series'][1]['values']);

        DB::table('stakeholder_reports')->insert([
            ['id' => 8, 'chapter_id' => 1, 'year' => 2025, 'month' => 12, 'edit_mode' => 0, 'school_not_in_session' => 0],
            ['id' => 9, 'chapter_id' => 2, 'year' => 2025, 'month' => 12, 'edit_mode' => 0, 'school_not_in_session' => 0],
        ]);
        DB::table('stakeholder_report_answers')->insert([
            ['report_id' => 8, 'question_id' => 10, 'answer_value' => '12'],
            ['report_id' => 9, 'question_id' => 10, 'answer_value' => '18'],
        ]);
        $yearly = $service->build(Request::create('/', 'GET', [
            'level' => 'zone', 'period' => 'year', 'measure_id' => 10,
            'from_month' => '2025-01', 'to_month' => '2026-03',
        ]), $scope, $service->measures());
        $this->assertSame(['2025', '2026'], $yearly['months']);
        $this->assertSame([15.0, 26.67], $yearly['series'][0]['values']);
        $this->assertSame([1, 3], $yearly['series'][0]['contributors']);
        $this->assertSame('2025', $yearly['series'][0]['previous_month']);
        $this->assertSame('2026', $yearly['series'][0]['latest_month']);
        $this->assertSame(11.67, $yearly['series'][0]['change']);
    }

    public function test_overall_index_weights_questions_equally_and_ignores_out_of_session_reports(): void
    {
        DB::table('fields')->insert(['id' => 1, 'name' => 'Field A']);
        DB::table('zones')->insert(['id' => 1, 'name' => 'Zone A']);
        DB::table('chapters')->insert([
            ['id' => 1, 'name' => 'Campus A', 'field_id' => 1, 'zone_id' => 1],
            ['id' => 2, 'name' => 'Campus B', 'field_id' => 1, 'zone_id' => 1],
        ]);
        DB::table('stakeholder_report_questions')->insert([
            ['id' => 10, 'slug' => 'highest_attendance', 'label' => 'Attendance', 'type' => 'number', 'is_quantifiable' => 1, 'status' => 1, 'module_type' => 'report', 'order' => 1, 'options' => null],
            ['id' => 11, 'slug' => 'active_workers', 'label' => 'Workers', 'type' => 'number', 'is_quantifiable' => 1, 'status' => 1, 'module_type' => 'report', 'order' => 2, 'options' => null],
            ['id' => 12, 'slug' => 'leaders_communication', 'label' => 'Communication', 'type' => 'select', 'is_quantifiable' => 1, 'status' => 1, 'module_type' => 'report', 'order' => 3, 'options' => json_encode(['1' => 'Very poor', '2' => 'Poor', '3' => 'Average', '4' => 'Good', '5' => 'Very good'])],
            ['id' => 13, 'slug' => 'ordinary_number', 'label' => 'Not a growth measure', 'type' => 'number', 'is_quantifiable' => 0, 'status' => 1, 'module_type' => 'report', 'order' => 4, 'options' => null],
        ]);
        DB::table('stakeholder_reports')->insert([
            ['id' => 1, 'chapter_id' => 1, 'year' => 2026, 'month' => 1, 'edit_mode' => 0, 'school_not_in_session' => 0],
            ['id' => 2, 'chapter_id' => 2, 'year' => 2026, 'month' => 1, 'edit_mode' => 0, 'school_not_in_session' => 0],
            ['id' => 3, 'chapter_id' => 1, 'year' => 2026, 'month' => 2, 'edit_mode' => 0, 'school_not_in_session' => 0],
            ['id' => 4, 'chapter_id' => 2, 'year' => 2026, 'month' => 2, 'edit_mode' => 0, 'school_not_in_session' => 1],
        ]);
        DB::table('stakeholder_report_answers')->insert([
            ['report_id' => 1, 'question_id' => 10, 'answer_value' => '10', 'answer_quantity' => null],
            ['report_id' => 1, 'question_id' => 11, 'answer_value' => '1', 'answer_quantity' => null],
            ['report_id' => 1, 'question_id' => 12, 'answer_value' => '4', 'answer_quantity' => 4],
            ['report_id' => 1, 'question_id' => 13, 'answer_value' => '10000', 'answer_quantity' => null],
            ['report_id' => 2, 'question_id' => 10, 'answer_value' => '20', 'answer_quantity' => null],
            ['report_id' => 2, 'question_id' => 11, 'answer_value' => '2', 'answer_quantity' => null],
            ['report_id' => 2, 'question_id' => 12, 'answer_value' => '2', 'answer_quantity' => 2],
            ['report_id' => 3, 'question_id' => 10, 'answer_value' => '30', 'answer_quantity' => null],
            ['report_id' => 3, 'question_id' => 11, 'answer_value' => '2', 'answer_quantity' => null],
            ['report_id' => 3, 'question_id' => 12, 'answer_value' => '5', 'answer_quantity' => 5],
            ['report_id' => 4, 'question_id' => 10, 'answer_value' => '999', 'answer_quantity' => null],
        ]);

        $service = new GrowthAnalysisService;
        $measures = $service->measures();
        $this->assertSame('Overall growth index', $measures[0]['label']);
        $this->assertNotContains(13, array_column($measures, 'id'));
        $scope = ['chapterIds' => collect([1, 2])];
        $zone = $service->build(Request::create('/', 'GET', [
            'level' => 'zone', 'measure_id' => 0, 'from_month' => '2026-01', 'to_month' => '2026-02',
        ]), $scope, $measures);
        $this->assertSame([61.67, 100.0], $zone['series'][0]['values']);
        $this->assertSame([2, 1], $zone['series'][0]['contributors']);
        $yearlyIndex = $service->build(Request::create('/', 'GET', [
            'level' => 'zone', 'period' => 'year', 'measure_id' => 0,
            'from_month' => '2026-01', 'to_month' => '2026-02',
        ]), $scope, $measures);
        $this->assertSame([80.84], $yearlyIndex['series'][0]['values']);
        $this->assertSame([2], $yearlyIndex['series'][0]['contributors']);

        $selectedCampus = $service->build(Request::create('/', 'GET', [
            'level' => 'chapter', 'measure_id' => 0, 'from_month' => '2026-01', 'to_month' => '2026-02', 'entity_ids' => [2],
        ]), $scope, $measures);
        $this->assertSame([68.89, null], $selectedCampus['series'][0]['values']);
        $this->assertSame('insufficient', $selectedCampus['series'][0]['status']);
    }
}
