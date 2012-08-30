<?php
/**
 * This file is part of the Small Neat Box Framework
 * Copyright (c) 2011-2012 Small Neat Box Ltd.
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace asset\assets;
use asset\filters\FilterInterface;

abstract class BaseAsset implements AssetInterface
{
    protected $filters;
    protected $lastModified;

    public function __construct($filters = array())
    {
        $this->clearFilters();
        $this->lastModified = 0;

        // Copy the filters over
        if (is_array($filters)) {
            foreach ($filters as $filter) {
                $this->addFilter($filter);
            }
        }
    }

    /**
     * add a new filter to the asset
     * @param \asset\filters\FilterInterface $filter
     */
    public function addFilter(FilterInterface $filter)
    {
        $this->filters[] = $filter;
    }


    /**
     * Gets the list of filters attached to the asset
     * @return mixed
     */
    public function getFilters()
    {
        return $this->filters;
    }


    /**
     * Get rid of any filters attached
     */
    public function clearFilters()
    {
        $this->filters = array();
    }


    /**
     * @param string $content
     * @return string
     */
    protected function applyFilters($content)
    {
        // Apply any filters attached to us
        foreach ($this->filters as $filter) {
            $content = $filter->filter($content);
        }

        // return the result of that work
        return $content;
    }
}
