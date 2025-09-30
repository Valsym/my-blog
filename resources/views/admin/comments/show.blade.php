@extends('layouts.admin')

@section('title', 'Модерация комментария')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1>Модерация комментария</h1>


                <!-- Секция комментария -->
                @include('posts._comment', ['comment' => $comment, 'depth' => 0]);

@endsection


