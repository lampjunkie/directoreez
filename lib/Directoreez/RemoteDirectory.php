<?php

namespace Directoreez;

use Directoreez\Authentication\Authentication;

/**
 * This class provides methods to work with remote
 * system directories.
 * 
 * @author LampJunkie
 */
class RemoteDirectory implements Directory
{
	/**
	 * The root path for the current directory
	 * 
	 * @var string
	 */
	protected $path;
	
	/**
	 * The remote host
	 * 
	 * @var string
	 */
	protected $host;
	
	/**
	 * The remote port number
	 * 
	 * @var integer
	 */
	protected $port;
	
	/**
	 * The Authentication object
	 * 
	 * @var Authentication
	 */
	protected $authentication;
	
	/**
	 * The ssh connection resource
	 * 
	 * @var resource
	 */
	protected $connection;

	/**
	 * Establish a connection to a remote directory using
	 * an Authentication method
	 * 
	 * @param string $path
	 * @param boolean $isAutoCreate
	 * @param string $host
	 * @param integer $port
	 * @param Authentication $authentication
	 * @throws AuthenticationException
	 */
	public function __construct($path, $host, $port, Authentication $authentication)
	{
		$this->path = $path;
		$this->host = $host;
		$this->port = $port;
		$this->authentication = $authentication;
		$this->connection = ssh2_connect($host, $port, array('hostkey'=>'ssh-rsa'));
		$this->authentication->authenticate($this->connection);
	}

	/**
	 * (non-PHPdoc)
	 * @see Directoreez.Directory::create()
	 */
	public function create($permissions = 0777)
	{
		$this->mkdir('/', $permissions);
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
		ssh2_scp_send($this->connection, $from, $this->getPathFor($to));
	}

	/**
	 * (non-PHPdoc)
	 * @see Directoreez.Directory::copyTo()
	 */
	public function copyTo($to, $from)
	{
		ssh2_scp_recv($this->connection, $this->getPathFor($from), $to);
	}

	/**
	 * (non-PHPdoc)
	 * @see Directoreez.Directory::clean()
	 */
	public function clean()
	{
		$this->execute('/*', function($path){
			return "rm -rf {$path}";
		});
	}

	/**
	 * (non-PHPdoc)
	 * @see Directoreez.Directory::fputs()
	 */
	public function fputs($destination, $contents)
	{
		$tmp = '/tmp/' . md5(time() . rand());
		file_put_contents($tmp, $contents);
		ssh2_scp_send($this->connection, $tmp, $this->getPathFor($destination));
		unlink($tmp);
	}

	/**
	 * (non-PHPdoc)
	 * @see Directoreez.Directory::mkdir()
	 */
	public function mkdir($directory, $permissions = 0777)
	{
		$sftp = ssh2_sftp($this->connection);
		ssh2_sftp_mkdir($sftp, $this->getPathFor($directory), $permissions, true); 
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
		return $this->execute($path, function($path){
			return "rm -rf {$path}";
		});
	}

	/**
	 * (non-PHPdoc)
	 * @see Directoreez.Directory::getFileContents()
	 */
	public function getFileContents($path)
	{
		$tmp = '/tmp/' . md5(time() . rand());
		file_put_contents($tmp, $contents);
		ssh2_scp_recv($this->connection, $this->getPathFor($path), $tmp);
		$contents = file_get_contents($tmp);
		unlink($tmp);
		return $contents;
	}

	/**
	 * (non-PHPdoc)
	 * @see Directoreez.Directory::ls()
	 */
	public function ls($dir = '')
	{
		return $this->execute($dir, function($path){
			return "ls -la {$path} | awk '{ print($8) }'";
		});
	}

	/**
	 * (non-PHPdoc)
	 * @see Directoreez.Directory::execute()
	 */
	public function execute($path, \Closure $closure)
	{
		$path = $this->getPathFor($path);
		$command = $closure($path);
		$stream = ssh2_exec($this->connection, $command);
		stream_set_blocking($stream, true);
		$contents = stream_get_contents($stream);
		return split("\n", trim($contents));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Directoreez.Directory::exists()
	 */
	public function exists($path)
	{	
		$sftp = ssh2_sftp($this->connection);
		$statinfo = @ssh2_sftp_stat($sftp, $this->getPathFor($path));
		return is_array($statinfo) ? true : false;
	}

	/**
	 * (non-PHPdoc)
	 * @see Directoreez.Directory::chmod()
	 */
	public function chmod($path, $permissions = 0777)
	{
		$command = "chmod -R {$permissions} {$this->getPathFor($path)}";
		ssh2_exec($this->connection, $command);
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
}
