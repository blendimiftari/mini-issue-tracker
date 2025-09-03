<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Tag;
use App\Models\Comment;
use App\Models\Issue;

class ProjectIssueTagCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Project::factory(5)
            ->has(Issue::factory(3)
            ->has(Comment::factory(2))
            )
            ->create();


        $tags = Tag::factory(10)->create();


        Issue::all()->each(function ($issue) use ($tags) {
            $issue->tags()->attach(
                $tags->random(rand(1, 3))->pluck('id')->toArray()
            );
        });
    }
}
