<?php

namespace Directoreez\Authentication;

/**
 * This authentication method allows you to connect
 * to a remote server using a simple ssh username/password
 * 
 * @author LampJunkie
 */
class Password implements Authentication
{
	/**
	 * The username
	 * 
	 * @var string
	 */
	protected $username;
	
	/**
	 * The password
	 * 
	 * @var string
	 */
	protected $password;
	
	/**
	 * Create Authentication with a username and password
	 * 
	 * @param string $username
	 * @param string $password
	 */
	public function __construct($username, $password)
	{
		$this->username = $username;
		$this->password = $password;
	}

	/**
	 * (non-PHPdoc)
	 * @see Directoreez\Authentication.Authentication::authenticate()
	 */
	public function authenticate($connection)
	{
		if(@ssh2_auth_password($connection, $this->username, $this->password)){
			return true;
		} else {
			throw new AuthenticationException("Couldn't connect to remote server");
		}
	}
}
