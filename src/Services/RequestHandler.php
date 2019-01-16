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

use Psr\Http\Message\ServerRequestInterface;

/**
 * Request Handler
 */
class RequestHandler implements RequestHandlerInterface
{
    /**
     * The built in detectors used with `is()` can be modified with `addDetector()`.
     *
     * There are several ways to specify a detector, see \Cake\Http\ServerRequest::addDetector() for the
     * various formats and ways to define detectors.
     *
     * @var array
     */
    protected $detectors = [
        'get' => ['env' => 'REQUEST_METHOD', 'value' => 'GET'],
        'post' => ['env' => 'REQUEST_METHOD', 'value' => 'POST'],
        'put' => ['env' => 'REQUEST_METHOD', 'value' => 'PUT'],
        'patch' => ['env' => 'REQUEST_METHOD', 'value' => 'PATCH'],
        'delete' => ['env' => 'REQUEST_METHOD', 'value' => 'DELETE'],
        'head' => ['env' => 'REQUEST_METHOD', 'value' => 'HEAD'],
        'options' => ['env' => 'REQUEST_METHOD', 'value' => 'OPTIONS'],
        'ssl' => ['env' => 'HTTPS', 'options' => [1, 'on']],
        'ajax' => ['env' => 'HTTP_X_REQUESTED_WITH', 'value' => 'XMLHttpRequest'],
        'flash' => ['env' => 'HTTP_USER_AGENT', 'pattern' => '/^(Shockwave|Adobe) Flash/'],
        'json' => ['accept' => ['application/json'], 'param' => '_ext', 'value' => 'json'],
        'xml' => ['accept' => ['application/xml', 'text/xml'], 'param' => '_ext', 'value' => 'xml'],
        'html' => ['accept' => ['text/html', 'application/xhtml', 'application/xhtml+xml'], 'param' => '_ext', 'value' => 'html'],
    ];

    /**
     * Server Request
     *
     * @var \Psr\Http\Message\ServerRequestInterface
     */
    protected $request;

    /**
     * Instance cache for results of is(something) calls
     *
     * @var array
     */
    protected $detectorCache = [];

    /**
     * Array of environment data.
     *
     * @var array
     */
    protected $environment = [];

    /**
     * Constructor
     *
     * @var \Psr\Http\Message\ServerRequestInterface
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
        $this->environment = $request->getServerParams();
    }

    /**
     * Check whether or not a Request is a certain type.
     *
     * Uses the built in detection rules as well as additional rules
     * defined with Cake\Http\ServerRequest::addDetector(). Any detector can be called
     * as `is($type)` or `is$Type()`.
     *
     * @param string|array $type The type of request you want to check. If an array
     *   this method will return true if the request matches any type.
     * @param array ...$args List of arguments
     * @return bool Whether or not the request is the type you are checking.
     */
    public function is($type, ...$args): bool
    {
        if (is_array($type)) {
            $result = array_map([$this, 'is'], $type);

            return count(array_filter($result)) > 0;
        }

        $type = strtolower($type);
        if (!isset($this->detectors[$type])) {
            return false;
        }
        if ($args) {
            return $this->_is($type, $args);
        }
        if (!isset($this->_detectorCache[$type])) {
            $this->_detectorCache[$type] = $this->_is($type, $args);
        }

        return $this->_detectorCache[$type];
    }

    /**
     * Check that a request matches all the given types.
     *
     * Allows you to test multiple types and union the results.
     * See Request::is() for how to add additional types and the
     * built-in types.
     *
     * @param array $types The types to check.
     * @return bool Success.
     */
    public function isAll(array $types): bool
    {
        $result = array_filter(array_map([$this, 'is'], $types));

        return count($result) === count($types);
    }

    /**
     * Add a new detector to the list of detectors that a request can use.
     * There are several different formats and types of detectors that can be set.
     *
     * ### Callback detectors
     *
     * Callback detectors allow you to provide a callable to handle the check.
     * The callback will receive the request object as its only parameter.
     *
     * ```
     * addDetector('custom', function ($request) { //Return a boolean });
     * addDetector('custom', ['SomeClass', 'somemethod']);
     * ```
     *
     * ### Environment value comparison
     *
     * An environment value comparison, compares a value fetched from `env()` to a known value
     * the environment value is equality checked against the provided value.
     *
     * e.g `addDetector('post', ['env' => 'REQUEST_METHOD', 'value' => 'POST'])`
     *
     * ### Pattern value comparison
     *
     * Pattern value comparison allows you to compare a value fetched from `env()` to a regular expression.
     *
     * ```
     * addDetector('iphone', ['env' => 'HTTP_USER_AGENT', 'pattern' => '/iPhone/i']);
     * ```
     *
     * ### Option based comparison
     *
     * Option based comparisons use a list of options to create a regular expression. Subsequent calls
     * to add an already defined options detector will merge the options.
     *
     * ```
     * addDetector('mobile', ['env' => 'HTTP_USER_AGENT', 'options' => ['Fennec']]);
     * ```
     *
     * ### Request parameter detectors
     *
     * Allows for custom detectors on the request parameters.
     *
     * e.g `addDetector('requested', ['param' => 'requested', 'value' => 1]`
     *
     * You can also make parameter detectors that accept multiple values
     * using the `options` key. This is useful when you want to check
     * if a request parameter is in a list of options.
     *
     * `addDetector('extension', ['param' => 'ext', 'options' => ['pdf', 'csv']]`
     *
     * @param string $name The name of the detector.
     * @param callable|array $callable A callable or options array for the detector definition.
     * @return void
     */
    public function addDetector(string $name, callable $callable): void
    {
        $name = strtolower($name);
        if (is_callable($callable)) {
            $this->detectors[$name] = $callable;

            return;
        }
        if (isset($this->detectors[$name], $callable['options'])) {
            $callable = Hash::merge($this->detectors[$name], $callable);
        }
        $this->detectors[$name] = $callable;
    }

    /**
     * Worker for the public is() function
     *
     * @param string $type The type of request you want to check.
     * @param array $args Array of custom detector arguments.
     * @return bool Whether or not the request is the type you are checking.
     */
    protected function _is($type, $args)
    {
        $detect = $this->detectors[$type];
        if (is_callable($detect)) {
            array_unshift($args, $this);

            return $detect(...$args);
        }

        if (isset($detect['env']) && $this->_environmentDetector($detect)) {
            return true;
        }
        if (isset($detect['header']) && $this->_headerDetector($detect)) {
            return true;
        }
        if (isset($detect['accept']) && $this->_acceptHeaderDetector($detect)) {
            return true;
        }
        if (isset($detect['param']) && $this->_requestAttributeDetector($detect)) {
            return true;
        }

        return false;
    }

    /**
     * Detects if a specific accept header is present.
     *
     * @param array $detect Detector options array.
     * @return bool Whether or not the request is the type you are checking.
     */
    protected function _acceptHeaderDetector($detect)
    {
        $acceptHeaders = explode(',', $this->getEnv('HTTP_ACCEPT'));
        foreach ($detect['accept'] as $header) {
            if (in_array($header, $acceptHeaders)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detects if a specific header is present.
     *
     * @param array $detect Detector options array.
     * @return bool Whether or not the request is the type you are checking.
     */
    protected function _headerDetector($detect)
    {
        foreach ($detect['header'] as $header => $value) {
            $header = $this->getEnv('http_' . $header);
            if ($header !== null) {
                if (!is_string($value) && !is_bool($value) && is_callable($value)) {
                    return call_user_func($value, $header);
                }

                return ($header === $value);
            }
        }

        return false;
    }

    /**
     * Detects if a specific request attribute is present.
     *
     * @param array $detect Detector options array.
     * @return bool Whether or not the request is the type you are checking.
     */
    protected function _requestAttributeDetector($detect)
    {
        $key = $detect['param'];
        if (isset($detect['value'])) {
            $value = $detect['value'];

            return !is_null($this->request->getAttribute($key)) ? $this->request->getAttribute($key) == $value : false;
        }
        if (isset($detect['options'])) {
            return is_null($this->request->getAttribute($key)) ? in_array($this->request->getAttribute($key), $detect['options']) : false;
        }

        return false;
    }

    /**
     * Detects if a specific environment variable is present.
     *
     * @param array $detect Detector options array.
     * @return bool Whether or not the request is the type you are checking.
     */
    protected function _environmentDetector($detect)
    {
        if (isset($detect['env'])) {
            if (isset($detect['value'])) {
                return $this->getEnv($detect['env']) == $detect['value'];
            }
            if (isset($detect['pattern'])) {
                return (bool)preg_match($detect['pattern'], $this->getEnv($detect['env']));
            }
            if (isset($detect['options'])) {
                $pattern = '/' . implode('|', $detect['options']) . '/i';

                return (bool)preg_match($pattern, $this->getEnv($detect['env']));
            }
        }

        return false;
    }

    /**
     * Get a value from the request's environment data.
     * Fallback to using env() if the key is not set in the $environment property.
     *
     * @param string $key The key you want to read from.
     * @param string|null $default Default value when trying to retrieve an environment
     *   variable's value that does not exist.
     * @return string|null Either the environment value, or null if the value doesn't exist.
     */
    public function getEnv($key, $default = null)
    {
        $key = strtoupper($key);
        if (!array_key_exists($key, $this->environment)) {
            $this->environment[$key] = \App\env($key);
        }

        return $this->environment[$key] !== null ? $this->environment[$key] : $default;
    }

    /**
     * Gets an environment variable from available sources, and provides emulation
     * for unsupported or inconsistent environment variables (i.e. DOCUMENT_ROOT on
     * IIS, or SCRIPT_NAME in CGI mode). Also exposes some additional custom
     * environment information.
     *
     * @param string $key Environment variable name.
     * @param string|null $default Specify a default value in case the environment variable is not defined.
     * @return string|bool|null Environment variable setting.
     * @link https://book.cakephp.org/3.0/en/core-libraries/global-constants-and-functions.html#env
     */
    public function env($key, $default = null)
    {
        if ($key === 'HTTPS') {
            if (isset($_SERVER['HTTPS'])) {
                return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
            }

            return (strpos((string)$this->env('SCRIPT_URI'), 'https://') === 0);
        }

        if ($key === 'SCRIPT_NAME' && $this->env('CGI_MODE') && isset($_ENV['SCRIPT_URL'])) {
            $key = 'SCRIPT_URL';
        }

        $val = null;
        if (isset($_SERVER[$key])) {
            $val = $_SERVER[$key];
        } elseif (isset($_ENV[$key])) {
            $val = $_ENV[$key];
        } elseif (getenv($key) !== false) {
            $val = getenv($key);
        }

        if ($key === 'REMOTE_ADDR' && $val === env('SERVER_ADDR')) {
            $addr = $this->env('HTTP_PC_REMOTE_ADDR');
            if ($addr !== null) {
                $val = $addr;
            }
        }

        if ($val !== null) {
            return $val;
        }

        switch ($key) {
            case 'DOCUMENT_ROOT':
                $name = $this->env('SCRIPT_NAME');
                $filename = $this->env('SCRIPT_FILENAME');
                $offset = 0;
                if (!strpos($name, '.php')) {
                    $offset = 4;
                }

                return substr($filename, 0, -(strlen($name) + $offset));
            case 'PHP_SELF':
                return str_replace($this->env('DOCUMENT_ROOT'), '', $this->env('SCRIPT_FILENAME'));
            case 'CGI_MODE':
                return (PHP_SAPI === 'cgi');
        }

        return $default;
    }
}
