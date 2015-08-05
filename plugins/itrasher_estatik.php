<?php

require_once('itrasher_interface.php');

class ITrasher_Estatik implements ITrasher_Interface
{

	protected $prefixes = array(
								'list_',
								'2column_',
								'table_',
								'single_lr_',
								'single_center_',
								'single_thumb_'
							);

	public function scan()
	{
		global $wpdb;

		if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."estatik_properties_meta'")) {
			return $this->start();
		}
		
		return;
	}

	public function start()
	{
		return $this->get_verified_unused_images($images);
	}

	public function get_images_from_db()
	{
		global $wpdb;

		$images = array();

		//get rows from property meta - this contains images used for all properties
		$results = $wpdb->get_results( 'SELECT prop_meta_value, prop_id FROM '.$wpdb->prefix.'estatik_properties_meta WHERE prop_meta_key = "images"');

		foreach($results as $row)
		{
			//only include images that do not have an active property
			if(!$this->is_prop_active($row->prop_id))
			{
				$unserialized = unserialize($row->prop_meta_value);

				//add all unused images into a list and simply return it
				foreach($unserialized as $image)
				{
					$images[] = $image;
				}
			}
		}

		return $images;
	}

	public function is_prop_active($id)
	{
		global $wpdb;

		$prop = $wpdb->get_row( 'SELECT * FROM '.$wpdb->prefix.'estatik_properties WHERE prop_id = ' . $id);

		return !empty($prop) ? true : false;
	}

	public function get_verified_unused_images()
	{
		//start by getting all images from estatik table
		$images = $this->get_images_from_db();

		$paths = array();

		foreach($images as $image)
		{
			$variants = $this->get_variants($image);

			//make sure the file exists before deletion
			//this returns verified images or images that really exist
			$verified_variants = $this->filter_verified($variants);

			foreach($verified_variants as $variant)
			{
				$paths[] = $variant;
			}

		}
		
		return $paths;
	}

	public function filter_verified($variants)
	{
		$verified_variants = array();

		foreach($variants as $variant)
		{

			if($this->verify($variant))
			{
				$verified_variants[] = $variant;
			}
		}

		return $verified_variants;
	}

	public function verify($path)
	{

		$dir = wp_upload_dir()['basedir'];
		$file = $dir . '/' . $path;

		if(file_exists($file))
		{
			return true;
		}

		return false;
	}

	public function get_variants($path)
	{

		$paths = array();

		$segments = explode('/', ltrim($path, '/'));
		$filename = $segments[2];

		foreach($this->prefixes as $prefix)
		{
			$prefixed = $prefix . $filename;
			$segments[2] = $prefixed;

			//store path as 2015/07/prefix_filename.jpg
			$paths[] = implode('/', $segments);
		}

		return $paths;
	}
}