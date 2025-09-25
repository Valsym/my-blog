<div class="comments-section mt-5">
    <h3 class="h4 mb-3">Комментарии ({{ $post->comments->count() }})</h3>

    <!-- Форма добавления комментария -->
    @auth
        <div class="comment-form mb-4">
            <form action="{{ route('comments.store', $post) }}" method="POST" class="new-comment-form">
                @csrf
                <input type="hidden" name="parent_id" value="">

                <div class="form-group">
                    <label for="body" class="font-weight-bold">Ваш комментарий</label>
                    <textarea name="body" id="body" rows="3"
                              class="form-control"
                              placeholder="Напишите ваш комментарий..."></textarea>
                    @error('body')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    Оставить комментарий
                </button>
            </form>
        </div>
    @else
        <p class="mb-3">
            <a href="{{ route('login') }}" class="text-primary">Войдите</a>,
            чтобы оставить комментарий
        </p>
    @endauth

    <!-- Список комментариев -->
    <div class="comments-list">
        @foreach($post->comments as $comment)
            @include('posts._comment', ['comment' => $comment, 'depth' => 0])
        @endforeach

        @if($post->comments->isEmpty())
            <p class="text-muted">Пока нет комментариев. Будьте первым!</p>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Обработка ответов на комментарии
        document.querySelectorAll('.reply-btn').forEach(button => {
            button.addEventListener('click', function() {
                const commentId = this.dataset.commentId;
                const form = document.getElementById(`reply-form-${commentId}`);
                if (form) {
                    form.style.display = form.style.display === 'none' ? 'block' : 'none';
                    form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            });
        });

        // Обработка отмены ответа
        document.querySelectorAll('.cancel-reply').forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('.comment-reply-form');
                if (form) {
                    form.style.display = 'none';
                }
            });
        });

        // Простая отправка форм (перезагрузка страницы после успеха)
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                // Можно добавить AJAX здесь, но пока оставляем стандартное поведение
                console.log('Form submitted:', this.action);
            });
        });
    });
</script>
