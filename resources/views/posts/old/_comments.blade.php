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
            <a href="{{ route('admin.login') }}" class="bg-info hover:underline">Войдите</a>,
            чтобы оставить комментарий
        </p>
    @endauth

    <!-- Список комментариев -->
    <div class="comments-list">
        @foreach($post->comments as $comment)
            @include('posts._comment', ['comment' => $comment, 'depth' => 0])
        @endforeach

        @if($post->comments->isEmpty())
            <p class="text-secondary">Пока нет комментариев. Будьте первым!</p>
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

    [type=button]:not(:disabled), [type=reset]:not(:disabled), [type=submit]:not(:disabled), button:not(:disabled)
    {
        background-color: #6464df;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Безопасное получение CSRF-токена
        function getCsrfToken() {
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            return metaTag ? metaTag.getAttribute('content') : '';
        }

        const csrfToken = getCsrfToken();

        if (!csrfToken) {
            console.warn('CSRF token not found. Forms will submit normally.');
        }

        // Обработка ответов на комментарии
        document.querySelectorAll('.reply-btn').forEach(button => {
            button.addEventListener('click', function() {
                const commentId = this.dataset.commentId;
                const form = document.getElementById(`reply-form-${commentId}`);
                if (form) {
                    form.classList.toggle('reply-form-active');
                    form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            });
        });

        // Обработка отмены ответа
        document.querySelectorAll('.cancel-reply').forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('.comment-reply-form');
                if (form) {
                    form.classList.remove('reply-form-active');
                }
            });
        });

        // AJAX отправка форм комментариев
        document.querySelectorAll('.comment-form form').forEach(form => {
            form.addEventListener('submit', async function(e) {
                // Если нет CSRF токена, отправляем форму обычным способом
                if (!csrfToken) {
                    return true; // Позволяем браузеру отправить форму стандартно
                }

                e.preventDefault();

                const formData = new FormData(this);
                const url = this.action;
                const submitButton = this.querySelector('button[type="submit"]');

                // Блокируем кнопку на время отправки
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.textContent = 'Отправка...';
                }

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        // Успешная отправка - перезагружаем страницу
                        window.location.reload();
                    } else {
                        // Ошибка сервера
                        const errorText = await response.text();
                        console.error('Server error:', errorText);
                        alert('Ошибка при отправке комментария. Попробуйте еще раз.');
                    }
                } catch (error) {
                    console.error('Network error:', error);
                    alert('Ошибка сети. Проверьте подключение к интернету.');
                } finally {
                    // Разблокируем кнопку
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Оставить комментарий';
                    }
                }
            });
        });
    });
</script>
<!--<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Проверяем, существует ли элемент meta с csrf-token
        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        if (!csrfTokenMeta) {
            console.error('CSRF token meta tag not found');
            return;
        }

        // Обработка ответов на комментарии
        document.querySelectorAll('.reply-btn').forEach(button => {
            button.addEventListener('click', function() {
                const commentId = this.dataset.commentId;
                const form = document.getElementById(`reply-form-${commentId}`);
                if (form) {
                    form.classList.toggle('reply-form-active');
                    form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
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
                            'X-CSRF-TOKEN': csrfTokenMeta.content
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        // Вместо перезагрузки, можно динамически добавить комментарий
                        // Но для простоты пока перезагрузим страницу
                        location.reload();
                    } else {
                        console.error('Server response not OK');
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            });
        });
    });
</script>-->
<!--<script>
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
</script>-->
