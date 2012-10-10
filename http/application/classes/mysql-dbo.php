<?php

global $dbh;
$dbconf = Kohana::$config->load('database.default.connection');
$dbh = new PDO("mysql:dbname={$dbconf['database']};host={$dbconf['hostname']}", $dbconf['username'], $dbconf['password']) or die("WTF");

function query($q, $params, $type) {
        global $dbh;
        $sth = $dbh->prepare($q);
        $sth->execute($params);
        return $sth->fetchObject($type);
}

function queryID($q, $params) {
        global $dbh;
        $sth = $dbh->prepare($q);
        $sth->execute($params);
        return $sth->fetchColumn(0);
}

function insert($q, $params) {
        global $dbh;
        $sth = $dbh->prepare($q);
        $sth->execute($params);
        return $dbh->lastInsertId();
}

