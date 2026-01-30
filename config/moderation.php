<?php

return [
    // Автоматическое одобрение комментариев
    'auto_approve' => [
        'enabled' => true,
        'min_word_count' => 3,
        'trusted_users_auto_approve' => true,
    ],

    // Запрещенные слова
    'spam_words' => [
        'casino',
        'gambling',
        'viagra',
        'xxx',
        'porn',
        // Добавьте свои слова
    ],

    // Лимиты
    'limits' => [
        'max_comments_per_hour' => 10,
        'max_links_per_comment' => 3,
    ],

    // Уведомления
    'notifications' => [
        'admin_on_pending' => true,
        'user_on_approval' => false,
    ],
];
