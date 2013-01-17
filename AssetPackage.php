<?php
/**
 * AssetPackage.php
 * Created by Rik on 07/01/2013
 */

namespace asset;

use snb\core\ContainerAware;
use snb\core\PackageInterface;
use snb\core\KernelInterface;

class AssetPackage extends ContainerAware implements PackageInterface
{
	/**
	 * Called during startup to allow this package to register its components
	 * @param \snb\core\KernelInterface $kernel
	 */
	public function boot(KernelInterface $kernel)
	{
		// Should be in Buzz, but its simpler to leave that package completely unmodified...
		$kernel->addService('twig.extension.asset', '\asset\extensions\twig\AssetExtension')
			->setArguments(array('::service::config', '::service::kernel', '::service::cache'));
	}
}