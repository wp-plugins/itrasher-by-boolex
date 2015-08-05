<?php

require_once('plugins/itrasher_interface.php');

class ITrasher_Scanner implements itrasher_interface
{

	protected $plugins = array();

	public function __construct($config)
	{
		$this->plugins = $config['plugins'];

		return $this->scan();
	}

	public function scan()
	{
		$results = array();
		
		//load plugins
		if(!empty($this->plugins))
		{
			foreach($this->plugins as $plugin)
			{
				require_once('plugins/itrasher_' . $plugin . '.php');
				$objName = 'ITrasher_' . ucfirst($plugin);
				$objects[] = new $objName();
			}
		}

		foreach($objects as $obj)
		{
			$results[] = $obj->scan();
		}

		$new_results = array();
		
		//now combine multidimentional arrays
		array_walk_recursive($results, function($arr) use(&$new_results) {

				$new_results[] = $arr;
		});

		return $new_results;
	}

	public function trash($images)
	{
		if(!empty($images))
		{
			foreach($images as $image)
			{
				if($file = $this->verify($image))
				{
					unlink($file);
				}
			}
		}
	}

	public function verify($path)
	{

		$dir = wp_upload_dir()['basedir'];
		$file = $dir . '/' . $path;

		if(file_exists($file))
		{
			return $file;
		}

		return false;
	}
}