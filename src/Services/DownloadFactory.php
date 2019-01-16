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

/**
 * Download Factory
 */
class DownloadFactory implements DownloadFactoryInterface
{
    /**
     * Classname to be instantiated for downloads
     *
     * @var string
     */
    public static $downloadClass = Download::class;

    /**
     * PSR Stream Factory
     *
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
     * Constructor
     */
    public function __construct(
        StreamFactoryInterface $streamFactory,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
    }

    /**
     * Create download
     *
     * @return \Phauthentic\Http\Services\DownloadInterface
     */
    public function createDownload(): DownloadInterface
    {
        return new static::$downloadClass(
            $this->streamFactory,
            $this->responseFactory
        );
    }
}
