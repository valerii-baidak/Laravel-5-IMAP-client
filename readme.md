#Laravel-5-IMAP-client

##Installation
  You can install the package using the Composer package manager. You can install it by running this command in your project root:

  `composer update`

  Edit .env file to fill database credentials
  run `php artisan migrate`

  add the following to your .env file.

     IMAP_SERVER=imap.gmail.com
     IMAP_SERVER_PORT=993
     IMAP_LOGIN=example@gmail.com
     IMAP_PASSWORD=secret

  To send a message, add the following to your .env file.

     MAIL_DRIVER=smtp
     MAIL_HOST=smtp.gmail.com
     MAIL_PORT=587
     MAIL_USERNAME=example@gmail.com
     MAIL_PASSWORD=secret
     MAIL_ENCRYPTION=tls

  `php artisan key:generate`

  `php artisan cache:clear`

  `php artisan config:cache`

