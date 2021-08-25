<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('chapter_id')->nullable();
            $table->integer('zone_id');
            $table->integer('field_id');           
            $table->integer('zone_status')->default(0);
            $table->integer('field_status')->default(0);//0:Pending zonal pastor, 1:Pending Field, 2:pending national scretariat, 3:complete 
            $table->integer('status_complete')->default(0);

            $table->string('president_name')->nullable();
            $table->string('president_number')->nullable();
            $table->string('gen_sec_name')->nullable();
            $table->string('gen_sec_number')->nullable();
            $table->string('evang_sec_name')->nullable();
            $table->string('evang_sec_number')->nullable();
            $table->string('fin_sec_name')->nullable();
            $table->string('fin_sec_number')->nullable();
            $table->string('bible_study_venue')->nullable();
            $table->string('bible_study_time')->nullable();
            $table->string('bible_study_highest_attendance')->nullable();
            $table->string('bible_study_lowest_attendance')->nullable();
            $table->string('prayer_meeting_venue')->nullable();
            $table->string('prayer_meeting_time')->nullable();
            $table->string('prayer_meeting_highest_attendance')->nullable();
            $table->string('prayer_meeting_lowest_attendance')->nullable();
            $table->string('believer_foundation_class_venue')->nullable();
            $table->string('believer_foundation_class_time')->nullable();
            $table->string('believer_foundation_class_highest_attendance')->nullable();
            $table->string('believer_foundation_class_lowest_attendance')->nullable();
           
            $table->string('sunday_school_highest_attendance')->nullable();
            $table->string('sunday_school_lowest_attendance')->nullable();
            $table->string('sunday_highest_attendance')->nullable();
            $table->string('sunday_lowest_attendance')->nullable();
            
            $table->string('visit_to_assembly_venue')->nullable();
            $table->string('visit_to_assembly_time')->nullable();
            $table->string('visit_to_assembly_fellowship_attendance')->nullable();
            $table->string('visit_to_assembly_fellowship_activity')->nullable();
            $table->string('special_programs')->nullable();//json, seperate each by comma, e.g Program1:Objective, Program2:Objective|date|venue|time|attendance
            
            $table->string('holy_communion')->nullable();
            $table->string('holy_communion_minister')->nullable();
            $table->string('holy_communion_minister_rank')->nullable();
            $table->string('holy_communion_attendance')->nullable();

            $table->text('evangelism_report')->nullable();
            $table->integer('evangelism_number_of_souls')->nullable();
            $table->integer('evangelism_number_of_souls_who_joined_fellowship')->nullable();
            $table->text('evangelism_follow_up_efforts')->nullable();
            $table->integer('evangelism_number_of_converts_baptized')->nullable();

            $table->float('bible_study_offering')->nullable();
            $table->float('prayer_meeting_offering')->nullable();
            $table->float('special_program_offering')->nullable();
            $table->float('other_special_program_offering')->nullable();
            $table->float('thanksgiving_offering')->nullable();
            $table->float('total_sunday_worship_offering')->nullable();
            $table->float('grand_total_offering')->nullable();

            $table->float('president_tithe')->nullable();
            $table->float('total_executive_tithe')->nullable();
            $table->float('total_workers_tithe')->nullable();
            $table->float('total_members_tithe')->nullable();
            $table->float('grand_total_tithe')->nullable();
            $table->float('tithe_of_tithe')->nullable();

            $table->string('other_levies_purpose')->nullable();
            $table->string('other_levies_projection')->nullable();
            $table->string('other_levies_period_of_collection')->nullable();
            $table->string('other_levies_total_amount')->nullable();
            $table->string('other_levies_total_accumulation')->nullable();

            //Expenses
            $table->float('capital_projects')->nullable();
            $table->float('recurrent_expenses')->nullable();
            $table->float('maintenance')->nullable();
            $table->float('misc')->nullable();
            $table->float('expenses_grand_total')->nullable();

            //Summary
            $table->text('spiritual_state')->nullable();
            $table->text('challenges')->nullable();

            $table->text('proposed_capital_project')->nullable();
            $table->text('completed_capital_project')->nullable();

            //Signatures
            $table->string('president_signature')->nullable();
            $table->string('gen_sec_signature')->nullable();
            $table->string('evan_sec_signature')->nullable();
            $table->string('fin_sec_signature')->nullable();

            $table->string('zonal_pastor_approval')->nullable();
            $table->string('zonal_pastor_affirmation')->nullable();

            $table->string('field_pastor_approval')->nullable();
            $table->string('field_pastor_comment')->nullable();

            $table->string('ncp_comment')->nullable();

            $table->string('session');
            $table->string('semester');
            $table->string('month');
            $table->string('year');
            $table->integer('day');
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports');
    }
}
