<?php
/**
 * This file is part of the Small Neat Box Framework
 * Copyright (c) 2011-2012 Small Neat Box Ltd.
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace asset\extensions\twig;

use asset\assets\AssetCollection;
use asset\assets\FileAsset;
use asset\AssetManager;
use asset\filters\CssMinifyFilter;

use snb\config\ConfigSettings;
use snb\core\KernelInterface;


/**
 * A Twig extension that adds supports for the asset system to the templates
 * Provides integration with the Band Framework and Twig
 */
class AssetExtension extends \Twig_Extension
{
    protected $kernel;
    protected $baseUrl;
    protected $writeTo;


    /**
     * @param \snb\config\ConfigSettings $config
     * @param \snb\core\KernelInterface $kernel
     * @throw \InvalidArgumentException
     */
	public function __construct(ConfigSettings $config, KernelInterface $kernel)
    {
        // Get some settings from the config
        $this->baseUrl = $config->get('assets.base_url', '');
        $this->writeTo = $kernel->findPath($config->get('assets.write_to', ''));

        // check we have a valid path
        if (empty($this->writeTo)) {
            throw new \InvalidArgumentException("write_to path has not been set in the Asset Extension. Did you set assets.write_to in your config.yml");
        }

        // we'll need the kernel later
        $this->kernel = $kernel;
    }



    /**
     * @return string
     */
    public function getName()
    {
        return 'asset';
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            'asset_path_css'  => new \Twig_Function_Method($this, 'handleCssAssets'),
            'asset_path_js'  => new \Twig_Function_Method($this, 'handleJsAssets')
        );
    }

    /**
     * @param array $fileList
     * @param $target
     * @return string
     */
    public function handleCssAssets($fileList, $target)
    {
        return $this->handleGeneralAssets($fileList, $target, array(new CssMinifyFilter()));
    }


    /**
     * @param $fileList
     * @param $target
     * @return string
     */
    public function handleJsAssets($fileList, $target)
    {
        return $this->handleGeneralAssets($fileList, $target, array());
    }


    /**
     * @param $fileList
     * @param $target
     * @param $filters
     * @return string
     */
    protected function handleGeneralAssets($fileList, $target, $filters)
    {
        // Build a list of assets that we depend on
        $assets = array();
        foreach ($fileList as $resource) {
            $path = $this->kernel->findResource($resource, 'assets');
            $assets[] = new FileAsset($path );
        }

        // Prepare the assets in the asset manager
        $m = new AssetManager(new AssetCollection($assets, $filters));
        $m->setWriteTo($this->writeTo);
        $m->setBaseUrl($this->baseUrl);

        // build the assets if needed and return the name to use
        $m->setOutputName($target);
        return $m->refresh();
    }
}
