<?php

/*
 * mkf AppServer 正式入口程序
 */

require(__DIR__ . '/../../vendor/autoload.php');

$configs = require(__DIR__ . '/../configs/server.config.php');

(new mkf\ApplicationServer($configs))->run();
