<?php
session_start();

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit;
}

const DS = DIRECTORY_SEPARATOR;
const ROOT = __DIR__;
