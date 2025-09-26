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
                <p><small class="text-muted">
                    <strong>Поддерживается Markdown:</strong><br>
                    **жирный текст**<br>
                    *курсив*<br>
                    `код`<br>
                    [ссылка](https://example.com)<br>
                    > цитата
                </small></p>
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

<!-- Modal for editing comment -->
<div class="modal fade" id="editCommentModal" tabindex="-1" role="dialog" aria-labelledby="editCommentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCommentModalLabel">Редактировать комментарий</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editCommentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editCommentBody">Комментарий</label>
                        <textarea class="form-control" id="editCommentBody" name="body" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                </div>
            </form>
        </div>
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

        // ОБРАБОТКА РЕДАКТИРОВАНИЯ КОММЕНТАРИЕВ
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const commentId = this.dataset.commentId;
                const commentBody = this.closest('.comment-item')
                    .querySelector('.comment-body p').textContent;
                const editUrl = "{{ route('comments.update', ':id') }}".replace(':id', commentId);

                // Заполняем форму редактирования
                document.getElementById('editCommentBody').value = commentBody.trim();
                document.getElementById('editCommentForm').action = editUrl;

                // Показываем модальное окно
                //$('#editCommentModal').modal('show');
            });
        });

        // Обработка отправки формы редактирования
        /*document.getElementById('editCommentForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);
            const url = form.action;

            // Блокируем кнопку отправки
            const submitButton = form.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.textContent = 'Сохранение...';

            fetch(url, {
                method: 'POST', // Laravel ожидает POST для PUT через метод spoofing
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    }
                    throw new Error('Network response was not ok.');
                })
                .then(data => {
                    // Закрываем модальное окно
                    $('#editCommentModal').modal('hide');

                    // Обновляем комментарий на странице
                    const commentElement = document.querySelector(`#comment-${data.comment.id} .comment-body p`);
                    if (commentElement) {
                        commentElement.textContent = data.comment.body;
                    }

                    // Показываем сообщение об успехе
                    alert('Комментарий успешно обновлен!');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ошибка при обновлении комментария.');
                })
                .finally(() => {
                    // Разблокируем кнопку
                    submitButton.disabled = false;
                    submitButton.textContent = 'Сохранить изменения';
                });
        });*/
    });
</script>
<!--<script>
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

<script>
    // 2. Добавим JavaScript для обработки нажатия на кнопку "Редактировать":
    document.addEventListener('DOMContentLoaded', function() {
        // Обработка кнопки редактирования
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const commentId = this.dataset.commentId;
                const commentBody = document.querySelector(`#comment-${commentId} .comment-body p`).textContent;

                // Заполняем форму модального окна
                document.getElementById('editCommentBody').value = commentBody;
                document.getElementById('editCommentForm').action = `/comments/${commentId}`;

                // Показываем модальное окно
                $('#editCommentModal').modal('show');
            });
        });

        // Обработка отправки формы редактирования через AJAX
        document.getElementById('editCommentForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);
            const url = form.action;

            fetch(url, {
                method: 'POST', // Но мы используем метод PUT, поэтому добавим _method
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Закрываем модальное окно
                        $('#editCommentModal').modal('hide');
                        // Обновляем комментарий на странице
                        const commentElement = document.querySelector(`#comment-${data.comment.id} .comment-body p`);
                        commentElement.textContent = data.comment.body;
                        // Можно показать сообщение об успехе
                        alert('Комментарий обновлен!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ошибка при обновлении комментария.');
                });
        });
    });
</script>
-->
