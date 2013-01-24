<?php
    /**
     * This file is part of the Small Neat Box Framework
     * Copyright (c) 2011-2012 Small Neat Box Ltd.
     * For the full copyright and license information, please view the LICENSE.txt
     * file that was distributed with this source code.
     */

namespace asset\assets;
use asset\assets\BaseAsset;


/**
 * An asset that represents a single file
 */
class AssetCollection extends BaseAsset
{
    protected $assets;


    /**
     * @param $assets
     * @param array $filters
     */
    public function __construct($assets, $filters=array())
    {
        // default
        parent::__construct($filters);

        // Default to an empty list
        $this->assets = array();

        // Find all the assets in the array passed in, and keep them
        if (is_array($assets)) {
            foreach($assets as $asset) {
                if ($asset instanceof AssetInterface) {
                    $this->assets[] = $asset;
                }
            }
        }
    }



    /**
     * Find the most recently modified time in the list of assets
     * @return int
     */
    public function getLastModified()
    {
        $lastMod = 0;
        foreach ($this->assets as $asset)
        {
            // Find the last modified time of each asset
            $mod = $asset->getLastModified();

            // and remember the most recent change
            if ($mod > $lastMod) {
                $lastMod = $mod;
            }
        }

        return $lastMod;
    }



    /**
     * Generates a token that is based on all the assets in the collection
     * @return string
     */
    public function getToken()
    {
        $key = '';
        foreach ($this->assets as $asset)
        {
            // Add the last modified data to the token key
            $key .= $asset->getToken();
        }

        // Get all the names of the filters
        // (changing the set of filters on a file should change its token)
        foreach ($this->filters as $filter) {
            $key .= $filter->getName();
        }

        // generate the token from the key
        return md5($key);
    }



    /**
     * Returns the content from all the assest in the collection
     * @return string
     */
    public function getContent()
    {
        $content = '';
        foreach ($this->assets as $asset) {
            // Add the last modified data to the token key
            $content .= $asset->getContent();
            $content .= "\n\n";
        }

        // apply the filters and return it
        return $this->applyFilters($content);
    }
}