# Development Guide
This guide's purpose is to save time for next developers and thus improve the quality of the work done.
You can and you should write anything you think people should be aware of.

## Developers and their tasks (in chronological order)
1. Gaurang Evince (Duplicated e-sakafo into e-market with different colors and logo only)
2. Michael Randrianarisona (Corrected front-end and backoffice to focus on shop rather than restaurant && Integrated MVOLA, Airtel Money)


## Technology stack
- CodeIgniter 3.1.9 (because it's a duplicate of e-sakafo)
- Mysql
- PHP 7 because of CI 3.1.9

## Recommended work tools
### Windows
- XAMPP 7.4.2.9 (Any version with php version >> 7.4.1 is ok because we need to install XDebug after)
- VSCode with PHP Extension Pack installed
- Xdebug (we want to avoid `print_r` and `var_dump` and `echo` for debugging purpose)

## Setting up project locally
### Windows
1. Install XAMPP 7.4.2.9
2. Clone this project into htdocs folder
3. Download from Server using Filezilla or File Manager the files: `application/config/config.php` and `application/config/database.php` (Reason: These files are .gitignored because they could be different from a developer to another but the same on production Server)
4. Place the previous files locally following the same paths
5. Export the database `jobmadam_zoopia` from  cpanel's phpmyadmin
6. Create user `jobmadam_zoopia` with password `En42NusS3mUPfeZq` inside mysql:
```
mysql -u root -p
GRANT ALL PRIVILEGES ON *.* TO 'jobmadam_zoopia'@'localhost' IDENTIFIED BY 'En42NusS3mUPfeZq';
\q
```
7. Connect with the freshly created user (copy paste password when asked):
```
mysql -u jobmadam_zoopia -p
```
8. Create the database and disconnect:
```
CREATE DATABASE jobmadam_zoopia;
\q
```
9. Open your local phpmyadmin, choose `jobmadam_zoopia` and import the data we previously exported.
10. Open http://localhost/e-market
11. If no problems occured you're ready to work.

## Note
- Inside `index.php` in the root of the project, you may wonder why we don't show any error even though we are in development case.
It is because of deprecated code which shows a lot of warnings inside of Codeigniter 3 Core. These warnings are returned with the response when a resource is asked and it sometimes freeze the server even locally. It is why a migration to CodeIgniter 4 is recommended as it perfectly matches php 7.3+
- We strongly advise you to use XDebug or any other php debugger that suits you to avoid using print_r and var_dump as we can easily forget to remove this kind of code and push it to production. 
