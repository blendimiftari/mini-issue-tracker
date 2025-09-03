@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4 bg-white rounded-lg shadow">

        <h2 class="text-2xl font-bold mb-4">Tags</h2>

        <!-- Create Tag Form -->
        <div class="mb-6">
            <form id="tag-form" class="flex gap-2">
                @csrf
                <input type="text" name="name" id="tag-name" placeholder="New tag name" class="border p-2 rounded flex-1">
                <input type="color" name="color" id="tag-color" value="#3b82f6" class="rounded">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Add Tag
                </button>
            </form>
            <p id="tag-error" class="text-red-500 mt-2 hidden"></p>
        </div>

        <!-- Tags List -->
        <div class="flex flex-wrap gap-6" id="tags-list">
            @foreach($tags as $tag)
                <div class="flex items-center bg-gray-100 rounded-full" data-id="{{ $tag->id }}">
            <span class="px-4 mr-1 py-2 rounded-full" style="background-color: {{ $tag->color ?? '#3b82f6' }}; color: white;">
                {{ $tag->name }}
            </span>
                    <button class="delete-tag text-white bg-red-500 rounded-full w-5 h-5 flex items-center justify-center hover:bg-red-600 ml-4" title="Delete">
                        &times;
                    </button>
                </div>
            @endforeach
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('tag-form');
            const nameInput = document.getElementById('tag-name');
            const colorInput = document.getElementById('tag-color');
            const errorMsg = document.getElementById('tag-error');
            const tagsList = document.getElementById('tags-list');


            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                errorMsg.classList.add('hidden');
                errorMsg.innerText = '';

                const formData = new FormData(form);

                try {
                    const res = await fetch("{{ route('tags.store') }}", {
                        method: 'POST',
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept':'application/json'},
                        body: formData
                    });

                    const data = await res.json();
                    if(data.status === 'success') {
                        const tag = data.tag;
                        const div = document.createElement('div');
                        div.className = 'flex items-center justify-between bg-gray-100 px-2 py-1 rounded';
                        div.dataset.id = tag.id;
                        div.innerHTML = `
                    <span class="px-4 mr-2 py-1 rounded" style="background-color: ${tag.color || '#3b82f6'}; color: white;">
                        ${tag.name}
                    </span>
                    <button class="delete-tag text-white bg-red-500 rounded-full w-5 h-5 flex items-center justify-center hover:bg-red-600 ml-4" title="Delete">&times;</button>
                `;
                        tagsList.prepend(div);
                        nameInput.value = '';
                    } else if(data.errors) {
                        errorMsg.innerText = Object.values(data.errors).flat().join(', ');
                        errorMsg.classList.remove('hidden');
                    }
                } catch(err) {
                    console.error(err);
                    errorMsg.innerText = 'Something went wrong';
                    errorMsg.classList.remove('hidden');
                }
            });


            tagsList.addEventListener('click', async (e) => {
                if(!e.target.classList.contains('delete-tag')) return;
                const div = e.target.closest('div');
                const id = div.dataset.id;

                try {
                    const res = await fetch(`/tags/${id}`, {
                        method: 'DELETE',
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept':'application/json'}
                    });
                    if(res.ok) div.remove();
                    else alert('Failed to delete tag');
                } catch(err) {
                    console.error(err);
                }
            });
        });
    </script>
@endsection
