@extends("layouts.app")
@section("content")
    <h1>Сообщение отправлено успешно!</h1>
    <br>
    <h5>Уважаемый {{ $name }}</h5>
    <p>Ваш е-мейл: {{ $email }}</p>
    <p>Ваше сообщение: {{ $message }}</p>

    <div class="row">
        <div class="col-md-12">

            <div class="card mb-3">
                <div class="card-body">
                     <div class="card-body">Ваше сообщение было передано Администрации сайта.<br>
                        В ближайшее время постараемся Вам ответить.</div>
                </div>
            </div>

        </div>
    </div>
@endsection
