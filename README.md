FileSystem
==========

Helper functions to work with the file system.

Files
-----

```php
<?php

$file = new SugiPHP\FileSystem\Files();

// Checks the existence of file with a given filename
$file->exists($filename);

// Determine if the file can be opened for reading
$file->isReadable($filename);

// Determine if the file can be opened for writing
$file->isWritable($filename);

// Trying to get the contents of the file. The file must exists and
//  must be readable. If not - the default value is returned.
$contents = $file->get($filename, $default = null);
// Returns a default value
$contents = $file->get("unexisting_file", "Your default contents...");
// or you can use read as an alias of get
$contents = $file->read($filename, $default = null);

// Writes data in the file.
// If the file does not exists it will be created.
// If the $mode parameter is set the newly created file will have mode $mode
// Returns FALSE if operation fails.
$bytes_written = $file->put($filename, $data, $mode = null);
// You can use write alias of put
$bytes_written = $file->write($filename, $data, $mode = null);

// Appends data in the file.
// If the file does not exists it will be created.
// Returns FALSE if operation fails.
$bytes_appended = $file->append($filename, $data);

// Deletes a file. Returns TRUE if the file is successfully removed or didn't exists.
$file->delete($filename);

// Change mode for files. Returns FALSE on error.
$file->chmod($filename, 0664);

// Changes owner of the file. Returns FALSE on error.
// A user can be a user name (string) or a number (UID)
$file->chown($filename, $user);

// Changes group owner of the file. Returns FALSE on error.
// A group can be a string or a number (GID)
$file->chgrp($filename, $group);

// Get extension of the file. Returns false if the file does not exists
$file->ext($filename);

// Returns owner's user ID.
$file->getUID($filename)

// Returns owner's name
$file->getOwner($filanme)

// Returns group ID
$file->getGID($filename)

// Returns group name
$file->getGroup($filename)

// Gets file modification time
$file->mtime($filename)

// TODO:
// copy()
// move()
// symlink()
// touch()
?>

```

Directories
-----------

```php
<?php

$dir = new SugiPHP\FileSystem\Directories();

// Checks the existence of directory with a given name
$dir->exists($name);

// Determine if the directory can be accessed
$dir->isReadable($name);

// Determine if we can write files in the directory
$dir->isWritable($name);

// Change mode for directories.
$dir->chmod($name, 0755);

?>
```
