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

                    <!-- Список комментариев -->
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
                                                    <input type="checkbox" name="comment_ids[]" value="{{ $comment->id }}" class="comment-checkbox">
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
                                                    <a href="{{ route('posts.show', $comment->post) }}" target="_blank">
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
                                                            <form action="{{ route('admin.comments.approve', $comment) }}"
                                                                  method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success" title="Одобрить">
                                                                    ✓
                                                                </button>
                                                            </form>
                                                            <button type="button" class="btn btn-danger reject-btn"
                                                                    data-comment-id="{{ $comment->id }}" title="Отклонить">
                                                                ✗
                                                            </button>
                                                        @endif
                                                        <form action="{{ route('comments.destroy', $comment) }}"
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-dark"
                                                                    onclick="return confirm('Удалить комментарий?')" title="Удалить">
                                                                🗑️
                                                            </button>
                                                        </form>
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
@endsection

@section('scripts')
    <script>
        // Пакетные действия
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.comment-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            toggleBulkActions();
        });

        document.querySelectorAll('.comment-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', toggleBulkActions);
        });

        function toggleBulkActions() {
            const checkedCount = document.querySelectorAll('.comment-checkbox:checked').length;
            const bulkActions = document.querySelector('.bulk-actions');
            bulkActions.style.display = checkedCount > 0 ? 'block' : 'none';
        }

        function setBulkAction(action) {
            document.getElementById('bulk-action').value = action;
            if (confirm(`Вы уверены, что хотите ${action} выбранные комментарии?`)) {
                document.getElementById('bulk-form').submit();
            }
        }

        function clearSelection() {
            document.querySelectorAll('.comment-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            toggleBulkActions();
        }

        // Отклонение комментария
        document.querySelectorAll('.reject-btn').forEach(button => {
            button.addEventListener('click', function() {
                const commentId = this.dataset.commentId;
                const form = document.getElementById('reject-form');
                form.action = `/admin/comments/${commentId}/reject`;
                $('#rejectModal').modal('show');
            });
        });
    </script>
@endsection

