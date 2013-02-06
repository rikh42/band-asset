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
use snb\cache\CacheInterface;


/**
 * A Twig extension that adds supports for the asset system to the templates
 * Provides integration with the Band Framework and Twig
 */
class AssetExtension extends \Twig_Extension
{
	/**
	 * @var \snb\config\ConfigSettings
	 */
	protected $config;

	/**
	 * @var \snb\core\KernelInterface
	 */
	protected $kernel;

	/**
	 * @var \snb\cache\CacheInterface
	 */
	protected $cache;


    protected $baseUrl;
    protected $writeTo;
	protected $cacheTime;


    /**
     * @param \snb\config\ConfigSettings $config
     * @param \snb\core\KernelInterface $kernel
	 * @param \snb\cache\CacheInterface $cache
	 * @throw \InvalidArgumentException
     */
	public function __construct(ConfigSettings $config, KernelInterface $kernel, CacheInterface $cache)
    {
        // Get some settings from the config
        $this->baseUrl = $config->get('assets.base_url', '');
        $this->writeTo = $kernel->findPath($config->get('assets.write_to', ''));
		$this->cacheTime = $config->get('assets.cachetime', 6000);

        // check we have a valid path
        if (empty($this->writeTo)) {
            throw new \InvalidArgumentException("write_to path has not been set in the Asset Extension. Did you set assets.write_to in your config.yml");
        }

        // we'll need the kernel later
        $this->kernel = $kernel;
		$this->config = $config;
		$this->cache = $cache;
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
            'asset_path_css' => new \Twig_Function_Method($this, 'handleCssAssets'),
            'asset_path_js'  => new \Twig_Function_Method($this, 'handleJsAssets'),
			'asset'			 => new \Twig_Function_Method($this, 'handleAssets')
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
	 * Looks up the asset group name in your config and takes
	 * all the files and settings from there
	 * @param $name - the name of the asset set to load (a-z 0-9 _ and - only)
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function handleAssets($name)
	{
		// clean the name
		$name = preg_replace('/[^a-z0-9-_]/u', '', strtolower($name));

		// Get the name of the config key
		$key = 'assets.filesets.'.$name;

		// look the asset name up in the cache
		$url = $this->cache->get($key);
		if ($url != null)
			return $url;

		// Check the file list is decent.
		$fileList = $this->config->get($key.'.files.*', array());
		if (!is_array($fileList) || count($fileList) == 0) {
			throw new \InvalidArgumentException("Asset File list for '$name' is missing or empty. Can't generate asset files");
		}

		// check that the type is one of the ones we support
		$type = $this->config->get($key.'.type', 'css');
		if (!preg_match('/css|js/u', $type)) {
			throw new \InvalidArgumentException("Asset File list for '$name'' uses invalid type of '$type''");
		}

		// built the filter list...
		$filters = array();
		if ($type == 'css')
			$filters = array(new CssMinifyFilter());

		// Build the target name
		$target = $name.'-%token%.'.$type;

		// Finally, generate the content, write it to the cache, and return it
		$url = $this->handleGeneralAssets($fileList, $target, $filters);
		$this->cache->set($key, $url, $this->cacheTime);
		return $url;
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
