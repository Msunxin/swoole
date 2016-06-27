<?php

/*
 * mkf AppServer 开发入口程序
 */

require(__DIR__ . '/../../vendor/autoload.php');

$configs = require(__DIR__ . '/../configs/server.dev.config.php');

(new mkf\ApplicationServer($configs))->run();
