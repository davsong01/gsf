<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Stakeholder;
use App\Modules\Blog\Models\BlogPost;
use Illuminate\Database\Seeder;
use RuntimeException;

class LocalAapiiEventsSeeder extends Seeder
{
    public function run(): void
    {
        if (!app()->environment('local')) {
            throw new RuntimeException('Sample campus events may only be seeded locally.');
        }

        $chapter = Chapter::where('slug', 'aapii')->firstOrFail();
        $author = Stakeholder::where('chapter_id', $chapter->id)->firstOrFail();
        $events = [
            ['aapii-campus-worship-evening', 'Campus Worship Evening', 7, '16:30', 'Main Auditorium, AAPII', 'aapii-fellowship-community.jpg', 'An evening of worship, prayer and fellowship for students at Abraham Adesanya Polytechnic, Ijebu Igbo. Come with a friend and join the campus fellowship as we make room to seek God together.'],
            ['aapii-bible-study-and-questions', 'Bible Study and Questions', 14, '17:00', 'GSF Meeting Room, AAPII', 'aapii-devotional-life.jpg', 'Bring your Bible and your questions for an open, practical study of Scripture. The fellowship will explore how faith shapes student life, followed by small group discussion and prayer.'],
            ['aapii-campus-outreach-day', 'Campus Outreach Day', 21, '09:00', 'AAPII Main Gate', 'aapii-serving-campus.jpg', 'Join fellow students for a morning of service and conversations across the Abraham Adesanya Polytechnic campus. Volunteers will meet at the main gate before moving out in teams.'],
            ['aapii-student-prayer-gathering', 'Student Prayer Gathering', -12, '17:00', 'GSF Meeting Room, AAPII', 'aapii-study-rest.jpg', 'Students gathered to pray for their studies, friends and campus community. The meeting made space for encouragement, shared concerns and renewed hope for the semester.'],
            ['aapii-new-student-welcome', 'New Student Welcome Fellowship', -30, '16:00', 'Main Auditorium, AAPII', 'aapii-fresh-start.jpg', 'The fellowship welcomed new students to Abraham Adesanya Polytechnic with worship, introductions and a chance to meet other members of the GSF campus community.'],
        ];

        foreach ($events as [$slug, $title, $days, $time, $venue, $image, $summary]) {
            if (!is_file(public_path('uploads/blog/featured_image/'.$image))) {
                throw new RuntimeException('Missing local sample image: '.$image);
            }

            BlogPost::updateOrCreate(['slug' => $slug], [
                'type' => 'event',
                'title' => $title,
                'excerpt' => $summary,
                'body' => '<p>'.e($summary).'</p>',
                'featured_image' => 'uploads/blog/featured_image/'.$image,
                'author_type' => Stakeholder::class,
                'author_id' => $author->id,
                'status' => 'published',
                'published_at' => now(),
                'event_starts_at' => today()->addDays($days)->setTimeFromTimeString($time),
                'event_venue' => $venue,
                'scope_type' => 'chapter',
                'scope_id' => $chapter->id,
                'allow_comments' => true,
            ]);
        }
    }
}
