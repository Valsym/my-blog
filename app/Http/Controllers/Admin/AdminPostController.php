<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminPostController extends Controller
{
    /**
     * Фильтрация и сортировка постов в админке
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $query = Post::with(['categories', 'tags', 'user']);

        // Применяем фильтры через scope-методы
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('categories')) {
            $query->byCategories($request->categories);
        }

        if ($request->filled('tags')) {
            $query->byTags($request->tags);
        }

        if ($request->filled('date_from')) {
            $query->dateFrom($request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->dateTo($request->date_to);
        }

        // Сортировка
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');
        $query->orderBy($sort, $order);

        $posts = $query->paginate(10)->appends($request->except('page'));

        $categories = Category::all();
        $tags = Tag::all();

        return view('admin.posts.index', compact('posts', 'categories', 'tags'));
    }

    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('admin.posts.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        // Декодируем HTML-сущности перед валидацией
        $decodedContent = html_entity_decode($request->input('content'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $request->merge(['content' => $decodedContent]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'published' => 'required|in:published,moderation,draft',
            'created_at' => 'nullable|date',
            'updated_at' => 'nullable|date',
        ]);

        // Генерация slug
        $slug = Str::slug($request->title);
        $originalSlug = $slug;
        $count = 1;

        while (Post::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$count++;
        }

        $post = Post::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'slug' => $slug,
            'excerpt' => $validated['excerpt'],
            'user_id' => Auth::id(),
            'published' => $validated['published'],
            'created_at' => $validated['created_at'] ?? now(),
            'updated_at' => $validated['updated_at'] ?? now(),
        ]);

        // Привязываем категории и теги
        if ($request->has('categories')) {
            $post->categories()->sync($request->categories);
        }

        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        return redirect()->route('admin.posts.index')
            ->with('success', 'Пост успешно создан!');
    }

    public function edit(Post $post)
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('admin.posts.create'/* edit' */, compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, Post $post)
    {
        // Декодируем HTML-сущности перед валидацией
        $decodedContent = html_entity_decode($request->input('content'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $request->merge(['content' => $decodedContent]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'published' => 'required|in:published,moderation,draft',
            'created_at' => 'nullable|date',
            'updated_at' => 'nullable|date',
        ]);

        // Явно устанавливаем значения
        $post->title = $validated['title'];
        $post->content = $validated['content']; // ← явное присвоение
        $post->excerpt = $validated['excerpt'];
        $post->published = $validated['published'];
        $post->created_at = $validated['created_at'];
        $post->updated_at = $validated['updated_at'];

        $post->save(); // ← используем save() вместо update()

        // Синхронизируем категории и теги
        $post->categories()->sync($request->input('categories', []));
        $post->tags()->sync($request->input('tags', []));

        return redirect()->route('admin.posts.index')
            ->with('success', 'Пост успешно обновлен!');
    }

    public function destroy(Post $post)
    {
        $post->categories()->detach();
        $post->tags()->detach();
        $post->delete();

        return redirect()->route('admin.posts.index')
            ->with('success', 'Пост успешно удален!');
    }

    public function upload(Request $request)
    {

        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        try {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = Str::random(20).'_'.time().'.'.$file->getClientOriginalExtension();

                // Сохраняем в public/images вместо storage
                $path = $file->move(public_path('images'), $filename);

                Log::info('File uploaded to public path:', [
                    'path' => $path,
                    'filename' => $filename,
                ]);

                // Возвращаем прямой URL
                $url = url('images/'.$filename);

                return response()->json([
                    'location' => $url,
                ]);
            }

            return response()->json(['error' => 'File not found'], 400);

        } catch (\Exception $e) {
            Log::error('Upload failed:', ['error' => $e->getMessage()]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
