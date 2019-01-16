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

use Psr\Http\Message\ResponseInterface;

/**
 * Redirect interface
 */
interface RedirectInterface
{
     const STATUS_MULTIPLE_CHOICES = 300;
     const STATUS_MOVED_PERMANENTLY = 301;
     const STATUS_FOUND = 302;
     const STATUS_SEE_OTHER = 303;
     const STATUS_NOT_MODIFIED = 304;
     const STATUS_USE_PROXY = 305;
     const STATUS_RESERVED = 306;
     const STATUS_TEMPORARY_REDIRECT = 307;
     const STATUS_PERMANENT_REDIRECT = 308;

    /**
     * @param \Psr\Http\Message\ResponseFactoryInterface $response Response
     * @param string $url URL
     * @param int $status HTTP Status Code from 301 to 308
     * @return \Psr\Http\Message\ResponseFactoryInterface
     */
    public static function to(ResponseInterface $response, string $url, int $status): ResponseInterface;
}
