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

```php
protected function getBootable()
{
	return array(
		new \asset\AssetPackage(),
		// You other packages will be here...
	);
}
```

This will register the Twig extensions with the service container, so that Twig
will automatically find the extension and enable it.

2. Add a line to the autoload.php to register the asset namespace, like so...

```php
AutoLoadContainer::addNamespaces(array(
	'asset' => __DIR__.'/path/to/asset/package',
	// Your other namespaces defined here...
));
```

3. Add some info to your config file to tell the asset manager where to write
it's cached files. Here is an example config

```yaml
assets:
  write_to: :/../htdocs/css/
  base_url: http://example.com
```

- write_to is a standard system path name (<packagename:path>)
- base_url is a URL that all asset paths will have prepended to them


Simple usage
============

You can add calls to the 2 basic asset functions in your Twig templates. For
css files, use asset_path_css, and for javascript files use asset_path_js, like so...

```html
	<link href="{{ asset_path_css(
		['package:folder:file.css', 'package:folder:another.css'],
		'styles-%token%.css')
	}}" rel="stylesheet" type="text/css"/>
```

The first argument is an array of files to be found in the resource folder of one of your
packages, the second is the name of the output file / url to generate. %token% will be
replaced with a unique id that will change whenever any of the source files are changed.

Advanced Usage
==============

The latest version of the asset manager now includes a way of pushing almost all the information
about the asset list to your config file, making it much simpler to use in your templates. Lets
start with the example template code...

```html
	<link href="{{ asset('home') }}" rel="stylesheet" type="text/css"/>
```

The asset manager then looks up various bits of information from the config. Below is an
example config file that defines the 'home' asset set used above.

```yaml
assets:
  write_to: :/../htdocs/css/
  base_url: /css/
  cachetime: 6000
  filesets:
    home:
      type: css
      files:
        - example:css:reset.css
        - example:css:grid.css
        - example:css:home.css
```

The 'type' value defaults to css, but can be css or js.

You'll also notice a 'cachetime' setting, which is used by the asset() twig function to cache
the resulting URL for the number of seconds specified. This is recommended on your production config