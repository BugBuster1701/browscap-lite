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
 * Updater factory class.
 *
 * This class checks the current settings and returns the best matching
 * updater instance (except the local updater, which requires additional
 * settings and can therefore only be set manually).
 */
class FactoryUpdater
{
    /**
     * Get a available updater instance, or returns NULL is none available.
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return \BugBuster\Browscap\Updater\AbstractUpdater
     */
    public static function getInstance()
    {
        if (\function_exists('curl_init')) {
            return new Curl();
        }
        if (false !== (bool) (int) ini_get('allow_url_fopen')) {
            return new FileGetContents();
        }
        if ('' !== ($browscapFile = (string) ini_get('browscap'))) {
            $updater = new Local();
            $updater->setOption('LocalFile', $browscapFile);

            return $updater;
        }

        return new None();
    }
}
