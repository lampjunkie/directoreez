Directoreez
===========

Overview
--------

Directoreez is a PHP 5.3 library which abstracts the manipulation of local or remote directories. It
is intended to enable the developer to perform standard file operations relative to a given root
directory path. By providing a standard "Directory" interface the developer is also able to easily switch out
local and remote directories within their applications by simply using a different Directory object.

Requirements
------------

    - PHP 5.3+
    - PHP ssh2 extension installed

Usage
-----

Local Directory:

    // create a Directory object for a local directory
    $directory = new \Directoreez\LocalDirectory('/path/to/directory/1');

Remote Directory:

    // create an authentication object to connect to remote server
    $authentication = new \Directoreez\Authentication\Password('username', 'password');

    // create a Directory object for a remote directory
    $directory = new \Directoreez\RemoteDirectory('/directory/path/on/remote/server', 'somehost.com', 22, $authentication);

Operations:

    // create a new directory relative to the Directory object's root
    $directory->mkdir('/new-directory', 777);

    // put a new file
    $directory->fputs('/new-directory/new-file.txt', 'hello world');

    // check if a file exits
    $exists = $directory->exists('/new-directory/new-file.txt');

    // change permissions
    $directory->chmod('/new-directory', 0644);

    // delete a file
    $directory->delete('/new-directory/new-file.txt');

    // get directory contents (as an array)
    $contents = $directory->ls('/new-directory');

    // copy a file from a location to the Directory
    $directory->copyFrom('/my/local/file.txt', '/new-directory/file.txt');

    // copy a file from the Dirctory to another location
    $directory->copyTo('/new-directory/file.txt', '/my/local/file.txt');

    // empty an entire directory
    $directory->clean();

	// execute a command
	// you must provide an anonymous function which accepts the absolute path to the given directory
	$contents = $directory->execute('/some/directory', function($path){
		return "ls -la {$path}";
	});

