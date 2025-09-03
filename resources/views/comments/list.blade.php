
<div id="comments-list" class="flex flex-col gap-2">
    @forelse($comments as $comment)
        <div class="comment-card border p-2 rounded bg-gray-50">
            <strong class="block text-gray-800 text-sm">{{ $comment->author_name }}</strong>
            <p class="mt-1 text-gray-700 text-sm">{{ $comment->body }}</p>
            <small class="text-gray-500 text-xs">{{ $comment->created_at->diffForHumans() }}</small>
        </div>
    @empty
        <p id="empty-comments" class="text-center text-gray-500">No comments yet</p>
    @endforelse
</div>

@if($comments->hasMorePages())
    <div class="mt-4 text-center">
        <button id="load-more-comments" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                data-next="{{ $comments->nextPageUrl() }}">
            Load More
        </button>
    </div>
@endif
