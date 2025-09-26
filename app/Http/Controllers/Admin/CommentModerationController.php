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
        $this->middleware('can:moderate comments');
    }

    /**
     * Список комментариев для модерации
     */
    public function index(Request $request)
    {
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
            'moderation_notes' => 'required|min:5|max:500'
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
            'action' => 'required|in:approve,reject,delete'
        ]);

        $count = 0;

        foreach ($request->comment_ids as $commentId) {
            $comment = Comment::find($commentId);

            switch ($request->action) {
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

        return back()->with('success', "Обработано комментариев: {$count}");
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

