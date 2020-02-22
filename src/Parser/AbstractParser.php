<?php

declare(strict_types=1);

/*
 * This file is part of a BugBuster Contao Bundle
 *
 * @copyright  Glen Langer 2020 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @author     Christoph Ziegenberg (crossjoin/browscap)
 * @package    Contao Browscap Lite Bundle
 * @license    MIT
 * @see        https://github.com/BugBuster1701/browscap-lite
 */

namespace BugBuster\Browscap\Parser;

use BugBuster\Browscap\Browscap;
use BugBuster\Browscap\Cache\CacheInterface;
use BugBuster\Browscap\Cache\File;
use BugBuster\Browscap\Formatter\FormatterInterface;

/**
 * Abstract parser class.
 *
 * The parser is the component, that parses a specific type of browscap source
 * data for the browser settings of a given user agent.
 *
 */
abstract class AbstractParser
{
    /**
     * Detected browscap version (read from INI file).
     *
     * @var int
     */
    protected static $version;

    /**
     * The cache instance.
     *
     * @var CacheInterface
     */
    protected static $cache;

    /**
     * The type to use when downloading the browscap source data
     * (default version: all browsers, default properties),
     * has to be set by the extending class, e.g. 'PHP_BrowscapINI'.
     *
     * @see http://browscap.org/
     *
     * @var string
     */
    protected $sourceType = '';

    /**
     * The type to use when downloading the browscap source data
     * (small version: popular browsers, default properties),
     * has to be set by the extending class, e.g. 'Lite_PHP_BrowscapINI'.
     *
     * @see http://browscap.org/
     *
     * @var string
     */
    protected $sourceTypeSmall = '';

    /**
     * The type to use when downloading the browscap source data
     * (large version: all browsers, extended properties),
     * has to be set by the extending class, e.g. 'Full_PHP_BrowscapINI'.
     *
     * @see http://browscap.org/
     *
     * @var string
     */
    protected $sourceTypeLarge = '';

    /**
     * Gets the type of source to use.
     *
     * @return string
     */
    public function getSourceType()
    {
        switch (Browscap::getDataSetType()) {
            case Browscap::DATASET_TYPE_SMALL:
                return $this->sourceTypeSmall;
            case Browscap::DATASET_TYPE_LARGE:
                return $this->sourceTypeLarge;
            default:
                return $this->sourceType;
        }
    }

    /**
     * Gets the version of the Browscap data.
     *
     * @return int
     */
    abstract public function getVersion();

    /**
     * Gets the browser data formatter for the given user agent
     * (or null if no data available, no even the default browser).
     *
     * @param string $userAgent
     *
     * @return FormatterInterface|null
     */
    abstract public function getBrowser($userAgent);

    /**
     * Gets a cache instance.
     *
     * @return CacheInterface
     */
    public static function getCache()
    {
        if (null === static::$cache) {
            static::$cache = new File();
        }

        return static::$cache;
    }

    /**
     * Sets a cache instance.
     */
    public static function setCache(CacheInterface $cache): void
    {
        static::$cache = $cache;
    }

    /**
     * Checks if the source needs to be updated and processes the update.
     *
     * @param bool $forceUpdate
     */
    abstract public function update($forceUpdate = false);

    /**
     * Resets cached data (e.g. the version) after an update of the source.
     */
    public static function resetCachedData(): void
    {
        static::$version = null;
    }

    /**
     * Gets the cache prefix, dependent of the used browscap data set type.
     *
     * @return string
     */
    protected static function getCachePrefix()
    {
        switch (Browscap::getDataSetType()) {
            case Browscap::DATASET_TYPE_SMALL:
                return 'smallbrowscap';
            case Browscap::DATASET_TYPE_LARGE:
                return 'largebrowscap';
            default:
                return 'browscap';
        }
    }
}
