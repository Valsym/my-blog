#!/bin/bash
# На продакшн сервере
cd /path/to/project
git pull origin main
php artisan cache:clear
php artisan view:clear
bin/php artisan config:clear
echo "Deployment completed!"
