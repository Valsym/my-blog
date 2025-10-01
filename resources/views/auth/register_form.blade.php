@extends('layouts.app')

@section('title', 'Регистрация')

@section('content')
<div class="container">
    <h1>Регистрация пользователя</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('register') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Имя пользователя</label>
            <input type="name" name="name" id="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email">Email пользователя</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password_confirmation">Подтверждение пароля</label>
            <input type="password_confirmation" name="password_confirmation" id="password_confirmation" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Отправить</button>
    </form>
</div>
@endsection
