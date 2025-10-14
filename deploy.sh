#!/bin/bash
cd www/valsy.ru/Laravel/my-blog-12/
git pull origin main
/opt/php83/bin/php artisan cache:clear
/opt/php83/bin/php artisan view:clear
/opt/php83/bin/php artisan config:clear
echo "Deployment completed!"
