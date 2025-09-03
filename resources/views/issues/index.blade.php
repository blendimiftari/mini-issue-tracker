@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4 bg-white rounded-lg shadow">

        <!-- Header -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">Issues</h2>
            <button id="createIssueBtn" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Create Issue
            </button>
        </div>


        <form id="filterForm" method="GET" class="flex flex-wrap gap-4 mb-6">
            <select name="status" class="border rounded p-1">
                <option value="" class="">Status</option>
                <option value="open" {{ request('status')=='open'?'selected':'' }}>Open</option>
                <option value="in_progress" {{ request('status')=='in_progress'?'selected':'' }}>In Progress</option>
                <option value="closed" {{ request('status')=='closed'?'selected':'' }}>Closed</option>
            </select>

            <select name="priority" class="border rounded p-2">
                <option value="">Priority</option>
                <option value="low" {{ request('priority')=='low'?'selected':'' }}>Low</option>
                <option value="medium" {{ request('priority')=='medium'?'selected':'' }}>Medium</option>
                <option value="high" {{ request('priority')=='high'?'selected':'' }}>High</option>
            </select>

            <select name="tag_id" class="border rounded p-2">
                <option value="">Tags</option>
                @foreach($tags as $tag)
                    <option value="{{ $tag->id }}" {{ request('tag_id')==$tag->id?'selected':'' }}>
                        {{ $tag->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Apply Filters
            </button>
        </form>


        @if($issues->isEmpty())
            <p class="text-gray-500 text-center">No issues found. Create one above or adjust filters.</p>
        @else
            <table class="table-auto w-full border-collapse">
                <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="p-2">Title</th>
                    <th class="p-2">Project</th>
                    <th class="p-2">Status</th>
                    <th class="p-2">Priority</th>
                    <th class="p-2">Tags</th>
                    <th class="p-2">Due Date</th>
                    <th class="p-2 text-right">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($issues as $issue)
                    <tr class="border-b">
                        <td class="p-2">{{ $issue->title }}</td>
                        <td class="p-2">{{ $issue->project->name }}</td>
                        <td class="p-2">{{ $issue->status }}</td>
                        <td class="p-2">{{ $issue->priority }}</td>
                        <td class="p-2">
                            @foreach($issue->tags as $tag)
                                <span class="bg-blue-200 text-blue-800 px-2 py-1 rounded text-xs mr-1">
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        </td>
                        <td class="p-2">{{ $issue->due_date }}</td>
                        <td class="p-2 text-right">
                            <div class="inline-flex space-x-2">
                                <a href="{{ route('issues.show', $issue) }}"
                                   class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">
                                    View
                                </a>
                                <button data-id="{{ $issue->id }}"
                                        class="edit-issue bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600">
                                    Edit
                                </button>
                                <button data-id="{{ $issue->id }}"
                                        class="delete-issue bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>


            <div class="p-4">
                {{ $issues->links() }}
            </div>
        @endif
    </div>


     <div id="issue-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 id="issue-modal-title" class="text-lg font-semibold mb-4">Create Issue</h2>
            <form id="issue-form">
                @csrf
                <input type="hidden" name="id" id="issue-id">

                <label class="text-sm font-semibold">Title</label>
                <input type="text" name="title" id="issue-title" class="border rounded p-2 w-full mb-2">

                <label class="text-sm font-semibold">Description</label>
                <textarea name="description" id="issue-description" class="border rounded p-2 w-full mb-2"></textarea>

                <label class="text-sm font-semibold">Project</label>
                <select name="project_id" id="issue-project" class="border rounded p-2 w-full mb-2">
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>

                <label class="text-sm font-semibold">Due Date</label>
                <input type="date" name="due_date" id="issue-due_date" class="border rounded p-2 w-full mb-2">

                <label class="text-sm font-semibold">Status</label>
                <select name="status" id="issue-status" class="border rounded p-2 w-full mb-2">
                    <option value="open">Open</option>
                    <option value="in_progress">In Progress</option>
                    <option value="closed">Closed</option>
                </select>

                <label class="text-sm font-semibold">Priority</label>
                <select name="priority" id="issue-priority" class="border rounded p-2 w-full mb-2">
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>

                <div class="flex justify-end space-x-2 mt-4">
                    <button type="button" id="issue-cancel" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Cancel</button>
                    <button type="submit" class="bg-blue-500 px-4 py-2 rounded text-white hover:bg-blue-600">Save</button>
                </div>
            </form>
            <p id="issue-error" class="text-red-500 mt-2 hidden"></p>
        </div>
    </div>



@endsection


@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', ()=>{

            const modal = document.getElementById('issue-modal');
            const openBtn = document.getElementById('createIssueBtn');
            const cancelBtn = document.getElementById('issue-cancel');
            const form = document.getElementById('issue-form');
            const errorMsg = document.getElementById('issue-error');
            const list = document.getElementById('issue-list');


            openBtn.addEventListener('click', ()=>{
                modal.classList.remove('hidden');
                errorMsg.classList.add('hidden');
                form.reset();
                document.getElementById('issue-modal-title').innerText = "Create Issue";
            });


            cancelBtn.addEventListener('click', ()=> modal.classList.add('hidden'));
            modal.addEventListener('click', e => { if(e.target===modal) modal.classList.add('hidden'); });


            form.addEventListener('submit', async (e)=>{
                e.preventDefault();
                errorMsg.classList.add('hidden');
                errorMsg.innerText = '';

                const formData = new FormData(form);
                const id = document.getElementById('issue-id').value;
                const url = id ? `/issues/${id}` : "{{ route('issues.store') }}";

                if (id) {

                    formData.append('_method', 'PUT');
                }

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept':'application/json'},
                        body: formData
                    });

                    const data = await response.json();
                    if(data.status==='success'){
                        window.location.reload();
                    } else if(data.errors){
                        errorMsg.innerText = Object.values(data.errors).flat().join(', ');
                        errorMsg.classList.remove('hidden');
                    }

                } catch(err){
                    console.error(err);
                    errorMsg.innerText = 'Something went wrong';
                    errorMsg.classList.remove('hidden');
                }
            });


            document.querySelectorAll('.edit-issue').forEach(btn=>{
                btn.addEventListener('click', async ()=>{
                    const id = btn.getAttribute('data-id');
                    try {
                        const res = await fetch(`/issues/${id}/edit`);
                        const data = await res.json();



                        document.getElementById('issue-id').value = data.id;
                        document.getElementById('issue-title').value = data.title;
                        document.getElementById('issue-description').value = data.description;
                        document.getElementById('issue-project').value = data.project_id;
                        document.getElementById('issue-due_date').value = data.due_date;
                        document.getElementById('issue-status').value = data.status;
                        document.getElementById('issue-priority').value = data.priority;

                        document.getElementById('issue-modal-title').innerText = "Edit Issue";

                        modal.classList.remove('hidden');

                    } catch(err){ console.error(err); alert('Failed to load issue'); }
                });
            });


            document.querySelectorAll('.delete-issue').forEach(btn=>{
                btn.addEventListener('click', async ()=>{

                    const id = btn.getAttribute('data-id');
                    try {
                        const res = await fetch(`/issues/${id}`, {
                            method:'DELETE',
                            headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}
                        });
                        if(res.ok) btn.closest('tr').remove();
                        else alert('Failed to delete');
                    } catch(err){ console.error(err); }
                });
            });

        });
    </script>
@endsection




