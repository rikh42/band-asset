<?php
/**
 * This file is part of the Small Neat Box Framework
 * Copyright (c) 2011-2012 Small Neat Box Ltd.
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace asset\tests\assets;

use asset\assets\FileAsset;
use asset\filters\CssMinifyFilter;


/**
 * File Asset test class
 */
class FileAssetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Last Modified date
     */
    public function testLastModified()
    {
        $asset = new FileAsset(__FILE__);
        $this->assertInternalType('int', $asset->getLastModified(), "Test that Last Modified is an int");
    }

    public function testContentPresent()
    {
        // Get this file
        $asset = new FileAsset(__FILE__);
        $this->assertNotEmpty($asset->getContent(), "Asset content should contain something");
    }

    public function testContent()
    {
        // Get this file
        $asset = new FileAsset(__FILE__);
        $this->assertStringEqualsFile(__FILE__, $asset->getContent(), "Asset content should match file content");
    }


    public function testIsAssetInterface()
    {
        // Get this file
        $asset = new FileAsset(__FILE__);
        $this->assertTrue($asset instanceof \asset\assets\AssetInterface, "Asset is an instance of AssetInterface");
    }

    public function testGetFilters()
    {
        $asset = new FileAsset(__FILE__);
        $this->assertInternalType('array', $asset->getFilters(), "Filters is an array");
        $this->assertEmpty($asset->getFilters(), "Filters array should be empty when new");
        $this->assertCount(0, $asset->getFilters(), "Filters array should be empty when new");

        // add a filter
        $filter = new CssMinifyFilter();
        $asset->addFilter($filter);
        $this->assertCount(1, $asset->getFilters(), "Filters should have a single filter in it");

        $asset->addFilter($filter);
        $this->assertCount(2, $asset->getFilters(), "Filters array should have 2 filters in it");

        // Check all the filters are as expected
        $f = $asset->getFilters();
        foreach($f as $item)
        {
            $this->assertSame($filter, $item, "Filter should be the same as the one we added");
        }
    }

    public function testClearFilters()
    {
        $asset = new FileAsset(__FILE__);
        $filter = new CssMinifyFilter();
        $asset->addFilter($filter);
        $this->assertCount(1, $asset->getFilters(), "Filters should have a single filter in it");

        $asset->clearFilters();
        $this->assertCount(0, $asset->getFilters(), "Filters should be empty again");
    }

    public function testGetToken()
    {
        // Check the token looks good
        $asset = new FileAsset(__FILE__);
        $token = $asset->getToken();
        $this->assertNotEmpty($token, "Ensure asset has a token");
        $this->assertInternalType('string', $token, "Token is a string");
        $this->assertEquals(32, strlen($token), "Ensure token is 32 character long");

        // Check that the token of a different file is different
        $asset2 = new FileAsset(__DIR__.'/testAsset.css');
        $token2 = $asset2->getToken();
        $this->assertNotEquals($token, $token2, "Ensure tokens from 2 files are different");
        $this->assertEquals(strlen($token), strlen($token2), "Ensure tokens are the same length");

    }
}