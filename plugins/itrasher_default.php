<?php

require_once('itrasher_interface.php');

class ITrasher_Default implements ITrasher_Interface
{
	public function scan()
	{
		return $this->start();
	}

	public function start()
	{
		return $this->get_unused_images();
	}

	public function get_unused_images()
	{
		//find good candidates for deletion
		$candidates = $this->get_candidates_for_deletion();

		//maybe they are set as feature? check and only return non-featured ones
		$candidates = $this->remove_featured($candidates);

		//maybe they are embedded in pages? check and only return non-embedded ones
		$candidates = $this->remove_embedded($candidates);

		//this performs actions such as verify path, remove base url, find variants for each image
		$candidates = $this->final_touch($candidates);

		//now we are 99% safe to assume that images are not in use
		return $candidates;
	}

	//this finds images that have no parent post or have parent but non-existent
	public function get_candidates_for_deletion()
	{
		global $wpdb;

		$images = array();

		//get rows from property meta - this contains images used for all properties
		$results = $wpdb->get_results( 'SELECT ID, post_parent, guid  FROM '.$wpdb->prefix.'posts WHERE post_type = "attachment"');

		foreach($results as $row)
		{
			//only include images that do not have an active parent post
			if(!$row->post_parent && !$this->has_existent_parent($row->post_parent))
			{
				$images[$row->ID] = $row->guid;
			}
		}

		return $images;
	}

	public function has_existent_parent($id)
	{
		global $wpdb;

		$row = $wpdb->get_row( 'SELECT * FROM '.$wpdb->prefix.'posts WHERE ID = '.$id);

		return !empty($row) ? true : false;
	}

	//checks if candidates are not set as featured
	public function remove_featured($images)
	{
		global $wpdb;

		//will hold non-featured images
		$non_featured = array();

		foreach($images as $id => $path)
		{

			$row = $wpdb->get_row( 'SELECT * FROM '.$wpdb->prefix.'postmeta WHERE meta_key = "_thumbnail_id" AND meta_value = '.$id);

			if(empty($row) || null)
			{
				$non_featured[] = $path;
			}
		}

		return $non_featured;
	}

	//checks if candidates are not embedded in pages
	public function remove_embedded($images)
	{

		$non_embedded = array();

		//$content = $this->gather_pages_content();

		foreach($images as $image)
		{

			//get variants
			$variants = $this->get_variants($image);

			//try to find each variant in the database
			foreach($variants as $variant)
			{
				//find images in fields in db
				if(!$this->in_db($variant)) {
					$non_embedded[] = $variant;
				}
			}

		}

		return $non_embedded;
	}

	//look up image in db
	public function in_db($image)
	{
		global $wpdb;

		$results = $wpdb->get_results('select * from information_schema.columns
										where table_schema = "'.$wpdb->dbname.'"
										order by table_name,ordinal_position');

		$tables_and_columns = array();

		array_walk($results, function($item) use(&$tables_and_columns) {

			$tables_and_columns[$item->TABLE_NAME][] = $item->COLUMN_NAME;
			
		});

		foreach($tables_and_columns as $table => $columns)
		{
			$where = '';
			$excluded_columns = array('guid');
			$excluded_tables = array($wpdb->prefix.'postmeta');

			//generate where clause and exclude some columns as defined
			foreach ($columns as $column) {
				

				if(!in_array($column, $excluded_columns))
				{
					$where .= $column . " LIKE '%" . $image . "%' OR ";
				}
			}

			$where = rtrim($where, ' OR');

			if(!in_array($table, $excluded_tables))
			{
				//generate query and exclude some tables as defined
				$query = 'SELECT * FROM '.$table.' WHERE ' .$where;
			}

			$row = $wpdb->get_row($query);

			$mime_types = array('image/jpeg','image/png');

			//return true if record is found - exclude image self record by checking mime type
			if( (!empty($row) || !is_null($row)) && !in_array($row->post_mime_type, $mime_types) )
			{
				return true;
			}
		}

		return false;
	}

	//this gathers pages in a single string var
	public function gather_pages_content()
	{
		global $wpdb;

		$content = '';

		//scan pages content and combine content
		//this is where we check if images exist in this content
		$rows = $wpdb->get_results( 'SELECT post_content FROM '.$wpdb->prefix.'posts WHERE post_type = "page"');

		if(!empty($rows))
		{
			foreach($rows as $row)
			{
				$content .= $row->post_content;
			}
		}

		return $content;		
	}

	public function final_touch($images)
	{
		return $images;
	}

	public function remove_base_url($image)
	{
		return preg_replace('/.*uploads\//', '', $image);
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
		//remove base url
		$path = $this->remove_base_url($path);

		$paths = array();

		$segments = explode('/', ltrim($path, '/'));
		$filename = $segments[2];

		$images = $this->find_images_from_dir($segments[0], $segments[1], $filename);

		foreach($images as $image)
		{
			$segments[2] = $image;

			//store path as 2015/07/prefix_filename.jpg
			$paths[] = implode('/', $segments);
		}

		return $paths;
	}

	public function find_images_from_dir($year, $month, $file)
	{
		$dir = plugin_dir_path( __FILE__ );
		require_once($dir . '../lib/scan-dir.php');

		$file_wo_ext = preg_replace('/.jpg|.png|.bmp|.gif/', '', $file);
		$dir = wp_upload_dir()['basedir'].'/'.$year.'/'.$month;

		$files = scanDir::scan($dir, array('jpg','bmp','png','gif'), false);

		$cleaned_filenames = $this->remove_base_dir($files);

		$variants = array();

		foreach($cleaned_filenames as $item)
		{
			if(preg_match('/^'.$file_wo_ext.'?/', $item))
			{
				$variants[] = $item;
			}
		}

		return $variants;
	}

	public function remove_base_dir($files)
	{
		$new_files = [];

		foreach($files as $file)
		{
			$new_files[] = preg_replace('/.*\/\d{2}./', '', $file);
		}	

		return $new_files;
	}
}