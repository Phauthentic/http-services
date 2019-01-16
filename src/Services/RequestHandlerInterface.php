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
 * Maps mime types to renderer implementations
 */
interface RequestHandlerInterface
{
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
    public function is($type, ...$args): bool;

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
    public function isAll(array $types): bool;

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
    public function addDetector(string $name, callable $callable): void;

    /**
     * Get a value from the request's environment data.
     * Fallback to using env() if the key is not set in the $environment property.
     *
     * @param string $key The key you want to read from.
     * @param string|null $default Default value when trying to retrieve an environment
     *   variable's value that does not exist.
     * @return string|null Either the environment value, or null if the value doesn't exist.
     */
    public function getEnv($key, $default = null);

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
    public function env($key, $default = null);
}
