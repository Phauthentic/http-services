<?php
declare(strict_types = 1);
/**
 * Copyright (c) Phauthentic (https://github.com/Phauthentic)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Phauthentic (https://github.com/Phauthentic)
 * @link          https://github.com/Phauthentic
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Phauthentic\Http\Services;

use Composer\Downloader\DownloaderInterface;
use DateTime;
use DateTimeInterface;
use InvalidArgumentException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

/**
 * Download helper for PSR compatible downloads via the Response object
 *
 * @todo Make this immutable?
 */
class Download implements DownloadInterface
{
    const DATE_TIME_FORMAT = 'D, d M Y H:i:s \G\M\T';

    /**
     * PSR Response Object
     *
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $response;

    /**
     * PSR Stream Factory
     * @var \Psr\Http\Message\StreamFactoryInterface
     */
    protected $streamFactory;

    /**
     * PSR Response Factory
     *
     * @var \Psr\Http\Message\ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * Content Types for the download
     *
     * @var array
     */
    protected $contentTypes = [
        'application/force-download',
        'application/octet-stream',
        'application/download'
    ];

    /**
     * Transfer encoding
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Transfer-Encoding
     * @var string
     */
    protected $transferEncoding = 'binary';

    /**
     * @var string
     */
    protected $description = 'File Transfer';

    /**
     * Cache Control Header
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
     * @var null|string
     */
    protected $cacheControl = null;

    /**
     * Expires Header
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Expires
     * @var int
     */
    protected $expires = 0;

    /**
     * Pragma
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Pragma
     * @var null|string
     */
    protected $pragma = null;

    /**
     * File name to send
     *
     * @var null|string
     */
    protected $filename = null;

    /**
     * PSR Message Stream Interface
     *
     * @var \Psr\Http\Message\StreamInterface
     */
    protected $stream;

    /**
     * Constructor
     *
     * @param null|\Psr\Http\Message\StreamFactoryInterface $streamFactory Response Factory
     * @param null|\Psr\Http\Message\ResponseFactoryInterface $responseFactory Stream Factory
     */
    public function __construct(
        ?StreamFactoryInterface $streamFactory = null,
        ?ResponseFactoryInterface $responseFactory = null
    ) {
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
    }

    /**
     * Sets the Cache-Control header for the download
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
     * @param string Cache-Control header value
     * @return $this
     */
    public function setCacheControl(string $cacheControl): DownloadInterface
    {
        $this->cacheControl = $cacheControl;

        return $this;
    }

    /**
     * Sets the expiration time to N days in the future
     *
     * @param int $days Time in days
     * @return $this
     */
    public function expiresInDays(int $days): self
    {
        return $this->expiresAt(
            $this->getDateTime(time() + $days * 24 * 60 * 60)
        );
    }

    /**
     * Sets the expiration time to N minutes in the future
     *
     * @param int $hours Time in hours
     * @return $this
     */
    public function expiresInHours(int $hours): self
    {
        return $this->expiresAt(
            $this->getDateTime(time() + $hours * 60 * 60)
        );
    }

    /**
     * Sets the expiration time to N minutes in the future
     *
     * @param int $minutes Time in minutes
     * @return $this
     */
    public function expiresInMinutes(int $minutes): self
    {
        return $this->expiresAt(
            $this->getDateTime(time() + $minutes * 60)
        );
    }

    /**
     * Sets the expiration date based on a DateTime object
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Expires
     * @param \DateTimeInterface $dateTime DateTime
     * @return $this
     */
    public function expiresAt(DateTimeInterface $dateTime): DownloadInterface
    {
        $this->expires = $dateTime->format(self::DATE_TIME_FORMAT);

        return $this;
    }

    /**
     * Sets the content type
     *
     * @param string $contentType Content-Type header value
     * @return $this
     */
    public function setContentType(string $contentType): DownloadInterface
    {
        $this->checkMimeType($contentType);
        $this->contentTypes = [$contentType];

        return $this;
    }

    /**
     * Sets content types
     *
     * @param array $contentTypes Content types
     * @return $this
     */
    public function setContentTypes(array $contentTypes): DownloadInterface
    {
        foreach ($contentTypes as $contentType) {
            $this->checkMimeType($contentType);
        }
        $this->contentTypes = $contentTypes;

        return $this;
    }

    /**
     * Validates mime types
     *
     * @param string $mimeType Mime Type string
     * @return void
     */
    protected function checkMimeType(string $mimeType): void
    {
        if (!preg_match('#^[-\w]+/[-\w+]+$#', $mimeType)) {
            throw new RuntimeException(sprintf(
                '`%s` is not a valid mime type string',
                $mimeType
            ));
        }
    }

    /**
     * Sets the pragma
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Pragma
     * @link https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.32
     * @link https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.9
     * @param string $pragma Pragma value
     * @return $this
     */
    public function setPragma(string $pragma): DownloadInterface
    {
        $this->pragma = $pragma;

        return $this;
    }

    /**
     * Sets the name of the file that is send to the client
     *
     * If you used setFile() with a string or setFileFromString() you don't need
     * to call this method. The methods will set the filename based on what they
     * get from the files path.
     *
     * @param string $filename The filename the client will receive
     * @return $this
     */
    public function setFilename(string $filename): DownloadInterface
    {
        if (empty($filename)) {
            throw new RuntimeException('Filename can not be empty');
        }

        $this->filename = $filename;

        return $this;
    }

    /**
     * Sets a file based on the PSR Message StreamInterface
     *
     * @param \Psr\Http\Message\StreamInterface $stream Stream Interface
     * @return $this
     */
    public function setFileStream(StreamInterface $stream): self
    {
        $this->stream = $stream;

        return $this;
    }

    /**
     * Sets the file to download based on a resource
     *
     * @param resource $file Resource
     * @return $this
     */
    public function setFileFromResource($file): self
    {
        if (get_resource_type($file) !== 'stream') {
            throw new InvalidArgumentException('File must be a `stream` resource');
        }

        $this->streamFactory->createStreamFromResource($file);

        return $this;
    }

    /**
     * Sets the file to download based on a file path
     *
     * @param string $file File to download
     * @return $this
     */
    public function setFileFromString(string $file): self
    {
        if (!is_file($file)) {
            throw new InvalidArgumentException('The file does not exist');
        }

        if (!is_readable($file)) {
            throw new InvalidArgumentException('The file is not readable');
        }

        if ($this->filename === null) {
            $this->filename = basename($file);
        }

        $this->stream = $this->streamFactory->createStreamFromFile($file);

        return $this;
    }

    /**
     * Sets the file you want to send to the client
     *
     * @param string|resource|\Psr\Http\Message\StreamInterface $file File stream string or resource
     * @return $this
     */
    public function setFile($file): self
    {
        if ($file instanceof StreamInterface) {
            return $this->setFileStream($file);
        }

        if (is_resource($file)) {
            return $this->setFileFromResource($file);
        }

        if (is_string($file)) {
            return $this->setFileFromString($file);
        }

        throw new InvalidArgumentException('$file must be a string, resource or stream');
    }

    /**
     * Applies the configured download properties to a response object
     *
     * @param \Psr\Http\Message\ResponseInterface $response Response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function applyToResponse(ResponseInterface $response): ResponseInterface
    {
        if (empty($this->stream)) {
            throw new RuntimeException('No file was provided that could be send to the client');
        }

        if ($this->filename === null) {
            throw new RuntimeException('No file name was provided');
        }

        foreach ($this->contentTypes as $contentType) {
            $this->response = $this->response->withAddedHeader(
                'Content-Type',
                $contentType
            );
        }

        if ($this->cacheControl !== null) {
            $response = $response->withHeader('Cache-Control', $this->cacheControl);
        };

        if ($this->pragma !== null) {
            $response = $response->withHeader('Pragma', $this->pragma);
        }

        if ($this->expires !== null) {
            $response = $response->withHeader('Expires', $this->expires);
        }

        return $response
            ->withHeader('Content-Description', $this->description)
            ->withHeader('Content-Transfer-Encoding', $this->transferEncoding)
            ->withHeader('Content-Disposition', 'attachment; filename="' . $this->filename . '"')
            ->withBody($this->stream);
    }

    /**
     * Gets a response configured for downloading the file
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        if ($this->responseFactory === null) {
            throw new RuntimeException('You must construct the download class with a response factory');
        }

        return $this->applyToResponse(
            $this->responseFactory->createResponse(200)
        );
    }

    /**
     * Convenience method / syntactic sugar to quickly create a download
     *
     * @param \Psr\Http\Message\StreamInterface $stream Stream
     * @param \Psr\Http\Message\ResponseInterface $response Response
     * @param string $filename Filename
     * @return \Psr\Http\Message\ResponseInterface
     */
    public static function create(
        StreamInterface $stream,
        ResponseInterface $response,
        string $filename
    ): ResponseInterface {
        return (new self())
            ->setFilename($filename)
            ->setFileStream($stream)
            ->applyToResponse($response);
    }

    /**
     * Get Date Time
     *
     * @throws \Exception
     * @param null|int $timestamp Unix Timestamp
     * @return \DateTimeInterface
     */
    protected function getDateTime(?int $timestamp): DateTimeInterface
    {
        $dateTime = new DateTime();
        if ($timestamp !== null) {
            return $dateTime->setTimeStamp($timestamp);
        }

        return $dateTime;
    }
}
