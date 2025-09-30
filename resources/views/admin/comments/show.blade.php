@extends('layouts.admin')

@section('title', 'Модерация комментария')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1>Модерация комментария</h1>


                <!-- Секция комментария -->
                @include('posts._comment', ['comment' => $comment, 'depth' => 0]);


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

