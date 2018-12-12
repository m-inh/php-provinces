<?php

function getPath() {
    $parts = parse_url($_SERVER['REQUEST_URI']);
    return $parts['path'];
};

function getQueries() {
    $parts = parse_url($_SERVER['REQUEST_URI']);
    $queries = array();
    parse_str($parts['query'], $queries);
    return $queries;
};

function getQuery($queryProp) {
    if (!$queryProp) {
        return "";
    } else {
        return getQueries()[$queryProp];
    }
};

?>