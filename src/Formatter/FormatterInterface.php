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
 * Formatter interface.
 *
 * The formatter is used to convert the basic browscap settings
 * array into the preferred format. It can also be used to unset unnecessary
 * data or extend the result with additional data from other sources.
 *
 */
interface FormatterInterface
{
    /**
     * Sets the data (done by the parser).
     */
    public function setData(array $settings);

    /**
     * Gets the data (in the preferred format).
     */
    public function getData();
}
