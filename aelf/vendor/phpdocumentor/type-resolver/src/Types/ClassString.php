<?php
namespace Yurun\Util\YurunHttp\Http\Psr7;

use Yurun\Util\YurunHttp\Stream\FileStream;
use Psr\Http\Message\UploadedFileInterface;

class UploadedFile implements UploadedFileInterface
{
    /**
     * 文件在客户端时的文件名
     * @var string
     */
    protected $fileName;

    /**
     * 文件mime类型
     * @var string
     */
    protected $mediaType;

    /**
     * 临时文件名
     * @var string
     */
    protected $tmpFileName;
    
    /**
     * 文件大小，单位：字节
     * @var int
     */
    protected $size;

    /**
     * 错误码
     * @var int
     */
    protected $error;
    
    /**
     * 文件流
     * @var \Yurun\Util\YurunHttp\Stream\FileStream
     */
    protected $stream;
    
    /**
     * 文件是否被移动过
     * @var boolean
     */
    protected $isMoved = false;

    public function __construct($fileName, $mediaType, $tmpFileName, $size = null, $error = 0)
    {
        $this->fileName = $fileName;
        $this->mediaType = $mediaType;
        $this->tmpFileName = $tmpFileName;
        if(null === $size)
        {
            $this->size = filesize($tmp