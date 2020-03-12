<?php
namespace Yurun\Swoole\CoPool;

class CoPool
{
    /**
     * 工作协程数量
     *
     * @var int
     */
    private $coCount;

    /**
     * 队列最大长度
     *
     * @var int
     */
    private $queueLength;

    /**
     * 任务队列
     *
     * @var \Swoole\Coroutine\Channel
     */
    private $taskQueue;

    /**
     * 是否正在运行
     *
     * @var boolean
     */
    private $running = false;

    /**
     * 任务类
     *
     * @var string
     */
    public $taskClass;

    /**
     * 任务参数类名
     *
     * @var string
     */
    public $taskParamClass;

    /**
     * 创建协程的函数
     * 
     * 有些框架自定�