<?php

class Kohana_JadeView extends View {

	/**
	 * @var \Jade\Jade
	 */
	public static $jade = null;

	public function __toString()
	{
		return $this->render();
	}

	public static function factory($file = NULL, array $data = NULL)
	{
		return new self($file, $data);
	}

	public static function instance()
	{
		if (!(self::$jade instanceof \Jade\Jade))
		{
			self::$jade = new \Jade\Jade(true);
		}

		return self::$jade;
	}

	public function render($file = NULL)
	{
		if ($file !== NULL)
		{
			$this->set_filename($file);
		}

		if (empty($this->_file))
		{
			throw new View_Exception('You must set the file to use within your view before rendering');
		}

		// Combine local and global data and capture the output
		return self::capture($this->_file, $this->_data);
	}

	protected static function folder2file($filename)
	{
		$views = 'views' . DIRECTORY_SEPARATOR;

		$filename = UTF8::transliterate_to_ascii(substr($filename, strpos($filename, $views) + strlen($views)));

		$filename = strtr($filename, array(
			'.jade' => '.php',
			'/'     => '_',
			'\\'    => '_',
			'-'     => '_',
			'~'     => '_'
		));

		return $filename;
	}

	protected static function cached($kohana_view_filename, $data)
	{
		$cacheTime = 0;

		$folder = Kohana::$cache_dir . DIRECTORY_SEPARATOR . 'jade' . DIRECTORY_SEPARATOR;

		$path = $folder . self::folder2file($kohana_view_filename);

		if (file_exists($path))
		{
			$cacheTime = filemtime($path);
		}

		if ($cacheTime && filemtime($kohana_view_filename) < $cacheTime)
		{
			return $path;
		}

		if (!is_writable($folder))
		{
			throw new Exception(sprintf('Cache directory "%s" must be writable.', $folder));
		}

		$rendered = self::instance()->render($kohana_view_filename, $data);
		file_put_contents($path, $rendered);

		return $path;
	}

	protected static function capture($kohana_view_filename, array $kohana_view_data)
	{
		// Import the view variables to local namespace
		extract($kohana_view_data, EXTR_SKIP);

		if (self::$_global_data)
		{
			// Import the global view variables to local namespace
			extract(self::$_global_data, EXTR_SKIP | EXTR_REFS);
		}

		$rendered = self::cached($kohana_view_filename, get_defined_vars());

		// Capture the view output
		ob_start();

		try
		{
			// Load the view within the current scope
			include $rendered;
		}
		catch (Exception $e)
		{
			// Delete the output buffer
			ob_end_clean();

			// Re-throw the exception
			throw $e;
		}

		// Get the captured output and close the buffer
		return ob_get_clean();
	}

	public function set_filename($file)
	{
		if (($path = Kohana::find_file('views', $file, 'jade')) === false)
		{
			throw new View_Exception('The requested view :file could not be found', array(
				':file' => $file,
			));
		}

		// Store the file path locally
		$this->_file = $path;

		return $this;
	}

	public static function bind_global($key, & $value)
	{
		self::$_global_data[$key] =& $value;
	}

	public static function set_global($key, $value = NULL)
	{
		if (is_array($key))
		{
			foreach ($key as $key2 => $value)
			{
				self::$_global_data[$key2] = $value;
			}
		}
		else
		{
			self::$_global_data[$key] = $value;
		}
	}
}