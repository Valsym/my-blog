@extends('layouts.admin')

@section('title', 'Управление пользователями')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Список пользователей</h6>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Имя</th>
                                    <th>Email</th>
                                    <th>Постов</th>
                                    <th>Статус</th>
                                    <th>Роль</th>
                                    <th>Дата регистрации</th>
                                    <th>Действия</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->posts_count }}</td>
                                        <td>
                                            @if($user->is_blocked)
                                                <span class="badge badge-danger">Заблокирован</span>
                                            @else
                                                <span class="badge badge-success">Активен</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->is_admin)
                                                <span class="badge badge-primary">Админ</span>
                                            @else
                                                <span class="badge badge-secondary">Пользователь</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.users.edit', $user) }}"
                                                   class="btn btn-outline-primary">Редактировать</a>
                                                @if($user->id !== auth()->id())
                                                    <form action="{{ route('admin.users.toggle-block', $user) }}"
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-warning">
                                                            {{ $user->is_blocked ? 'Разблокировать' : 'Заблокировать' }}
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.users.destroy', $user) }}"
                                                          method="POST" class="d-inline"
                                                          onsubmit="return confirm('Удалить пользователя?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger">Удалить</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
