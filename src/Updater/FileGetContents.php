<?php

declare(strict_types=1);

/*
 * This file is part of a BugBuster Library
 *
 * @copyright  Glen Langer 2020 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @author     Christoph Ziegenberg (crossjoin/browscap)
 * @package    Browscap Lite Library
 * @license    MIT
 * @see        https://github.com/BugBuster1701/browscap-lite
 */

namespace BugBuster\Browscap\Updater;

/**
 * FileGetContents updater class.
 *
 * This class loads the source data using the file_get_contents() function.
 * Please note, that this requires 'allow_url_fopen' set to '1' to work
 * with remote files.
 */
class FileGetContents extends AbstractUpdaterRemote
{
    /**
     * FileGetContents constructor.
     *
     * @param null $options
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function __construct($options = null)
    {
        parent::__construct($options);

        if (false === (bool) (int) ini_get('allow_url_fopen')) {
            throw new \RuntimeException("Please activate 'allow_url_fopen'.");
        }

        // Set update method
        $this->updateMethod = 'URL-wrapper';

        // Add additional options
        $this->options['ScriptTimeLimit'] = 300;
    }

    /**
     * Gets the data from a given URL (or false on failure).
     *
     * @param string $url
     *
     * @throws \RuntimeException
     *
     * @return string|bool
     */
    protected function getRemoteData($url)
    {
        // set time limit, required to get the data
        $maxExecutionTime = ini_get('max_execution_time');
        set_time_limit($this->getOption('ScriptTimeLimit'));

        $context = $this->getStreamContext();
        $return = file_get_contents($url, false, $context);

        // reset time limit to the previous value
        set_time_limit($maxExecutionTime);

        /**
         * $http_response_header is a predefined variables,
         * automatically created by PHP after the call above.
         *
         * @see http://php.net/manual/en/reserved.variables.httpresponseheader.php
         */
        /* @noinspection UnSafeIsSetOverArrayInspection */
        if (isset($http_response_header) &&
            \is_array($http_response_header) &&
            \array_key_exists(0, $http_response_header)
        ) {
            // extract status from first array entry, e.g. from 'HTTP/1.1 200 OK'
            $statusParts = explode(' ', $http_response_header[0], 3);
            $httpCode = $statusParts[1];

            // check for HTTP error
            $this->getHttpErrorException($httpCode, true);
        }

        return $return;
    }

    protected function getStreamContext()
    {
        // set basic stream context configuration
        $config = [
            'http' => [
                'user_agent' => $this->getUserAgent(),
                // ignore errors, handle them manually
                'ignore_errors' => true,
            ],
        ];

        // check and set proxy settings
        $proxyHost = $this->getOption('ProxyHost');
        if (null !== $proxyHost) {
            // check for supported protocol
            $proxyProtocol = $this->getOption('ProxyProtocol');
            if (null !== $proxyProtocol) {
                if (!\in_array($proxyProtocol, [self::PROXY_PROTOCOL_HTTP, self::PROXY_PROTOCOL_HTTPS], true)) {
                    throw new \RuntimeException("Invalid/unsupported value '$proxyProtocol' for option 'ProxyProtocol'.");
                }
            } else {
                $proxyProtocol = self::PROXY_PROTOCOL_HTTP;
            }

            // prepare port for the proxy server address
            $proxyPort = $this->getOption('ProxyPort');
            if (null !== $proxyPort) {
                $proxyPort = ':'.$proxyPort;
            } /* @noinspection DefaultValueInElseBranchInspection */ else {
                $proxyPort = '';
            }

            // check auth settings
            $proxyAuth = $this->getOption('ProxyAuth');
            if (null !== $proxyAuth) {
                if (self::PROXY_AUTH_BASIC !== $proxyAuth) {
                    throw new \RuntimeException("Invalid/unsupported value '$proxyAuth' for option 'ProxyAuth'.");
                }
            } else {
                $proxyAuth = self::PROXY_AUTH_BASIC;
            }

            // set proxy server address
            $config['http']['proxy'] = 'tcp://'.$proxyHost.$proxyPort;
            // full uri required by some proxy servers
            $config['http']['request_fulluri'] = true;

            // add authorization header if required
            $proxyUser = $this->getOption('ProxyUser');
            if (null !== $proxyUser) {
                $proxyPassword = $this->getOption('ProxyPassword');
                if (null === $proxyPassword) {
                    $proxyPassword = '';
                }
                $auth = base64_encode($proxyUser.':'.$proxyPassword);
                $config['http']['header'] = 'Proxy-Authorization: Basic '.$auth;
            }

            // @todo Add SSL context options
            // @see  http://www.php.net/manual/en/context.ssl.php
            //if ($proxy_protocol === self::PROXY_PROTOCOL_HTTPS) {
            //}
        }

        return stream_context_create($config);
    }
}
