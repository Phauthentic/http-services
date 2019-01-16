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

namespace App\Application\Http;

use InvalidArgumentException;
use Phauthentic\Http\Services\Redirect;
use Phauthentic\Http\Services\RedirectInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Redirect Factory
 */
class RedirectFactory
{
    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * Creates a new redirect
     */
    public function createRedirect(string $url, int $status = RedirectInterface::STATUS_FOUND): RedirectInterface
    {
        return (new Redirect($this->responseFactory));
    }
}
