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
 * Abstract updater class.
 *
 * With the updater class you get all required data from local or remote
 * sources - the new source content, the version (time stamp) and
 * (in most cases) also the version number. It also offers to set individual
 * options for each type of updater.
 */
abstract class AbstractUpdater
{
    /**
     * Update interval in seconds, default 432000 (5 days).
     *
     * @var int
     */
    protected $interval = 432000;

    /**
     * Name of the update method, used in the user agent for the request,
     * for browscap download statistics. Has to be overwritten by the
     * extending class.
     *
     * @var string
     */
    protected $updateMethod = '';

    /**
     * Options for the updater. The array should be overwritten,
     * containing all options as keys, set to the default value.
     *
     * @var array
     */
    protected $options = [];

    /**
     * @param array|null $options
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($options = null)
    {
        if (null !== $options) {
            if (\is_array($options)) {
                $this->setOptions($options);
            } else {
                throw new \InvalidArgumentException("Invalid value for 'options', array expected.");
            }
        }
    }

    /**
     * Sets the update interval in seconds.
     *
     * @param int $interval
     *
     * @return \BugBuster\Browscap\Updater\AbstractUpdater
     */
    public function setInterval($interval)
    {
        $this->interval = (int) $interval;

        return $this;
    }

    /**
     * Gets the update interval in seconds.
     *
     * @return int
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * Sets multiple updater options at once.
     *
     * @throws \InvalidArgumentException
     *
     * @return \BugBuster\Browscap\Updater\AbstractUpdater
     */
    public function setOptions(array $options)
    {
        foreach ($options as $optionKey => $optionValue) {
            $this->setOption($optionKey, $optionValue);
        }

        return $this;
    }

    /**
     * Sets an updater option value.
     *
     * @param string $key
     *
     * @throws \InvalidArgumentException
     *
     * @return \BugBuster\Browscap\Updater\AbstractUpdater
     */
    public function setOption($key, $value)
    {
        if (\array_key_exists($key, $this->options)) {
            $this->options[$key] = $value;
        } else {
            throw new \InvalidArgumentException("Invalid option key '".(string) $key."'.");
        }

        return $this;
    }

    /**
     * Gets an updater option value.
     *
     * @param string $key
     *
     * @return mixed|null
     */
    public function getOption($key)
    {
        if (\array_key_exists($key, $this->options)) {
            return $this->options[$key];
        }

        return null;
    }

    /**
     * Gets the current browscap version (time stamp).
     *
     * @return int
     */
    abstract public function getBrowscapVersion();

    /**
     * Gets the current browscap version number (if possible for the source).
     *
     * @return int|null
     */
    abstract public function getBrowscapVersionNumber();

    /**
     * Gets the browscap data of the used source type.
     *
     * @return string
     */
    abstract public function getBrowscapSource();

    /**
     * Gets the configured update method, used in the user agent for the request.
     *
     * @return string
     */
    protected function getUpdateMethod()
    {
        return $this->updateMethod;
    }
}
