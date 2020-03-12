<?php
namespace Yurun\Swoole\CoPool;

use Swoole\Coroutine\Channel;

/**
 * 协程批量执行器
 */
class CoBatch
{
    /**
     * 任务回调列表
     *
     * @var callable[]
     */
    private $taskCallables;

    /**
     * 超时时间，为 -1 则不限时
     *
     * @var float|null
     */
    private $timeout;

    /**
     * 限制并发协程数量，为 -1 则不限制
     *
     * @var int|null
     */
    private $limit;

    public function __construct(array $taskCallables, ?float $timeout = -1, ?int $limit = -1)
    {
        $this->taskCallables = $taskCallables;
        $this->timeout = $timeout;
        $this->limit = $limit;
    }

    /**
     * 执行并获取执行结果
     *
     * @param float|null $timeout 超时时间，为 -1 则不�