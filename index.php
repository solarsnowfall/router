<?php

$parsed = parse_url(trim($_SERVER['REQUEST_URI'], '/'));

$parts = explode('/', $parsed['path']);

if (empty($parts[count($parts) - 1]))
    array_pop($parts);

var_dump($parts);