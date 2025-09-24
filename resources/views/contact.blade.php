@extends('layouts.app')

@section('title', 'Контакты')

@section('content')
    <div class="container my-5">
        <h1 class="text-center">Свяжитесь со мной</h1>

        <div class="row my-4">
            <div class="col-md-6">
                <h2>Контактная информация</h2>
                <p>
                    <strong>Валерий</strong><br>
                    12345, Город, Улица, 1<br>
                    Телефон: <a href="tel:+1234567890">+1 (234) 567-890</a><br>
                    Email: <a href="mailto:info@my-blog.ru">info@my-blog.ru</a><br>
                </p>

                <h3>Наши часы работы</h3>
                <ul>
                    <li>Понедельник - Пятница: 9:00 - 18:00</li>
                    <li>Суббота: 10:00 - 14:00</li>
                    <li>Воскресенье: Выходной</li>
                </ul>
            </div>

            <div class="col-md-6">
                <h2>Форма обратной связи</h2>
                <form action="{{ route('contact.submit') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Имя</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Сообщение</label>
                        <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Отправить</button>
                </form>
            </div>
        </div>

        <h2 class="text-center">Наша локация</h2>
        <div class="text-center">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.835434509631!2d144.9537353153151!3d-37.81720997975121!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad642af0f11e555%3A0x4a5b4cd4320e05b!2sEcoLife%20Company!5e0!3m2!1sen!2sru!4v1633648269878!5m2!1sen!2sru"
                width="100%"
                height="350"
                style="border:0;"
                allowfullscreen=""
                loading="lazy">
            </iframe>
        </div>
    </div>
@endsection
