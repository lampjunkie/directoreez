<?php

namespace Directoreez\Authentication;

/**
 * This is the interface which defines the methods that
 * all remote authentication methods must implement.
 * 
 * @author LampJunkie
 */
interface Authentication
{
	/**
	 * Authenticate against a given ssh2 connection resource
	 * 
	 * @param resource $connection
	 * @return void
	 * @throws AuthenticationException
	 */
	public function authenticate($connection);
}
