# My Blog on Laravel

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql)

Полнофункциональный блог на Laravel с админ-панелью, системой комментариев, тегами и категориями.

## 🚀 Демо

- **Live Demo**: [https://valsy.ru/Laravel/my-blog-12/](https://valsy.ru/Laravel/my-blog-12/)
- **Admin Demo**: [https://valsy.ru/Laravel/my-blog-12/admin](https://valsy.ru/Laravel/my-blog-12/admin)

## ✨ Особенности

### Для пользователей
- 📝 Просмотр постов с пагинацией
- 🔍 Поиск по содержанию и заголовкам
- 🏷️ Фильтрация по категориям и тегам
- 💬 Система комментариев с ответами
- 👤 Регистрация и авторизация
- 📱 Адаптивный дизайн

### Для администраторов
- 🛠️ Полнофункциональная админ-панель
- 📊 Дашboard со статистикой
- ✏️ WYSIWYG редактор (TinyMCE)
- 🖼️ Загрузка изображений в посты
- 👥 Управление пользователями
- 💬 Модерация комментариев

## 🛠️ Технологии

- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Bootstrap 5, JavaScript
- **Database**: MySQL
- **Editor**: TinyMCE 6
- **Authentication**: Laravel Sanctum
- **Deployment**: Git-based deployment

## 📦 Установка

1. **Клонирование репозитория**
   ```bash
   git clone https://github.com/Valsym/my-blog.git
   cd my-blog
2. **Установка зависимостей**

```bash
composer install
npm install
```
3. **Настройка окружения**

```bash
cp .env.example .env
php artisan key:generate
```
4. **Настройка базы данных**

```bash
php artisan migrate --seed
```
5. **Запуск**

```bash
php artisan serve
npm run dev
```
## 🗃️ База данных
```bash
# Миграции
php artisan migrate

# Сиды (тестовые данные)
php artisan db:seed

# Или все вместе
php artisan migrate --seed
```
## 👤 Аутентификация
Проект использует стандартную аутентификацию Laravel с дополнительными функциями:

- Регистрация с подтверждением email

- Сброс пароля

- Роли пользователей (Admin/User)

## 📁 Структура проекта
text
my-blog/
├── app/
│   ├── Http/Controllers/   # Контроллеры
│   ├── Models/            # Модели Eloquent
│   └── Providers/         # Сервис-провайдеры
├── resources/
│   ├── views/             # Blade шаблоны
│   └── lang/              # Локализация
├── routes/                # Маршруты
├── database/              # Миграции и сиды
└── public/               # Публичные assets
## 🎯 Основные функции
### Блог
- Создание, редактирование, удаление постов

- Rich-text редактор с загрузкой изображений

- Система тегов и категорий

- Статусы постов (опубликовано, черновик, на модерации)

### Комментарии
- Многоуровневые комментарии с ответами

- Модерация комментариев

- Аватары пользователей

### Поиск и фильтрация
- Полнотекстовый поиск по постам

- Фильтрация по категориям

### Облако тегов

## Админ-панель
- Дашборд со статистикой

- Управление контентом

- Управление пользователями
- Управление комментариями
- Редактирование своего профиля

## Настройки системы

### 🔧 Команды Artisan
```bash
# Очистка кеша
php artisan cache:clear

# Генерация ссылок
php artisan storage:link

# Создание сидов
php artisan db:seed

# Просмотр маршрутов
php artisan route:list
```
## 🚀 Деплой
Проект использует Git-based деплой:

```bash
# На продакшн сервере
cd /path/to/project
git pull origin main
php artisan cache:clear
php artisan view:clear
```
## 🤝 Вклад в проект
- Форкните репозиторий
- Создайте ветку для фичи (git checkout -b feature/AmazingFeature)
- Закоммитьте изменения (git commit -m 'Add some AmazingFeature')
- Запушьте ветку (git push origin feature/AmazingFeature)
- Откройте Pull Request

## 📄 Лицензия

Этот проект лицензирован под **The Valsym Blog License** - некоммерческой лицензией с ограничениями.

### Можно:
- Использовать для личных и образовательных целей
- Изучать код и вносить изменения
- Делиться проектом с другими

### Нельзя без разрешения:
- Использовать в коммерческих проектах
- Продавать или включать в платные продукты
- Использовать для бизнес-целей

### Для коммерческого использования:
Пожалуйста, свяжитесь со мной для получения разрешения:
- **Email**: simonvn@yandex.ru
- **GitHub**: [Valsym](https://github.com/Valsym)
- **Website**: [valsy.ru](https://valsy.ru)

Полный текст лицензии: [LICENSE](LICENSE)
## 📞 Контакты
Автор: Valery Simonov

Email: simonvn@yandex.ru

GitHub: Valsym

Website: valsy.ru

⭐ Не забудьте поставить звезду, если проект вам понравился!




### My Blog on 
<p align="center"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></p>

