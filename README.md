# stanistics
PHP tool to send PostgreSQL queries to the database of a Bibliomondo Portfolio ILS and get the result as an html table and a csv file
## INSTALLATION
1) copy all files to an http/php server
2) be sure your server can use postgresql (cf below)
## CONFIGURATION
1) Uncomment (by removing the ";") the line ;extension=php_pgsql.dll in php.ini
2) fill the config.ini file with : 
  * host, port, database, user, password (mandatory)
  * excluded values
## USAGE
