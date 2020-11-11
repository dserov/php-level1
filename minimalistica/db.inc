<?php

/**
 * @var $DBLink PDO
 */
$DBLink = FALSE;

function DBConnect($conf)
{
    global $DBLink;

    if ($DBLink != FALSE)
        unset($DBLink);

    $DBLink = new PDO("mysql:host={$conf['host']};dbname={$conf['db']};charset=UTF8", $conf['user'], $conf['password'],
        array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\';'));
    $DBLink->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

/**
 * Выполняет заданный запрос и вовращает массив значений при удаче,
 * пустой массив, если записей нет и false при ошибке.
 *
 * @param ...
 * @return bool|array
 * @throws Exception
 */
function DBQueryMany()
{
    global $DBLink;
    global $DBError;

    try {
        $rs = false;
        if ($DBLink == FALSE)
            throw new Exception('Соединение с базой данных не установлено.');

        $args = func_get_args();
        $query = array_shift($args);
        if (defined('SQL_QUERY_LOG')) {
            $line = $query;
            $line = str_replace('%', '%%', $line); // hack
            $query_log = vsprintf(str_replace('?', "%s", $line), array_map("DBEscapeString", $args));
            echo $query_log;
        }
        if (!($rs = $DBLink->prepare($query))) {
            throw new Exception('Ошибка prepare');
        }
        if (!$rs->execute($args)) {
            throw new Exception('Ошибка execute');
        }
        if (!preg_match('/^\s*(insert|update|delete)/si', $query) && $rs->columnCount() > 0) {
            $rows = array();
            while ($row = $rs->fetch(PDO::FETCH_ASSOC)) {
                if (isset($row['id']))
                    $rows[$row['id']] = $row;
                else
                    $rows[] = $row;
            }
            return $rows;
        }
        return true;
    } catch (Exception $exc) {
        $DBError = $exc->getMessage();
        if (!$DBLink) {
            throw new Exception($DBError);
        }
        if ($DBLink->inTransaction()) $DBLink->rollBack();
        throw new Exception($DBError);
    }
}

/**
 * Выполняет заданный запрос и вовращает единственную строку результат в виде массива,
 * пустой массив, если записей нет и false при ошибке.
 *
 * @param ...
 * @return bool|array
 * @throws Exception
 */
function DBQueryOne()
{
    $args = func_get_args();
    $result = call_user_func_array("DBQueryMany", $args);
    return is_array($result) ? current($result) : $result;
}

/**
 *  запрос с подстановками приводит к простому текстовому виду
 *  представляет из себя основной запрос. после него может идти еще любое количество параметров для подстановки
 *
 * @param ...
 * @return string подготовленная строка
 */
function DBPrepareStatement()
{
    $args = func_get_args();
    $query = array_shift($args);
    $replace = str_replace('?', "%s", $query);
    $arr = array_map("DBEscapeString", $args);
    return vsprintf($replace, $arr);
}

function DBEscapeString($Value)
{
    global $DBLink;
    global $DBError;

    if ($DBLink == FALSE) {
        $DBError = 'Соединение с базой данных не установлено.';
        return $Value;
    }

    $result = $DBLink->quote($Value);
    if ($result == FALSE) {
        return $Value;
    }

    return $result;
}

DBConnect([
    'host' => '127.0.0.1',
    'user' => 'root',
    'password' => 'root',
    'db' => 'dserov__gallery'
]);
