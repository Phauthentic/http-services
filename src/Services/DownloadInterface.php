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

use DateTimeInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Download helper for PSR compatible downloads via the Response object
 */
interface DownloadInterface
{
    /**
     * Sets content types
     *
     * @param array $contentTypes Content types
     * @return $this
     */
    public function setContentTypes(array $contentTypes): self;

    /**
     * Sets the content type
     *
     * @param string $contentType Content-Type header value
     * @return $this
     */
    public function setContentType(string $contentType): self;

    /**
     * Sets the pragma
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Pragma
     * @link https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.32
     * @link https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.9
     * @param string $pragma Pragma value
     * @return $this
     */
    public function setPragma(string $pragma): self;

    /**
     * Sets the Cache-Control header for the download
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
     * @param string Cache-Control header value
     * @return $this
     */
    public function setCacheControl(string $cacheControl): self;

    /**
     * Sets the name of the file that is send to the client
     * If you used setFile() with a string or setFileFromString() you don't need
     * to call this method. The methods will set the filename based on what they
     * get from the files path.
     *
     * @param string $filename The filename the client will receive
     * @return $this
     */
    public function setFilename(string $filename): self;

    /**
     * Sets the expiration date based on a DateTime object
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Expires
     * @param \DateTimeInterface $dateTime DateTime
     * @return $this
     */
    public function expiresAt(DateTimeInterface $dateTime): self;

    /**
     * Gets a response configured for downloading the file
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getResponse(): ResponseInterface;
}
