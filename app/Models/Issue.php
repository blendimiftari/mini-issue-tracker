<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\IssueStatus;
use App\Enums\IssuePriority;


class Issue extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'status', 'priority', 'due_date', 'project_id'];

    protected $casts = [
        'status' => IssueStatus::class,
        'priority' => IssuePriority::class,
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, "issue_tag");
    }

}
