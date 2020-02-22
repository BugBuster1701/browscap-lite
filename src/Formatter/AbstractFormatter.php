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

namespace BugBuster\Browscap\Formatter;

/**
 * Abstract formatter class.
 *
 * The formatter is used to convert the basic browscap settings
 * array into the preferred format. It can also be used to unset unnecessary
 * data or extend the result with additional data from other sources.
 *
 * @deprecated implement FormatterInterface instead
 */
abstract class AbstractFormatter implements FormatterInterface
{
    /**
     * Variable to save the settings in, type depends on implementation.
     *
     * @var mixed
     */
    protected $settings;

    /**
     * Sets the data (done by the parser).
     */
    abstract public function setData(array $settings);

    /**
     * Gets the data (in the preferred format).
     */
    abstract public function getData();
}
