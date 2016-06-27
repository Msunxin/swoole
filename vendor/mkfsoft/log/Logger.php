<?php

namespace mkf\log;

/**
 * Logger
 *
 * @author zhengluming <luming.zheng@baozun.cn>
 */
class Logger {

    /**
     * Error message level. An error message is one that indicates the abnormal termination of the
     * application and may require developer's handling.
     */
    const LEVEL_ERROR = 0x01;

    /**
     * Warning message level. A warning message is one that indicates some abnormal happens but
     * the application is able to continue to run. Developers should pay attention to this message.
     */
    const LEVEL_WARNING = 0x02;

    /**
     * Informational message level. An informational message is one that includes certain information
     * for developers to review.
     */
    const LEVEL_INFO = 0x04;

    /**
     * @var Target Logger Target
     */
    private $target;

    /**
     * @var array 日志消息列表
     */
    private $messages = array();

    /**
     * @var int 刷新间隔，为0表示每次都刷新
     */
    private $flushInterval = 0;

    public function __construct(Target $target) {
        $this->target = $target;
    }

    /**
     * Logs a message with the given type and category.
     * If [[traceLevel]] is greater than 0, additional call stack information about
     * the application code will be logged as well.
     * @param string|array $message the message to be logged. This can be a simple string or a more
     * complex data structure that will be handled by a [[Target|log target]].
     * @param integer $level the level of the message. This must be one of the following:
     * `Logger::LEVEL_ERROR`, `Logger::LEVEL_WARNING`, `Logger::LEVEL_INFO`
     * @param string $category the category of the message.
     */
    public function log($message, $level = self::LEVEL_INFO, $category = 'application') {
        $time = microtime(true);
        $this->messages[] = array(
            'message' => $message,
            'level' => $level,
            'category' => $category,
            'time' => $time,
        );
        if ($this->flushInterval == 0 || count($this->messages) >= $this->flushInterval) {
            $this->flush();
        }
    }

    /**
     * Flushes log messages from memory to target.
     */
    public function flush() {
        $messages = $this->messages;
        $this->messages = array();
        $this->target->flush($messages);
    }

    /**
     * Returns the text display of the specified level.
     * @param integer $level the message level, e.g. [[LEVEL_ERROR]], [[LEVEL_WARNING]].
     * @return string the text display of the level
     */
    public static function getLevelName($level) {
        static $levels = array(
            self::LEVEL_ERROR => 'error',
            self::LEVEL_WARNING => 'warning',
            self::LEVEL_INFO => 'info',
        );

        return isset($levels[$level]) ? $levels[$level] : 'unknown';
    }

}
