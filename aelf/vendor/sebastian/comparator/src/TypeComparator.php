<?php
namespace Yurun\Util\YurunHttp\Http\Psr7;

use Yurun\Util\YurunHttp\Http\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\RequestInterface;
use Yurun\Util\YurunHttp\Http\Psr7\Consts\RequestMethod;

class Request extends AbstractMessage implements RequestInterface
{
    /**
     * 请求地址
     * @var Yurun\Util\YurunHttp\Http\Psr7\Uri
     */
    protected $uri;

    /**
     * 请求目标
     * @var mixed
     */
    protected $requestTarget;

    /**
     * 请求方法
     * @var string
     */
    protected $method;

    /**
     * 构造方法
     * @param string|Yurun\Util\YurunHttp\Http\Psr7\Uri $url
     * @param array $headers
     * @param string $body
     * @param string $method
     * @param string $version
     */
    public function __construct($uri = null, array $headers = [], $body = '', $method = RequestMethod::GET, $version = '1.1')
    {
        parent::__construct($body);
        if(! $uri instanceof Uri)
        {
            $this->uri = new Uri($uri);
        }
        else if(null !== $uri)
        {
            $this->uri = $uri;
        }
        $this->setHeaders($headers);
        $this->method = strtoupper($method);
        $this->protocolVersion = $version;
    }

    /**
     * Retrieves the message's request target.
     *
     * Retrieves the message's request-target either as it will appear (for
     * clients), as it appeared at request (for servers), or as it was
     * specified for the instance (see withRequestTarget()).
     *
     * In most cases, this will be the origin-form of the composed URI,
     * unless a value was provided to the concrete implementation (see
     * withRequestTarget() below).
     *
     * If no URI is available, and no request-target has bee