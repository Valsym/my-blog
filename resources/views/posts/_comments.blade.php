<div class="comments-section mt-5">
    <h3 class="h4 mb-3">Комментарии</h3>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    @auth
        <form action="{{ route('comments.store', $post) }}" method="POST" class="mb-4">
            @csrf
            <div class="form-group">
                <textarea name="body" rows="3" class="form-control"
                          placeholder="Напишите ваш комментарий..." required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Оставить комментарий</button>
        </form>
    @else
        <p class="mb-3">
            <a href="{{ route('login') }}" class="text-primary">Войдите</a>, чтобы оставить комментарий
        </p>
    @endauth

    <!-- Список комментариев -->
    <div class="comments-list">
        @foreach($post->comments as $comment)
            <div class="card mb-2">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h6 class="card-title mb-1">{{ $comment->user->name }}</h6>
                        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                    </div>
                    <p class="card-text">{{ $comment->body }}</p>

                    <!-- Форма ответа (простая версия) -->
                    @auth
                        <form action="{{ route('comments.store', $post) }}" method="POST" class="mt-2">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                            <div class="form-group mb-2">
                                <textarea name="body" rows="2" class="form-control form-control-sm"
                                          placeholder="Ответить..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-sm btn-outline-primary">Ответить</button>
                        </form>
                    @endauth
                </div>
            </div>
        @endforeach
    </div>
</div>
