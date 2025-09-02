
@extends('layouts.app')

@section('content')
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between mb-6">
            <h2 class="text-2xl text-center  font-semibold">Projects</h2>
            <button id="createBtn" class="text-md font-semibold bg-green-400 hover:bg-green-200 rounded-lg p-2">Create Project</button>
        </div>

        @if($projects->isEmpty())
            <p class="text-gray-500 text-center">No projects found. Create one above!</p>
        @else
            <div class="">
                <table class="table-auto w-full border-collapse">
                    <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="p-2">Name</th>
                        <th class="p-2">Description</th>
                        <th class="p-2">Start Date</th>
                        <th class="p-2">Deadline</th>
                        <th class="p-2 text-right pr-10">Actions</th>
                    </tr>
                    </thead>
                    <tbody id="project-list">
                    @foreach($projects as $project)
                        <tr class="border-b">
                            <td class="p-2 ">{{ $project->name }}</td>
                            <td class="p-2 break-words">{{ $project->description }}</td>
                            <td class="p-2">{{ $project->start_date }}</td>
                            <td class="p-2">{{ $project->deadline }}</td>
                            <td class="p-2 text-right">
                                <div class="inline-flex space-x-2">
                                    <a href="{{ route('projects.show', $project) }}"
                                       class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                                        View
                                    </a>
                                    <button data-id="{{ $project->id }}"
                                            class="edit-project bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                                        Edit
                                    </button>
                                    <button data-id="{{ $project->id }}"
                                            class="delete-project bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
        @endif
    </div>


    <div id="create-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 class="text-lg font-semibold mb-4">Create Project</h2>
            <form id="create-project-form">
                @csrf
                <input type="hidden" name="id" id="create-project-id">
                <label class="text-sm font-semibold">Project Name</label>
                <input type="text" name="name" id="create-project-name" placeholder="Name"
                       class="border rounded text-sm p-2 w-full mb-4">
                <label class="text-sm font-semibold">Description</label>
                <input type="text" name="description" id="create-project-description" placeholder="Description"
                       class="border rounded p-2 text-sm w-full mb-4">
                <label class="text-sm font-semibold">Start Date</label>
                <input type="date" name="start_date" id="create-project-start_date" placeholder="Start Date"
                       class="border rounded p-2  text-sm w-full mb-4">
                <label class="text-sm font-semibold">Deadline</label>
                <input type="date" name="deadline" id="create-project-deadline" placeholder="Deadline"
                       class="border rounded p-2 text-sm w-full mb-4">
                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancel-create"
                            class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit"
                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Save
                    </button>
                </div>
            </form>
            <p id="create-error" class="text-red-500 mt-2 hidden"></p>
        </div>
    </div>


    <div id="edit-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 class="text-lg font-semibold mb-4">Edit Project</h2>
            <form id="edit-project-form">
                @csrf
                @method('PUT')

                <input type="hidden" name="id" id="edit-project-id">

                <label class="text-sm font-semibold">Project Name</label>
                <input type="text" name="name" id="edit-project-name" class="border rounded p-2 w-full mb-4">

                <label class="text-sm font-semibold">Description</label>
                <input type="text" name="description" id="edit-project-description" class="border rounded p-2 w-full mb-4">

                <label class="text-sm font-semibold">Start Date</label>
                <input type="date" name="start_date" id="edit-project-start_date" class="border rounded p-2 w-full mb-4">

                <label class="text-sm font-semibold">Deadline</label>
                <input type="date" name="deadline" id="edit-project-deadline" class="border rounded p-2 w-full mb-4">

                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancel-edit" class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400">Cancel</button>
                    <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Save Changes</button>
                </div>
            </form>
            <p id="edit-error" class="text-red-500 mt-2 hidden"></p>
        </div>
    </div>


@endsection


@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', ()=>{
            const createModal = document.getElementById('create-modal');
            const openCreateBtn = document.getElementById('createBtn');
            const cancelBtn = document.getElementById('cancel-create');
            const createForm = document.getElementById('create-project-form');
            const errorMsg = document.getElementById('create-error');
            const editErrorMsg = document.getElementById('edit-error');
            const editModal = document.getElementById('edit-modal');
            const editForm = document.getElementById('edit-project-form');
            const cancelEditBtn = document.getElementById('cancel-edit');


            openCreateBtn.addEventListener('click', () => {

                createModal.classList.remove('hidden');
                errorMsg.classList.add('hidden');
                createForm.reset();
            });


            cancelBtn.addEventListener('click', () => {
                createModal.classList.add('hidden');
            });


            createModal.addEventListener('click', (e) => {
                if (e.target === createModal) createModal.classList.add('hidden');
            });

            createForm.addEventListener('submit', async (e) => {
                console.log('Submit triggered'); // Should appear in console
                e.preventDefault();
                errorMsg.classList.add('hidden');
                errorMsg.innerText = '';

                const formData = new FormData(createForm);

                try {
                    const response = await fetch("{{ route('projects.store') }}", {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.status === 'success') {
                        createModal.classList.add('hidden');
                        createForm.reset();
                        window.location.reload();
                    } else if (data.errors) {
                        errorMsg.innerText = Object.values(data.errors).flat().join(', ');
                        errorMsg.classList.remove('hidden');
                    }

                } catch (error) {
                    console.error(error);
                    errorMsg.innerText = 'Something went wrong. Try again!';
                     errorMsg.classList.remove('hidden');
                 }
            });

            document.querySelectorAll('.delete-project').forEach(button=>{
                button.addEventListener('click', async (e)=>{
                    e.preventDefault();
                    const projectId = button.getAttribute('data-id');

                    try {

                        const response = await fetch(`projects/${projectId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                        })

                        if(response.ok) {
                            button.closest('tr').remove();
                        } else {
                            console.error('Failed to delete project');
                            alert('Something went wrong while deleting.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    }
                })
            })

            document.querySelectorAll('.edit-project').forEach(button => {
                button.addEventListener('click', () => {
                    const row = button.closest('tr');
                    const projectId = button.getAttribute('data-id');
                    const name = row.children[0].innerText;
                    const description = row.children[1].innerText;
                    const start_date = row.children[2].innerText;
                    const deadline = row.children[3].innerText;

                    document.getElementById('edit-project-id').value = projectId;
                    document.getElementById('edit-project-name').value = name;
                    document.getElementById('edit-project-description').value = description;
                    document.getElementById('edit-project-start_date').value = start_date;
                    document.getElementById('edit-project-deadline').value = deadline;

                    editModal.classList.remove('hidden');
                    errorMsg.classList.add('hidden');
                    errorMsg.innerText = '';
                });
            });


            cancelEditBtn.addEventListener('click', () => editModal.classList.add('hidden'));
            editModal.addEventListener('click', e => { if(e.target === editModal) editModal.classList.add('hidden'); });


            editForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                editErrorMsg.classList.add('hidden');
                editErrorMsg.innerText = '';

                const projectId = document.getElementById('edit-project-id').value;
                const formData = new FormData(editForm);

                try {
                    const response = await fetch(`/projects/${projectId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if(data.status === 'success') {
                        editModal.classList.add('hidden');


                        const row = document.querySelector(`.edit-project[data-id="${projectId}"]`).closest('tr');
                        row.children[0].innerText = formData.get('name');
                        row.children[1].innerText = formData.get('description');
                        row.children[2].innerText = formData.get('start_date');
                        row.children[3].innerText = formData.get('deadline');
                    } else if(data.errors) {
                        editErrorMsg.innerText = Object.values(data.errors).flat().join(', ');
                        editErrorMsg.classList.remove('hidden');
                    }

                } catch(err) {
                    console.error(err);
                    editErrorMsg.innerText = 'Something went wrong. Try again!';
                    editErrorMsg.classList.remove('hidden');
                }
            });
        })



    </script>
@endsection
