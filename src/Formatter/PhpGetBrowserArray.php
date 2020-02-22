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
 * PhpGetBrowser formatter class.
 *
 * This formatter modifies the basic data, so that you get the same result
 * as with the PHP get_browser() function (an array, and all keys lower case).
 *
 * @note There is one difference: The wrong encoded character used in
 * "browser_name_regex" of the standard PHP get_browser() result has been
 * replaced. The regular expression itself is the same.
 *
 * @see https://bugs.debian.org/cgi-bin/bugreport.cgi?bug=612364
 * @see https://github.com/crossjoin/browscap
 */
class PhpGetBrowserArray implements FormatterInterface
{
    /**
     * @var array
     */
    protected $settings = [];

    /**
     * Sets the data (done by the parser).
     */
    public function setData(array $settings): void
    {
        $this->settings = [];
        foreach ($settings as $key => $value) {
            $key = strtolower($key);
            $this->settings[$key] = $value;
        }
    }

    /**
     * Gets the data (in the preferred format).
     *
     * @return \stdClass
     */
    public function getData()
    {
        return $this->settings;
    }
}
