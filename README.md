php-mysql-sessionhandler
=======================

MySQL-session is a very simple session handler plugin for PHP.

The table structure is compatible with Zend Framework's
Zend_Session_SaveHandler_DbTable.

To use the library, simply include mysql-session.php into your project.

Use the following global varialbes to fine-tune your application.
Default values are also as they are below:

    $mysql_session_server = '127.0.0.1:3306';
    $mysql_session_user = 'php';
    $mysql_session_password = 'session123';
    $mysql_session_db = 'session';
    $mysql_session_table = 'session';

    $mysql_session_id_column = 'id';
    $mysql_session_data_column = 'data';
    $mysql_session_modified_column = 'modified';
    $mysql_session_lifetime_column = 'lifetime';

    $mysql_session_lifetime = get_cfg_var('session.gc_maxlifetime');

    $mysql_session_use_locking = true;
    $mysql_session_lock_timeout = 10;

These can be set in your code or a mysql-session-config.php file that is
in the same directory as mysql-session.php.

Copy mysql-session-config.php.dist to mysql-session-config.php and
modify any variables.

Use the following table for session data storage:

    CREATE TABLE session (
      id VARBINARY(128) NOT NULL PRIMARY KEY,
      modified INT(11) UNSIGNED NOT NULL,
      lifetime INT(11) UNSIGNED NOT NULL,
      data MEDIUMBLOB NOT NULL
    ) ENGINE=InnoDB;

Table and column names can be altered.
