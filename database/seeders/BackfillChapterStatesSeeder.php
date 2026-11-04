<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BackfillChapterStatesSeeder extends Seeder
{
    public function run(): void
    {
        $chapters = DB::table('chapters')
            ->leftJoin('zones', 'zones.id', '=', 'chapters.zone_id')
            ->where('chapters.id', '<>', 86) // The legacy "Other" record is not a campus.
            ->whereNull('chapters.state')
            ->select('chapters.id', 'chapters.name', 'chapters.address', 'zones.name as zone_name')
            ->get();

        foreach ($chapters as $chapter) {
            $state = $this->stateFor($chapter->name, $chapter->zone_name);

            if ($state !== null) {
                DB::table('chapters')->where('id', $chapter->id)->update(['state' => $state]);
            }
        }
    }

    private function stateFor(string $name, ?string $zone): ?string
    {
        $name = mb_strtolower($name);

        // Institution names take precedence: some chapters belong to a zone in another state.
        $institutions = [
            'ahmadu bello' => 'Kaduna',
            'adeyemi federal university' => 'Ondo',
            'emmanuel alayande' => 'Oyo',
            'obafemi awolowo' => 'Osun',
            'federal university of technology minna' => 'Niger',
            'federal polytechnic bida' => 'Niger',
            'federal polytechnic ado' => 'Ekiti',
            'federal polytechnic, nekede' => 'Imo',
            'federal university, otuoke' => 'Bayelsa',
            'university of portharcourt' => 'Rivers',
            'university of abuja' => 'Federal Capital Territory',
            'olusegun agagu' => 'Ondo',
            'michael otedola' => 'Lagos',
            'oke-ogun polytechnic' => 'Oyo',
            'olabisi onabanjo' => 'Ogun',
            'tai solarin' => 'Ogun',
            'ladoke akintola' => 'Oyo',
            'university of medical science' => 'Ondo',
            'university of benin' => 'Edo',
            'university of ibadan' => 'Oyo',
            'university of lagos' => 'Lagos',
            'university of ilorin' => 'Kwara',
            'the polytechnic ibadan' => 'Oyo',
            'university of ilesha' => 'Osun',
            'yaba college' => 'Lagos',
            'moshood abiola polytechnic' => 'Ogun',
        ];

        foreach ($institutions as $fragment => $state) {
            if (str_contains($name, $fragment)) {
                return $state;
            }
        }

        foreach (array_keys(config('gsf_states')) as $state) {
            if (preg_match('/\b'.preg_quote(mb_strtolower($state), '/').'\b/u', $name)) {
                return $state;
            }
        }

        $towns = [
            'owo' => 'Ondo', 'akungba' => 'Ondo', 'akure' => 'Ondo', 'ile-oluji' => 'Ondo',
            'eruwa' => 'Oyo', 'ibadan' => 'Oyo', 'iseyin' => 'Oyo', 'lanlate' => 'Oyo', 'saki' => 'Oyo',
            'ekpoma' => 'Edo', 'usen' => 'Edo', 'auchi' => 'Edo',
            'agbor' => 'Delta', 'abraka' => 'Delta', 'warri' => 'Delta',
            'ijero' => 'Ekiti', 'ado ekiti' => 'Ekiti', 'ado-ekiti' => 'Ekiti', 'ikere' => 'Ekiti',
            'offa' => 'Kwara', 'oro' => 'Kwara', 'malete' => 'Kwara',
            'ilaro' => 'Ogun', 'osiele' => 'Ogun', 'saapade' => 'Ogun', 'abeokuta' => 'Ogun',
            'ijebu' => 'Ogun', 'igbesa' => 'Ogun', 'ago iwoye' => 'Ogun', 'ayetoro' => 'Ogun', 'ibogun' => 'Ogun',
            'ikole' => 'Ekiti', 'oye' => 'Ekiti',
            'iree' => 'Osun', 'ikire' => 'Osun', 'okuku' => 'Osun', 'ipetu' => 'Osun',
            'ilesa' => 'Osun', 'ilesha' => 'Osun', 'esa-oke' => 'Osun', 'ede' => 'Osun', 'oshogbo' => 'Osun',
            'ijanikin' => 'Lagos', 'epe' => 'Lagos', 'yaba' => 'Lagos',
            'idah' => 'Kogi', 'anyigba' => 'Kogi', 'lokoja' => 'Kogi', 'okene' => 'Kogi',
        ];

        foreach ($towns as $town => $state) {
            if (preg_match('/\b'.preg_quote($town, '/').'\b/u', $name)) {
                return $state;
            }
        }

        $zone = mb_strtolower($zone ?? '');
        $zoneStates = [
            'abuja' => 'Federal Capital Territory', 'lafia' => 'Nasarawa', 'nasarawa' => 'Nasarawa',
            'jos' => 'Plateau', 'minna' => 'Niger', 'bida' => 'Niger',
            'kogi' => 'Kogi', 'delta' => 'Delta', 'ekiti' => 'Ekiti',
            'akure' => 'Ondo', 'ondo' => 'Ondo', 'owo' => 'Ondo',
            'abeokuta' => 'Ogun', 'ijebu' => 'Ogun',
            'oyo' => 'Oyo', 'ibadan' => 'Oyo',
            'oshogbo' => 'Osun', 'ife' => 'Osun', 'ilesha' => 'Osun',
            'benin' => 'Edo', 'lagos' => 'Lagos', 'ilorin' => 'Kwara',
            'port harcourt' => 'Rivers', 'bayelsa' => 'Bayelsa',
        ];

        foreach ($zoneStates as $fragment => $state) {
            if (str_contains($zone, $fragment)) {
                return $state;
            }
        }

        return null;
    }
}
