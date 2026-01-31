<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
    }

    /**
     * Отображение комментариев для поста
     */
    public function index(Post $post)
    {
        $comments = $post->comments()
            ->with(['user', 'replies.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($comments);
    }

    /**
     * Сохранение нового комментария
     */
    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'body' => 'required|min:3|max:5000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        // Автоматическая модерация // 26/09/2025
        $status = $this->determineCommentStatus($validated['body']);

        $comment = new Comment;
        $comment->body = $validated['body'];
        $comment->user_id = Auth::id();
        $comment->post_id = $post->id;
        $comment->status = $status; // 26/09/2025

        if ($request->has('parent_id')) {
            $comment->parent_id = $validated['parent_id'];
        }

        $comment->save();
        $comment->load('user');

        // 26/09/2025
        return back()->with('success', 'Комментарий добавлен! '.
            ($status === 'pending' ? 'Он будет опубликован после проверки модератором.' : ''));
    }

    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $validated = $request->validate([
            'body' => 'required|min:3|max:5000',
        ]);

        $comment->update($validated);

        // Пересоздаем HTML после обновления
        $converter = new \League\CommonMark\CommonMarkConverter;
        $bodyHtml = $converter->convertToHtml($comment->body)->getContent();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Комментарий обновлен!',
                'comment' => [
                    'id' => $comment->id,
                    'body' => $comment->body,
                    'body_html' => $bodyHtml,
                ],
            ]);
        }

        return back()->with('success', 'Комментарий обновлен!');
    }

    /**
     * Удаление комментария (мягкое удаление)
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        if (request()->ajax()) {
            return response()->json(['message' => 'Комментарий удален']);
        }

        return back()->with('success', 'Комментарий удален!');
    }

    // 26/09/2025

    /**
     * Метод определения статуса комментария
     */
    private function determineCommentStatus(string $body): string
    {
        // Автоматическое одобрение для доверенных пользователей
        $user = User::find(Auth::id());
        if ($user->isTrusted()) {
            return Comment::STATUS_APPROVED;
        }

        // Проверка на спам
        $spamWords = config('moderation.spam_words', []);
        $lowerBody = strtolower($body);

        foreach ($spamWords as $word) {
            if (str_contains($lowerBody, strtolower($word))) {
                return Comment::STATUS_PENDING;
            }
        }

        // Проверка количества ссылок
        $linkCount = preg_match_all('/https?:\/\/[^\s]+/', $body);
        if ($linkCount > config('moderation.limits.max_links_per_comment', 3)) {
            return Comment::STATUS_PENDING;
        }

        // По умолчанию - на модерацию
        return config('moderation.auto_approve.enabled', false) ?
            Comment::STATUS_APPROVED : Comment::STATUS_PENDING;
    }
}
