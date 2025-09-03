@extends('layouts.app')

@section('content')
    <div class="bg-white rounded-lg shadow p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">{{ $project->name }}</h2>
            <a href="{{ route('projects.index') }}"
               class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400">
                Back
            </a>
        </div>

        <!-- Project Info -->
        <div class="mb-6 space-y-1">
            <p><strong>Description:</strong> {{ $project->description ?? 'N/A' }}</p>
            <p><strong>Start Date:</strong> {{ $project->start_date }}</p>
            <p><strong>Deadline:</strong> {{ $project->deadline }}</p>
        </div>

        <!-- IssueController Section -->
        <div class="mb-4 flex justify-between items-center">
            <h3 class="text-xl font-semibold">Issues</h3>
{{--            <a href="{{ route('issues.create', ['project_id' => $project->id]) }}"--}}
{{--               class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">--}}
{{--                Add Issue--}}
{{--            </a>--}}
        </div>

        @if($project->issues->isEmpty())
            <p class="text-center text-gray-500">No issues for this project.</p>
        @else
            <div class="overflow-x-auto">
                <table class="table-auto w-full border-collapse">
                    <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="p-2">Title</th>
                        <th class="p-2">Status</th>
                        <th class="p-2">Priority</th>
                        <th class="p-2">Tags</th>
                        <th class="p-2 text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($project->issues as $issue)
                        <tr class="border-b">
                            <td class="p-2">{{ $issue->title }}</td>
                            <td class="p-2">{{ $issue->status }}</td>
                            <td class="p-2">{{ $issue->priority }}</td>
                            <td class="p-2">
                                @foreach($issue->tags as $tag)
                                    <span class="bg-gray-300 text-gray-700 px-2 py-1 rounded text-xs mr-1">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="p-2 text-right">
                                <a href="{{ route('issues.show', $issue) }}"
                                   class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
