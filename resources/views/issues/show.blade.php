@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6 bg-white rounded-lg shadow">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">{{ $issue->title }}</h2>
            <a href="{{ route('issues.index') }}"
               class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400">Back</a>
        </div>

        <!-- Issue Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <p><span class="font-semibold">Description:</span> {{ $issue->description }}</p>
                <p><span class="font-semibold">Project:</span> {{ $issue->project->name }}</p>
            </div>
            <div>
                <p>
                    <span class="font-semibold">Status:</span>
                    <span class="px-2 py-1 rounded
                    {{ $issue->status == 'open' ? 'bg-green-200 text-green-800' : '' }}
                    {{ $issue->status == 'in_progress' ? 'bg-yellow-200 text-yellow-800' : '' }}
                    {{ $issue->status == 'closed' ? 'bg-red-200 text-red-800' : '' }}">
                    {{ ucfirst($issue->status->value) }}
                </span>
                </p>
                <p>
                    <span class="font-semibold">Priority:</span>
                    <span class="px-2 py-1 rounded
                    {{ $issue->priority == 'low' ? 'bg-green-200 text-green-800' : '' }}
                    {{ $issue->priority == 'medium' ? 'bg-yellow-200 text-yellow-800' : '' }}
                    {{ $issue->priority == 'high' ? 'bg-red-200 text-red-800' : '' }}">
                    {{ ucfirst($issue->priority->value) }}
                </span>
                </p>
                <p><span class="font-semibold">Due Date:</span> {{ $issue->due_date ?? 'N/A' }}</p>
                <div class="flex mt-1">
                    <p class="font-semibold ">Tags: </p>
                    <div id="attached-tags" class="flex flex-wrap gap-2 mb-2 ml-2 ">
                        @foreach($issue->tags as $tag)
                            <span class="tag-badge px-2 py-1 rounded flex items-center cursor-pointer attached-tag"
                                  style="background-color: {{ $tag->color ?? '#3b82f6' }}; color: white;"
                                  data-id="{{ $tag->id }}">
                                         {{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

        <div class="mb-6">

            <h4 class="font-semibold mt-4 mb-2">Available Tags</h4>
            <div id="available-tags" class="flex flex-wrap gap-2">
                @php
                    $availableTags = $tags->filter(fn($tag) => !$issue->tags->contains('id', $tag->id));
                @endphp

                @if($availableTags->isEmpty())
                    <p class="text-gray-500">No available tags.</p>
                @else
                    @foreach($availableTags as $tag)
                        <span class="tag-badge px-2 py-1 rounded flex items-center cursor-pointer available-tag"
                              style="background-color: {{ $tag->color ?? '#3b82f6' }}; color: white;"
                              data-id="{{ $tag->id }}">
                    {{ $tag->name }}
                </span>
                    @endforeach
                @endif
            </div>
        </div>









        <!-- Comments Section -->
        <div class="mb-6">
            <h3 class="font-semibold mb-2">Comments</h3>

{{--            <div id="comments-list" class="flex flex-col gap-2">--}}
{{--                @forelse($comments as $comment)--}}
{{--                    <div class="border p-4 rounded">--}}
{{--                        <strong>{{ $comment->author_name }}</strong>--}}
{{--                        <p>{{ $comment->body }}</p>--}}
{{--                        <small class="text-gray-500">{{ $comment->created_at->diffForHumans() }}</small>--}}
{{--                    </div>--}}
{{--                @empty--}}
{{--                    <p class="text-center text-gray-500">No comments yet.</p>--}}
{{--                @endforelse--}}
{{--            </div>--}}

{{--            @if($comments->hasMorePages())--}}
{{--                <div class="mt-4 text-center">--}}
{{--                    <button id="load-more-comments" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Load More</button>--}}
{{--                </div>--}}
{{--            @endif--}}

            <!-- Add Comment -->
            <div class="mt-6">
                <h4 class="font-semibold mb-2">Add Comment</h4>
                <form id="comment-form" class="flex flex-col gap-2">
                    @csrf
                    <input type="text" name="author_name" placeholder="Your Name" class="border p-2 rounded w-full" required>
                    <textarea name="body" placeholder="Comment" class="border p-2 rounded w-full" required></textarea>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Add Comment</button>
                </form>
                <p id="comment-error" class="text-red-500 mt-2 hidden"></p>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>


    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const issueId = {{ $issue->id }};

            function updateAvailableTagsMessage() {
                const container = document.getElementById('available-tags');
                const availableTags = container.querySelectorAll('.available-tag');

                // Remove existing message
                const existingMessage = container.querySelector('.no-tags-msg');
                if (existingMessage) existingMessage.remove();

                if (availableTags.length === 0) {
                    const msg = document.createElement('p');
                    msg.className = 'text-gray-500 no-tags-msg';
                    msg.innerText = 'No available tags.';
                    container.appendChild(msg);
                }
            }

            function attachTag(tagId, el) {
                fetch(`/issues/${issueId}/attach-tag`, {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}','Accept':'application/json','Content-Type': 'application/json'},
                    body: JSON.stringify({ tag_id: tagId })
                })
                    .then(res => res.json())
                    .then(data => {
                        if(data.status==='success'){
                            document.getElementById('attached-tags').appendChild(el);
                            el.classList.remove('available-tag');
                            el.classList.add('attached-tag');

                            updateAvailableTagsMessage();
                        }
                    });
            }


            function detachTag(tagId, el) {
                fetch(`/issues/${issueId}/detach-tag/${tagId}`, {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}','Accept':'application/json'}
                })
                    .then(res => res.json())
                    .then(data => {
                        if(data.status==='success'){

                            document.getElementById('available-tags').appendChild(el);
                            el.classList.remove('attached-tag');
                            el.classList.add('available-tag');

                            updateAvailableTagsMessage();
                        }
                    });
            }

            document.body.addEventListener('click', e => {
                if(e.target.classList.contains('available-tag')){
                    const tagId = e.target.dataset.id;
                    attachTag(tagId, e.target);
                } else if(e.target.classList.contains('attached-tag')){
                    const tagId = e.target.dataset.id;
                    detachTag(tagId, e.target);
                }
            });

            // Add Comment
            const commentForm = document.getElementById('comment-form');
            const commentError = document.getElementById('comment-error');
            commentForm.addEventListener('submit', async (e)=>{
                e.preventDefault();
                commentError.classList.add('hidden');
                const formData = new FormData(commentForm);
                try {
                    const res = await fetch(`/issues/${issueId}/comments`, {
                        method: 'POST',
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}','Accept':'application/json'},
                        body: formData
                    });
                    const data = await res.json();
                    if(data.status==='success'){
                        // append comment
                        const div = document.createElement('div');
                        div.classList.add('border','p-4','rounded');
                        div.innerHTML = `<strong>${data.comment.author_name}</strong>
                                 <p>${data.comment.body}</p>
                                 <small class="text-gray-500">just now</small>`;
                        document.getElementById('comments-list').prepend(div);
                        commentForm.reset();
                    } else if(data.errors){
                        commentError.innerText = Object.values(data.errors).flat().join(', ');
                        commentError.classList.remove('hidden');
                    }
                } catch(err){ console.error(err); commentError.innerText='Something went wrong'; commentError.classList.remove('hidden'); }
            });

            // Load More Comments
            let nextPage = 2;
            const loadMoreBtn = document.getElementById('load-more-comments');
            if(loadMoreBtn){
                loadMoreBtn.addEventListener('click', async ()=>{
                    try{
                        const res = await fetch(`/issues/${issueId}/comments?page=${nextPage}`);
                        const data = await res.text(); // could return HTML partial
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(data, 'text/html');
                        const newComments = doc.querySelectorAll('#comments-list > div');
                        newComments.forEach(c => document.getElementById('comments-list').appendChild(c));
                        nextPage++;
                        if(!doc.querySelector('#load-more-comments')) loadMoreBtn.remove();
                    } catch(err){ console.error(err); }
                });
            }

        });
    </script>
@endsection
