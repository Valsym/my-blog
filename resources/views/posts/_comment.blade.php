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
            <!-- Форма редактирования (изначально скрыта) -->
            <div id="edit-form-{{ $comment->id }}" style="display: none;" class="mt-2">
                <form action="{{ route('comments.update', $comment) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group mb-2">
                        <textarea name="body" rows="5" class="form-control">{{ $comment->body }}</textarea>
                    </div>
                    <div class="d-flex">
                        <button type="submit" class="btn btn-sm btn-primary mr-1">Сохранить</button>
                        <button type="button" class="cancel-edit btn btn-sm btn-secondary"
                                data-comment-id="{{ $comment->id }}">Отмена</button>
                    </div>
                </form>
            </div>
        @endauth
    </div>

    <!--<p class="comment-body mb-2">
        <p class="mb-0">{--!! nl2br(e($comment->body)) !!--}</p>
        <p class="mb-0"{--!! $comment->body_html !!--}</p>
    </div>-->
    <div class="comment-body mb-2" style="display: block;">
        <div class="comment-text">{!! $comment->body_html !!}</div>
        <!-- Добавляем скрытое поле с исходным текстом Markdown -->
        <textarea class="d-none raw-comment-body">{{ $comment->body }}</textarea>
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
                        <p><small class="text-muted">
                                <strong>Поддерживается Markdown:</strong><br>
                                **жирный текст**<br>
                                *курсив*<br>
                                `код`<br>
                                [ссылка](https://example.com)<br>
                                > цитата
                            </small>
                        </p>
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

<script>
    // Простой JavaScript без AJAX
    document.addEventListener('DOMContentLoaded', function() {
        // Обработка кнопки редактирования
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const commentId = this.dataset.commentId;
                const commentBody = this.closest('.comment-item').querySelector('.comment-body');
                const editForm = document.getElementById(`edit-form-${commentId}`);

                commentBody.style.display = 'none';
                editForm.style.display = 'block';
            });
        });

        // Обработка отмены редактирования
        document.querySelectorAll('.cancel-edit').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const commentId = this.dataset.commentId;
                const commentBody = this.closest('.comment-item').querySelector('.comment-body');
                const editForm = document.getElementById(`edit-form-${commentId}`);

                commentBody.style.display = 'block';
                editForm.style.display = 'none';
            });
        });
    });
</script>
<!--<script>
    // Обработка кнопки редактирования (inline версия)
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            const commentItem = this.closest('.comment-item');
            const commentBody = commentItem.querySelector('.comment-body');
            const editForm = document.getElementById(`edit-form-${commentId}`);
            const rawTextarea = commentItem.querySelector('.raw-comment-body');

            // Получаем исходный Markdown текст
            const rawBody = rawTextarea ? rawTextarea.value : '';

            // Заполняем текстовое поле в форме редактирования
            const textarea = editForm.querySelector('textarea[name="body"]');
            textarea.value = rawBody;

            // Скрываем тело комментария, показываем форму редактирования
            commentBody.style.display = 'none';
            editForm.style.display = 'block';
        });
    });

    // Обработка отмены редактирования
    document.querySelectorAll('.cancel-edit').forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            const commentItem = this.closest('.comment-item');
            const commentBody = commentItem.querySelector('.comment-body');
            const editForm = document.getElementById(`edit-form-${commentId}`);

            // Показываем тело комментария, скрываем форму
            commentBody.style.display = 'block';
            editForm.style.display = 'none';
        });
    });

    // Обработка отправки формы редактирования
    document.querySelectorAll('.edit-comment-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const url = this.action;

            // Блокируем кнопку отправки
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Сохранение...';

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    }
                    throw new Error('Ошибка сети');
                })
                .then(data => {
                    if (data.success) {
                        // Перезагружаем страницу
                        window.location.reload();
                        // Обновляем отображаемый комментарий
                        // const commentItem = document.querySelector(`#comment-${data.comment.id}`);
                        // const commentText = commentItem.querySelector('.comment-text');
                        // const rawTextarea = commentItem.querySelector('.raw-comment-body');
                        //
                        // commentText.innerHTML = data.comment.body_html;
                        // rawTextarea.value = data.comment.body;
                        //
                        // // Возвращаем к исходному виду
                        // const commentBody = commentItem.querySelector('.comment-body');
                        // const editForm = commentItem.querySelector(`#edit-form-${data.comment.id}`);
                        //
                        // commentBody.style.display = 'block';
                        // editForm.style.display = 'none';
                        //
                        // alert('Комментарий обновлен!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ошибка при обновлении комментария.');
                })
                .finally(() => {
                    // Разблокируем кнопку
                    submitButton.disabled = false;
                    submitButton.textContent = originalText;
                });
        });
    });

</script>-->
