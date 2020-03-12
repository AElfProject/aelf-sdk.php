<?php
namespace Yurun\Util;

use Yurun\Util\YurunHttp\Attributes;
use Yurun\Util\YurunHttp\Http\Request;
use Yurun\Util\YurunHttp\Http\Psr7\UploadedFile;
use Yurun\Util\YurunHttp\Http\Psr7\Consts\MediaType;

class HttpRequest
{
    /**
     * 处理器
     *
     * @var \Yurun\Util\YurunHttp\Handler\IHandler
     */
    private $handler;

    /**
     * 需要请求的Url地址
     * @var string
     */
    public $url;

    /**
     * 发送内容，可以是字符串、数组（支持键值、Yurun\Util\YurunHttp\Http\Psr7\UploadedFile，其中键值会作为html编码，文件则是上传）
     * @var mixed
     */
    public $content;

    /**
     * `curl_setopt_array()`所需要的第二个参数
     * @var array
     */
    public $options = array();

    /**
     * 请求头
     * @var array
     */
    public $headers = array();

    /**
     * Cookies
     * @var array
     */
    public $cookies = array();

    /**
     * 失败重试次数，默认为0
     * @var int
     */
    public $retry = 0;

    /**
     * 是否使用代理，默认false
     * @var bool
     */
    public $useProxy = false;

    /**
     * 代理设置
     * @var array
     */
    public $proxy = array();

    /**
     * 是否验证证书
     * @var bool
     */
    public $isVerifyCA = false;

    /**
     * CA根证书路径
     * @var string
     */
    public $caCert;

    /**
     * 连接超时时间，单位：毫秒
     * @var int
     */
    public $connectTimeout = 30000;

    /**
     * 总超时时间，单位：毫秒
     * @var int
     */
    public $timeout = 30000;

    /**
     * 下载限速，为0则不限制，单位：字节
     * @var int
     */
    public $downloadSpeed;

    /**
     * 上传限速，为0则不限制，单位：字节
     * @var int
     */
    public $uploadSpeed;

    /**
     * 用于连接中需要的用户名
     * @var string
     */
    public $username;

    /**
     * 用于连接中需要的密码
     * @var string
     */
    public $password;

    /**
     * 请求结果保存至文件的配置
     * @var mixed
     */
    public $saveFileOption = array();

    /**
     * 是否启用重定向
     * @var bool
     */
    public $followLocation = true;

    /**
     * 最大重定向次数
     * @var int
     */
    public $maxRedirects = 10;

    /**
     * 证书类型
     * 支持的格式有"PEM" (默认值), "DER"和"ENG"
     * @var string
     */
    public $certType = 'pem';

    /**
     * 一个包含 PEM 格式证书的文件名
     * @var string
     */
    public $certPath = '';
    /**
     * 使用证书需要的密码
     * @var string
     */
    public $certPassword = null;

    /**
     * certType规定的私钥的加密类型，支持的密钥类型为"PEM"(默认值)、"DER"和"ENG"
     * @var string
     */
    public $keyType = 'pem';
    
    /**
     * 包含 SSL 私钥的文件名
     * @var string
     */
    public $keyPath = '';

    /**
     * SSL私钥的密码
     * @var string
     */
    public $keyPassword = null;

    /**
     * 请求方法
     *
     * @var string
     */
    public $method = 'GET';

    /**
     * Http 协议版本
     *
     * @var string
     */
    public $protocolVersion = '1.1';

    /**
     * 代理认证方式
     */
    public static $proxyAuths = array();

    /**
     * 代理类型
     */
    public static $proxyType = array();

    /**
     * 构造方法
     * @return mixed 
     */
    public function __construct()
    {
        $this->open();
    }

    /**
     * 析构方法
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * 打开一个新连接，初始化所有参数。一般不需要手动调用。
     * @return void
     */
    public function open()
    {
        $handlerClass = YurunHttp::getDefaultHandler();
        $this->handler = new $handlerClass;
        $this->retry = 0;
        $this->headers = $this->options = array();
        $this->url = $this->content = '';
        $this->useProxy = false;
        $this->proxy = array(
            'auth'    =>    'basic',
            'type'    =>    'http',
        );
        $this->isVerifyCA = false;
        $this->caCert = null;
        $this->connectTimeout = 30000;
        $this->timeout = 30000;
        $this->downloadSpeed = null;
        $this->uploadSpeed = null;
        $this->username = null;
        $this->password = null;
        $this->saveFileOption = array();
    }

    /**
     * 关闭连接。一般不需要手动调用。
     * @return void
     */
    public function close()
    {
        $this->handler->close();
        $this->handler = null;
    }

    /**
     * 创建一个新会话，等同于new
     * @return static
     */
    public static function newSession()
    {
        return new static;
    }

    /**
     * 获取处理器
     *
     * @return \Yurun\Util\YurunHttp\Handler\IHandler
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * 设置请求地址
     * @param string $url 请求地址
     * @return static
     */
    public function url($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * 设置发送内容，requestBody的别名
     * @param mixed $content 发送内容，可以是字符串、数组
     * @return static
     */
    public function content($content)
    {
        return $this->requestBody($content);
    }

    /**
     * 设置参数，requestBody的别名
     * @param mixed $params 发送内容，可以是字符串、数组
     * @return static
     */
    public function params($params)
    {
        return $this->requestBody($params);
    }

    /**
     * 设置请求主体
     * @param mi