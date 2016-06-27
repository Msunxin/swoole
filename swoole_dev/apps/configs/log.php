<?php
$log['master'] = array(
    'type' => 'FileLog',
    'file' => WEBPATH . '/logs/app.log',
    'dir' => WEBPATH . '/logs',
    'date' => true,
);

$log['test'] = array(
    'type' => 'FileLog',
    'file' => WEBPATH . '/logs/test.log',
);

return $log;