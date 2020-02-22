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

namespace BugBuster\Browscap\Updater;

/**
 * None updater class.
 *
 * This updater does nothing, so if you set it, the source data won't be updated.
 *
 */
class None extends AbstractUpdater
{
    /**
     * None constructor.
     *
     * @param null $options
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($options = null)
    {
        parent::__construct($options);

        // Set update method
        $this->updateMethod = 'none';
    }

    /**
     * Gets the current browscap version (time stamp).
     *
     * @return int
     */
    public function getBrowscapVersion()
    {
        return 0;
    }

    /**
     * Gets the current browscap version number (if possible for the source).
     *
     * @return int|null
     */
    public function getBrowscapVersionNumber()
    {
        return null;
    }

    /**
     * Gets the browscap data of the used source type.
     *
     * @return string|bool
     */
    public function getBrowscapSource()
    {
        return false;
    }
}
