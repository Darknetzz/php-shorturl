# php-shorturl
A URL shortener.

Not quite ready for public use yet, but if you're very curious:
* Clone repo to PHP webserver
* Import `short.sql` to your database
* Create a user and sha512 password (with optional salt) in your MySQL database
* Change `includes/sqlcon.php` to match your connection settings.

## Features
- [x] Add URLs
- [x] Delete URLs
- [x] Extensive tooltips
- [x] Favorites
- [x] User accounts with ACL
- [x] Logs
- [ ] Edit URLs
- [ ] Edit settings
- [ ] Create/distribute as docker container