<?php

namespace Database\Seeders;

use App\Models\StakeholderPermission;
use App\Models\StakeholderDesignation;
use App\Models\StakeholderRole;
use App\Models\StakeholderQuestionSection;
use App\Models\StakeholderQuestionSubSection;
use App\Models\StakeholderReportQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AppraisalQuestionSeeder extends Seeder
{
    protected string $sectionPrefix = '';
    protected array $permissionMap = [];

    public function run(): void
    {
        DB::transaction(function () {
            $permissions = $this->seedPermissions();

            $this->clearExistingAppraisalStructure();

            $this->seedFieldPastorAppraisal($permissions);
            $this->seedZonalPastorAppraisal($permissions);
            $this->seedNationalOfficerAppraisal($permissions);
            $this->seedNationalPresidentAppraisal($permissions);
        });
    }

    protected function seedPermissions(): array
    {
        $permissions = [
            'field_pastor_fill' => StakeholderPermission::updateOrCreate(
                ['slug' => 'field-pastor-fill'],
                ['name' => 'Field Pastor Fill']
            ),
            'zonal_pastor_fill' => StakeholderPermission::updateOrCreate(
                ['slug' => 'zonal-pastor-fill'],
                ['name' => 'Zonal Pastor Fill']
            ),
            'nec_member_fill' => StakeholderPermission::updateOrCreate(
                ['slug' => 'nec-member-fill'],
                ['name' => 'NEC Member Fill']
            ),
            'nec_member_evaluate' => StakeholderPermission::updateOrCreate(
                ['slug' => 'nec-member-evaluate'],
                ['name' => 'NEC Member Evaluate']
            ),
            'field_pastor_evaluate' => StakeholderPermission::updateOrCreate(
                ['slug' => 'field-pastor-evaluate'],
                ['name' => 'Field Pastor Evaluate']
            ),
            'national_president_fill' => StakeholderPermission::updateOrCreate(
                ['slug' => 'national-president-fill'],
                ['name' => 'National President Fill']
            ),
            'national_president_evaluate' => StakeholderPermission::updateOrCreate(
                ['slug' => 'national-president-evaluate'],
                ['name' => 'National President Evaluate']
            ),
            'ncp_evaluate' => StakeholderPermission::updateOrCreate(
                ['slug' => 'ncp-evaluate'],
                ['name' => 'NCP Evaluate']
            ),
        ];

        return collect($permissions)->map(fn ($permission) => $permission->id)->all();
    }

    protected function clearExistingAppraisalStructure(): void
    {
        StakeholderReportQuestion::where('module_type', 'appraisal')
            ->get()
            ->each(function (StakeholderReportQuestion $question) {
                $question->permissions()->detach();
                $question->delete();
            });

        StakeholderQuestionSubSection::where('module_type', 'appraisal')->delete();
        StakeholderQuestionSection::where('module_type', 'appraisal')->delete();
    }

    protected function roleIds(array $roleNames): array
    {
        return collect($roleNames)
            ->map(function (string $roleName) {
                return StakeholderRole::firstOrCreate(
                    ['name' => $roleName],
                    ['slug' => Str::slug($roleName)]
                )->id;
            })
            ->all();
    }

    protected function designationIds(array $designationNames): array
    {
        return collect($designationNames)
            ->map(function (string $designationName) {
                return StakeholderDesignation::firstOrCreate(
                    ['name' => $designationName],
                    ['type' => 'nec', 'status' => 'active']
                )->id;
            })
            ->all();
    }

    protected function applyAccessRoles($model, array $roleNames): void
    {
        $model->update([
            'access_roles' => $this->roleIds($roleNames) ?: null,
        ]);
    }

    protected function applyAccessDesignations($model, array $designationNames): void
    {
        $model->update([
            'access_roles' => $this->designationIds($designationNames) ?: null,
        ]);
    }

    protected function applyAccessEntities($model, array $roleNames = [], array $designationNames = []): void
    {
        $accessRoles = array_merge($this->roleIds($roleNames), $this->designationIds($designationNames));

        $model->update([
            'access_roles' => !empty($accessRoles) ? $accessRoles : null,
        ]);
    }

    protected function seedFieldPastorAppraisal(array $permissions): void
    {
        $form = 'field-pastor';
        $this->sectionPrefix = 'Field Pastor';
        $this->permissionMap = [
            'fill' => $permissions['field_pastor_fill'],
            'evaluate' => $permissions['field_pastor_evaluate'],
        ];

        $sectionA = $this->createSection('SECTION A: Personal and Ministry Information');
        $this->applyAccessRoles($sectionA, ['Field Pastor']);
        $subA = $this->createSubSection($sectionA, 'Personal and Ministry Information');
        $this->applyAccessRoles($subA, ['Field Pastor']);
        $this->createQuestionSet($subA, [
            ['label' => 'Name of Field Pastor', 'slug' => "{$form}-name-of-field-pastor", 'type' => 'text', 'audience' => 'appraisee'],
            ['label' => 'Field/Area Covered', 'slug' => "{$form}-field-area-covered", 'type' => 'text', 'audience' => 'appraisee'],
            ['label' => 'Period under review', 'slug' => "{$form}-period-under-review", 'type' => 'text', 'audience' => 'appraisee'],
            ['label' => 'Number of zones under supervision', 'slug' => "{$form}-number-of-zones-under-supervision", 'type' => 'number', 'audience' => 'appraisee'],
            ['label' => 'Number of official visits made during the period under review', 'slug' => "{$form}-number-of-official-visits", 'type' => 'number', 'audience' => 'appraisee'],
        ], $permissions);

        $sectionB = $this->createSection('SECTION B: Spiritual Leadership and Pastoral Oversight');
        $this->applyAccessRoles($sectionB, ['Field Pastor']);
        $subB = $this->createSubSection($sectionB, 'Appraisal Items');
        $this->applyAccessRoles($subB, ['Field Pastor']);
        $this->createQuestionSet($subB, [
            ['label' => 'Spiritual guidance to fellowship leaders and members', 'slug' => "{$form}-spiritual-guidance", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Commitment to prayer, teaching, and discipleship', 'slug' => "{$form}-commitment-to-prayer-teaching-discipleship", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Holiness, discipline, and spiritual growth among students', 'slug' => "{$form}-holiness-discipline-spiritual-growth", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Pastoral attention to struggling or weak fellowships', 'slug' => "{$form}-pastoral-attention-to-weak-fellowships", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Encourages unity and cooperation among fellowship leaders', 'slug' => "{$form}-encourages-unity-and-cooperation", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
        ], $permissions);

        $sectionC = $this->createSection('SECTION C: Administrative Effectiveness');
        $this->applyAccessRoles($sectionC, ['Field Pastor']);
        $subC = $this->createSubSection($sectionC, 'Appraisal Items');
        $this->applyAccessRoles($subC, ['Field Pastor']);
        $this->createQuestionSet($subC, [
            ['label' => 'Communicates effectively with zonal pastors', 'slug' => "{$form}-communicates-effectively-with-zonal-pastors", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Submits reports, updates, and feedback', 'slug' => "{$form}-submits-reports-updates-and-feedback", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Monitors the growth, challenges, and needs of fellowships under his care', 'slug' => "{$form}-monitors-growth-challenges-and-needs", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Supports record keeping and accountability within the field', 'slug' => "{$form}-supports-record-keeping-and-accountability", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Responds promptly to issues requiring pastoral intervention', 'slug' => "{$form}-responds-promptly-to-issues", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
        ], $permissions);

        $sectionD = $this->createSection('SECTION D: Leadership Development and Mentorship');
        $this->applyAccessRoles($sectionD, ['Field Pastor']);
        $subD = $this->createSubSection($sectionD, 'Appraisal Items');
        $this->applyAccessRoles($subD, ['Field Pastor']);
        $this->createQuestionSet($subD, [
            ['label' => 'Mentors students leaders and prepares them for effective service', 'slug' => "{$form}-mentors-students-leaders", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Identifies leadership gaps and recommends appropriate corrective steps', 'slug' => "{$form}-identifies-leadership-gaps", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Encourages leadership succession and continuity', 'slug' => "{$form}-encourages-leadership-succession", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Supports training programmes, retreats, and leadership meetings', 'slug' => "{$form}-supports-training-programmes", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Demonstrates maturity, fairness, and spiritual wisdom in handling conflicts', 'slug' => "{$form}-demonstrates-maturity-fairness-and-wisdom", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
        ], $permissions);

        $sectionE = $this->createSection('SECTION E: Programme Support and Fellowship Growth');
        $this->applyAccessRoles($sectionE, ['Field Pastor']);
        $subE = $this->createSubSection($sectionE, 'Appraisal Items');
        $this->applyAccessRoles($subE, ['Field Pastor']);
        $this->createQuestionSet($subE, [
            ['label' => 'Supports national, field, zonal, and fellowship programmes', 'slug' => "{$form}-supports-programmes", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Evangelism, discipleship, and campus fellowship expansion', 'slug' => "{$form}-evangelism-discipleship-expansion", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Helps fellowships address membership decline and inactivity', 'slug' => "{$form}-helps-fellowships-address-decline", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Useful counsel toward improving fellowship activities', 'slug' => "{$form}-useful-counsel-toward-improving-activities", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Commitment to the vision and mission of GOFAMINT Students Fellowship', 'slug' => "{$form}-commitment-to-vision-and-mission", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
        ], $permissions);

        $sectionF = $this->createSection('SECTION F: Attendance and Participation at National Programmes and Meetings');
        $this->applyAccessRoles($sectionF, ['Field Pastor']);
        $subF1 = $this->createSubSection($sectionF, 'National Programme Attendance');
        $this->applyAccessRoles($subF1, ['Field Pastor']);
        $this->createQuestionSet($subF1, [
            ['label' => 'NEC Orientation Programme (May 2025)', 'slug' => "{$form}-nec-orientation-programme", 'type' => 'select', 'options' => $this->attendanceOptions(), 'audience' => 'appraiser'],
            ['label' => 'NEC Leadership Retreat (December 2025)', 'slug' => "{$form}-nec-leadership-retreat", 'type' => 'select', 'options' => $this->attendanceOptions(), 'audience' => 'appraiser'],
            ['label' => 'General Assembly 2026', 'slug' => "{$form}-general-assembly-2026", 'type' => 'select', 'options' => $this->attendanceOptions(), 'audience' => 'appraiser'],
            ['label' => 'General Assembly 2026 - January, South West Region', 'slug' => "{$form}-general-assembly-2026-january-south-west", 'type' => 'select', 'options' => $this->attendanceOptions(), 'audience' => 'appraiser'],
            ['label' => 'General Assembly 2026 - February, Northern Region', 'slug' => "{$form}-general-assembly-2026-february-northern", 'type' => 'select', 'options' => $this->attendanceOptions(), 'audience' => 'appraiser'],
            ['label' => 'General Assembly 2026 - March, South-South and Eastern Region', 'slug' => "{$form}-general-assembly-2026-march-south-south-eastern", 'type' => 'select', 'options' => $this->attendanceOptions(), 'audience' => 'appraiser'],
            ['label' => 'National Prayer Conference (July 17th - 19th 2026)', 'slug' => "{$form}-national-prayer-conference-2026", 'type' => 'select', 'options' => $this->attendanceOptions(), 'audience' => 'appraiser'],
            ['label' => 'Total Monthly Field Pastors Meetings Attended', 'slug' => "{$form}-total-monthly-field-pastors-meetings-attended", 'type' => 'number', 'audience' => 'appraisee'],
        ], $permissions);

        $subF2 = $this->createSubSection($sectionF, 'Digital Report Compliance');
        $this->applyAccessRoles($subF2, ['Field Pastor']);
        $this->createQuestionSet($subF2, [
            ['label' => 'Level of compliance with the Monthly Digital Report Platform', 'slug' => "{$form}-monthly-digital-report-compliance", 'type' => 'select', 'options' => $this->complianceOptions(), 'audience' => 'appraiser'],
        ], $permissions);

        $sectionG = $this->createSection('SECTION G: Kindly respond to the questions below in not more than 2 lines per question');
        $this->applyAccessRoles($sectionG, ['Field Pastor']);
        $subG = $this->createSubSection($sectionG, 'Self-Reflection');
        $this->applyAccessRoles($subG, ['Field Pastor']);
        $this->createQuestionSet($subG, [
            ['label' => 'What are your top 3 strategic goals for the remainder of your tenure?', 'slug' => "{$form}-self-reflection-top-3-strategic-goals", 'type' => 'textarea', 'audience' => 'appraisee'],
            ['label' => 'What key challenges impacted your performance during the period under review?', 'slug' => "{$form}-self-reflection-key-challenges", 'type' => 'textarea', 'audience' => 'appraisee'],
            ['label' => 'What specific resources or administrative support do you require?', 'slug' => "{$form}-self-reflection-resources-needed", 'type' => 'textarea', 'audience' => 'appraisee'],
            ['label' => 'What specific initiatives do you intend to implement before the end of your administration?', 'slug' => "{$form}-self-reflection-initiatives", 'type' => 'textarea', 'audience' => 'appraisee'],
            ['label' => 'What are your major strengths as a Field Pastor?', 'slug' => "{$form}-self-reflection-major-strengths", 'type' => 'textarea', 'audience' => 'appraisee'],
            ['label' => 'What areas do you require improvement?', 'slug' => "{$form}-self-reflection-areas-for-improvement", 'type' => 'textarea', 'audience' => 'appraisee'],
            ['label' => 'What notable achievements were recorded under your pastoral oversight?', 'slug' => "{$form}-self-reflection-notable-achievements", 'type' => 'textarea', 'audience' => 'appraisee'],
            ['label' => 'What challenges affected your performance?', 'slug' => "{$form}-self-reflection-performance-challenges", 'type' => 'textarea', 'audience' => 'appraisee'],
            ['label' => 'What recommendations should be made for further development?', 'slug' => "{$form}-self-reflection-recommendations", 'type' => 'textarea', 'audience' => 'appraisee'],
        ], $permissions);

        $sectionH = $this->createSection('SECTION H: Official Use only (To be completed by the NCP)');
        $this->applyAccessRoles($sectionH, ['NCP']);
        $subH1 = $this->createSubSection($sectionH, 'Recommendation on Continuation in Office');
        $this->applyAccessRoles($subH1, ['NCP']);
        $this->createQuestionSet($subH1, [
            ['label' => 'Recommendation on continuation in office', 'slug' => "{$form}-recommendation-on-continuation", 'type' => 'select', 'options' => $this->fieldRecommendationOptions(), 'audience' => 'appraiser'],
            ['label' => 'Reason', 'slug' => "{$form}-continuation-reason", 'type' => 'textarea', 'audience' => 'appraiser'],
        ], $permissions);

        $subH2 = $this->createSubSection($sectionH, 'Evaluator Assessment');
        $this->applyAccessRoles($subH2, ['NCP']);
        $this->createQuestionSet($subH2, [
            ['label' => 'Key strengths observed in this officer', 'slug' => "{$form}-evaluator-key-strengths", 'type' => 'textarea', 'audience' => 'appraiser'],
            ['label' => 'Specific performance gaps or areas requiring improvement', 'slug' => "{$form}-evaluator-performance-gaps", 'type' => 'textarea', 'audience' => 'appraiser'],
            ['label' => 'Key notable achievements recorded under their leadership', 'slug' => "{$form}-evaluator-notable-achievements", 'type' => 'textarea', 'audience' => 'appraiser'],
            ['label' => 'Name of Appraiser', 'slug' => "{$form}-appraiser-name", 'type' => 'text', 'audience' => 'appraiser'],
            ['label' => 'Position', 'slug' => "{$form}-appraiser-position", 'type' => 'text', 'audience' => 'appraiser'],
            ['label' => 'Signature', 'slug' => "{$form}-appraiser-signature", 'type' => 'file', 'audience' => 'appraiser'],
            ['label' => 'Date', 'slug' => "{$form}-appraiser-date", 'type' => 'date', 'audience' => 'appraiser'],
        ], $permissions);
    }

    protected function seedZonalPastorAppraisal(array $permissions): void
    {
        $form = 'zonal-pastor';
        $this->sectionPrefix = 'Zonal Pastor';
        $this->permissionMap = [
            'fill' => $permissions['zonal_pastor_fill'],
            'evaluate' => $permissions['field_pastor_evaluate'],
        ];

        $sectionA = $this->createSection('SECTION A: Personal and Ministry Information');
        $this->applyAccessRoles($sectionA, ['Zonal Pastor']);
        $subA = $this->createSubSection($sectionA, 'Personal and Ministry Information');
        $this->applyAccessRoles($subA, ['Zonal Pastor']);
        $this->createQuestionSet($subA, [
            ['label' => 'Name of Zonal Pastor', 'slug' => "{$form}-name-of-zonal-pastor", 'type' => 'text', 'audience' => 'appraisee'],
            ['label' => 'Zone', 'slug' => "{$form}-zone", 'type' => 'text', 'audience' => 'appraisee'],
            ['label' => 'Period under review', 'slug' => "{$form}-period-under-review", 'type' => 'text', 'audience' => 'appraisee'],
            ['label' => 'Number of campuses/fellowships in the zone', 'slug' => "{$form}-number-of-campuses", 'type' => 'number', 'audience' => 'appraisee'],
            ['label' => 'Number of active fellowships', 'slug' => "{$form}-number-of-active-fellowships", 'type' => 'number', 'audience' => 'appraisee'],
            ['label' => 'Number of struggling fellowships', 'slug' => "{$form}-number-of-struggling-fellowships", 'type' => 'text', 'audience' => 'appraisee'],
            ['label' => 'Number of inactive fellowships', 'slug' => "{$form}-number-of-inactive-fellowships", 'type' => 'text', 'audience' => 'appraisee'],
            ['label' => 'Number of official visits made during the period under review', 'slug' => "{$form}-number-of-official-visits", 'type' => 'number', 'audience' => 'appraisee'],
        ], $permissions);

        $sectionB = $this->createSection('SECTION B: Spiritual Oversight and Pastoral Care');
        $this->applyAccessRoles($sectionB, ['Zonal Pastor']);
        $subB = $this->createSubSection($sectionB, 'Appraisal Items');
        $this->applyAccessRoles($subB, ['Zonal Pastor']);
        $this->createQuestionSet($subB, [
            ['label' => 'Effective spiritual oversight to fellowships in the zone', 'slug' => "{$form}-effective-spiritual-oversight", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Encourages prayer, Bible study, discipleship, and holiness among students', 'slug' => "{$form}-encourages-prayer-bible-study-discipleship", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Visits or follows up with campus fellowships regularly', 'slug' => "{$form}-visits-or-follows-up-regularly", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Responds to spiritual and welfare needs of students and leaders', 'slug' => "{$form}-responds-to-spiritual-and-welfare-needs", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Promotes unity, discipline, and sound doctrine across the zone', 'slug' => "{$form}-promotes-unity-discipline-and-sound-doctrine", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
        ], $permissions);

        $sectionC = $this->createSection('SECTION C: Coordination and Administrative Performance');
        $this->applyAccessRoles($sectionC, ['Zonal Pastor']);
        $subC = $this->createSubSection($sectionC, 'Appraisal Items');
        $this->applyAccessRoles($subC, ['Zonal Pastor']);
        $this->createQuestionSet($subC, [
            ['label' => 'Coordinates zonal activities effectively', 'slug' => "{$form}-coordinates-zonal-activities-effectively", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Regular communication with campus leaders and field/national leadership', 'slug' => "{$form}-regular-communication-with-leaders", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Submits timely reports on zonal activities, challenges, and progress', 'slug' => "{$form}-submits-timely-reports", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Keeps accurate information on active fellowships, leaders, and members', 'slug' => "{$form}-keeps-accurate-information", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Ensures proper planning and execution of zonal programmes', 'slug' => "{$form}-ensures-proper-planning", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
        ], $permissions);

        $sectionD = $this->createSection('SECTION D: Leadership Development');
        $this->applyAccessRoles($sectionD, ['Zonal Pastor']);
        $subD = $this->createSubSection($sectionD, 'Appraisal Items');
        $this->applyAccessRoles($subD, ['Zonal Pastor']);
        $this->createQuestionSet($subD, [
            ['label' => 'Trains and mentors campus fellowship executives', 'slug' => "{$form}-trains-and-mentors-campus-executives", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Identifies and develops emerging student leaders', 'slug' => "{$form}-identifies-and-develops-emerging-leaders", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Encourages accountability among campus leaders', 'slug' => "{$form}-encourages-accountability-among-leaders", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Supports smooth leadership transition in campus fellowships', 'slug' => "{$form}-supports-smooth-leadership-transition", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Handles leadership conflicts with wisdom and fairness', 'slug' => "{$form}-handles-leadership-conflicts-with-wisdom", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
        ], $permissions);

        $sectionE = $this->createSection('SECTION E: Fellowship Growth and Programme Impact');
        $this->applyAccessRoles($sectionE, ['Zonal Pastor']);
        $subE = $this->createSubSection($sectionE, 'Appraisal Items');
        $this->applyAccessRoles($subE, ['Zonal Pastor']);
        $this->createQuestionSet($subE, [
            ['label' => 'Promotes evangelism and membership growth within the zone', 'slug' => "{$form}-promotes-evangelism-and-membership-growth", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Supports revival of weak or inactive campus fellowships', 'slug' => "{$form}-supports-revival-of-weak-fellowships", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Participation in national and field programmes', 'slug' => "{$form}-participation-in-national-and-field-programmes", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Commitment to the fellowships vision and objectives', 'slug' => "{$form}-commitment-to-vision-and-objectives", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Provides strategic recommendations for zonal development', 'slug' => "{$form}-provides-strategic-recommendations", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
        ], $permissions);

        $sectionF = $this->createSection('SECTION F: Accountability and Integrity');
        $this->applyAccessRoles($sectionF, ['Zonal Pastor']);
        $subF = $this->createSubSection($sectionF, 'Appraisal Items');
        $this->applyAccessRoles($subF, ['Zonal Pastor']);
        $this->createQuestionSet($subF, [
            ['label' => 'Transparency in handling responsibilities', 'slug' => "{$form}-transparency-in-handling-responsibilities", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Financial and administrative accountability within the zone', 'slug' => "{$form}-financial-and-administrative-accountability", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Provides honest feedback to leadership', 'slug' => "{$form}-provides-honest-feedback", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Relationship with students, pastors, and church authorities', 'slug' => "{$form}-relationship-with-students-pastors-and-church-authorities", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Represents GOFAMINT Students Fellowship positively', 'slug' => "{$form}-represents-gofamint-students-fellowship-positively", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
        ], $permissions);

        $sectionG = $this->createSection('SECTION G: Attendance and Participation at National Programmes and Meetings');
        $this->applyAccessRoles($sectionG, ['Zonal Pastor']);
        $subG1 = $this->createSubSection($sectionG, 'National Programme Attendance');
        $this->applyAccessRoles($subG1, ['Zonal Pastor']);
        $this->createQuestionSet($subG1, [
            ['label' => 'NEC Orientation Programme (May 2025)', 'slug' => "{$form}-nec-orientation-programme", 'type' => 'select', 'options' => $this->attendanceOptions(), 'audience' => 'appraiser'],
            ['label' => 'NEC Leadership Retreat (December 2025)', 'slug' => "{$form}-nec-leadership-retreat", 'type' => 'select', 'options' => $this->attendanceOptions(), 'audience' => 'appraiser'],
            ['label' => 'General Assembly 2026', 'slug' => "{$form}-general-assembly-2026", 'type' => 'select', 'options' => $this->attendanceOptions(), 'audience' => 'appraiser'],
            ['label' => 'General Assembly 2026 - January, South West Region', 'slug' => "{$form}-general-assembly-2026-january-south-west", 'type' => 'select', 'options' => $this->attendanceOptions(), 'audience' => 'appraiser'],
            ['label' => 'General Assembly 2026 - February, Northern Region', 'slug' => "{$form}-general-assembly-2026-february-northern", 'type' => 'select', 'options' => $this->attendanceOptions(), 'audience' => 'appraiser'],
            ['label' => 'General Assembly 2026 - March, South-South and Eastern Region', 'slug' => "{$form}-general-assembly-2026-march-south-south-eastern", 'type' => 'select', 'options' => $this->attendanceOptions(), 'audience' => 'appraiser'],
            ['label' => 'National Prayer Conference (July 17th - 19th 2026)', 'slug' => "{$form}-national-prayer-conference-2026", 'type' => 'select', 'options' => $this->attendanceOptions(), 'audience' => 'appraiser'],
            ['label' => 'Total NEC Meetings Attended', 'slug' => "{$form}-total-nec-meetings-attended", 'type' => 'text', 'audience' => 'appraiser'],
        ], $permissions);

        $subG2 = $this->createSubSection($sectionG, 'Digital Report Compliance');
        $this->applyAccessRoles($subG2, ['Zonal Pastor']);
        $this->createQuestionSet($subG2, [
            ['label' => 'Level of compliance with the Monthly Digital Report Platform', 'slug' => "{$form}-monthly-digital-report-compliance", 'type' => 'select', 'options' => $this->complianceOptions(), 'audience' => 'appraiser'],
        ], $permissions);

        $sectionH = $this->createSection('SECTION H: Office-Specific Questions. Kindly respond to the questions below in not more than 2 lines per question');
        $this->applyAccessRoles($sectionH, ['Zonal Pastor']);
        $subH = $this->createSubSection($sectionH, 'Self-Reflection');
        $this->applyAccessRoles($subH, ['Zonal Pastor']);
        $this->createQuestionSet($subH, [
            ['label' => 'What are your top 3 strategic goals for the remainder of your tenure?', 'slug' => "{$form}-office-top-3-strategic-goals", 'type' => 'textarea', 'audience' => 'appraisee'],
            ['label' => 'What key challenges impacted your performance during the period under review?', 'slug' => "{$form}-office-key-challenges", 'type' => 'textarea', 'audience' => 'appraisee'],
            ['label' => 'What specific resources or administrative support do you require?', 'slug' => "{$form}-office-resources-needed", 'type' => 'textarea', 'audience' => 'appraisee'],
            ['label' => 'What specific initiatives do you intend to implement before the end of your administration?', 'slug' => "{$form}-office-initiatives", 'type' => 'textarea', 'audience' => 'appraisee'],
            ['label' => 'What were the major responsibilities assigned to this officer during the period under review?', 'slug' => "{$form}-office-major-responsibilities", 'type' => 'textarea', 'audience' => 'appraisee'],
            ['label' => 'Which of these responsibilities were effectively carried out?', 'slug' => "{$form}-office-responsibilities-effectively-carried-out", 'type' => 'textarea', 'audience' => 'appraisee'],
            ['label' => 'Which responsibilities were not fully carried out, and why?', 'slug' => "{$form}-office-responsibilities-not-carried-out", 'type' => 'textarea', 'audience' => 'appraisee'],
            ['label' => 'What specific achievement can be credited to this officer?', 'slug' => "{$form}-office-specific-achievement", 'type' => 'textarea', 'audience' => 'appraisee'],
            ['label' => 'What area of the officer performance requires improvement?', 'slug' => "{$form}-office-area-requires-improvement", 'type' => 'textarea', 'audience' => 'appraisee'],
            ['label' => 'What training, support, or resources does your office need?', 'slug' => "{$form}-office-training-support-resources", 'type' => 'textarea', 'audience' => 'appraisee'],
            ['label' => 'What recommendations should be made for further development?', 'slug' => "{$form}-office-recommendations", 'type' => 'textarea', 'audience' => 'appraisee'],
        ], $permissions);

        $sectionI = $this->createSection('SECTION I: Official Use only (To be completed by Field Pastor)');
        $this->applyAccessRoles($sectionI, ['Field Pastor']);
        $subI1 = $this->createSubSection($sectionI, 'Recommendation on Continuation in Office');
        $this->applyAccessRoles($subI1, ['Field Pastor']);
        $this->createQuestionSet($subI1, [
            ['label' => 'Recommendation on continuation in office', 'slug' => "{$form}-recommendation-on-continuation", 'type' => 'select', 'options' => $this->fieldRecommendationOptions(), 'audience' => 'appraiser'],
            ['label' => 'Reason', 'slug' => "{$form}-continuation-reason", 'type' => 'textarea', 'audience' => 'appraiser'],
        ], $permissions);

        $subI2 = $this->createSubSection($sectionI, 'Evaluator Assessment');
        $this->applyAccessRoles($subI2, ['Field Pastor']);
        $this->createQuestionSet($subI2, [
            ['label' => 'Key strengths observed in this officer', 'slug' => "{$form}-evaluator-key-strengths", 'type' => 'textarea', 'audience' => 'appraiser'],
            ['label' => 'Specific performance gaps or areas requiring improvement', 'slug' => "{$form}-evaluator-performance-gaps", 'type' => 'textarea', 'audience' => 'appraiser'],
            ['label' => 'Key notable achievements recorded under their leadership', 'slug' => "{$form}-evaluator-notable-achievements", 'type' => 'textarea', 'audience' => 'appraiser'],
            ['label' => 'Name of Appraiser', 'slug' => "{$form}-appraiser-name", 'type' => 'text', 'audience' => 'appraiser'],
            ['label' => 'Position', 'slug' => "{$form}-appraiser-position", 'type' => 'text', 'audience' => 'appraiser'],
            ['label' => 'Signature', 'slug' => "{$form}-appraiser-signature", 'type' => 'file', 'audience' => 'appraiser'],
            ['label' => 'Date', 'slug' => "{$form}-appraiser-date", 'type' => 'date', 'audience' => 'appraiser'],
        ], $permissions);
    }

    protected function seedNationalOfficerAppraisal(array $permissions): void
    {
        $form = 'national-officer';
        $this->sectionPrefix = 'National Officer';
        $this->permissionMap = [
            'fill' => $permissions['nec_member_fill'],
            'evaluate' => $permissions['nec_member_evaluate'],
        ];

        $sectionA = $this->createSection('SECTION A: Personal and Ministry Information');
        $this->applyAccessRoles($sectionA, ['Nec Member']);
        $subA = $this->createSubSection($sectionA, 'Personal and Ministry Information');
        $this->applyAccessRoles($subA, ['Nec Member']);
        $this->createQuestionSet($subA, [
            ['label' => 'Name of National Officer', 'slug' => "{$form}-name-of-national-officer", 'type' => 'text', 'audience' => 'appraisee'],
            ['label' => 'Office/Position Held', 'slug' => "{$form}-office-position-held", 'type' => 'text', 'audience' => 'appraisee'],
            ['label' => 'Period under review', 'slug' => "{$form}-period-under-review", 'type' => 'text', 'audience' => 'appraisee'],
            ['label' => 'Major responsibilities of the office', 'slug' => "{$form}-major-responsibilities-of-the-office", 'type' => 'textarea', 'audience' => 'appraisee'],
        ], $permissions);

        $sectionB = $this->createSection('SECTION B: Leadership and Commitment');
        $this->applyAccessRoles($sectionB, ['Nec Member']);
        $subB = $this->createSubSection($sectionB, 'Appraisal Items');
        $this->applyAccessRoles($subB, ['Nec Member']);
        $this->createQuestionSet($subB, [
            ['label' => 'Commitment to the vision and mission of the fellowship', 'slug' => "{$form}-commitment-to-vision-and-mission", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Performs assigned duties faithfully and consistently', 'slug' => "{$form}-performs-assigned-duties-faithfully", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Shows spiritual maturity and godly character', 'slug' => "{$form}-shows-spiritual-maturity", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Provides responsible and inspiring leadership', 'slug' => "{$form}-provides-responsible-leadership", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Punctuality, availability for meetings and assignments', 'slug' => "{$form}-punctuality-and-availability", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
        ], $permissions);

        $sectionC = $this->createSection('SECTION C: Administrative Performance');
        $this->applyAccessRoles($sectionC, ['Nec Member']);
        $subC = $this->createSubSection($sectionC, 'Appraisal Items');
        $this->applyAccessRoles($subC, ['Nec Member']);
        $this->createQuestionSet($subC, [
            ['label' => 'Plans and executes responsibilities effectively', 'slug' => "{$form}-plans-and-executes-responsibilities-effectively", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Keeps records and documentation related to the office', 'slug' => "{$form}-keeps-records-and-documentation", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Timely reports to the appropriate leadership body', 'slug' => "{$form}-timely-reports-to-leadership-body", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Communication with other officers and stakeholders', 'slug' => "{$form}-communication-with-officers-and-stakeholders", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Initiative and problem-solving ability', 'slug' => "{$form}-initiative-and-problem-solving-ability", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
        ], $permissions);

        $sectionD = $this->createSection('SECTION D: Teamwork and Relationship Management');
        $this->applyAccessRoles($sectionD, ['Nec Member']);
        $subD = $this->createSubSection($sectionD, 'Appraisal Items');
        $this->applyAccessRoles($subD, ['Nec Member']);
        $this->createQuestionSet($subD, [
            ['label' => 'Works cooperatively with other national officers', 'slug' => "{$form}-works-cooperatively-with-other-national-officers", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Respect, humility, and discipline in leadership relationships', 'slug' => "{$form}-respect-humility-and-discipline-in-leadership", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Supports collective decisions and promotes unity', 'slug' => "{$form}-supports-collective-decisions-and-promotes-unity", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Handles disagreements maturely and constructively', 'slug' => "{$form}-handles-disagreements-maturely", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Participation and collaboration among members', 'slug' => "{$form}-participation-and-collaboration-among-members", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
        ], $permissions);

        $sectionE = $this->createSection('SECTION E: Accountability and Stewardship');
        $this->applyAccessRoles($sectionE, ['Nec Member']);
        $subE = $this->createSubSection($sectionE, 'Appraisal Items');
        $this->applyAccessRoles($subE, ['Nec Member']);
        $this->createQuestionSet($subE, [
            ['label' => 'Transparency in handling assigned responsibilities', 'slug' => "{$form}-transparency-in-handling-assigned-responsibilities", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Accountable in the use of fellowship resources', 'slug' => "{$form}-accountable-in-use-of-fellowship-resources", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Provision of financial or operational reports where applicable', 'slug' => "{$form}-provision-of-financial-or-operational-reports", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Avoids misuse of office, influence, or resources', 'slug' => "{$form}-avoids-misuse-of-office-influence-or-resources", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Trust, responsibility, and integrity in leadership', 'slug' => "{$form}-trust-responsibility-and-integrity", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
        ], $permissions);

        $sectionF = $this->createSection('SECTION F: Programme and Vision Implementation');
        $this->applyAccessRoles($sectionF, ['Nec Member']);
        $subF = $this->createSubSection($sectionF, 'Appraisal Items');
        $this->applyAccessRoles($subF, ['Nec Member']);
        $this->createQuestionSet($subF, [
            ['label' => 'Contributes to national programmes and initiatives', 'slug' => "{$form}-contributes-to-national-programmes", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Supports the growth and visibility of GOFAMINT Students Fellowship', 'slug' => "{$form}-supports-growth-and-visibility", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Ideas or strategies for fellowship development', 'slug' => "{$form}-ideas-or-strategies-for-development", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Address challenges affecting students and campus fellowships', 'slug' => "{$form}-address-challenges-affecting-students-and-campuses", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Measurable impact in the assigned office', 'slug' => "{$form}-measurable-impact-in-assigned-office", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
        ], $permissions);

        $sectionG = $this->createSection('SECTION G: Attendance and Participation at National Programmes and Meetings');
        $this->applyAccessRoles($sectionG, ['Nec Member']);
        $subG = $this->createSubSection($sectionG, 'National Programme Attendance');
        $this->applyAccessRoles($subG, ['Nec Member']);
        $this->createQuestionSet($subG, [
            ['label' => 'NEC Orientation Programme (May 2025)', 'slug' => "{$form}-nec-orientation-programme", 'type' => 'select', 'options' => $this->attendanceOptions(), 'audience' => 'appraiser'],
            ['label' => 'NEC Leadership Retreat (December 2025)', 'slug' => "{$form}-nec-leadership-retreat", 'type' => 'select', 'options' => $this->attendanceOptions(), 'audience' => 'appraiser'],
            ['label' => 'General Assembly 2026', 'slug' => "{$form}-general-assembly-2026", 'type' => 'select', 'options' => $this->attendanceOptions(), 'audience' => 'appraiser'],
            ['label' => 'General Assembly 2026 - January, South West Region', 'slug' => "{$form}-general-assembly-2026-january-south-west", 'type' => 'select', 'options' => $this->attendanceOptions(), 'audience' => 'appraiser'],
            ['label' => 'General Assembly 2026 - February, Northern Region', 'slug' => "{$form}-general-assembly-2026-february-northern", 'type' => 'select', 'options' => $this->attendanceOptions(), 'audience' => 'appraiser'],
            ['label' => 'General Assembly 2026 - March, South-South and Eastern Region', 'slug' => "{$form}-general-assembly-2026-march-south-south-eastern", 'type' => 'select', 'options' => $this->attendanceOptions(), 'audience' => 'appraiser'],
            ['label' => 'National Prayer Conference (July 17th - 19th 2026)', 'slug' => "{$form}-national-prayer-conference-2026", 'type' => 'select', 'options' => $this->attendanceOptions(), 'audience' => 'appraiser'],
        ], $permissions);

        $subG2 = $this->createSubSection($sectionG, 'Digital Report Compliance');
        $this->applyAccessDesignations($subG2, ['National President']);
        $this->createQuestionSet($subG2, [
            ['label' => 'Level of compliance with the Monthly Digital Report Platform', 'slug' => "{$form}-monthly-digital-report-compliance", 'type' => 'select', 'options' => $this->complianceOptions(), 'audience' => 'appraiser'],
        ], $permissions);

        $sectionH = $this->createSection('SECTION H: Office-Specific Questions. Kindly respond to the questions below in not more than 2 lines per question');
        $this->applyAccessRoles($sectionH, ['Nec Member']);
        $subH = $this->createSubSection($sectionH, 'Self-Reflection');
        $this->applyAccessRoles($subH, ['Nec Member']);
        $this->createQuestionSet($subH, [
            ['label' => 'What are your top 3 strategic goals for the remainder of your tenure?', 'slug' => "{$form}-office-top-3-strategic-goals", 'type' => 'textarea', 'audience' => 'appraisee'],
            ['label' => 'What key challenges impacted your performance during the period under review?', 'slug' => "{$form}-office-key-challenges", 'type' => 'textarea', 'audience' => 'appraisee'],
            ['label' => 'What specific resources or administrative support do you require?', 'slug' => "{$form}-office-resources-needed", 'type' => 'textarea', 'audience' => 'appraisee'],
            ['label' => 'What specific initiatives do you intend to implement before the end of your administration?', 'slug' => "{$form}-office-initiatives", 'type' => 'textarea', 'audience' => 'appraisee'],
            ['label' => 'What were the major responsibilities assigned to this officer during the period under review?', 'slug' => "{$form}-office-major-responsibilities", 'type' => 'textarea', 'audience' => 'appraisee'],
            ['label' => 'Which of these responsibilities were effectively carried out?', 'slug' => "{$form}-office-responsibilities-effectively-carried-out", 'type' => 'textarea', 'audience' => 'appraisee'],
            ['label' => 'Which responsibilities were not fully carried out, and why?', 'slug' => "{$form}-office-responsibilities-not-carried-out", 'type' => 'textarea', 'audience' => 'appraisee'],
            ['label' => 'What specific achievement can be credited to this officer?', 'slug' => "{$form}-office-specific-achievement", 'type' => 'textarea', 'audience' => 'appraisee'],
            ['label' => 'What area of the officer performance requires improvement?', 'slug' => "{$form}-office-area-requires-improvement", 'type' => 'textarea', 'audience' => 'appraisee'],
            ['label' => 'What training, support, or resources does your office need?', 'slug' => "{$form}-office-training-support-resources", 'type' => 'textarea', 'audience' => 'appraisee'],
            ['label' => 'What recommendations should be made for further development?', 'slug' => "{$form}-office-recommendations", 'type' => 'textarea', 'audience' => 'appraisee'],
        ], $permissions);

        $sectionI = $this->createSection('SECTION I: General Observations and Recommendations');
        $this->applyAccessDesignations($sectionI, ['National President']);
        $subI1 = $this->createSubSection($sectionI, 'General Observations');
        $this->applyAccessDesignations($subI1, ['National President']);
        $this->createQuestionSet($subI1, [
            ['label' => 'What are the major strengths of this National Officer?', 'slug' => "{$form}-major-strengths-of-national-officer", 'type' => 'textarea', 'audience' => 'national_president'],
            ['label' => 'What weaknesses or concerns were observed?', 'slug' => "{$form}-weaknesses-or-concerns-observed", 'type' => 'textarea', 'audience' => 'national_president'],
            ['label' => 'What support, training, or mentoring does this officer need?', 'slug' => "{$form}-support-training-or-mentoring-needed", 'type' => 'textarea', 'audience' => 'national_president'],
            ['label' => 'Recommendation on continuation in office', 'slug' => "{$form}-recommendation-on-continuation", 'type' => 'select', 'options' => $this->nationalRecommendationOptions(), 'audience' => 'national_president'],
            ['label' => 'Reason', 'slug' => "{$form}-continuation-reason", 'type' => 'textarea', 'audience' => 'national_president'],
        ], $permissions);

        $subI2 = $this->createSubSection($sectionI, 'Evaluator Assessment');
        $this->applyAccessDesignations($subI2, ['National President']);
        $this->createQuestionSet($subI2, [
            ['label' => 'Key strengths observed in this officer', 'slug' => "{$form}-evaluator-key-strengths", 'type' => 'textarea', 'audience' => 'national_president'],
            ['label' => 'Specific performance gaps or areas requiring improvement', 'slug' => "{$form}-evaluator-performance-gaps", 'type' => 'textarea', 'audience' => 'national_president'],
            ['label' => 'Key notable achievements recorded under their leadership', 'slug' => "{$form}-evaluator-notable-achievements", 'type' => 'textarea', 'audience' => 'national_president'],
            ['label' => 'Name of Appraiser', 'slug' => "{$form}-appraiser-name", 'type' => 'text', 'audience' => 'national_president'],
            ['label' => 'Position', 'slug' => "{$form}-appraiser-position", 'type' => 'text', 'audience' => 'national_president'],
            ['label' => 'Signature', 'slug' => "{$form}-appraiser-signature", 'type' => 'file', 'audience' => 'national_president'],
            ['label' => 'Date', 'slug' => "{$form}-appraiser-date", 'type' => 'date', 'audience' => 'national_president'],
        ], $permissions);
    }

    protected function seedNationalPresidentAppraisal(array $permissions): void
    {
        $form = 'national-president';
        $this->sectionPrefix = 'National President';
        $this->permissionMap = [
            'fill' => $permissions['national_president_fill'],
            'evaluate' => $permissions['national_president_evaluate'],
        ];

        $sectionA = $this->createSection('SECTION A: Personal and Ministry Information');
        $this->applyAccessRoles($sectionA, ['Nec Member']);
        $subA = $this->createSubSection($sectionA, 'Personal and Ministry Information');
        $this->applyAccessRoles($subA, ['Nec Member']);
        $this->createQuestionSet($subA, [
            ['label' => 'Name of National President', 'slug' => "{$form}-name-of-national-president", 'type' => 'text', 'audience' => 'appraisee'],
            ['label' => 'Office/Position Held', 'slug' => "{$form}-office-position-held", 'type' => 'text', 'audience' => 'appraisee'],
            ['label' => 'Period under review', 'slug' => "{$form}-period-under-review", 'type' => 'text', 'audience' => 'appraisee'],
            ['label' => 'Major responsibilities of the office', 'slug' => "{$form}-major-responsibilities-of-the-office", 'type' => 'textarea', 'audience' => 'appraisee'],
        ], $permissions);

        $sectionB = $this->createSection('SECTION B: Leadership and Commitment');
        $this->applyAccessRoles($sectionB, ['Nec Member']);
        $subB = $this->createSubSection($sectionB, 'Appraisal Items');
        $this->applyAccessRoles($subB, ['Nec Member']);
        $this->createQuestionSet($subB, [
            ['label' => 'Commitment to the vision and mission of the fellowship', 'slug' => "{$form}-commitment-to-vision-and-mission", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Performs assigned duties faithfully and consistently', 'slug' => "{$form}-performs-assigned-duties-faithfully", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Shows spiritual maturity and godly character', 'slug' => "{$form}-shows-spiritual-maturity", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Provides responsible and inspiring leadership', 'slug' => "{$form}-provides-responsible-leadership", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Punctuality, availability for meetings and assignments', 'slug' => "{$form}-punctuality-and-availability", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
        ], $permissions);

        $sectionC = $this->createSection('SECTION C: Administrative Performance');
        $this->applyAccessRoles($sectionC, ['Nec Member']);
        $subC = $this->createSubSection($sectionC, 'Appraisal Items');
        $this->applyAccessRoles($subC, ['Nec Member']);
        $this->createQuestionSet($subC, [
            ['label' => 'Plans and executes responsibilities effectively', 'slug' => "{$form}-plans-and-executes-responsibilities-effectively", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Keeps records and documentation related to the office', 'slug' => "{$form}-keeps-records-and-documentation", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Timely reports to the appropriate leadership body', 'slug' => "{$form}-timely-reports-to-leadership-body", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Communication with other officers and stakeholders', 'slug' => "{$form}-communication-with-officers-and-stakeholders", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Initiative and problem-solving ability', 'slug' => "{$form}-initiative-and-problem-solving-ability", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
        ], $permissions);

        $sectionD = $this->createSection('SECTION D: Teamwork and Relationship Management');
        $this->applyAccessRoles($sectionD, ['Nec Member']);
        $subD = $this->createSubSection($sectionD, 'Appraisal Items');
        $this->applyAccessRoles($subD, ['Nec Member']);
        $this->createQuestionSet($subD, [
            ['label' => 'Works cooperatively with other national officers', 'slug' => "{$form}-works-cooperatively-with-other-national-officers", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Respect, humility, and discipline in leadership relationships', 'slug' => "{$form}-respect-humility-and-discipline-in-leadership", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Supports collective decisions and promotes unity', 'slug' => "{$form}-supports-collective-decisions-and-promotes-unity", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Handles disagreements maturely and constructively', 'slug' => "{$form}-handles-disagreements-maturely", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Participation and collaboration among members', 'slug' => "{$form}-participation-and-collaboration-among-members", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
        ], $permissions);

        $sectionE = $this->createSection('SECTION E: Accountability and Stewardship');
        $this->applyAccessRoles($sectionE, ['Nec Member']);
        $subE = $this->createSubSection($sectionE, 'Appraisal Items');
        $this->applyAccessRoles($subE, ['Nec Member']);
        $this->createQuestionSet($subE, [
            ['label' => 'Transparency in handling assigned responsibilities', 'slug' => "{$form}-transparency-in-handling-assigned-responsibilities", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Accountable in the use of fellowship resources', 'slug' => "{$form}-accountable-in-use-of-fellowship-resources", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Provision of financial or operational reports where applicable', 'slug' => "{$form}-provision-of-financial-or-operational-reports", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Avoids misuse of office, influence, or resources', 'slug' => "{$form}-avoids-misuse-of-office-influence-or-resources", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Trust, responsibility, and integrity in leadership', 'slug' => "{$form}-trust-responsibility-and-integrity", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
        ], $permissions);

        $sectionF = $this->createSection('SECTION F: Programme and Vision Implementation');
        $this->applyAccessRoles($sectionF, ['Nec Member']);
        $subF = $this->createSubSection($sectionF, 'Appraisal Items');
        $this->applyAccessRoles($subF, ['Nec Member']);
        $this->createQuestionSet($subF, [
            ['label' => 'Contributes to national programmes and initiatives', 'slug' => "{$form}-contributes-to-national-programmes", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Supports the growth and visibility of GOFAMINT Students Fellowship', 'slug' => "{$form}-supports-growth-and-visibility", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Ideas or strategies for fellowship development', 'slug' => "{$form}-ideas-or-strategies-for-development", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Address challenges affecting students and campus fellowships', 'slug' => "{$form}-address-challenges-affecting-students-and-campuses", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
            ['label' => 'Measurable impact in the assigned office', 'slug' => "{$form}-measurable-impact-in-assigned-office", 'type' => 'select', 'options' => $this->ratingOptions(), 'audience' => 'appraiser'],
        ], $permissions);

        $sectionG = $this->createSection('SECTION G: Attendance and Participation at National Programmes and Meetings');
        $this->applyAccessRoles($sectionG, ['Nec Member']);
        $subG = $this->createSubSection($sectionG, 'National Programme Attendance');
        $this->applyAccessRoles($subG, ['Nec Member']);
        $this->createQuestionSet($subG, [
            ['label' => 'NEC Orientation Programme (May 2025)', 'slug' => "{$form}-nec-orientation-programme", 'type' => 'select', 'options' => $this->attendanceOptions(), 'audience' => 'appraiser'],
            ['label' => 'NEC Leadership Retreat (December 2025)', 'slug' => "{$form}-nec-leadership-retreat", 'type' => 'select', 'options' => $this->attendanceOptions(), 'audience' => 'appraiser'],
            ['label' => 'General Assembly 2026', 'slug' => "{$form}-general-assembly-2026", 'type' => 'select', 'options' => $this->attendanceOptions(), 'audience' => 'appraiser'],
            ['label' => 'General Assembly 2026 - January, South West Region', 'slug' => "{$form}-general-assembly-2026-january-south-west", 'type' => 'select', 'options' => $this->attendanceOptions(), 'audience' => 'appraiser'],
            ['label' => 'General Assembly 2026 - February, Northern Region', 'slug' => "{$form}-general-assembly-2026-february-northern", 'type' => 'select', 'options' => $this->attendanceOptions(), 'audience' => 'appraiser'],
            ['label' => 'General Assembly 2026 - March, South-South and Eastern Region', 'slug' => "{$form}-general-assembly-2026-march-south-south-eastern", 'type' => 'select', 'options' => $this->attendanceOptions(), 'audience' => 'appraiser'],
            ['label' => 'National Prayer Conference (July 17th - 19th 2026)', 'slug' => "{$form}-national-prayer-conference-2026", 'type' => 'select', 'options' => $this->attendanceOptions(), 'audience' => 'appraiser'],
            ['label' => 'Total NEC Meetings Attended', 'slug' => "{$form}-total-nec-meetings-attended", 'type' => 'text', 'audience' => 'appraiser'],
        ], $permissions);

        $sectionI = $this->createSection('SECTION I: Official Use only (To be completed by the National President)');
        $this->applyAccessDesignations($sectionI, ['National President']);
        $subI = $this->createSubSection($sectionI, 'Evaluator Assessment');
        $this->applyAccessDesignations($subI, ['National President']);
        $this->createQuestionSet($subI, [
            ['label' => 'Key strengths observed in this officer', 'slug' => "{$form}-evaluator-key-strengths", 'type' => 'textarea', 'audience' => 'appraiser'],
            ['label' => 'Specific performance gaps or areas requiring improvement', 'slug' => "{$form}-evaluator-performance-gaps", 'type' => 'textarea', 'audience' => 'appraiser'],
            ['label' => 'Key notable achievements recorded under their leadership', 'slug' => "{$form}-evaluator-notable-achievements", 'type' => 'textarea', 'audience' => 'appraiser'],
            ['label' => 'Name of Appraiser', 'slug' => "{$form}-appraiser-name", 'type' => 'text', 'audience' => 'appraiser'],
            ['label' => 'Position', 'slug' => "{$form}-appraiser-position", 'type' => 'text', 'audience' => 'appraiser'],
            ['label' => 'Signature', 'slug' => "{$form}-appraiser-signature", 'type' => 'file', 'audience' => 'appraiser'],
            ['label' => 'Date', 'slug' => "{$form}-appraiser-date", 'type' => 'date', 'audience' => 'appraiser'],
        ], $permissions);
    }

    protected function createSection(string $name): StakeholderQuestionSection
    {
        $slug = Str::slug(trim($this->sectionPrefix . ' ' . $name));

        return StakeholderQuestionSection::updateOrCreate(
            ['slug' => $slug, 'module_type' => 'appraisal'],
            [
                'name' => $name,
                'status' => 1,
                'access_roles' => null,
            ]
        );
    }

    protected function createSubSection(StakeholderQuestionSection $section, string $name): StakeholderQuestionSubSection
    {
        $slug = Str::slug(trim($section->slug . ' ' . $name));

        return StakeholderQuestionSubSection::updateOrCreate(
            [
                'slug' => $slug,
                'section_id' => $section->id,
                'module_type' => 'appraisal',
            ],
            [
                'name' => $name,
                'status' => 1,
                'access_roles' => null,
            ]
        );
    }

    protected function createQuestionSet(
        StakeholderQuestionSubSection $subSection,
        array $questions,
        array $permissions
    ): void {
        foreach ($questions as $index => $question) {
            $audience = $this->resolveQuestionAudience($subSection, $question);
            $permissionIds = $this->resolvePermissionIds($permissions, $audience);

            $record = StakeholderReportQuestion::updateOrCreate(
                ['slug' => $question['slug']],
                [
                    'label' => $question['label'],
                    'type' => $question['type'] ?? 'text',
                    'audience' => $audience,
                    'is_required' => $question['is_required'] ?? true,
                    'options' => $question['options'] ?? null,
                    'order' => $question['order'] ?? ($index + 1),
                    'width_class' => $question['width_class'] ?? 'col-md-6',
                    'section_id' => $subSection->section_id,
                    'sub_section_id' => $subSection->id,
                    'status' => 1,
                    'is_quantifiable' => $question['is_quantifiable'] ?? false,
                    'module_type' => 'appraisal',
                ]
            );

            $record->permissions()->sync($permissionIds);
        }
    }

    protected function resolveQuestionAudience(StakeholderQuestionSubSection $subSection, array $question): string
    {
        $sectionName = strtolower((string) ($subSection->section?->name ?? ''));
        $subSectionName = strtolower((string) ($subSection->name ?? ''));

        $isEvaluatorSection = str_contains($sectionName, 'official use only')
            || str_contains($sectionName, 'general observations and recommendations')
            || str_contains($subSectionName, 'evaluator assessment');

        return $this->normalizeAudience($question['audience'] ?? null, $isEvaluatorSection);
    }

    protected function resolvePermissionIds(array $permissions, string $audience): array
    {
        if ($audience === 'both') {
            return array_values(array_filter([
                $this->permissionMap['fill'] ?? null,
                $this->permissionMap['evaluate'] ?? null,
            ]));
        }

        return array_values(array_filter([
            $this->permissionMap[$audience] ?? $this->permissionMap['evaluate'] ?? null,
        ]));
    }

    protected function normalizeAudience(?string $audience, bool $isEvaluatorSection): string
    {
        $audience = strtolower(trim((string) $audience));

        if ($isEvaluatorSection) {
            if ($audience === 'evaluate' || $audience === 'appraiser' || $audience === 'national_president') {
                return 'evaluate';
            }

            return 'evaluate';
        }

        return 'fill';
    }

    protected function ratingOptions(): array
    {
        return [
            ['label' => 'Excellent', 'value' => 5],
            ['label' => 'Very Good', 'value' => 4],
            ['label' => 'Good', 'value' => 3],
            ['label' => 'Fair', 'value' => 2],
            ['label' => 'Poor', 'value' => 1],
        ];
    }

    protected function attendanceOptions(): array
    {
        return [
            ['label' => 'Attended', 'value' => 1],
            ['label' => 'Absent', 'value' => 0],
        ];
    }

    protected function complianceOptions(): array
    {
        return [
            ['label' => 'Full Compliance', 'value' => 5],
            ['label' => 'Substantial Compliance', 'value' => 4],
            ['label' => 'Partial Compliance', 'value' => 3],
            ['label' => 'Minimal Compliance', 'value' => 2],
            ['label' => 'Non-Compliance', 'value' => 1],
        ];
    }

    protected function fieldRecommendationOptions(): array
    {
        return [
            ['label' => 'Yes', 'value' => 'yes'],
            ['label' => 'No', 'value' => 'no'],
            ['label' => 'Yes, but with further mentoring/training', 'value' => 'yes_with_mentoring'],
        ];
    }

    protected function nationalRecommendationOptions(): array
    {
        return [
            ['label' => 'Continue in current office', 'value' => 'continue_current_office'],
            ['label' => 'Continue with mentoring/support', 'value' => 'continue_with_mentoring'],
            ['label' => 'Be reassigned to another office', 'value' => 'reassign'],
            ['label' => 'Step down from office', 'value' => 'step_down'],
            ['label' => 'Other recommendation', 'value' => 'other'],
        ];
    }
}
