@php
    $maxDepth = 5; // Максимальная глубина вложенности
@endphp

<div class="comment-item mb-4 p-4 bg-white rounded-lg shadow" id="comment-{{ $comment->id }}">
    <div class="comment-header flex justify-between items-start mb-2">
        <div class="comment-author flex items-center">
            <span class="font-semibold text-gray-900">{{ $comment->user->name }}</span>
            <span class="text-gray-500 text-sm ml-2">
                {{ $comment->created_at->diffForHumans() }}
            </span>
        </div>

        @auth
            <div class="comment-actions flex space-x-2">
                @if($depth < $maxDepth)
                    <button class="reply-btn text-blue-500 text-sm hover:text-blue-700"
                            data-comment-id="{{ $comment->id }}">
                        Ответить
                    </button>
                @endif

                @can('update', $comment)
                    <button class="edit-btn text-green-500 text-sm hover:text-green-700"
                            data-comment-id="{{ $comment->id }}">
                        Редактировать
                    </button>
                @endcan

                @can('delete', $comment)
                    <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="text-red-500 text-sm hover:text-red-700"
                                onclick="return confirm('Удалить комментарий?')">
                            Удалить
                        </button>
                    </form>
                @endcan
            </div>
        @endauth
    </div>

    <div class="comment-body mb-3">
        <p class="text-gray-800">{{ $comment->body }}</p>
    </div>

    <!-- Форма ответа -->
    @auth
        @if($depth < $maxDepth)
            <div id="reply-form-{{ $comment->id }}" class="comment-reply-form">
                <form action="{{ route('comments.store', $comment->post) }}" method="POST" class="reply-form">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">

                    <div class="mb-2">
                        <textarea name="body" rows="2"
                                  class="w-full border border-gray-300 rounded p-2 text-sm"
                                  placeholder="Ваш ответ..."></textarea>
                    </div>

                    <div class="flex space-x-2">
                        <button type="submit"
                                class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                            Отправить
                        </button>
                        <button type="button"
                                class="cancel-reply bg-gray-300 text-gray-700 px-3 py-1 rounded text-sm hover:bg-gray-400">
                            Отмена
                        </button>
                    </div>
                </form>
            </div>
        @endif
    @endauth

    <!-- Ответы на комментарий -->
    @if($comment->hasReplies() && $depth < $maxDepth)
        <div class="comment-replies mt-3">
            @foreach($comment->replies as $reply)
                @include('posts._comment', ['comment' => $reply, 'depth' => $depth + 1])
            @endforeach
        </div>
    @endif
</div>
