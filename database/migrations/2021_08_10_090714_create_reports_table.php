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
            $table->integer('field_status')->default(0); 
            $table->integer('status_complete')->default(0);
            
            $table->text('zone_reject_comment')->nullable();
            $table->text('field_reject_comment')->nullable();
            $table->text('status_complete_reject_comment')->nullable();

            $table->text('president_name')->nullable();
            $table->text('president_number')->nullable();
            $table->text('gen_sec_name')->nullable();
            $table->text('gen_sec_number')->nullable();
            $table->text('evang_sec_name')->nullable();
            $table->text('evang_sec_number')->nullable();
            $table->text('fin_sec_name')->nullable();
            $table->text('fin_sec_number')->nullable();
            $table->text('bible_study_venue')->nullable();
            $table->text('bible_study_time')->nullable();
            $table->text('bible_study_highest_attendance')->nullable();
            $table->text('bible_study_lowest_attendance')->nullable();
            $table->text('prayer_meeting_venue')->nullable();
            $table->text('prayer_meeting_time')->nullable();
            $table->text('prayer_meeting_highest_attendance')->nullable();
            $table->text('prayer_meeting_lowest_attendance')->nullable();
            $table->text('believer_foundation_class_venue')->nullable();
            $table->text('believer_foundation_class_time')->nullable();
            $table->text('believer_foundation_class_highest_attendance')->nullable();
            $table->text('believer_foundation_class_lowest_attendance')->nullable();
           
            $table->text('sunday_school_highest_attendance')->nullable();
            $table->text('sunday_school_lowest_attendance')->nullable();
            $table->text('sunday_highest_attendance')->nullable();
            $table->text('sunday_lowest_attendance')->nullable();
            
            $table->text('visit_to_assembly_venue')->nullable();
            $table->text('visit_to_assembly_time')->nullable();
            $table->text('visit_to_assembly_fellowship_attendance')->nullable();
            $table->text('visit_to_assembly_fellowship_activity')->nullable();
            $table->text('special_programs')->nullable();//json, seperate each by comma, e.g Program1:Objective, Program2:Objective|date|venue|time|attendance
            
            $table->text('holy_communion')->nullable();
            $table->text('holy_communion_minister')->nullable();
            $table->text('holy_communion_minister_rank')->nullable();
            $table->text('holy_communion_attendance')->nullable();

            $table->text('evangelism_report')->nullable();
            $table->integer('evangelism_number_of_souls')->nullable();
            $table->integer('evangelism_number_of_souls_who_joined_fellowship')->nullable();
            $table->text('evangelism_follow_up_efforts')->nullable();
            $table->integer('evangelism_number_of_converts_baptized')->nullable();

            $table->double('bible_study_offering')->nullable();
            $table->double('prayer_meeting_offering')->nullable();
            $table->double('special_program_offering')->nullable();
            $table->double('other_special_program_offering')->nullable();
            $table->double('thanksgiving_offering')->nullable();
            $table->double('total_sunday_worship_offering')->nullable();
            $table->double('grand_total_offering', 20, 8)->nullable();

            $table->double('president_tithe')->nullable();
            $table->double('total_executive_tithe')->nullable();
            $table->double('total_workers_tithe')->nullable();
            $table->double('total_members_tithe')->nullable();
            $table->double('grand_total_tithe', 20, 8)->nullable();
            $table->double('tithe_of_tithe')->nullable();

            $table->text('other_levies_purpose')->nullable();
            $table->text('other_levies_projection')->nullable();
            $table->text('other_levies_period_of_collection')->nullable();
            $table->text('other_levies_total_amount')->nullable();
            $table->text('other_levies_total_accumulation')->nullable();

            //Expenses
            $table->double('capital_projects')->nullable();
            $table->double('recurrent_expenses')->nullable();
            $table->double('maintenance')->nullable();
            $table->double('misc')->nullable();
            $table->double('expenses_grand_total')->nullable();

            //Summary
            $table->text('spiritual_state')->nullable();
            $table->text('challenges')->nullable();

            $table->text('proposed_capital_project')->nullable();
            $table->text('completed_capital_project')->nullable();

            //Signatures
            $table->text('president_signature')->nullable();
            $table->text('gen_sec_signature')->nullable();
            $table->text('evan_sec_signature')->nullable();
            $table->text('fin_sec_signature')->nullable();

            $table->text('zonal_pastor_approval')->nullable();
            $table->text('zonal_pastor_affirmation')->nullable();

            $table->text('field_pastor_approval')->nullable();
            $table->text('field_pastor_comment')->nullable();

            $table->text('ncp_comment')->nullable();

            $table->text('session');
            $table->text('semester');
            $table->text('month');
            $table->text('year');
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
