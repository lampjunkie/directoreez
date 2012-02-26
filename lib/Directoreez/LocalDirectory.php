<?php

namespace Directoreez;

/**
 * This class provides methods to work with local
 * system directories.
 * 
 * @author LampJunkie
 */
class LocalDirectory implements Directory
{
	/**
	 * The path to the current directory
	 * 
	 * @var string
	 */
	protected $path;

	/**
	 * Create the Directory object with the given path
	 * 
	 * @param string $path
	 * @param boolean $isAutoCreate
	 */
	public function __construct($path, $isAutoCreate = true)
	{
		$this->path = $path;
		if($isAutoCreate && !file_exists($path)){
			mkdir($path, 0777, true);
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see Directoreez.Directory::getPath()
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * (non-PHPdoc)
	 * @see Directoreez.Directory::copyFrom()
	 */
	public function copyFrom($from, $to)
	{
		copy($from, $this->getPathFor($to));
	}

	/**
	 * (non-PHPdoc)
	 * @see Directoreez.Directory::copyTo()
	 */
	public function copyTo($to, $from)
	{
		copy($this->getPathFor($from), $to);
	}

	/**
	 * (non-PHPdoc)
	 * @see Directoreez.Directory::clean()
	 */
	public function clean()
	{
		$dir = $this->getPath();
		if(file_exists($dir)){
			$this->rrmdir($dir);
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see Directoreez.Directory::fputs()
	 */
	public function fputs($destination, $contents)
	{
		file_put_contents($this->getPathFor($destination), $contents);
	}

	/**
	 * (non-PHPdoc)
	 * @see Directoreez.Directory::mkdir()
	 */
	public function mkdir($directory, $permissions = 0777)
	{
		if(!file_exists($this->getPathFor($directory))){
			mkdir($this->getPathFor($directory), $permissions, true);
		}	
	}

	/**
	 * (non-PHPdoc)
	 * @see Directoreez.Directory::mkdirs()
	 */
	public function mkdirs($directories, $permissions = 0777)
	{
		foreach($directories as $directory){
			$this->mkdir($directory, $permissions);
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see Directoreez.Directory::delete()
	 */
	public function delete($path)
	{
		$path = $this->getPathFor($path);
		if(is_dir($path)){
			$this->rrmdir($path);
		} else {
			unlink($this->getPathFor($path));
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Directoreez.Directory::getFileContents()
	 */
	public function getFileContents($path)
	{
		return file_get_contents($this->getPathFor($path));
	}

	/**
	 * (non-PHPdoc)
	 * @see Directoreez.Directory::ls()
	 */
	public function ls($path)
	{
		return scandir($this->getPathFor($path));
	}

	/**
	 * (non-PHPdoc)
	 * @see Directoreez.Directory::execute()
	 */
	public function execute($path, \Closure $closure)
	{
		$cmd = $closure($this->getPathFor($path));
		exec($cmd, $output);
		return $output;
	}

	/**
	 * (non-PHPdoc)
	 * @see Directoreez.Directory::exists()
	 */
	public function exists($path)
	{
		return file_exists($this->getPathFor($path));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Directoreez.Directory::chmod()
	 */
	public function chmod($path, $mode)
	{
		chmod($path, $mode);
	}

	/**
	 * (non-PHPdoc)
	 * @see Directoreez.Directory::switchTo()
	 */
	public function switchTo($path)
	{
		$this->path = $path;
	}
	
	/**
	 * Get the absolute path for a path relative to the
	 * current directory
	 * 
	 * @param string $destination
	 * @return string
	 */
	protected function getPathFor($destination)
	{
		return $this->path . $destination;
	}


	/**
	 * Utility method to recursively remove nested directories
	 * 
	 * @param string $dir
	 * @return void
	 */
	protected function rrmdir($dir)
	{
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) == "dir") $this->rrmdir($dir."/".$object); else unlink($dir."/".$object);
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}
}
