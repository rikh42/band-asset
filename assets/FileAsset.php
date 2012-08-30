<?php
/**
 * This file is part of the Small Neat Box Framework
 * Copyright (c) 2011-2012 Small Neat Box Ltd.
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace asset\assets;
use asset\filters\FilterInterface;
use asset\assets\BaseAsset;


/**
 * An asset that represents a single file
 */
class FileAsset extends BaseAsset
{
    protected $srcFile;
    protected $initialised;

    public function __construct($filePath, $filters=array())
    {
        // default
        parent::__construct($filters);
        $this->initialised = false;
        $this->srcFile = $filePath;
    }

    /**
     * Return the last modified time of the file
     * @return int
     */
    public function getLastModified()
    {
        $this->initialise();
        return $this->lastModified;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        $this->initialise();
        $content = file_get_contents($this->srcFile);
        return $this->applyFilters($content);
    }


    /**
     * Get a token that changes when the file does
     * @return string
     */
    public function getToken()
    {
        // Ensure everything is up to date
        $this->initialise();

        // Get all the names of the filters
        // (changing the set of filters on a file should change its token)
        $filters = '';
        foreach ($this->filters as $filter) {
            $filters .= $filter->getName();
        }

        // Build the token out of the bits we have
        return md5($this->srcFile . $this->lastModified . $filters);
    }


    /**
     * Avoid issueing more file system requests than we have to
     * @return mixed
     */
    protected function initialise()
    {
        // If we have already done this, stop now
        if ($this->initialised) {
            return;
        }

        // Find out when the file was last modified.
        $this->lastModified = filemtime($this->srcFile);
        $this->initialised = true;
    }
}
