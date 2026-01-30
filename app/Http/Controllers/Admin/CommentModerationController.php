<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentModerationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Список комментариев для модерации
     */
    public function index(Request $request)
    {
        // Временная проверка
        if (! auth()->user()->is_admin && ! auth()->user()->is_moderator) {
            abort(403, 'Access denied.');
        }

        $status = $request->get('status', 'pending');
        $perPage = 20;

        $comments = Comment::with(['user', 'post', 'moderator'])
            ->when($status !== 'all', function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $stats = [
            'pending' => Comment::pending()->count(),
            'approved' => Comment::approved()->count(),
            'rejected' => Comment::rejected()->count(),
            'total' => Comment::count(),
        ];

        return view('admin.comments.index', compact('comments', 'stats', 'status'));
    }

    /**
     * Одобрение комментария
     */
    public function approve(Comment $comment)
    {
        $comment->update([
            'status' => Comment::STATUS_APPROVED,
            'moderated_by' => Auth::id(),
            'moderated_at' => now(),
        ]);

        // Можно добавить уведомление пользователю

        return back()->with('success', 'Комментарий одобрен');
    }

    /**
     * Отклонение комментария
     */
    public function reject(Request $request, Comment $comment)
    {
        $request->validate([
            'moderation_notes' => 'required|min:5|max:500',
        ]);

        $comment->update([
            'status' => Comment::STATUS_REJECTED,
            'moderation_notes' => $request->moderation_notes,
            'moderated_by' => Auth::id(),
            'moderated_at' => now(),
        ]);

        return back()->with('success', 'Комментарий отклонен');
    }

    /**
     * Пакетная модерация
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'comment_ids' => 'required|array',
            'comment_ids.*' => 'exists:comments,id',
            'action' => 'required|in:approve,reject,delete',
        ]);

        $count = 0;
        $action = $request->action;

        foreach ($request->comment_ids as $commentId) {
            $comment = Comment::find($commentId);

            switch ($action) {
                case 'approve':
                    $comment->update([
                        'status' => Comment::STATUS_APPROVED,
                        'moderated_by' => Auth::id(),
                        'moderated_at' => now(),
                    ]);
                    $count++;
                    break;

                case 'reject':
                    $comment->update([
                        'status' => Comment::STATUS_REJECTED,
                        'moderation_notes' => 'Пакетное отклонение',
                        'moderated_by' => Auth::id(),
                        'moderated_at' => now(),
                    ]);
                    $count++;
                    break;

                case 'delete':
                    $comment->delete();
                    $count++;
                    break;
            }
        }

        $message = match ($action) {
            'approve' => "Одобрено комментариев: {$count}",
            'reject' => "Отклонено комментариев: {$count}",
            'delete' => "Удалено комментариев: {$count}",
            default => "Обработано комментариев: {$count}"
        };

        return back()->with('success', $message);
    }

    /**
     * Просмотр комментария
     */
    public function show(Comment $comment)
    {
        $comment->load(['user', 'post', 'moderator', 'replies.user']);

        return view('admin.comments.show', compact('comment'));
    }
}
