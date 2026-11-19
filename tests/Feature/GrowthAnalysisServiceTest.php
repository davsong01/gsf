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
            'level' => 'zone', 'measure_id' => 10, 'from_month' => '2026-01', 'to_month' => '2026-03',
        ]), $scope, $service->measures());
        $this->assertSame([15.0, 30.0, null], $zone['series'][0]['values']);
        $this->assertSame([2, 1, 0], $zone['series'][0]['contributors']);
        $this->assertSame('growing', $zone['series'][0]['status']);

        $chapters = $service->build(Request::create('/', 'GET', [
            'level' => 'chapter', 'measure_id' => 10, 'from_month' => '2026-01', 'to_month' => '2026-03',
        ]), $scope, $service->measures());
        $this->assertSame([20.0, null, null], $chapters['series'][1]['values']);
        $this->assertSame('insufficient', $chapters['series'][1]['status']);
    }
}
