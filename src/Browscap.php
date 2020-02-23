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

namespace BugBuster\Browscap;

use BugBuster\Browscap\Formatter\FormatterInterface;

/**
 * Main BugBuster\Browscap class.
 *
 * BugBuster\Browscap allows to check for browser settings, using the data
 * from the Browscap project (browscap.org). It's about 40x faster than the
 * get_browser() function in PHP, with a very small memory consumption.
 *
 * It includes automatic updates of the Browscap data and allows to extends
 * or replace nearly all components: the updater, the parser (including the
 * used source), and the formatter (for the result set).
 */
class Browscap
{
    /**
     * Current version of the package.
     * Has to be updated to automatically renew cache data.
     */
    public const VERSION = '1.1.0';

    /**
     * Data set types.
     */
    public const DATASET_TYPE_DEFAULT = 1;
    public const DATASET_TYPE_SMALL = 2;
    public const DATASET_TYPE_LARGE = 3;

    /**
     * Use automatic updates (if no explicit updater set).
     *
     * @var \BugBuster\Browscap\Updater\AbstractUpdater
     */
    protected $autoUpdate;

    /**
     * Updater to use.
     *
     * @var \BugBuster\Browscap\Updater\AbstractUpdater
     */
    protected static $updater;

    /**
     * Parser to use.
     *
     * @var \BugBuster\Browscap\Parser\AbstractParser
     */
    protected static $parser;

    /**
     * Formatter to use.
     *
     * @var FormatterInterface
     */
    protected static $formatter;

    /**
     * The data set type to use (default, small or large,
     * see constants).
     */
    protected static $datasetType = self::DATASET_TYPE_DEFAULT;

    /**
     * Probability in percent that the update check is done.
     *
     * @var float
     */
    protected $updateProbability = 1.0;

    /**
     * Constructor.
     *
     * @param bool $autoUpdate
     */
    public function __construct($autoUpdate = true)
    {
        $this->autoUpdate = (bool) (int) $autoUpdate;
    }

    /**
     * Checks the given/detected user agent and returns a
     * formatter instance with the detected settings.
     *
     * @param string $userAgent
     *
     * @return FormatterInterface
     */
    public function getBrowser($userAgent = null)
    {
        // automatically detect the user agent
        if (null === $userAgent) {
            $userAgent = '';
            if (\array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
                $userAgent = $_SERVER['HTTP_USER_AGENT'];
            }
        }

        // check for update first
        if (true === $this->autoUpdate) {
            $randomMax = floor(100 / $this->updateProbability);
            $randomInt = random_int(1, $randomMax);
            if (1 === $randomInt) {
                static::getParser()->update();
            }
        }

        // try to get browser data
        $return = static::getParser()->getBrowser($userAgent);

        // if not found, there has to be a problem with the source data,
        // because normally default browser data are returned,
        // so set the probability to 100%, to force an update.
        if (null === $return && $this->updateProbability < 100) {
            $updateProbability = $this->updateProbability;
            $this->updateProbability = 100;
            $return = $this->getBrowser($userAgent);
            $this->updateProbability = $updateProbability;
        }

        // if return is still NULL, updates are disabled... in this
        // case we return an empty formatter instance
        if (null === $return) {
            $return = static::getFormatter();
        }

        return $return;
    }

    /**
     * Set the formatter instance to use for the getBrowser() result.
     */
    public static function setFormatter(FormatterInterface $formatter): void
    {
        static::$formatter = $formatter;
    }

    /**
     * @return FormatterInterface
     */
    public static function getFormatter()
    {
        if (null === static::$formatter) {
            static::setFormatter(new Formatter\PhpGetBrowser());
        }

        return static::$formatter;
    }

    /**
     * Sets the parser instance to use.
     *
     * @param \BugBuster\Browscap\Parser\AbstractParser $parser
     */
    public static function setParser(Parser\AbstractParser $parser): void
    {
        static::$parser = $parser;
    }

    /**
     * @return Parser\AbstractParser
     */
    public static function getParser()
    {
        if (null === static::$parser) {
            static::setParser(new Parser\Ini());
        }

        return static::$parser;
    }

    /**
     * Sets the updater instance to use.
     *
     * @param \BugBuster\Browscap\Updater\AbstractUpdater $updater
     */
    public static function setUpdater(Updater\AbstractUpdater $updater): void
    {
        static::$updater = $updater;
    }

    /**
     * Gets the updater instance (and initializes the default one, if not set).
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return \BugBuster\Browscap\Updater\AbstractUpdater
     */
    public static function getUpdater()
    {
        if (null === static::$updater) {
            $updater = Updater\FactoryUpdater::getInstance();
            if (null !== $updater) {
                static::setUpdater($updater);
            }
        }

        return static::$updater;
    }

    /**
     * Sets the data set type to use for the source.
     *
     * @param int $dataSetType
     *
     * @throws \InvalidArgumentException
     */
    public static function setDataSetType($dataSetType): void
    {
        if (\in_array(
            $dataSetType,
            [self::DATASET_TYPE_DEFAULT, self::DATASET_TYPE_SMALL, self::DATASET_TYPE_LARGE],
            true
        )) {
            static::$datasetType = $dataSetType;
        } else {
            throw new \InvalidArgumentException("Invalid value for argument 'dataSetType'.");
        }
    }

    /**
     * Gets the data set type to use for the source.
     *
     * @return int
     */
    public static function getDataSetType()
    {
        return static::$datasetType;
    }

    /**
     * Triggers an update check (with the option to force an update).
     *
     * @param bool $forceUpdate
     */
    public static function update($forceUpdate = false): void
    {
        static::getParser()->update($forceUpdate);
    }
}
