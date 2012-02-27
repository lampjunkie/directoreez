<?php

namespace Directoreez;

/**
 * Common interface for all directory operations
 * 
 * @author LampJunkie
 */
interface Directory
{
	/**
	 * Get the root path of the current directory
	 * 
	 * @return string
	 */
	public function getPath();

	/**
	 * Create the root directory if it doesn't exist
	 *
	 * @return void
	 */
	public function create();

	/**
	 * Copy from a given location to a relative location
	 * within the current directory
	 * 
	 * @param string $from
	 * @param string $to
	 * @return void
	 */
	public function copyFrom($from, $to);

	/**
	 * Copy to a given destination from a given path
	 * relative to the current directory
	 * 
	 * @param string $to
	 * @param string $from
	 * @return void
	 */
	public function copyTo($to, $from);

	/**
	 * Recursively delete all the contents of
	 * the current directory
	 * 
	 * @return void
	 */
	public function clean();

	/**
	 * Create a new file with given contents
	 * 
	 * @param string $destination
	 * @param string $contents
	 * @return void
	 */
	public function fputs($destination, $contents);

	/**
	 * Create a new directory relative to the
	 * current directory's root
	 * 
	 * @param string $directory
	 * @param integer $permissions
	 * @return void
	 */
	public function mkdir($directory, $permissions = 0777);

	/**
	 * Create multiple directories relative to the
	 * current directory's root path
	 * 
	 * @param array<string> $directories
	 * @param integer $permissions
	 * @return void
	 */
	public function mkdirs($directories, $permissions = 0777);

	/**
	 * Delete the given relative path
	 * 
	 * @param string $path
	 * @return void
	 */
	public function delete($path);

	/**
	 * Get the contents of a relative file
	 * 
	 * @param string $path
	 * @return string
	 */
	public function getFileContents($path);
	
	/**
	 * Get the directory contents of a given
	 * relative directory path
	 * 
	 * @param string $path
	 * @return array<string>
	 */
	public function ls($path);
	
	/**
	 * Run a given command using a given path
	 * 
	 * This method provides a convenient means to run
	 * an arbitrary command and provide it with a full
	 * path to some location relative to the current Directory
	 * 
	 * The Closure should return the command to run.
	 * 
	 * The results of this method will be an array of the
	 * command's output.
	 * 
	 * @param string $path
	 * @param Closure $closure
	 * @return array<string>
	 */
	public function execute($path, \Closure $closure);
	
	/**
	 * Check if the given path exists
	 * 
	 * @param string $path
	 * @return boolean
	 */
	public function exists($path);
	
	/**
	 * Change the permissions of a given relative path
	 * 
	 * @param string $filename
	 * @param integer $mode
	 * @return void
	 */
	public function chmod($filename, $mode);

	/**
	 * Switch to a new root path
	 *
	 * @param string $path
	 * @return void
	 */
	public function switchTo($path);

	/**
	 * Get the full path for a sub path
	 *
	 * @param string $path
	 * @return string
	 */
	public function getPathFor($path);
}
