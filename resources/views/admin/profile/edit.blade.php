@extends('layouts.admin')

@section('title', 'Редактирование профиля')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Редактирование профиля</h6>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form action="{{ route('admin.profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="name">Имя</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr>

                            <h6>Смена пароля</h6>
                            <div class="form-group">
                                <label for="current_password">Текущий пароль</label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                       id="current_password" name="current_password">
                                @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password">Новый пароль</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password">
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation">Подтверждение пароля</label>
                                <input type="password" class="form-control"
                                       id="password_confirmation" name="password_confirmation">
                            </div>

                            <button type="submit" class="btn btn-primary">Обновить профиль</button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Отмена</a>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Информация о профиле</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>ID:</strong> {{ $user->id }}</p>
                        <p><strong>Роль:</strong> {{ $user->is_admin ? 'Администратор' : 'Пользователь' }}</p>
                        <p><strong>Зарегистрирован:</strong> {{ $user->created_at->format('d.m.Y H:i') }}</p>
                        <p><strong>Обновлен:</strong> {{ $user->updated_at->format('d.m.Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
