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

use InvalidArgumentException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Redirect helper for PSR compatible response objects
 */
class Redirect implements RedirectInterface, RedirectFactoryInterface
{
    /**
     * Response Factory
     *
     * @var \Psr\Http\Message\ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * Constructor
     *
     * @param \Psr\Http\Message\ResponseFactoryInterface $responseFactory
     */
    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * Checks that the HTTP Status is a valid redirect status
     *
     * @throws \InvalidArgumentException
     * @param int $status Redirect Status Code
     * @return void
     */
    protected static function checkStatus(int $status): void
    {
        if ($status < 300 || $status > 308) {
            throw new InvalidArgumentException(sprintf(
                'Status %d is not a valid redirect status code in the range of 300 to 308',
                $status
            ));
        }
    }

    /**
     * @inheritdoc
     */
    public function create(string $url, int $status = self::STATUS_FOUND): ResponseInterface
    {
        return static::to(
            $this->responseFactory->createResponse(),
            $url,
            $status
        );
    }

    /**
     * @inheritdoc
     */
    public static function to(
        ResponseInterface $response,
        string $url,
        int $status = self::STATUS_FOUND
    ): ResponseInterface {
        static::checkStatus($status);

        return $response
            ->withStatus($status)
            ->withHeader('Location', $url);
    }
}
