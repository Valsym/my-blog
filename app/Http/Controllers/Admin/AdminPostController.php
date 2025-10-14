<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AdminPostController extends Controller
{
    public function index0()
    {
        $posts = Post::with(['categories', 'tags', 'user'])
            ->latest()
            ->paginate(10);

        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Фильтрация и сортировка постов в админке
     *
     * @param Request $request
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

        // Поиск по заголовку и содержанию
        /*if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        // Фильтр по статусу
        if ($request->has('status') && $request->status) {
            $query->where('published', $request->status);
        }

        // Фильтр по категориям
        if ($request->has('categories') && $request->categories) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->whereIn('categories.id', $request->categories);
            });
        }

        // Фильтр по тегам
        if ($request->has('tags') && $request->tags) {
            $query->whereHas('tags', function($q) use ($request) {
                $q->whereIn('tags.id', $request->tags);
            });
        }

        // Фильтр по дате создания (от)
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        // Фильтр по дате создания (до)
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }*/

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
            $slug = $originalSlug . '-' . $count++;
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

        return view('admin.posts.create'/*edit'*/, compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, Post $post)
    {
//        dd($request->all()); // отладка
//        Log::debug('=== UPDATE METHOD STARTED ===');
//        Log::debug('Request data:', $request->all());
//        Log::debug('Post ID:', ['id' => $post->id]);

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

//        Log::debug('Validated data:', $validated);
//        Log::debug('Before update');

        $post->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'excerpt' => $validated['excerpt'],
            'published' => $validated['published'],
            'created_at' => $validated['created_at'],
            'updated_at' => $validated['updated_at'],
        ]);
//        Log::debug('After update');
//        Log::debug('Post after update', $post->toArray());
//        // Проверим, обновилась ли запись
//        $updatedPost = Post::find($post->id);
//        Log::debug('Updated post:', [
//            'title' => $updatedPost->title,
//            'content_length' => strlen($updatedPost->content)
//        ]);
        // Синхронизируем категории и теги
        $post->categories()->sync($request->input('categories', []));
        $post->tags()->sync($request->input('tags', []));

        return redirect()->route('admin.posts.index')
            ->with('success', 'Пост успешно обновлен!');
    }

    public function update2(Request $request, Post $post)
    {
        Log::debug('=== SIMPLIFIED UPDATE ===');
        Log::debug('Request data:', $request->all());
        Log::debug('Request input content:', ['content' => $request->input('content')]);

        // Простейшая валидация
        $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        // Логируем состояние поста до обновления
        Log::debug('Post before update:', $post->toArray());

        // Простое обновление
        $post->title = $request->title;
        $post->content = $request->input('content');

        // Логируем состояние поста после присвоения, но до сохранения
        Log::debug('Post after assignment:', $post->toArray());

        $result = $post->save();

        Log::debug('Save result:', ['result' => $result]);
        Log::debug('Post after save:', $post->toArray());

        // Проверим, был ли пост изменен
        Log::debug('Post wasChanged:', ['wasChanged' => $post->wasChanged()]);
        Log::debug('Post getChanges:', ['changes' => $post->getChanges()]);

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

    public function upload0(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        try {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
//                $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
                $filename = Str::random(20) . '_' . time() . '.' . $file->getClientOriginalExtension();
                // Сохраняем в storage/app/public/images
                $path = $file->storeAs('public/images', $filename);

//                отладка:
                // Проверяем, что файл действительно сохранился
                if (!Storage::exists($path)) {
                    dump([
                        'path' => $path,
                        'size' => Storage::size($path),
                        'filename' => $filename
                    ]); // отладка
                    throw new \Exception('File was not saved');
                }

                Log::info('File uploaded successfully', [
                    'path' => $path,
                    'size' => Storage::size($path),
                    'filename' => $filename
                ]);

                // Возвращаем URL для доступа к изображению
                $url = Storage::url($path);

                return response()->json([
                    'location' => asset($url)
                ]);
            }

            return response()->json(['error' => 'File not found'], 400);

        } catch (\Exception $e) {
//            return response()->json(['error' => $e->getMessage()], 500);
            Log::error('Upload failed', [
                'error' => $e->getMessage(),
                'file' => $request->file('file') ? $request->file('file')->getClientOriginalName() : 'no file'
            ]);

            return response()->json(['error' => $e->getMessage()], 500);

        }
    }

    public function upload1(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
        ]);

        try {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = Str::random(20) . '_' . time() . '.' . $file->getClientOriginalExtension();

                // Сохраняем файл
                $path = $file->storeAs('public/images', $filename);

                // Логируем для отладки
                Log::info('File upload details:', [
                    'original_name' => $file->getClientOriginalName(),
                    'stored_path' => $path,
                    'storage_path' => storage_path('app/' . $path),
                    'filesize' => $file->getSize(),
                ]);

                // Проверяем существование файла
                if (!Storage::exists($path)) {
                    throw new \Exception('File was not saved to: ' . $path);
                }

                // Получаем URL
                $url = Storage::url($path);

                Log::info('File URL:', ['url' => $url, 'full_url' => asset($url)]);

                return response()->json([
                    'location' => asset($url)
                ]);
            }

            return response()->json(['error' => 'File not found'], 400);

        } catch (\Exception $e) {
            Log::error('Upload failed:', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function upload(Request $request)
    {

        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
        ]);

        try {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = Str::random(20) . '_' . time() . '.' . $file->getClientOriginalExtension();

                // Сохраняем в public/images вместо storage
                $path = $file->move(public_path('images'), $filename);

                Log::info('File uploaded to public path:', [
                    'path' => $path,
                    'filename' => $filename
                ]);

                // Возвращаем прямой URL
                $url = url('images/' . $filename);

                return response()->json([
                    'location' => $url
                ]);
            }

            return response()->json(['error' => 'File not found'], 400);

        } catch (\Exception $e) {
            Log::error('Upload failed:', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
