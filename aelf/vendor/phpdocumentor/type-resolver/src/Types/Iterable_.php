<?php
namespace Yurun\Util\YurunHttp\Http\Psr7;

use Yurun\Util\YurunHttp\Http\Psr7\UploadedFile;
use Psr\Http\Message\ServerRequestInterface;
use Yurun\Util\YurunHttp;
use Yurun\Util\YurunHttp\Http\Psr7\Consts\MediaType;
use Yurun\Util\YurunHttp\Http\Psr7\Consts\RequestHeader;
use Yurun\Util\YurunHttp\Http\Psr7\Consts\RequestMethod;

class ServerRequest extends Request implements ServerRequestInterface
{
    /**
     * 服务器信息
     * @var array
     */
    protected $server = [];

    /**
     * cookie数据
     * @var array
     */
    protected $cookies = [];
    
    /**
     * get数据
     * @var array
     */
    protected $get = [];

    /**
     * post数据
     * @var array
     */
    protected $post = [];

    /**
     * 上传的文件
     * @var \Yurun\Util\YurunHttp