<?php

/*

CREATE TABLE session (
  id VARBINARY(128) NOT NULL PRIMARY KEY,
  modified INT(11) UNSIGNED NOT NULL,
  lifetime INT(11) UNSIGNED NOT NULL,
  data MEDIUMBLOB NOT NULL
) ENGINE=InnoDB;

*/

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

$mysql_session_db_handle = null;

@include 'mysql-session-config.php';

function mysql_session_open($savePath, $sessionName) {
    global
        $mysql_session_server, $mysql_session_user, $mysql_session_password,
        $mysql_session_db, $mysql_session_db_handle;

    $mysql_session_db_handle = mysql_connect($mysql_session_server, $mysql_session_user, $mysql_session_password);

    if ($mysql_session_db_handle === false) {
        trigger_error(
            "MySQL session save handler: could not connect to database.",
            E_USER_ERROR
        );
        return false;
    }

    if (mysql_select_db($mysql_session_db, $mysql_session_db_handle) === FALSE) {
        trigger_error(
            "MySQL session save handler: could not select database: " . mysql_error($mysql_session_db_handle),
            E_USER_ERROR
        );
        return false;
    }

    return true;
}

function mysql_session_close() {
    global $mysql_session_db_handle;

    $ret =  mysql_close($mysql_session_db_handle);

    $mysql_session_db_handle = null;

    return $ret;
}

function mysql_session_read($id) {
    global
        $mysql_session_server, $mysql_session_user, $mysql_session_password,
        $mysql_session_db, $mysql_session_table,
        $mysql_session_id_column, $mysql_session_data_column,
        $mysql_session_modified_column, $mysql_session_lifetime_column,
        $mysql_session_db_handle;

    $res = mysql_query(
        "SELECT $mysql_session_data_column AS v FROM $mysql_session_table " .
        "WHERE $mysql_session_id_column = '" .
        mysql_real_escape_string($id, $mysql_session_db_handle) .
        "' AND $mysql_session_modified_column + $mysql_session_lifetime_column >= UNIX_TIMESTAMP()",
        $mysql_session_db_handle
    );

    if ($res === false) {
        trigger_error(
            "MySQL session save handler: could not run select query: " . mysql_error($mysql_session_db_handle),
            E_USER_ERROR
        );
        return '';
    }

    $row = mysql_fetch_assoc($res);

    if($row === false) {
        return '';
    }

    return $row['v'];
}

function mysql_session_write($id, $data) {
    /* TODO: according to PHP docs, trigger_error messages are never seen
     * because this function is called after the output stream is closed.
     * I'm not sure though that if these are appearing in the php log.
     * Gotta try one day. */

    global
        $mysql_session_table, $mysql_session_id_column,
        $mysql_session_data_column, $mysql_session_modified_column,
        $mysql_session_lifetime_column, $mysql_session_lifetime,
        $mysql_session_db_handle;

    $res = mysql_query(
        "INSERT INTO $mysql_session_table " .
        "($mysql_session_id_column, $mysql_session_modified_column, " .
        "$mysql_session_lifetime_column, $mysql_session_data_column) " . 
        "VALUES ('" . mysql_real_escape_string($id) . "', " .
        "UNIX_TIMESTAMP(), $mysql_session_lifetime, " .
        "'" . mysql_real_escape_string($data) . "') " .
        "ON DUPLICATE KEY UPDATE " .
        "$mysql_session_data_column = VALUES($mysql_session_data_column), " .
        "$mysql_session_modified_column = VALUES($mysql_session_modified_column), " .
        "$mysql_session_lifetime_column = VALUES($mysql_session_lifetime_column)",
        $mysql_session_db_handle
    );

    if ($res === false) {
        trigger_error(
            "MySQL session save handler: could not save session data: " . mysql_error($mysql_session_db_handle),
            E_USER_ERROR
        );
        return false;
    }

    return true;
}

function mysql_session_destroy($id) {
    global
        $mysql_session_table, $mysql_session_id_column, $mysql_session_db_handle;

    $res = mysql_query(
        "DELETE FROM $mysql_session_table WHERE $mysql_session_id_column = '" .
        mysql_real_escape_string($id) . "'",
        $mysql_session_db_handle
    );

    if($res === false) {
        trigger_error(
            "MySQL session save handler: could destroy session: " . mysql_error($mysql_session_db_handle),
            E_USER_ERROR
        );
        return false;
    } else {
        return true;
    }
}


function mysql_session_gc($maxlifetime) {
    global
        $mysql_session_table, $mysql_session_modified_column,
        $mysql_session_lifetime_column, $mysql_session_db_handle;

    $res = mysql_query(
        "DELETE FROM $mysql_session_table WHERE " .
        "$mysql_session_modified_column + $mysql_session_lifetime_column < UNIX_TIMESTAMP()",
        $mysql_session_db_handle
    );

    if ($res === false) {
        trigger_error(
            "MySQL session save handler: could drop old sessions: " . mysql_error($mysql_session_db_handle),
            E_USER_ERROR
        );
        return false;
    } else {
        return true;
    }
}

session_set_save_handler(
    'mysql_session_open', 'mysql_session_close', 'mysql_session_read',
    'mysql_session_write', 'mysql_session_destroy', 'mysql_session_gc'
);
