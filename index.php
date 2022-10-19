<?php

require_once __DIR__ . '/' . 'autoload.php';
$delim = $argv[1] ?? ';';
(new \Epic\Task($delim))->run();

