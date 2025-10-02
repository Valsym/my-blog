@extends('layouts.admin')

@section('content')
    <form action="{{ isset($post) ? route('admin.posts.update', $post) : route('admin.posts.store') }}" method="POST">
        @csrf
        @if(isset($post))
            @method('PUT')
        @endif
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2>{{ isset($post) ? 'Редактирование поста' : 'Создание поста' }}</h2>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif



                            <div class="mb-3">
                                <label for="title" class="form-label">Заголовок *</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
                                       value="{{ old('title', $post->title ?? '') }}" required>
                                @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">Контент *</label>
                                <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="15" required>{{ old('content', $post->content ?? '') }}</textarea>
                                @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="excerpt" class="form-label">Краткое описание</label>
                                <textarea class="form-control @error('excerpt') is-invalid @enderror" id="excerpt" name="excerpt" rows="3">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
                                @error('excerpt')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="created_at" class="form-label">Дата создания</label>
                                        <input type="datetime-local" class="form-control @error('created_at') is-invalid @enderror" id="created_at" name="created_at"
                                               value="{{ old('created_at', isset($post) ? $post->created_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}">
                                        @error('created_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="updated_at" class="form-label">Дата обновления</label>
                                        <input type="datetime-local" class="form-control @error('updated_at') is-invalid @enderror" id="updated_at" name="updated_at"
                                               value="{{ old('updated_at', isset($post) ? $post->updated_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}">
                                        @error('updated_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                {{ isset($post) ? 'Обновить пост' : 'Создать пост' }}
                            </button>
                            @if(isset($post))
                                <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">Отмена</a>
                            @endif

                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Настройки поста</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="published" class="form-label">Статус</label>
                            <select class="form-select @error('published') is-invalid @enderror" id="published" name="published">
                                <option value="published" {{ old('published', $post->published ?? '') == 'published' ? 'selected' : '' }}>
                                    Опубликован
                                </option>
                                <option value="moderation" {{ old('published', $post->published ?? '') == 'moderation' ? 'selected' : '' }}>
                                    На модерации
                                </option>
                                <option value="draft" {{ old('published', $post->published ?? '') == 'draft' ? 'selected' : '' }}>
                                    Черновик
                                </option>
                            </select>
                            @error('published')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Категории</label>
                            @error('categories')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                            @foreach($categories as $category)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="categories[]"
                                           value="{{ $category->id }}" id="category{{ $category->id }}"
                                        {{ in_array($category->id, old('categories', isset($post) ? $post->categories->pluck('id')->toArray() : [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="category{{ $category->id }}">
                                        {{ $category->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Теги</label>
                            @error('tags')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                            @foreach($tags as $tag)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="tags[]"
                                           value="{{ $tag->id }}" id="tag{{ $tag->id }}"
                                        {{ in_array($tag->id, old('tags', isset($post) ? $post->tags->pluck('id')->toArray() : [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="tag{{ $tag->id }}">
                                        {{ $tag->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        {{-- Дополнительная информация для режима редактирования --}}
                        @if(isset($post))
                            <div class="mb-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6>Информация о посте</h6>
                                        <p><strong>ID:</strong> {{ $post->id }}</p>
                                        <p><strong>Автор:</strong> {{ $post->user->name }}</p>
                                        <p><strong>Просмотры:</strong> {{ $post->views }}</p>
                                        <p><strong>Slug:</strong> {{ $post->slug }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
@endsection

@section('scripts')
    <script src="https://cdn.tiny.cloud/1/xbk824yn8ekm6yvxt7xtssq0xcinql3qs1fthhwgrebotbeh/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            tinymce.init({
                selector: 'textarea#content',
                plugins: 'advlist autolink lists link image charmap preview anchor pagebreak',
                toolbar_mode: 'floating',
                toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
                height: 500,
                images_upload_url: '{{ route("admin.posts.upload") }}',
                automatic_uploads: true,
                file_picker_types: 'image',
                images_reuse_filename: true,
                images_upload_handler: function (blobInfo, progress) {
                    return new Promise((resolve, reject) => {
                        const formData = new FormData();
                        formData.append('file', blobInfo.blob(), blobInfo.filename());
                        formData.append('_token', '{{ csrf_token() }}');

                        fetch('{{ route("admin.posts.upload") }}', {
                            method: 'POST',
                            body: formData
                        })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('HTTP error: ' + response.status);
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.location) {
                                    console.log('Upload successful:', data.location);
                                    resolve(data.location);
                                } else {
                                    reject(data.error || 'Upload failed');
                                }
                            })
                            .catch(error => {
                                console.error('Upload error:', error);
                                reject('Upload failed: ' + error.message);
                            });
                    });
                },
                // setup: function (editor) {
                //     editor.on('change', function () {
                //         editor.save();
                //     });
                // }
            });
        });
    </script>
@endsection




<!-- Place the first <script> tag in your HTML's <head> -->
<!--<script src="https://cdn.tiny.cloud/1/xbk824yn8ekm6yvxt7xtssq0xcinql3qs1fthhwgrebotbeh/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
-->
<!-- Place the following <script> and <textarea> tags your HTML's <body> -->
<!--<script>
    tinymce.init({
        selector: 'textarea',
        plugins: [
            // Core editing features
            'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
            // Your account includes a free trial of TinyMCE premium features
            // Try the most popular premium features until Oct 16, 2025:
            'checklist', 'mediaembed', 'casechange', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'advtemplate', 'ai', 'uploadcare', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown','importword', 'exportword', 'exportpdf'
        ],
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography uploadcare | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
        tinycomments_mode: 'embedded',
        tinycomments_author: 'Author name',
        mergetags_list: [
            { value: 'First.Name', title: 'First Name' },
            { value: 'Email', title: 'Email' },
        ],
        ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
        uploadcare_public_key: '6c9ebce482028704f377',
    });
</script>-->
