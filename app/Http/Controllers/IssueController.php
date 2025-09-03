<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttachTagRequest;
use App\Http\Requests\StoreIssueRequest;
use App\Models\Issue;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Tag;

class IssueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Start the query with eager loading
        $query = Issue::with(['project', 'tags']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('tag_id')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('id', $request->tag_id);
            });
        }


        $issues = $query->paginate(10)->withQueryString();


        $projects = Project::all();
        $tags = Tag::all();

        return view('issues.index', compact('issues', 'projects', 'tags'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIssueRequest $request)
    {
        $issue = Issue::create($request->validated());

        if ($request->filled('tag_ids')) {
            $issue->tags()->sync($request->tag_ids);
        }

        return response()->json(['status'=>'success','issue'=>$issue]);

    }

    /**
     * Display the specified resource.
     */
    public function show(Issue $issue)
    {
        $issue->load(['project', 'tags', 'comments' => function ($q) {
            $q->latest()->take(5);
        }]);

        $tags = Tag::all();

        return view('issues.show', compact('issue', 'tags'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Issue $issue)
    {

        $issue->load('tags');

        return response()->json([
            'id' => $issue->id,
            'title' => $issue->title,
            'description' => $issue->description,
            'project_id' => $issue->project_id,
            'status' => $issue->status->value ?? $issue->status,
            'due_date' => $issue->due_date,
            'priority' => $issue->priority->value ?? $issue->priority,
            'tag_ids' => $issue->tags->pluck('id'),


        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreIssueRequest $request, Issue $issue)
    {
        $issue->update($request->validated());

        return response()->json(['status'=>'success','issue'=>$issue]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Issue $issue)
    {
        $issue->delete();
        return response()->json(['status'=>'success']);
    }

    public function attachTag(AttachTagRequest $request, Issue $issue)
    {
        $issue->tags()->syncWithoutDetaching([$request->tag_id]);

        $issue->load('tags');

        return response()->json([
            'status' => 'success',
            'message' => 'Tag attached successfully.',
            'issue' => $issue
        ]);
    }
    public function detachTag(Issue $issue, Tag $tag) {
        $issue->tags()->detach($tag->id);

        $issue->load('tags');

        return response()->json(['status'=>'success', 'issue'=>$issue]);
    }
}
