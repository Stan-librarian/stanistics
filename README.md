# stanistics
PHP tool to send PostgreSQL queries to the database of a Bibliomondo Portfolio ILS and get the result as an html table and a csv file
## INSTALLATION
1) copy all files to an http/php server
2) be sure your server can use postgresql (cf below)
## CONFIGURATION
1) Uncomment (by removing the ";") the line ;extension=php_pgsql.dll in php.ini (uncommenting the line ;extension=php_pdo_pgsql.dll should not be necessary)
2) fill the config.ini file with : 
  * host, port, database, user, password (mandatory)
  * excluded values (optionnal)
## USAGE
1. select the values you want to search
2. select the fields you want to display
3. get the result as an html table
4. (optionnal) downlad a csv file

## CREDITS
Uses [JQuery](https://jquery.com/) and [SortTable](https://www.kryogenix.org/code/browser/sorttable/) (under the X11 licence)
