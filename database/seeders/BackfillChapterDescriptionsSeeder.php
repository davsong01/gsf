<?php

namespace Database\Seeders;

use App\Models\Chapter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BackfillChapterDescriptionsSeeder extends Seeder
{
    public function run(): void
    {
        $updated = 0;
        $preserved = 0;

        Chapter::with(['zone.field', 'field'])->orderBy('id')->chunkById(100, function ($chapters) use (&$updated, &$preserved) {
            foreach ($chapters as $chapter) {
                if (Str::lower(trim((string) $chapter->name)) === 'other' || $this->hasDescription($chapter->about)) {
                    $preserved++;
                    continue;
                }

                $chapter->about = $this->descriptionFor($chapter);
                $chapter->save();
                $updated++;
            }
        });

        $this->command?->info("Campus descriptions: {$updated} added, {$preserved} existing or placeholder records preserved.");
    }

    private function hasDescription(?string $html): bool
    {
        $text = strip_tags(html_entity_decode((string) $html, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        return trim(str_replace("\u{00A0}", ' ', $text)) !== '';
    }

    private function descriptionFor(Chapter $chapter): string
    {
        $name = e(trim((string) $chapter->name));
        $state = trim((string) $chapter->state);
        $location = $state === ''
            ? 'in Nigeria'
            : (in_array(Str::lower($state), ['fct', 'federal capital territory'], true)
                ? 'in the Federal Capital Territory'
                : 'in '.e($state).' State');
        $widerLocation = $state === '' || in_array(Str::lower($state), ['fct', 'federal capital territory'], true)
            ? 'Nigeria'
            : e($state).' State';

        $zone = $chapter->zone?->name;
        $field = $chapter->field?->name ?: $chapter->zone?->field?->name;
        $network = $zone && $field
            ? 'Within the wider GSF network, the chapter is connected to '.e(Str::title(Str::lower($zone))).' and '.e(Str::title(Str::lower($field))).'.'
            : ($zone
                ? 'Within the wider GSF network, the chapter is connected to '.e(Str::title(Str::lower($zone))).'.'
                : 'The chapter is part of the wider GOFAMINT Students’ Fellowship family.');

        $openings = [
            "At {$name} {$location}, the GOFAMINT Students’ Fellowship brings students together around faith, friendship, and purposeful service. Campus life can be demanding, and this fellowship offers a welcoming place to find encouragement, ask honest questions, and grow alongside others who want to follow Christ.",
            "The GSF chapter at {$name} {$location} is a community for students seeking to grow in faith while making the most of their time on campus. It brings people together for encouragement, meaningful friendships, and a shared commitment to live with integrity and serve others.",
            "For students at {$name} {$location}, GSF offers a place to belong and a community in which faith can shape daily life. Students from different backgrounds can learn from one another, build lasting relationships, and discover practical ways to serve their campus and the people around them.",
        ];
        $opening = $openings[$chapter->id % count($openings)];

        $nameLower = Str::lower((string) $chapter->name);
        $focus = match (true) {
            Str::contains($nameLower, ['nursing', 'medicine', 'medical', 'health']) =>
                'In a setting shaped by health and care, students are preparing for work that calls for knowledge, patience, and compassion. The fellowship encourages them to carry those qualities into their studies, relationships, and future service, remembering the dignity of every person they meet.',
            Str::contains($nameLower, ['education', 'teacher']) =>
                'As many students prepare for careers in teaching and learning, the fellowship provides room to reflect on the influence they can have on others. It encourages patient leadership, strong character, and a willingness to help people grow, both on campus and beyond it.',
            Str::contains($nameLower, ['agriculture', 'agricultural']) =>
                'In a community connected to agricultural learning, students can think about stewardship, diligence, and the value of work that serves others. The fellowship encourages them to bring faith and integrity into their studies and the communities they will help shape.',
            Str::contains($nameLower, ['polytechnic', 'technology', 'technical']) =>
                'In a learning environment that values practical skills and innovation, the fellowship encourages students to pair their abilities with character and compassion. Their training can become a way to solve problems, serve people well, and lead responsibly wherever they go.',
            default =>
                'Across different courses and stages of study, students are discovering their gifts and considering the people they hope to become. The fellowship encourages them to approach that journey with curiosity, humility, and a desire to use what they learn in service to others.',
        };

        return <<<HTML
<p>{$opening}</p>
<h3>Faith for everyday life</h3>
<p>Through worship, prayer, Bible study, discipleship, and outreach, members are encouraged to know Scripture and put its teaching into practice. Faith is not limited to a gathering: it can shape decisions, friendships, study habits, leadership, and the way students care for their neighbours.</p>
<ul>
    <li>Grow in a deeper understanding of the Bible and Christian faith.</li>
    <li>Build supportive friendships marked by encouragement and care.</li>
    <li>Develop character, leadership, and a sense of responsibility.</li>
    <li>Look for thoughtful ways to serve the campus and its wider community.</li>
</ul>
<h3>A place to belong</h3>
<p>Whether a student is new to fellowship or has been part of a Christian community for years, there is room to listen, contribute, and grow. Shared experiences on campus can create friendships that support students through academic pressure, personal change, and important choices about the future.</p>
<h3>Learning and serving with purpose</h3>
<p>{$focus}</p>
<h3>Part of a wider family</h3>
<p>{$network} This connection gives students a sense of belonging beyond one institution and encourages fellowship, learning, and service across {$widerLocation} and beyond. The hope is that what students learn in community will remain with them long after their years on campus.</p>
HTML;
    }
}
