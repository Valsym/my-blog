@extends('layouts.admin')

@section('title', 'Просмотр комментария #' . $comment->id)

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Хлебные крошки -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.comments.index') }}">Модерация комментариев</a></li>
                        <li class="breadcrumb-item active">Комментарий #{{ $comment->id }}</li>
                    </ol>
                </nav>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Комментарий #{{ $comment->id }}</h4>
                        <div class="status-badge">
                        <span class="badge badge-{{ $comment->status === 'approved' ? 'success' : ($comment->status === 'rejected' ? 'danger' : 'warning') }}">
                            {{ $comment->status }}
                        </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Основная информация -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Информация о комментарии</h5>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="120">Автор:</th>
                                        <td>
                                            <strong>{{ $comment->user->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $comment->user->email }}</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Статья:</th>
                                        <td>
                                            <a href="{{ route('posts.show', $comment->post) }}" target="_blank">
                                                {{ $comment->post->title }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Дата создания:</th>
                                        <td>{{ $comment->created_at->format('d.m.Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Дата обновления:</th>
                                        <td>{{ $comment->updated_at->format('d.m.Y H:i') }}</td>
                                    </tr>
                                    @if($comment->is_edited)
                                        <tr>
                                            <th>Редактирован:</th>
                                            <td class="text-warning">Да</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>

                            <div class="col-md-6">
                                <h5>Модерация</h5>
                                <table class="table table-sm table-borderless">
                                    @if($comment->moderated_by)
                                        <tr>
                                            <th width="120">Модератор:</th>
                                            <td>{{ $comment->moderator->name ?? 'Неизвестно' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Дата модерации:</th>
                                            <td>{{ $comment->moderated_at->format('d.m.Y H:i') }}</td>
                                        </tr>
                                    @endif
                                    @if($comment->moderation_notes)
                                        <tr>
                                            <th>Примечания:</th>
                                            <td class="text-danger">{{ $comment->moderation_notes }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        <!-- Содержимое комментария -->
                        <div class="mb-4">
                            <h5>Содержимое комментария</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p class="mb-0">{{ $comment->body }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Действия -->
                        <div class="mb-4">
                            <h5>Действия</h5>
                            <div class="btn-group">
                                @if($comment->isPending())
                                    <form action="{{ route('admin.comments.approve', $comment) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check"></i> Одобрить
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-danger reject-btn"
                                            data-comment-id="{{ $comment->id }}">
                                        <i class="fas fa-times"></i> Отклонить
                                    </button>
                                @endif

                                <a href="{{ route('admin.comments.index', ['status' => $comment->status]) }}"
                                   class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Назад к списку
                                </a>

                                <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-dark"
                                            onclick="return confirm('Удалить комментарий?')">
                                        <i class="fas fa-trash"></i> Удалить
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Ответы на комментарий -->
                        @if($comment->replies->count() > 0)
                            <div class="mb-4">
                                <h5>Ответы на комментарий ({{ $comment->replies->count() }})</h5>
                                <div class="replies-list">
                                    @foreach($comment->replies as $reply)
                                        <div class="card mb-2">
                                            <div class="card-body py-2">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <strong>{{ $reply->user->name }}</strong>
                                                        <small class="text-muted ml-2">
                                                            {{ $reply->created_at->format('d.m.Y H:i') }}
                                                        </small>
                                                        <span class="badge badge-{{ $reply->status === 'approved' ? 'success' : ($reply->status === 'rejected' ? 'danger' : 'warning') }} ml-2">
                                                {{ $reply->status }}
                                            </span>
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('admin.comments.show', $reply) }}"
                                                           class="btn btn-sm btn-info">Просмотр</a>
                                                    </div>
                                                </div>
                                                <p class="mb-0 mt-1">{{ Str::limit($reply->body, 150) }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
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
                                      class="form-control" rows="4" placeholder="Укажите причину отклонения..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-danger">Отклонить комментарий</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Обработка кнопки отклонения
            document.querySelectorAll('.reject-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const commentId = this.dataset.commentId;
                    const form = document.getElementById('reject-form');
                    if (form) {
                        form.action = "{{ route('admin.comments.reject', ':id') }}".replace(':id', commentId);
                        $('#rejectModal').modal('show');
                    }
                });
            });
        });
    </script>

    <style>
        .table-borderless td, .table-borderless th {
            border: none;
            padding: 0.3rem 0;
        }

        .status-badge .badge {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
        }

        .replies-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
    </style>
@endsection


