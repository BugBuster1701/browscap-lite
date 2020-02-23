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
 * Curl updater class.
 *
 * This class loads the source data using the curl extension.
 */
class Curl extends AbstractUpdaterRemote
{
    /**
     * Curl constructor.
     *
     * @param null $options
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($options = null)
    {
        parent::__construct($options);

        // Set update method
        $this->updateMethod = 'cURL';

        // Add additional options
        $this->options['ConnectTimeout'] = 5;
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

        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->getOption('ConnectTimeout'));
        curl_setopt($curl, CURLOPT_USERAGENT, $this->getUserAgent());

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

            $proxyPort = $this->getOption('ProxyPort');

            // check auth settings
            $proxyAuth = $this->getOption('ProxyAuth');
            if (null !== $proxyAuth) {
                if (!\in_array($proxyAuth, [self::PROXY_AUTH_BASIC, self::PROXY_AUTH_NTLM], true)) {
                    throw new \RuntimeException("Invalid/unsupported value '$proxyAuth' for option 'ProxyAuth'.");
                }
            } else {
                $proxyAuth = self::PROXY_AUTH_BASIC;
            }
            $proxyUser = $this->getOption('ProxyUser');
            $proxyPassword = $this->getOption('ProxyPassword');

            // set basic proxy options
            curl_setopt($curl, CURLOPT_PROXY, $proxyProtocol.'://'.$proxyHost);
            if (null !== $proxyPort) {
                curl_setopt($curl, CURLOPT_PROXYPORT, $proxyPort);
            }

            // set proxy auth options
            if (null !== $proxyUser) {
                if (self::PROXY_AUTH_NTLM === $proxyAuth) {
                    curl_setopt($curl, CURLOPT_PROXYAUTH, CURLAUTH_NTLM);
                }
                curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxyUser.':'.$proxyPassword);
            }
        }

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        // reset time limit to the previous value
        set_time_limit($maxExecutionTime);

        // check for HTTP error
        $this->getHttpErrorException($httpCode, true);

        return $response;
    }
}
