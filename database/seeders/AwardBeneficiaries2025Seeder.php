<?php

namespace Database\Seeders;

use App\Models\Award;
use App\Models\AwardEntry;
use App\Models\Chapter;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class AwardBeneficiaries2025Seeder extends Seeder
{
    public function run(): void
    {
        $dataPath = __DIR__.'/data/award_beneficiaries_2025.json';
        if (! is_file($dataPath)) {
            throw new RuntimeException('Missing 2025 award beneficiary data file.');
        }

        $records = json_decode(file_get_contents($dataPath), true, 512, JSON_THROW_ON_ERROR);
        $normalize = static fn (string $value): string => trim(preg_replace(
            '/\s+/',
            ' ',
            preg_replace('/[^a-z0-9]+/i', ' ', Str::lower($value))
        ));

        $campusAliases = [
            'Adekunle Ajasin University Akungba-Akoko' => 'Adekunle Ajasin University Akungba-Akoko (AAUA)',
            'Adekunle AjasinUniversity AkungbaAkoko' => 'Adekunle Ajasin University Akungba-Akoko (AAUA)',
            'Ekiti State University' => 'Ekiti State University (EKSU)',
            'Federal University of Agriculture, Abeokuta' => 'Federal University of Agriculture, Abeokuta (FUNAAB)',
            'Federal University of Technology Minna' => 'Federal University of Technology Minna (FUTMINNA)',
            'Federal University of Technology, Akure' => 'Federal University of Technology, Akure (FUTA)',
            'Federal University, Oye-Ekiti' => 'Federal University, Oye-Ekiti (FUOYE) Oye Campus',
            'Obafemi Awolowo University' => 'Obafemi Awolowo University (OAU)',
            'Obafemi Awolowo University, Ile-Ife' => 'Obafemi Awolowo University (OAU)',
            'Olabisi Onabanjo University' => 'Olabisi Onabanjo University (OOU) Ago Iwoye',
            'University of Benin' => 'University of Benin (UNIBEN)',
            'University of Ibadan' => 'University of Ibadan (UI)',
            'University of Lagos' => 'University of Lagos (UNILAG)',
            'University of Lagos (UNILAG)' => 'University of Lagos (UNILAG)',
            'Ladoke Akintola University of Technology' => 'Ladoke Akintola University of Technology (LAUTECH)',
            'University of Medical Science Ondo' => 'University of Medical Science Ondo (UNIMED)',
            'University of Ilesa' => 'University of Ilesha (UNILESA), Ilesa',
            'University of Ibadan (UI)' => 'University of Ibadan (UI)',
            'College of Medicine, University of Lagos' => 'College of Medicine, University of Lagos (MEDILAG)',
            'Olabisi Onabanjo University Teaching Hospital' => 'Olabisi Onabanjo University Teaching Hospital (OOUTH)',
            'Federal Polytechnic Ilaro' => 'Federal Polytechnic Ilaro',
            'Ondo State College of Health Technology, Akure' => 'Ondo State College of Health Technology, Akure',
            'The Polytechnic Ibadan' => 'The Polytechnic Ibadan Main',
            'College of Health, Offa, Kwara State' => 'College of Health, Offa, Kwara State',
            'Federal Polytechnic Ede' => 'Federal Polytechnic Ede (FEDPOLYEDE)',
            'Federal Polytechnic Ede (FEDPOLYEDE)' => 'Federal Polytechnic Ede (FEDPOLYEDE)',
            'Yaba College of Technology' => 'Yaba College of Technology (YABATECH)',
            'Lagos State Polyethnic' => 'Lagos State University of Science and Technology (LASUSTECH)',
            'Olabisi Onabanjo University, (OOU) Ibogun Campus' => 'Olabisi Onabanjo University, (OOU) Ibogun Campus',
        ];
        $campusAliases = collect($campusAliases)
            ->mapWithKeys(fn (string $campusName, string $sourceName) => [$normalize($sourceName) => $normalize($campusName)])
            ->all();

        $chaptersByName = Chapter::query()->get(['id', 'name', 'zone_id', 'field_id'])
            ->keyBy(fn (Chapter $chapter) => $normalize($chapter->name));
        $unmatchedSchools = [];
        $counts = ['go' => 0, 'etf' => 0];

        DB::transaction(function () use ($records, $normalize, $campusAliases, $chaptersByName, &$unmatchedSchools, &$counts): void {
            foreach ($records as $record) {
                $type = $record['type'];
                $categoryCode = $record['category'] === 'University' ? 'U' : 'P';
                $reference = sprintf('LEGACY-%s-2025-%s-%03d', strtoupper($type), $categoryCode, $record['serial']);
                $campusKey = $campusAliases[$normalize($record['institution'])] ?? $normalize($record['institution']);
                $campus = $chaptersByName->get($campusKey);
                $existing = Award::withTrashed()->where('reference', $reference)->first();
                $date = Carbon::create(2025, 12, 31, 12, 0, 0);

                if (! $campus) {
                    $unmatchedSchools[$record['institution']] = true;
                }

                $award = Award::withTrashed()->updateOrCreate(
                    ['reference' => $reference],
                    [
                        'type' => $type,
                        'chapter_id' => $campus?->id ?? $existing?->chapter_id,
                        'zone_id' => $campus?->zone_id ?? $existing?->zone_id,
                        'field_id' => $campus?->field_id ?? $existing?->field_id,
                        'national_status' => 1,
                        'national_approved_by' => null,
                        'national_approved_on' => $date,
                        'national_rejected_on' => null,
                        'national_rejected_by' => null,
                        'is_archive' => true,
                        'created_at' => $date,
                        'updated_at' => $date,
                        'deleted_at' => null,
                    ]
                );

                $grade = trim((string) ($record['grade'] ?? ''));
                $cgpa = preg_match('/^\s*([0-9]+(?:\.[0-9]+)?)/', $grade, $matches)
                    ? $grade
                    : null;
                [$accountNumber, $bankName] = self::parseAccountDetails((string) ($record['account_details'] ?? ''));
                $awardCategory = $type === 'go'
                    ? ($categoryCode === 'U' ? 'First Class' : 'Distinction')
                    : 'Education Trust Fund';

                AwardEntry::updateOrCreate(
                    ['award_id' => $award->id],
                    [
                        'first_name' => $record['name'],
                        'select_institution' => $record['institution'],
                        'course_of_study' => $record['course'] ?: null,
                        'gender' => $record['gender'] ?: null,
                        'phone_number' => $record['phone_number'] ?: null,
                        'account_number' => $accountNumber,
                        'bank_name' => $bankName,
                        'current_level' => $record['current_level'] ?? null,
                        'name_of_zonal_pastor' => $record['name_of_zonal_pastor'] ?? null,
                        'proposed_year_of_completion' => $record['graduation_year'] ?? null,
                        'cgpa' => $cgpa,
                        'class_of_degree' => $type === 'go' ? $awardCategory : null,
                        'graduating_grade' => $awardCategory,
                        'legacy_category' => $record['category'],
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]
                );

                $counts[$type]++;
            }
        });

        $this->command?->info("2025 award beneficiaries seeded: {$counts['go']} G.O. Award and {$counts['etf']} ETF records.");
        $this->command?->warn('The ETF university table contains 53 listed records (serial 25 is absent), while its summary reports 54; no missing record was invented.');

        if ($unmatchedSchools) {
            $this->command?->warn('Institutions without a matching campus record: '.implode('; ', array_keys($unmatchedSchools)));
        }
    }

    private static function parseAccountDetails(string $details): array
    {
        $banks = [
            'stanbic\s*ibtc', 'sterling\s*bank', 'access\s*bank', 'polaris\s*bank',
            'zenith\s*bank', 'union\s*bank', 'first\s*bank', 'wema\s*bank',
            'providus\s*bank', 'ecobank', 'kuda\s*bank', 'monie\s*point',
            'palmpay', 'opay', 'fcmb', 'gt\s*bank', 'gtb', 'uba', 'fbn',
        ];
        $pattern = '~('.implode('|', $banks).')~iu';
        if (! preg_match($pattern, $details, $matches)) {
            return [null, null];
        }

        $bankName = preg_replace('/\s+/', ' ', trim($matches[1]));
        $bankName = match (strtolower($bankName)) {
            'palmpay' => 'PalmPay',
            'opay' => 'OPay',
            'uba' => 'UBA',
            'fbn' => 'FBN',
            'gtb' => 'GTB',
            'fcmb' => 'FCMB',
            'ecobank' => 'Ecobank',
            default => ucwords(strtolower($bankName)),
        };
        $accountNumber = preg_replace('/\D+/', '', str_replace($matches[1], '', $details));

        return [$accountNumber ?: null, $bankName];
    }
}
