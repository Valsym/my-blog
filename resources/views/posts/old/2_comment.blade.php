@php
    $maxDepth = 5; // Максимальная глубина вложенности
@endphp

<div class="comment-item mb-3 p-3 bg-light rounded" id="comment-{{ $comment->id }}">
    <div class="comment-header d-flex justify-content-between align-items-start mb-2">
        <div class="comment-author">
            <span class="font-weight-bold text-dark">{{ $comment->user->name }}</span>
            <span class="text-muted small ml-2">
                {{ $comment->created_at->diffForHumans() }}
            </span>
        </div>

        @auth
            <div class="comment-actions d-flex" style="opacity: 0; transition: opacity 0.2s;">
                @if($depth < $maxDepth)
                    <button class="reply-btn btn btn-sm btn-outline-secondary mr-1"
                            data-comment-id="{{ $comment->id }}">
                        Ответить
                    </button>
                @endif

                @can('update', $comment)
                    <button class="edit-btn btn btn-sm btn-outline-success mr-1"
                            data-comment-id="{{ $comment->id }}">
                        Редактировать
                    </button>
                @endcan

                @can('delete', $comment)
                    <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('Удалить комментарий?')">
                            Удалить
                        </button>
                    </form>
                @endcan
            </div>
        @endauth
    </div>

    <div class="comment-body mb-2">
        <p class="mb-0">{{ $comment->body }}</p>
    </div>

    <!-- Форма ответа -->
    @auth
        @if($depth < $maxDepth)
            <div id="reply-form-{{ $comment->id }}" class="comment-reply-form mt-2" style="display: none;">
                <form action="{{ route('comments.store', $comment->post) }}" method="POST" class="reply-form">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">

                    <div class="form-group mb-2">
                        <textarea name="body" rows="2"
                                  class="form-control form-control-sm"
                                  placeholder="Ваш ответ..."></textarea>
                    </div>

                    <div class="d-flex">
                        <button type="submit" class="btn btn-sm btn-primary mr-1">
                            Отправить
                        </button>
                        <button type="button" class="cancel-reply btn btn-sm btn-secondary">
                            Отмена
                        </button>
                    </div>
                </form>
            </div>
        @endif
    @endauth

    <!-- Ответы на комментарий -->
    @if($comment->hasReplies() && $depth < $maxDepth)
        <div class="comment-replies mt-3 ml-4 pl-3 border-left">
            @foreach($comment->replies as $reply)
                @include('posts._comment', ['comment' => $reply, 'depth' => $depth + 1])
            @endforeach
        </div>
    @endif
</div>

<style>
    .comment-item:hover .comment-actions {
        opacity: 1 !important;
    }

    .comment-replies {
        border-left: 2px solid #dee2e6 !important;
    }
</style>
