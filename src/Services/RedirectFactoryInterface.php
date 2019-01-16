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
 * Redirect Factory Interface
 */
interface RedirectFactoryInterface
{
    /**
     * @param string $url URL
     * @param int $status HTTP Status Code from 301 to 308
     * @return \Psr\Http\Message\ResponseFactoryInterface
     */
    public function create(string $url, int $status): ResponseInterface;
}
