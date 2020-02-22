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

namespace BugBuster\Browscap\Cache;

/**
 * Abstract cache class.
 *
 * This cache class is very simple, because the cache we use never expires.
 * So all we have are four basic methods, all with an option to cache the
 * data in dependence of the current version.
 *
 * @deprecated implement CacheInterface instead
 */
abstract class AbstractCache implements CacheInterface
{
    /**
     * @param string $key
     * @param bool   $with_version
     *
     * @return string|null
     */
    abstract public function get($key, $with_version = true);

    /**
     * Set cached data for a given key.
     *
     * @param string $key
     * @param string $content
     * @param bool   $with_version
     *
     * @return int|false
     */
    abstract public function set($key, $content, $with_version = true);

    /**
     * Delete cached data by a given key.
     *
     * @param string $key
     * @param bool   $with_version
     *
     * @return bool
     */
    abstract public function delete($key, $with_version = true);

    /**
     * Check if a key is already cached.
     *
     * @param string $key
     * @param bool   $with_version
     *
     * @return bool
     */
    abstract public function exists($key, $with_version = true);
}
