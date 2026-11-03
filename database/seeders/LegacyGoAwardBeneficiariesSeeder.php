<?php

namespace Database\Seeders;

use App\Models\Award;
use App\Models\AwardEntry;
use App\Models\Chapter;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LegacyGoAwardBeneficiariesSeeder extends Seeder
{
    public function run(): void
    {
        $normalize = static fn (string $value): string => trim(preg_replace('/\s+/', ' ', preg_replace('/[^a-z0-9]+/i', ' ', Str::lower($value))));

        $campusAliases = [
            'ADEYEMI UNIV OF EDU. ONDO' => 'Adeyemi Federal University of Education (Formerly, Adeyemi College of Education)',
            'OOU AGO IWOYE' => 'Olabisi Onabanjo University (OOU) Ago Iwoye',
            'OOU AGO IWOYRE' => 'Olabisi Onabanjo University (OOU) Ago Iwoye',
            'Lagos State University' => 'Lagos State University (LASU)',
            'LAGOS STATE UNIVERSITY' => 'Lagos State University (LASU)',
            'LASU' => 'Lagos State University (LASU)',
            'UNILAG' => 'University of Lagos (UNILAG)',
            'Tai Solarin Univ of Education' => 'Tai Solarin University of Education (TASUED)',
            'TASUED' => 'Tai Solarin University of Education (TASUED)',
            'FUNAAB' => 'Federal University of Agriculture, Abeokuta (FUNAAB)',
            'EKSU' => 'Ekiti State University (EKSU)',
            'FUTA' => 'Federal University of Technology, Akure (FUTA)',
            'FUTMINNA' => 'Federal University of Technology Minna (FUTMINNA)',
            'UNIBEN' => 'University of Benin (UNIBEN)',
            'LAUTECH' => 'Ladoke Akintola University of Technology (LAUTECH)',
            'OGITECH' => 'Ogun State Institute of Technology Igbesa (OGITECH)',
            'LASPOTECH' => 'Lagos State University of Science and Technology (LASUSTECH)',
            'AAU, AKUNGBA' => 'Adekunle Ajasin University Akungba-Akoko (AAUA)',
            'AAUA' => 'Adekunle Ajasin University Akungba-Akoko (AAUA)',
            'OAU' => 'Obafemi Awolowo University (OAU)',
            'UNILORIN' => 'University of Ilorin (UNILORIN)',
            'OSCOTECH ILESE' => 'Ogun State College of Health Technology, Ilese Ijebu',
            'OGSCOTECH, IJEBU ODE' => 'Ogun State College of Health Technology, Ilese Ijebu',
            'Federal Polytechnic Ado Ekiti' => 'Federal Polytechnic Ado-ekiti main campus (FEDPOADO)',
            'FEDPOLY ADO' => 'Federal Polytechnic Ado-ekiti main campus (FEDPOADO)',
            'FEDPOLY ADO, SATELLITE CAMPUS' => 'Federal Polytechnic Ado, Satellite Campus',
            'Fed Poly Ilaro' => 'Federal Polytechnic Ilaro',
            'FEDPOLY ILARO' => 'Federal Polytechnic Ilaro',
            'FECA' => 'Federal College of Agriculture, Akure (FECA)',
            'OKE OGUN POLY SAKI' => 'Oke-Ogun Polytechnic, Saki (TOPS)',
            'AUCHI POLY' => 'Federal Polythenic, Auchi, Edo State',
            'FEDPOLY OFFA' => 'Federal Polytechnic Offa (FEDPOFFA)',
            'COHEST IJERO' => 'College of Health Science and Technology, Ijero, Ekiti State',
            'UI (MOCPED)' => 'Michael Otedola College of Education, Epe (MOCPED)',
            'KWARA STATE COHEALTH' => 'College of Health, Offa, Kwara State',
            'COLL OF HEALTH TECH AKURE' => 'Ondo State College of Health Technology, Akure',
            'SHTA, AKURE' => 'Ondo State College of Health Technology, Akure',
            'SHTA' => 'Ondo State College of Health Technology, Akure',
            'UI' => 'University of Ibadan (UI)',
            'KSU, ANYIGBA' => 'Kogi State University, Anyigba',
            'FCOAH Ibadan' => 'Federal Colleges of Animal Health, Production Technology and Agriculture, Plantation, Moor Plantation, Ibadan',
            'FEDPOLY BAUCHI' => 'Federal Polytechnic Bauchi',
            'POLY IBADAN' => 'The Polytechnic Ibadan Main',
            'OSCOTECH ESAOKE' => 'Osun State College of Technology, Esa-Oke (OSCOTECH)',
        ];

        $campusesByName = Chapter::query()->get(['id', 'name', 'zone_id', 'field_id'])
            ->keyBy(fn (Chapter $chapter) => $normalize($chapter->name));
        $campusAliases = collect($campusAliases)
            ->mapWithKeys(fn (string $campusName, string $schoolName) => [$normalize($schoolName) => $normalize($campusName)])
            ->all();
        $unmatchedSchools = [];
        $matchedApplications = 0;

        $rows = <<<'DATA'
2023|1|Olaluwoye Opeyemi Ebun|ADEYEMI UNIV OF EDU. ONDO|Social Studies|4.53/ FIRST CLASS|University
2023|2|Imhangbe Blessing|ADEYEMI UNIV OF EDU. ONDO|Economics|4.67/FIRST CLASS|University
2023|3|Adeyinka Temidayo AYODELE|UI/OSCOED ILESA|AGRIC EDUCATION|3.6/4.00 FIRST CLASS|University
2023|4|Kosoko Maupego Luke|OOU AGO IWOYE|PHARMACOLOGY|4.51/FIRST CLASS|University
2023|5|Mejayi Esther Sefrinmi|Lagos State University|Health Education|4.52/ FIRST CLASS|University
2023|6|Akinfenwa Felicia Damilare|Tai Solarin Univ of Education|Home Econs/Hotel Mgt|4.67/FIRST CLASS|University
2023|7|Obaro Olamide Ezekiel|Tai Solarin Univ of Education|Mathematics|4.5/FIRST CLASS|University
2023|8|AJAYI PRAISE MAYOWA|Lagos State University|MUSIC|4.64/FIRST CLASS|University
2023|9|Adekanmbi Peace Adedayo|Lagos State University|MARKETING|4.80/FIRST CLASS|University
2023|10|Akinboboye Praise Opeoluwa|FUNAAB|NUTR. & Dietetics|4.76/FIRST CLASS|University
2023|11|AFUYE MIRACLE TIMILEHIN|LAGOS STATE UNIVERSITY|BUSINESS ADMIN|4.55/FIRST CLASS|University
2023|12|ADELAJA OLASANTAN EBENEZER|FUTA|PHYSICS ELECT|4.63/FIRST CLASS|University
2023|13|AYODELE MERCY BOLUWATIFE|FUTA|AGRIC & ENV. ENG|4.59/FIRST CLASS|University
2023|14|OLATUNDE KEHINDE|FUTA|PHYSIOLOGY|4.62/FIRST CLASS|University
2023|15|FALADE JEROME OLUMIDE|FUTA|BIOMEDICAL TECH|5.59/FIRST CLASS|University
2023|16|MAKANJUOLA OLUWASEUN|FUTA|AGRIC EXT & COMM|4.72/FIRST CLASS|University
2023|17|ADEDEJI OLUWALERE EZEKIEL|FUTA|QUANT SURVEYING|4.67/FIRST CLASS|University
2023|18|George Opeyemi Mary|FUNAAB|NUTR. & Dietetics|4.56/FIRST CLASS|University
2023|19|Olaniyi Grace Anuoluwapo|EKSU|ACCOUNTING|4.58/FIRST CLASS|University
2023|20|Akinbode Gloria Dolapo|EKSU|LAW|4.51/FIRST CLASS|University
2023|21|AKOMOLAFE TITILAYO BOLUWATIFE|EKSU|ACCOUNTING|4.57/FIRST CLASS|University
2023|22|OLOYEDE EMMANUEL SUNDAY|Tai Solarin Univ of Education|PHYSICS|4.55/FIRST CLASS|University
2023|23|OYEGOKE SAMUEL|FUTMINNA|EST MGT & EVALUATION|4.5/FIRST CLASS|University
2023|24|Omolade Similoluwa Odutol|FUTMINNA|EST MGT & EVALUATION|4.5/FIRST CLASS|University
2023|25|OLOMONIYI PRECIOUS OLUWAFEMI|EKSU|BIOCHEMISTRY|4.54/FIRST CLASS|University
2023|26|AKINLEYE ENOCH|UNILORIN|BIOCHEMISTRY|4.96/FIRST CLASS|University
2023|27|Ganiyu Taiwo Oladamola|AAU, AKUNGBA|ANIMAL SCIENCE|4.59/FIRST CLASS|University
2023|28|Isadare oluwafemi James|FUOYE|MECHATRONICS|4.67/FIRST CLASS|University
2023|29|OLANIRAN LIVINGSTONE TOLU|FUOYE|STATISTICS|4.53/FIRST CLASS|University
2023|30|OGWUCHE BLESSING OJOMAKPENE|FUOYE|ANIM & ENV BIOLOGY|4.66/FIRST CLASS|University
2023|31|ADENIYI GIDEON ADEOLA|OAU|CROP PROD & PROTECTION|4.55/FIRST CLASS|University
2023|32|AKINTAYO DAMILOLA MARY|UNILORIN|ANATOMY|4.6/FIRST CLASS|University
2023|33|AYANNUGA OYINKANSOLA|OOU AGO IWOYE|COMP SCIENCE|4.51/FIRST CLASS|University
2023|34|SORINMADE ABOLAJI BLESSING|OAU|MGT & ACCOUNTING|4.63/FIRST CLASS|University
2023|35|AFOLABI OLASUBOMI O|OSCOTECH ILESE|DENTAL TECH|DISTINCTION|Polytechnic/Monotechnics
2023|36|ODETUNDE SOLOMON|OGSCOTECH, IJEBU ODE|HEALTH INFO MGT|DISTINCTION|Polytechnic/Monotechnics
2023|37|Afijabi Olasunkunmi Omoyemi|Federal Polytechnic Ado Ekiti|Agric Tech|3.68/DISTINCTION|Polytechnic/Monotechnics
2023|38|Paul Timilehin Ayomide|Fed Poly Ilaro|Computer Eng|3.65/Distinction|Polytechnic/Monotechnics
2023|39|SHITTU RACHAEL OYINLOLA|Fed Poly Ilaro|Bus Admin & Mgt|3.61/DISTINCTION|Polytechnic/Monotechnics
2023|40|Arowolo Grace Morenikeji|OGSCOTECH, IJEBU ODE|commuitY health|3.56/DISTINCTION|Polytechnic/Monotechnics
2023|41|GBADAMOSI TOSIN RASHIDAT|OGSCOTECH, IJEBU ODE|HEALTH MGT INFO|3.57/DISTINCTION|Polytechnic/Monotechnics
2023|42|KUSEWON MOSES MAUKLO|OGITECH|COMP SCIENCE|3.60/DISTINCTION|Polytechnic/Monotechnics
2023|43|DARAMOLA GRACE FUNMILOLA|FECA|CROP PROD TECH|3.60/DISTINCTION|Polytechnic/Monotechnics
2023|44|ADELEKE OLUWADAMILARE MICHAEL|OKE OGUN POLY SAKI|MATHS & STAT|3.51/DISTINCTION|Polytechnic/Monotechnics
2023|45|ONI IRETIOLUWA IRAPADA|OKE OGUN POLY SAKI|ANIM PROD TECH|3.79/DISTINCTION|Polytechnic/Monotechnics
2023|46|FALUYI OPEYEMI|AUCHI POLY|URBAN & REG PLAMNNING|3.71/DISTINCTION|Polytechnic/Monotechnics
2023|47|Moronkeji Deborah oluwagbotemi|FEDPOLY OFFA|COMP ENG|3.61/DISTINCTION|Polytechnic/Monotechnics
2023|48|Adeosun Joy Titilayo|RUFUS GIWA POLY OWO|MASS COMM|3.54/DISTINCTION|Polytechnic/Monotechnics
2023|49|Bele Olatunde Prince|RUFUS GIWA POLY OWO|MASS COMM|3.50/DISTINCTION|Polytechnic/Monotechnics
2023|50|Ikharo Abraham Irivboje|RUFUS GIWA POLY OWO|ARCH TECH|3.51/DISTINCTION|Polytechnic/Monotechnics
2022|1|OLUSANJO OLUWASEUN JEREMIAH|EKSU|ECONOMICS|FIRST CLASS|
2022|2|OLATUNJI OLUWABUSOLA CHRISTIANAH|EKSU|BIO SCI/BIOTECH|FIRST CLASS|
2022|3|ONIBABA OLATOMIWA ESTHER|EKSU|ACCOUNTING|FIRST CLASS|
2022|4|ADEFIOYE OMOTOLA FUNMILOLA|EKSU|INDUSTRIAL CHEMISTRY|FIRST CLASS|
2022|5|AFENIFORO IBUKUN ISAAC|FEDPOLY ADO|AGRIC & BIO ENV ENG|DISTINCTION|
2022|6|ODEJINMI OLOLADE ESTHER|FEDPOLY ADO|ACCOUNTANCY|DISTINCTION|
2022|7|ADESEYE HEROMAN TEMITOPE|FEDPOLY ADO|PURCHASING & SUPPLY|DISTINCTION|
2022|8|OLUWASOLA EMMANUEL IYANU|FEDPOLY ADO|ACCOUNTANCY|DISTINCTION|
2022|9|OJO-ADALUMO AYOBAMI RHODA|COHEST IJERO|ORTHOPAEDIC CAST TECH|DISTINCTION|
2022|10|OGINNI OLUWAPELUMI FEYISAYO|FUOYE|ENG & LIT STUDIES|FIRST CLASS|
2022|11|ADEMILUYI DARE DAMILOLA|FUNAAB|NUTRITION & DIETETICS|FIRST CLASS|
2022|12|AMOS MERCY ADELEKE|KUMASI TECH UNIV, GHANA|SEC & MGT STUDIES|FIRST CLASS|
2022|13|ADEBIYI TAIWO AANUOLUWAPO|UNILAG|CIVIL ENG|FIRST CLASS|
2022|14|BALOGUN SEMETON|UNILAG|NURSING|DISTINCTION|
2022|15|OLALEKAN VICTOR ABIMBOLA|FEDPOLY OFFA|BANKING & FINANCE|DISTINCTION|
2022|16|IPOOLA VICTOR ROTIMI|OOU AGO IWOYRE|PHARMACOLOGY|FIRST CLASS|
2022|17|OKOROCHA OKECHUKWU SUCCESS|PLATEAU STATE POLY|SCI LAB TECH|DISTINCTION|
2022|18|AKINGBOYE DEBORAH ADURA|FEDPOLY ILARO|SCI LAB TECH|DISTINCTION|
2022|19|ADEODUN ADEBIMPE TEMITOPE|FEDPOLY ILARO|FOOD TECH|DISTINCTION|
2022|20|EKPEI LISA OJUROMI|UNIBEN|ANIM & ENV BIOLOGY|FIRST CLASS|
2022|21|BALOGUN GBEMISOLA BLESSING|UNIBEN|CIVIL ENG|FIRST CLASS|
2022|22|IYEKE GRACE EFE|UNIBEN|INSURANCE|FIRST CLASS|
2022|23|OSHUNBIYI AYOMIDE ADEDOTUN|FUNAAB|AGRIC ECONS & RURAL DEV|FIRST CLASS|
2022|24|ODEYEMI JETHRO OLUYINKA|FUNAAB|MECHATRONICS ENG|FIRST CLASS|
2022|25|ADEBUSUYI OMOWUNMI OLUWATOSIN|FUNAAB|FOOD SCI TECH|FIRST CLASS|
2022|26|SHOKUNBI JOHNSON OLUWASEUN|FUNAAB|CIVIL ENG|FIRST CLASS|
2022|27|MUSEDIQ BABATUNDE YUSUF|FUNAAB|ENTERPRENEURAL STUDY|FIRST CLASS|
2022|28|ADESETENIKAN ISEOLUWA|RUGIPO|SCI LAB TECH|DISTINCTION|
2022|29|OBE EBENEZER OLAMILEKAN|RUGIPO|SCI LAB TECH|DISTINCTION|
2022|30|MESOGBORIWON BLESSING IBUKUNOLUWA|RUGIPO|SCI LAB TECH|DISTINCTION|
2022|31|OGUNGBENLE ABOSEDE FUNMI|UI (MOCPED)|GUID & COUNSELLING|FIRST CLASS|
2022|32|ADINLEWA AYODEJI MICHAEL|KWARA STATE COHEALTH|COMMUNITY HEALTH|DISTINCTION|
2022|33|GBAMILA BINAWEI|UNIBEN|INDUSTRIAL MATHS|FIRST CLASS|
2022|34|ADEBIYI KEHINDE OREOLUWA|LASU|MICROBIOLOGY|FIRST CLASS|
2022|35|AINA OLANIYI STEPHEN|UBTH|HEALTH INFO MGT|DISTINCTION|
2022|36|AJAYI DEBORAH OLUWATOYOSI|UI|PHARMACY|DISTINCTION|
2022|37|OSENI BOLAJI NNEBOGO|UI|ANIMAL SCIENCE|FIRST CLASS|
2022|38|AKINRELE TAYE OLUWABUSAYO|FUTA|COMPUTER SCIENCE|FIRST CLASS|
2022|39|ODERINDE PAUL TIMILEHIN|FUTA|MECHANICAL ENGINEERING|FIRST CLASS|
2022|40|CHIJIOKE OLUCHI BLESSING|FUTA|ARCHITECTURE|FIRST CLASS|
2022|41|OLANREWAJU DUNSIN EBEN|COLL OF HEALTH TECH AKURE|MED LAB TECH|DISTINCTION|
2022|42|AKINDE EUNICE ADEOLA|COLL OF HEALTH TECH AKURE|COMM HEALTH EXT|DISTINCTION|
2022|43|OYELAMI JUSTINAH ABIOYE|COLL OF HEALTH TECH AKURE|COMM HEALTH EXT|DISTINCTION|
2021|1|AKINBOLA OLUWATIMILEHIN J.|WESLEY UNIVERSITY, ONDO|MICROBIOLOGY|FIRST CLASS|University
2021|2|AJILA ENIOLA CHRISTIANAH|EKSU|SCIENCE EDUCATION|FIRST CLASS|University
2021|3|ODIAH GIDEON ISAAC|EKSU|SCIENCE EDUCATION|FIRST CLASS|University
2021|4|OLADIPUPO SOLOMON OLALEKAN|UNILAG|MASS COMM|FIRST CLASS|University
2021|5|ADEDEJI DANIEL OLUWADAMILOLA|UNILAG|ARCHITECTURE|FIRST CLASS|University
2021|6|TONUEWA SEJORO|UNILAG|PHARMACY|DISTINCTION|University
2021|7|OLOLADE RUTH OLUWATUMININU|UI|NURSING|FIRST CLASS|University
2021|8|ADEROJU OPEYEMI RACHAEL|UI|ANIMAL SCIENCE|FIRST CLASS|University
2021|9|OLUKOREDE DAMILOLA ESTHER|UI|PHARMACY|DISTINCTION|University
2021|10|AJAYI DAMILOLA COMFORT|AAUA|BIOCHEMISTRY|FIRST CLASS|University
2021|11|OLOWU COMFORT|FUTA|BIOCHEMISTRY|FIRST CLASS|University
2021|12|JOSIAH FOLASHADE EUNICE|FUTA|AGRIC EXT & COMM TECH|FIRST CLASS|University
2021|13|AYIBIOWU EBENEZER OLADIPUPO|FUTA|FOOD SCI & GTECH|FIRST CLASS|University
2021|14|ADETOYESE MATTHEW ADEGOKE|TASUED|COMP SCI & INFO TECH|FIRST CLASS|University
2021|15|ADEGBOLA OMOBOLANLE COMFORT|TASUED|ECONOMICS|FIRST CLASS|University
2021|16|ONIGBINDE FAITH OLUWATOSIN|TASUED|HOME ECONS & HOTEL MGT|FIRST CLASS|University
2021|17|EHIGIEGBA PROMISE TOSIN|UNIBEN|ACCOUNTING|FIRST CLASS|University
2021|18|ASAOLU INIOLUWA GRACE|UNIBEN|HEALTH INFO MGT|DISTINCTION|University
2021|19|OJO BLESSING|EKSU|CLINICAL PHARM & THERAPEUTICS|DISTINCTION|University
2021|20|OGUNBAYO OMOLADUN SAFURAT|LASU|MICROBIOLOGY|FIRST CLASS|University
2021|21|ABIONA OLAIDE OLUDAYO|LASU|MICROBIOLOGY|FIRST CLASS|University
2021|22|AGIRI MOYOSORE TOLULOPE|BABCOCK UNIVERSITY|ECONOMICS|FIRST CLASS|University
2021|23|OJEDOTUN OMOLOLA AKINYANJU|FED UNIV WUKARI|CHEMICAL SCIENCES|FIRST CLASS|University
2021|24|OLANIYI DORCAS ABOSEDE|FEDPOLY BAUCHI|BUS ADMIN & MGT|DISTINCTION|Polytechnic/Monotechnics
2021|25|FALADE BOLUWATIFE ENIOLA|FEDPOLY ILARO|HOSPITALITY MGT|DISTINCTION|Polytechnic/Monotechnics
2021|26|ADESANYA ABOSEDE OLAMIDE|FEDPOLY ILARO|SLT|DISTINCTION|Polytechnic/Monotechnics
2021|27|AKINADE GBEMILEKE DANIEL|FEDPOLY ILARO|BUS ADMIN & MGT|DISTINCTION|Polytechnic/Monotechnics
2021|28|OGUNGBURE FUNMILAYO|RUFUS GIWA POLY OWO|MASS COMM|DISTINCTION|Polytechnic/Monotechnics
2021|29|ALABI MICHAEL MAYOWA|FEDPOLY ADO|URBAN & REG PLANNING|DISTINCTION|Polytechnic/Monotechnics
2021|30|FABUNMI ESTHER MERCY|FEDPOLY ADO|ACCOUNTANCY|DISTINCTION|Polytechnic/Monotechnics
2021|31|SALAKO JOSEPH TOSIN|FEDPOLY ADO|BUILDING TECHNOLOGY|DISTINCTION|Polytechnic/Monotechnics
2021|32|POBA JOHNSON AYODELE|FEDPOLY ADO|BUILDING TECHNOLOGY|DISTINCTION|Polytechnic/Monotechnics
2021|33|MUIBI DORCAS AANUOLUWAPO|FEDPOLY ADO|ACCOUNTANCY|DISTINCTION|Polytechnic/Monotechnics
2021|34|JEGEDE DANIEL AYOMIDE|FEDPOLY ADO|SCIENCE TECH (MCB)|DISTINCTION|Polytechnic/Monotechnics
2021|35|AJAYI AYOMIDE BUKOLA|FEDPOLY ADO|AGRIC & BIO-ENVIR ENG|DISTINCTION|Polytechnic/Monotechnics
2021|36|AGBONMHENLO JOY OSALUMHENSE|FEDPOLY ADO|SCIENCE TECH (MCB)|DISTINCTION|Polytechnic/Monotechnics
2021|37|ATIBA OLUWAKEMI DORCAS|SHTA, AKURE|MED LAB TECH|DISTINCTION|Polytechnic/Monotechnics
2021|38|AKINLAMILO SOLOMON ABIODUN|SHTA, AKURE|MED LAB TECH|DISTINCTION|Polytechnic/Monotechnics
2021|39|OLUWAFEMI DEBORAH OLUWAMAYOKUN|RUFUS GIWA POLY OWO|AGRIC EXT & MGT|DISTINCTION|Polytechnic/Monotechnics
2021|40|ALLEN AYOMIKUN ELIZABETH|LASPOTECH|BIOLOGICAL SCIENCES|DISTINCTION|Polytechnic/Monotechnics
2021|41|OLUWABIYI OLUWAFUNBI MARY|LASPOTECH|MICROBIOLOGY|DISTINCTION|Polytechnic/Monotechnics
2021|42|OLONIMOYO FAITH RONKE|SHTA|HEALTH INFO MGT|DISTINCTION|Polytechnic/Monotechnics
2019|1|BANKOLE IYANU|EKSU|Medicine & Surgery||
2019|2|ALIU GLORY AUGUSTINE|EKSU|HUMAN KINETICS||
2019|3|JAMES PHILIP OSHANO|FCOAH Ibadan|Animal Health Tech.||
2019|4|IDOWU OLUWADAMILOLA ABIGAEL|FECA|Crop Prod Tech||
2019|5|BUKUNMI MOYIN OLUWASOLA|FEDPOLY ADO|Science Technology||
2019|6|ISAAC PELUMI ADEDIRE|FEDPOLY ADO|ACCOUNTANCY||
2019|7|ALAO ADEDOLAPO LYDIA|FEDPOLY OFFA|OFFICE TECH & MGT.||
2019|8|EJEH FAITH RUTH|FEDPOLY IDAH|Public Admin||
2019|9|ODESANYA ADEOLA RUKAYAT|FUNAAB|Food Sci. & Tech.||
2019|10|STEPHEN KEHINDE MOSES|FUNAAB|Animal Nutrition||
2019|11|FADIPE TUNDE EMMANUEL|FUNAAB|Agric. Administration||
2019|12|WILLIAMS AYOMIDE SARAH|FUNAAB|Hospitality & Tourism||
2019|13|OGUNBAYO OLUWATIMILIHIN VICTOR|FUNAAB|Agric Econs & Farm Mgt.||
2019|14|SODIPE OLADAYO REUBEN|FUNAAB|Agric. Administration||
2019|15|POPOOLA DEBORAH|FUNAAB|Vet. Medicine||
2019|16|AKALA TOYOSI VICTORIA|FUTA|Ecotourism & Wildlife||
2019|17|OLATUNJI ABRAHAM OLUWATOMIWA|FUTA|Ecotourism & Wildlife||
2019|18|OLAYIOYE OLALEKAN OLUWATOBI|FUTA|Biology||
2019|19|GIWA MAKINDE TEMITOPE|KSU, ANYIGBA|Nursing||
2019|20|BANKOLE PAUL OLUWASEUN|LAUTECH|Transport Mgt.||
2019|21|ODEWUMI RUTH ABISOLA|LASU|ECONOMICS EDU||
2019|22|OLABANJI KAYODE TOYOSI|OAU|||
2019|23|OLANREWAJU TOMISIN LYDIA|OAU|LAW||
2019|24|OKE MAYOKUN ABIOLA|OAU|Chemical Engineering||
2019|25|OYETUNJI ADEWALE EMMANUEL|OSCOTECH ESAOKE|Civil Eng.||
2019|26|BUSARI IBRAHIM ENIOLA|POLY IBADAN|Building Tech.||
2019|27|AKINBODE JACOB DAMILARE|Rufus Giwa Owo|ACCOUNTANCY||
2019|28|OLUWOLE AYOOLA IFEOLUYEMI|Rufus Giwa Owo|SLT||
2019|29|OMONIJO ISRAEL OYENIYI|Rufus Giwa Owo|ELECT. ENG.||
2019|30|AGHANENU PRISCILLA|Rufus Giwa Owo|URBAN & REG. PLANNING||
2019|31|OLUWATOSIN HEPHZIBAH AYEJUYOLE|SEGI UNIVERSITY|Chemical Engineering||
2019|32|AIGBEFO MARVELOUS EBEHIREME|UBTH|Health Info. Mgt.||
2019|33|ADEBAYO PHILIP BUSAYO|UI|MECH ENG.||
2019|34|GBADEBO OLOLADE SAMSON|UI|PHARMACY||
2019|35|TAIWO JOSEPH OLUWASEUN|UI|Agric Econs||
2019|36|OLALEKAN SAMUEL OJUBE|UNILORIN|CHRISTIAN STUDIES||
DATA;

        DB::transaction(function () use ($rows, $normalize, $campusAliases, $campusesByName, &$unmatchedSchools, &$matchedApplications): void {
            foreach (preg_split('/\R/', trim($rows)) as $line) {
                [$year, $number, $name, $institution, $course, $grade, $category] = array_pad(explode('|', $line, 7), 7, '');
                $reference = sprintf('LEGACY-GO-%s-%03d', $year, $number);
                $date = Carbon::create((int) $year, 1, 1, 12, 0, 0);
                $campusName = $campusAliases[$normalize($institution)] ?? $normalize($institution);
                $campus = $campusesByName->get($campusName);
                $existingAward = Award::withTrashed()->where('reference', $reference)->first();

                if ($campus) {
                    $matchedApplications++;
                } else {
                    $unmatchedSchools[$institution] = true;
                }

                $award = Award::withTrashed()->updateOrCreate(
                    ['reference' => $reference],
                    [
                        'type' => 'go',
                        'chapter_id' => $campus?->id ?? $existingAward?->chapter_id,
                        'zone_id' => $campus?->zone_id ?? $existingAward?->zone_id,
                        'field_id' => $campus?->field_id ?? $existingAward?->field_id,
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

                $cgpa = preg_match('/^\s*([0-9]+(?:\.[0-9]+)?)/', $grade, $matches)
                    ? $matches[1]
                    : null;

                AwardEntry::updateOrCreate(
                    ['award_id' => $award->id],
                    [
                        'first_name' => $name,
                        'select_institution' => $institution,
                        'course_of_study' => $course ?: null,
                        'cgpa' => $cgpa,
                        'graduating_grade' => $grade ?: null,
                        'legacy_category' => $category ?: null,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]
                );
            }
        });

        $approvedCount = Award::query()
            ->where('type', 'go')
            ->where('reference', 'like', 'LEGACY-GO-%')
            ->where('is_archive', true)
            ->where('national_status', 1)
            ->count();

        $cgpaCount = AwardEntry::query()
            ->whereIn('award_id', Award::query()
                ->where('type', 'go')
                ->where('reference', 'like', 'LEGACY-GO-%')
                ->select('id'))
            ->whereNotNull('cgpa')
            ->where('cgpa', '!=', '')
            ->count();

        $this->command?->info("{$approvedCount} legacy G.O. beneficiaries are archived and nationally approved; {$cgpaCount} have a recorded CGPA.");
        $this->command?->info("Matched {$matchedApplications} of 171 beneficiary records to campus records.");

        if ($unmatchedSchools) {
            $this->command?->warn('Unmatched school names: ' . implode('; ', array_keys($unmatchedSchools)));
        }
    }
}
