<?php

$commands = array(
    "export COMPOSER_ALLOW_SUPERUSER=1",
    "export COMPOSER_HOME=/root",
    "cd /home/admin/web/helloduniya22.com/public_html",
    "/bin/git status",
    "/bin/git pull --no-edit origin master",
    "/usr/local/bin/composer update",
    "/usr/local/bin/composer install",
    "/bin/php artisan config:cache",
    "/bin/php artisan config:clear",
    "/bin/php artisan migrate",
    /*"cd /home/admin/web/bkashbd.eu/public_html/frontend",
    "/usr/bin/npm install",
    "/usr/bin/npm run build",
    "/usr/bin/pm2 restart bkashbd.eu",*/
    /*"chmod -R 777 /home/admin/web/bkashbd.eu/public_html/storage/*",
    "chmod -R 777 /home/admin/web/bkashbd.eu/public_html/storage",
    "chmod -R 777 /home/admin/web/bkashbd.eu/public_html/storage/*",
    "chmod -R 777 /home/admin/web/bkashbd.eu/public_html/frontend/static/*",
    "chmod -R 777 /home/admin/web/bkashbd.eu/public_html/frontend/static",
    "sleep 10",
    "service httpd restart"*/
);


echo shell_exec(implode(" && ", $commands));
