Asset Manager
=============

This was built as a simple asset management solution for PHP projects. It can
be used to merge and minify multiple css files into a single cached file. The
goal is to reduce the number of HTTP requests being made, and to automate the
process of combining and minify'ing the files involved.

It was created to integrate with the Band Framework (www.bandframework.com),
but it should be simple to integrate with other frameworks.

It also includes a Twig extension that provides a simple way of using the
component from within your twig templates.


Installation
------------

Copy the project into a folder inside your project. It is compatible with PSR-0
autoloaders. When adding it to the Band Framework, I recommend that you make the
following changes...

1. Add an item to the getBootable() method in AppKernel.php to register the asset
package, list so...

	protected function getBootable()
	{
        return array(
			new \asset\AssetPackage(),
			// You other packages will be here...
		);
	}

This will register the Twig extensions with the service container, so that Twig
will automatically find the extension and enable it.

2. Add a line to the autoload.php to register the asset namespace, like so...

	AutoLoadContainer::addNamespaces(array(
		'asset' => __DIR__.'/path/to/asset/package',
		// Your other namespaces defined here...
	));

3. Add some info to your config file to tell the asset manager where to write
it's cached files. Here is an example config

assets:
  write_to: :/../htdocs/css/
  base_url: http://example.com

- write_to is a standard system path name (<packagename:path>)
- base_url is a URL that all asset paths will have prepended to them

Finally, you can add calls to the 2 asset functions in your Twig templates...

	<link href="{{ asset_path_css(
		['package:folder:file.css', 'package:folder:another.css'],
		'styles-%token%.css')
	}}" rel="stylesheet" type="text/css"/>
