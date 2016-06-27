<?php

namespace mkf\log;

/**
 * ConsoleTarget
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class ConsoleTarget implements Target {

    public function flush($messages) {
        foreach ($messages as $message) {
            list($time, $timeFraction) = explode('.', $message['time']);
            $formatedTime = date('Y-m-d H:i:s', $time) . '.' . str_pad($timeFraction, 4, '0');
            $levelName = Logger::getLevelName($message['level']);
            echo "[{$formatedTime} {$levelName} {$message['category']}] {$message['message']}\n";
        }
    }

}
