php-mysql-sessionhandler
=======================

MySQL-session is a very simple session handler plugin for PHP.

To use the library, simply include mysql-session.php into your project.

Use the following global varialbes to fine-tune your application.
Default values are also as they are below:

    $mysql_session_server = '127.0.0.1:3306';
    $mysql_session_user = 'php';
    $mysql_session_password = 'session123';
    $mysql_session_db = 'session';
    $mysql_session_table = 'session';
    $mysql_session_id_column = 'id';
    $mysql_session_value_column = 'value';
    $mysql_session_expiration_column = 'expires';
    $mysql_session_db_handle = null;
