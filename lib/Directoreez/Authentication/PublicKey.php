<?php

namespace Directoreez\Authentication;

/**
 * This authentication method allows you to connect
 * to a remote server using a public key
 *
 * @author LampJunkie
 */
class PublicKey implements Authentication
{
	/**
	 * The username
	 *
	 * @var string
	 */
	protected $username;

	/**
	 * The public key file location
	 *
	 * @var string
	 */
	protected $publicKeyFile;

	/**
	 * The private key file location
	 *
	 * @var string
	 */
	protected $privateKeyFile;

	/**
	 * The key passphrase
	 *
	 * @var string
	 */
	protected $passphrase;

	/**
	 * Create an authentication method with info for a public key
	 *
	 * @param string $username
	 * @param string $publicKeyFile
	 * @param string $privateKeyFile
	 * @param string $passphrase
	 */
	public function __construct($username, $publicKeyFile, $privateKeyFile, $passphrase)
	{
		$this->username = $username;
		$this->publicKeyFile = $publicKeyFile;
		$this->privateKeyFile = $privateKeyFile;
		$this->passphrase = $passphrase;
	}

	/**
	 * (non-PHPdoc)
	 * @see Directoreez\Authentication.Authentication::authenticate()
	 */
	public function authenticate($connection)
	{
		if(@ssh2_auth_pubkey_file($connection, $this->username,
			$this->publicKeyFile, $this->privateKeyFile, $this->passphrase)){
			return true;
		} else {	
			throw new AuthenticationException('Could not connect to remote server');
		}
	}
}
