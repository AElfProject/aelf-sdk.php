<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Yurun\Swoole\CoPool\CoPool;
use Yurun\Swoole\CoPool\Interfaces\ICoTask;
use Yurun\Swoole\CoPool\Interfaces\ITaskParam;

Swoole\Runtime::enableCoroutine();

go(function(){
    $coCount = 10; // 同时工作协程数，可以改小改大，看一下执行速度
    $queueLength = 1024; // 队列长度
    $pool = new CoPool($coCount, $queueLength,
        // 定义任务匿名类，当然你也可以定义成普通类，传入完整类名
        new class implements ICoTask
        {
            /**
             * 执行任务
             *
             * @param ITaskParam $param
             * @return mixed
             */
            public function run(ITaskParam $param)
            {
                usleep