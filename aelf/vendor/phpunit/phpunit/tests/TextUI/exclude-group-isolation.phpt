<?php
namespace Yurun\Util\YurunHttp\Http\Psr7;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\MessageInterface;
use Yurun\Util\YurunHttp\Stream\MemoryStream;

abstract class AbstractMessage implements MessageInterface
{
    /**
     * Http协议版本
     * @var string
     */
    protected $protocolVersion = '1.1';

    /**
     * 头
     * @var array
     */
    protected $headers = [];

    /**
     * 头名称数组
     * 小写的头 => 第一次使用的头名称
     * @var array
     */
    protected $headerNames = [];

    /**
     * 消息主体
     * @var \Psr\Http\Message\StreamInterface
     */
    protected $body;

    public function __construct($b