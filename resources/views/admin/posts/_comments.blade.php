<div class="comments-section mt-8">
    <h3 class="text-xl font-bold mb-4">Комментарии ({{ $post->comments->count() }})</h3>

    <!-- Форма добавления комментария -->
    @auth
        <div class="comment-form mb-6">
            <form action="{{ route('comments.store', $post) }}" method="POST" class="new-comment-form">
                @csrf
                <input type="hidden" name="parent_id" value="">

                <div class="mb-3">
                    <label for="body" class="block text-sm font-medium text-gray-700">Ваш комментарий</label>
                    <textarea name="body" id="body" rows="3"
                              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                              placeholder="Напишите ваш комментарий..."></textarea>
                    @error('body')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit"
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Оставить комментарий
                </button>
            </form>
        </div>
    @else
        <p class="mb-4">
            <a href="{{ route('login') }}" class="text-blue-500 hover:underline">Войдите</a>,
            чтобы оставить комментарий
        </p>
    @endauth

    <!-- Список комментариев -->
    <div class="comments-list">
        @foreach($post->comments as $comment)
            @include('posts._comment', ['comment' => $comment, 'depth' => 0])
        @endforeach

        @if($post->comments->isEmpty())
            <p class="text-gray-500">Пока нет комментариев. Будьте первым!</p>
        @endif
    </div>
</div>

<style>
    .comment-reply-form {
        display: none;
        margin-top: 10px;
    }

    .reply-form-active {
        display: block;
    }

    .comment-replies {
        margin-left: 40px;
        border-left: 2px solid #e2e8f0;
        padding-left: 20px;
    }

    .comment-actions {
        opacity: 0;
        transition: opacity 0.2s;
    }

    .comment-item:hover .comment-actions {
        opacity: 1;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Обработка ответов на комментарии
        document.querySelectorAll('.reply-btn').forEach(button => {
            button.addEventListener('click', function() {
                const commentId = this.dataset.commentId;
                const form = document.getElementById(`reply-form-${commentId}`);
                form.classList.toggle('reply-form-active');

                // Прокрутка к форме ответа
                form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            });
        });

        // AJAX отправка форм
        document.querySelectorAll('.comment-form form').forEach(form => {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const url = this.action;

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        location.reload(); // или динамическое добавление комментария
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            });
        });
    });
</script>
