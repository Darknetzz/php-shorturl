# php-shorturl
A URL shortener.

Not quite ready for public use yet, but if you're very curious:
* Clone repo to PHP webserver
* Import `short.sql` to your database
* Create a user and sha512 password (with optional salt) in your MySQL database
* Change `includes/sqlcon.php` to match your connection settings.