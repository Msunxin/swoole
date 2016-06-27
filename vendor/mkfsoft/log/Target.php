<?php

namespace mkf\log;

/**
 * Logger Target
 * @author zhengluming <luming.zheng@baozun.cn>
 */
interface Target {

    /**
     * 把日志消息列表刷新到目标存储中
     * @param array $messages 日志消息列表，其中每条日志消息包含以下字段：
     * <ul>
     *   <li>message  消息文本</li>
     *   <li>level  日志级别</li>
     *   <li>category  消息类别</li>
     *   <li>time  日志记录时间（微妙）</li>
     * </p>
     */
    public function flush($messages);
}
