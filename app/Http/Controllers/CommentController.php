<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use Illuminate\Http\Request;
use App\Models\Issue;

class CommentController extends Controller
{
    public function index(Issue $issue)
    {
        $comments = $issue->comments()
            ->latest()
            ->paginate(5);

        return view('comments.list', compact('comments'));
    }


    public function store(StoreCommentRequest $request, Issue $issue)
    {
        $comment = $issue->comments()->create($request->validated());

        return response()->json([
            'status' => 'success',
            'comment' => $comment
        ]);
    }
}
