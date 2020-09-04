You might need to create a mysql user with a native password for this library to use:

CREATE user 'nodeadmin'@'localhost' IDENTIFIED WITH mysql_native_password BY 'password';

GRANT ALL PRIVILEGES ON radiusdb.* TO 'nodeadmin'@'localhost';