<?php

namespace Efemer\Higg\Factory\Traits;

use App;
use File;
use View;
use Config;
use Log;
use Illuminate\Support\Str;
use Illuminate\Foundation\AliasLoader;

trait ServiceProviderTrait {

	public function boot(){
		if ($this->activated()) {

			$this->loadConfigFiles();
			$this->registerActions();
			$this->registerViews();
			$this->loadRoutes();

			if (method_exists($this, 'onBoot')) {
				$this->onBoot();
			}

		}
	}

	public function register(){

		$this->registerBindings();
		$this->registerFacades();
		$this->registerRequiredFiles();

		if (method_exists($this, 'onRegister')) {
			$this->onRegister();
		}

	}

	function activated(){

		if (method_exists($this, 'isActive')) {
			return call_user_func([$this, 'isActive']);
		}

		$context = property_exists($this, 'context') ? $this->context : false;
		if ($context !== false) {
			if (!higg()->isContext($context)) {
				return false;
			}
		}

		return true;
	}


	function registerRequiredFiles(){

		$required = property_exists($this, 'required') ? $this->required : [];

		$requireFiles = $required;
		if ($required !== false && empty($required)) {
			$requireFolder = $this->package_path('/src/Library/Require');
			$requireFiles = File::files($requireFolder);
		}

		if (!empty($requireFiles)) {
			foreach ($requireFiles as $file) {
				//if (!Str::startsWith($file, '/')) $file = $this->package_path($file);
				require $file;
			}
		}

	}

	// register singleton bindings
	function registerBindings(){

		$namespace = $this->package_namespace();

		// check if bindings defined within provider class
		$bindings = property_exists($this, 'bindings') ? $this->bindings : [];

		if ( $bindings !== false && !empty($bindings)) {
			foreach ($bindings as $alias => $classPath) {
				if (!Str::startsWith($classPath, '\\')) {
					// $classPath = $namespace . '\\' . $classPath;
				}
				App::singleton($alias, function () use ($classPath) {
					return new $classPath;
				});
			}
		}
	}


	function registerFacades(){

		$namespace = $this->package_namespace();

		$facades = property_exists($this, 'facades') ? $this->facades : [];

		if ($facades !== false && empty($facades)) {
			$facadesFolder = $this->package_path('/src/Factory/Facades');
			$files = File::files($facadesFolder);
			if (!empty($files)) {
				foreach ($files as $file) {
					$path_info = pathinfo($file);
					if ($path_info['extension'] == 'php') {
						$className = $path_info['filename'];
						$facades[$className] = $namespace . '\Factory\Facades\\'.$className;
					}
				}
			}
		}

		if (!empty($facades)) {
			$aliasLoader = AliasLoader::getInstance();
			foreach ($facades as $alias => $classPath) {
				$aliasLoader->alias($alias, $classPath);
			}
			$fileNames = collect($facades)->map(function($file){
				return pathinfo($file)['basename'];
			})->implode('|');
			// Log::debug( class_basename($this) . " facades registered as {$fileNames}");
		}
	}


	function loadConfigFiles(){

		$configFiles = property_exists($this, 'configs') ? $this->configs : [];
		$publishFiles = empty($configFiles) ? false : $configFiles;

		// autoload config files
		if ($configFiles !== false && empty($configFiles)) {
			$configFolder = $this->package_path('/config');
			$files = File::files($configFolder);
			if (!empty($files)) {
				foreach ($files as $file) {
					$path_info = pathinfo($file);
					if ($path_info['extension'] == 'php') {
						$configNS = $path_info['filename'];
						if (strpos($configNS, '.skip') > 0) continue;
						if (strpos($configNS, '.live') === false) {
							if (!isset($configFiles[$configNS]))
								$configFiles[$configNS] = $file;
						} else {
							if (!is_local()) {
								$configNS = str_replace('.live', '', $configNS);
								$configFiles[$configNS] = $file;
							}
						}
					}
				}
			}
		}

		// if ($this->packageName == 'nuforce') pr($configFiles);

		// load configs grouped by filename
		if (!empty($configFiles)) {
			$publishList = [];
			foreach ($configFiles as $namespace => $configFile) {

				if (strpos($configFile, '/') !== 0) {
					if (strpos($configFile, '.') === false) {
						$configFile = $this->package_path("/config/{$configFile}.php");
					}
				}

				if (isset($publishFiles[$namespace])) {
					$publishList[$configFile] = config_path("{$namespace}.php");
				}

				if (file_exists($configFile)) {
					$keys = Config::get($namespace, []);
					$merge = require $configFile;

					if ( is_array($merge) && !empty($merge)) {
						$keys = array_merge($merge, $keys);
						Config::set($namespace, $keys);
					}
				}
			}
			if (!empty($publishList)) {
				$this->publishes($publishList);
			}
			$fileNames = collect($configFiles)->map(function($file){
				return pathinfo($file)['basename'];
			})->implode('|');
			// Log::debug( class_basename($this) . " configs loaded from {$fileNames}");
		}

	}


	function registerActions(){

		$actions = property_exists($this, 'actions') ? $this->actions : [];

		if ($actions !== false && !empty($actions)) {
			foreach ($actions as $key => $config) {
				$key = "actions.".$key;
				Config::set($key, $config);
			}
		}

	}

	function registerViews($views = []){
		$name = $this->package_name();

		// manually defined views
		$locallyDefined = property_exists($this, 'views') ? $this->views : [];
		if (!empty($locallyDefined)) $views = array_merge($views, $locallyDefined);

		// or autoload
		if (empty($views)) {
			$viewsFolder = [
				$this->package_path('/views/'.$name),
				$this->package_path('/views')
			];
			foreach ($viewsFolder as $folder) {
				if (file_exists($folder)) {
					$views = [ $name => $folder ];
					break;
				}
			}
		}

		if (!empty($views)) {
			foreach ($views as $namespace => $path) {
				// $this->loadViewsFrom( $path, $namespace);
				View::addNamespace($namespace, $path);
			}
			$fileNames = collect($views)->map(function($file){
				return pathinfo($file)['basename'];
			})->implode('|');
			// Log::debug( class_basename($this) . " views registered from {$fileNames}");
		}

	}

	function loadRoutes(){

		$routes = property_exists($this, 'routes') ? $this->routes : [];
		// autoload config files
		if ($routes !== false && empty($routes)) {
			$routeFolder = $this->package_path('/routes');
			$files = File::files($routeFolder);
			if (!empty($files)) {
				foreach ($files as $file) {
					$path_info = pathinfo($file);
					if ($path_info['extension'] == 'php') {
						$routes[] = $file;
					}
				}
			}
		}

		if (!empty($routes)) {
			foreach ($routes as $routeFile) {
				if (file_exists($routeFile)) {
					$this->loadRoutesFrom($routeFile);
				}
			}

			$fileNames = collect($routes)->map(function($file){
				return pathinfo($file)['basename'];
			})->implode('|');
			// Log::debug( class_basename($this) . " routes loaded from {$fileNames}");

		}

	}


	function package_name(){
		$name = property_exists($this, 'packageName') ? $this->packageName : null;
		if (empty($name)) {
			$name = str_replace('ServiceProvider', '', class_basename($this));
		}
		return Str::snake($name);
	}

	function package_namespace(){
		$namespace = property_exists($this, 'packageNamespace') ? $this->packageNamespace : null;
		if (empty($namespace)) {
			$className = get_class($this);
			$parts = explode('\\', $className);
			$namespace = '\\' . $parts[0];
			if (isset($parts[1])) $namespace .= '\\' . $parts[1];
		}
		return $namespace;
	}

	function package_path($path = null){
		$packageDir = property_exists($this, 'packageDir') ? $this->packageDir : null;
		if (empty($packageDir)) exit( class_basename($this) . ' Service provider must define packageDir');
		if (!empty($path) && !Str::startsWith($path, '/')) $path = '/'.$path;
		return is_null($path) ? $packageDir : $packageDir . $path;
	}

} // end class
