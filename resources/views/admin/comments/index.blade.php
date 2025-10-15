@extends('layouts.admin')

@section('title', 'Модерация комментариев')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1>Модерация комментариев</h1>

                <!-- Статистика -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h4>{{ $stats['pending'] }}</h4>
                                <p>Ожидают модерации</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h4>{{ $stats['approved'] }}</h4>
                                <p>Одобрено</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-danger">
                            <div class="card-body">
                                <h4>{{ $stats['rejected'] }}</h4>
                                <p>Отклонено</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <h4>{{ $stats['total'] }}</h4>
                                <p>Всего</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Фильтры -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="btn-group" role="group">
                            <a href="{{ route('admin.comments.index', ['status' => 'all']) }}"
                               class="btn btn-{{ $status === 'all' ? 'primary' : 'outline-primary' }}">Все</a>
                            <a href="{{ route('admin.comments.index', ['status' => 'pending']) }}"
                               class="btn btn-{{ $status === 'pending' ? 'warning' : 'outline-warning' }}">Ожидают ({{ $stats['pending'] }})</a>
                            <a href="{{ route('admin.comments.index', ['status' => 'approved']) }}"
                               class="btn btn-{{ $status === 'approved' ? 'success' : 'outline-success' }}">Одобрены</a>
                            <a href="{{ route('admin.comments.index', ['status' => 'rejected']) }}"
                               class="btn btn-{{ $status === 'rejected' ? 'danger' : 'outline-danger' }}">Отклонены</a>
                        </div>
                    </div>
                </div>

                <!-- Форма пакетных действий -->
                <form action="{{ route('admin.comments.bulk-action') }}" method="POST" id="bulk-form">
                    @csrf
                    <input type="hidden" name="action" id="bulk-action">

                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Комментарии</h5>
                                <div class="bulk-actions" style="display: none;">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-success btn-sm" onclick="setBulkAction('approve')">
                                            Одобрить выбранные
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="setBulkAction('reject')">
                                            Отклонить выбранные
                                        </button>
                                        <button type="button" class="btn btn-dark btn-sm" onclick="setBulkAction('delete')">
                                            Удалить выбранные
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="clearSelection()">
                                            Отмена
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($comments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                        <tr>
                                            <th width="50">
                                                <input type="checkbox" id="select-all">
                                            </th>
                                            <th>Комментарий</th>
                                            <th>Автор</th>
                                            <th>Статья</th>
                                            <th>Статус</th>
                                            <th>Дата</th>
                                            <th>Действия</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($comments as $comment)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="comment_ids[]"
                                                           value="{{ $comment->id }}" class="comment-checkbox">
                                                </td>
                                                <td>
                                                    <div class="comment-preview">
                                                        {{ Str::limit($comment->body, 100) }}
                                                        @if($comment->is_edited)
                                                            <span class="badge badge-info">ред.</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>{{ $comment->user->name }}</td>
                                                <td>
                                                    <a href="{{ route('public.posts.show', $comment->post) }}" target="_blank">
                                                        {{ Str::limit($comment->post->title, 30) }}
                                                    </a>
                                                </td>
                                                <td>
                                        <span class="badge badge-{{ $comment->status === 'approved' ? 'success' : ($comment->status === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ $comment->status }}
                                        </span>
                                                </td>
                                                <td>{{ $comment->created_at->format('d.m.Y H:i') }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('admin.comments.show', $comment) }}"
                                                           class="btn btn-info" title="Просмотр">
                                                            👁️
                                                        </a>
                                                        @if($comment->isPending())
                                                            <!-- Кнопка одобрения без формы -->
                                                            <button type="button" class="btn btn-success approve-single-btn"
                                                                    data-comment-id="{{ $comment->id }}" title="Одобрить">
                                                                ✓
                                                            </button>
                                                            <!-- Кнопка отклонения без формы -->
                                                            <button type="button" class="btn btn-danger reject-btn"
                                                                    data-comment-id="{{ $comment->id }}" title="Отклонить">
                                                                ✗
                                                            </button>
                                                        @endif
                                                        <!-- Кнопка удаления без формы -->
                                                        <button type="button" class="btn btn-dark delete-single-btn"
                                                                data-comment-id="{{ $comment->id }}" title="Удалить">
                                                            🗑️
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Пагинация -->
                                <div class="d-flex justify-content-center">
                                    {{ $comments->links() }}
                                </div>
                            @else
                                <p class="text-muted">Комментарии не найдены</p>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно для отклонения -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="reject-form" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Отклонить комментарий</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="moderation_notes">Причина отклонения</label>
                            <textarea name="moderation_notes" id="moderation_notes"
                                      class="form-control" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-danger">Отклонить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .bulk-actions {
            transition: all 0.3s ease;
        }

        .comment-checkbox {
            cursor: pointer;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0,0,0,.075);
        }

        #select-all {
            cursor: pointer;
        }
    </style>

@endsection

@section('scripts')
    <script>
        // Добавьте в начало скрипта для отладки
        // console.log('Script loaded');
        // console.log('Select All:', document.getElementById('select-all'));
        // console.log('Comment checkboxes:', document.querySelectorAll('.comment-checkbox').length);
        // console.log('Bulk actions:', document.querySelector('.bulk-actions'));
        // console.log('Bulk form:', document.getElementById('bulk-form'));

        document.addEventListener('DOMContentLoaded', function() {
            // Элементы DOM
            const selectAllCheckbox = document.getElementById('select-all');
            const commentCheckboxes = document.querySelectorAll('.comment-checkbox');
            const bulkActions = document.querySelector('.bulk-actions');
            const bulkForm = document.getElementById('bulk-form');
            const bulkActionInput = document.getElementById('bulk-action');

            // Функция для показа/скрытия блока пакетных действий
            function toggleBulkActions() {
                const checkedCount = document.querySelectorAll('.comment-checkbox:checked').length;
                if (bulkActions) {
                    bulkActions.style.display = checkedCount > 0 ? 'block' : 'none';
                }
            }

            // Обработчик для чекбокса "Выделить все"
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    const isChecked = this.checked;
                    commentCheckboxes.forEach(checkbox => {
                        checkbox.checked = isChecked;
                    });
                    toggleBulkActions();
                });
            }

            // Обработчики для отдельных чекбоксов
            commentCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    // Снимаем выделение с "Выделить все", если снята одна галочка
                    if (selectAllCheckbox && !this.checked) {
                        selectAllCheckbox.checked = false;
                    }
                    toggleBulkActions();
                });
            });

            // Функции для пакетных действий (делаем их глобальными)
            window.setBulkAction = function(action) {
                if (!bulkActionInput) return;

                const checkedCount = document.querySelectorAll('.comment-checkbox:checked').length;
                if (checkedCount === 0) {
                    alert('Пожалуйста, выберите хотя бы один комментарий');
                    return;
                }

                const actionText = {
                    'approve': 'одобрить',
                    'reject': 'отклонить',
                    'delete': 'удалить'
                }[action] || 'выполнить действие с';

                if (confirm(`Вы уверены, что хотите ${actionText} выбранные комментарии (${checkedCount} шт.)?`)) {
                    bulkActionInput.value = action;
                    bulkForm.submit();
                }
            }

            window.clearSelection = function() {
                // Снимаем все галочки
                if (selectAllCheckbox) selectAllCheckbox.checked = false;
                commentCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                toggleBulkActions();
            }

            // Инициализация - скрываем блок пакетных действий при загрузке
            toggleBulkActions();

            // Обработка модального окна отклонения (если еще не добавлено)
            document.querySelectorAll('.reject-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const commentId = this.dataset.commentId;
                    const form = document.getElementById('reject-form');
                    if (form) {
                        form.action = `/admin/comments/${commentId}/reject`;
                        $('#rejectModal').modal('show');
                    }
                });
            });
        });

        // Обработка одиночного одобрения
        document.querySelectorAll('.approve-single-btn').forEach(button => {
            button.addEventListener('click', function() {
                const commentId = this.dataset.commentId;
                if (confirm('Одобрить этот комментарий?')) {
                    // Создаем временную форму для отправки
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/comments/${commentId}/approve`;

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;

                    form.appendChild(csrfToken);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });

        // Обработка одиночного удаления
        document.querySelectorAll('.delete-single-btn').forEach(button => {
            button.addEventListener('click', function() {
                const commentId = this.dataset.commentId;
                if (confirm('Удалить этот комментарий?')) {
                    // Создаем временную форму для отправки
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/comments/${commentId}`;

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;

                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';

                    form.appendChild(csrfToken);
                    form.appendChild(methodInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });

        // Обработка модального окна отклонения (уже есть, но на всякий случай оставляем)
        document.querySelectorAll('.reject-btn').forEach(button => {
            button.addEventListener('click', function() {
                const commentId = this.dataset.commentId;
                const form = document.getElementById('reject-form');
                if (form) {
                    form.action = `/admin/comments/${commentId}/reject`;
                    $('#rejectModal').modal('show');
                }
            });
        });
    </script>


@endsection

