<?php
/**
 * @package    SugiPHP
 * @subpackage FileSystem
 * @author     Plamen Popov <tzappa@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php (MIT License)
 */

namespace SugiPHP\FileSystem;

use SugiPHP\FileSystem\Directories;

/**
 * Files - Helper functions to ease file specific operations.
 * Directory operations are intentionally avoided.
 */
class Files
{
	/**
	 * Determine if the file exists.
	 *
	 * @param  string $filename Filename with optional path
	 * @return boolean
	 */
	public function exists($filename)
	{
		return is_file($filename);
	}

	/**
	 * Determine if the file can be opened for reading
	 *
	 * @param  string $filename Filename with optional path
	 * @return boolean
	 */
	public function isReadable($filename)
	{
		return is_file($filename) && is_readable($filename);
	}

	/**
	 * Determine if the file is writable.
	 *
	 * @param  string $filename Filename with optional path
	 * @return boolean
	 */
	public function isWritable($filename)
	{
		return is_file($filename) && is_writable($filename);
	}

	/**
	 * Trying to get the contents of the file.
	 * The file should exists and should be readable. If not default value will be returned.
	 *
	 * <code>
	 * 		// Get the contents of a file
	 *		$contents = File::get('foo/bar.txt');
	 *
	 *		// Get the contents of a file or return a default value if it doesn't exist
	 *		$contents = File::get('foo/bar.txt', 'Default Value');
	 * </code>
	 *
	 * @param  string $filename
	 * @param  string $default
	 * @return string
	 */
	public function get($filename, $default = null)
	{
		return $this->isReadable($filename) ? file_get_contents($filename) : $default;
	}

	/**
	 * Alias of get
	 * @see get()
	 */
	public function read($filename, $default = null)
	{
		return $this->get($filename, $default);
	}

	/**
	 * Writes data in the file.
	 * If the $mode parameter is set chmod will be made ONLY if
	 * the file did not exists before the operation.
	 *
	 * @param  string $filename
	 * @param  string $data
	 * @param  octal $mode Default null
	 * @return integer The number of bytes (not chars!) that were written to the file, or FALSE on failure.
	 */
	public function put($filename, $data, $mode = null)
	{
		$chmod = !is_null($mode) && !is_file($filename);
		$res = @file_put_contents($filename, $data, LOCK_EX);
		$chmod && $this->chmod($filename, $mode);

		return $res;
	}

	/**
	 * Alias of put.
	 * @see put()
	 */
	public function write($filename, $data, $mode = null)
	{
		return $this->put($filename, $data, $mode);
	}

	/**
	 * Append given data to the file.
	 *
	 * @param  string $filename
	 * @param  string $data
	 * @return integer The number of bytes that were written to the file, or FALSE on failure.
	 */
	public function append($filename, $data)
	{
		return @file_put_contents($filename, $data, LOCK_EX | FILE_APPEND);
	}

	/**
	 * Copy a file.
	 *
	 * @param string  $source
	 * @param string  $destination
	 * @param boolean $override TRUE to overrides destination file if it exists
	 */
	public function copy($source, $destination, $override = false)
	{
		if (!$this->isReadable($source)) {
			return false;
		}

		if ($this->exists($destination) && !$override) {
			return false;
		}

		// make sure the directory exists!
		$dir = new Directories();
		$dir->mkdir(dirname($destination));

		return @copy($source, $destination);
	}

	/**
	 * Deletes a file.
	 *
	 * @param  string  $filename
	 * @return boolean Returns TRUE if the file is deleted or did not exists.
	 */
	public function delete($filename)
	{
		if (is_file($filename)) {
			@unlink($filename);
		}

		return !file_exists($filename);
	}

	/**
	 * Changes file mode
	 *
	 * @param  string $filename
	 * @param  octal $mode
	 * @return boolean TRUE on success or FALSE on failure.
	 */
	public function chmod($filename, $mode)
	{
		// intentionally check $filename is a file not a path since chmod works also on paths
		return /*preg_match('@^0[0-7]{3}$@', $mode) &&*/ is_file($filename) && @chmod($filename, $mode);
	}

	/**
	 * Changes the owner of the file.
	 *
	 * @param  string  $files
	 * @param  mixed   $user A user name or number.
	 * @return boolean
	 */
	public function chown($filename, $user)
	{
		return is_file($filename) && @chown($filename, $user);
	}

	/**
	 * Changes file group.
	 *
	 * @param  string $filename
	 * @param  mixed   $group A group name or number.
	 * @return boolean
	 */
	public function chgrp($filename, $group)
	{
		return is_file($filename) && @chgrp($filename, $group);
	}

	/**
	 * Gets last modification time of the file.
	 *
	 * @param  string $filename
	 * @return integer or FALSE on failure (e.g. file does not exists)
	 */
	public function mtime($filename)
	{
		return is_file($filename) ? @filemtime($filename) : false;
	}

	/**
	 * @see mtime()
	 */
	public function modified($filename)
	{
		return $this->mtime($filename);
	}

	/**
	 * Extracts file extension from the name of the file.
	 *
	 * @param  string $filename
	 * @return string of FALSE on failure (file does not exists)
	 */
	public function ext($filename)
	{
		return is_file($filename) ? pathinfo($filename, PATHINFO_EXTENSION) : false;
	}

	/**
	 * Returns owner's user id.
	 *
	 * @param  string $filename
	 * @return integer or FALSE on failure
	 */
	public function getUID($filename)
	{
		if (!is_file($filename)) {
			return false;
		}

		return fileowner($filename);
	}

	/**
	 * Returns owner's name
	 *
	 * @param  string $filename
	 * @return string Username or FALSE on failure
	 */
	public function getOwner($filename)
	{
		if (!is_file($filename)) {
			return false;
		}
		$info = posix_getpwuid(fileowner($filename));

		return $info["name"];
	}

	/**
	 * Returns group owner id.
	 *
	 * @param  string $filename
	 * @return integer or FALSE on failure
	 */
	public function getGID($filename)
	{
		if (!is_file($filename)) {
			return false;
		}

		return filegroup($filename);
	}

	/**
	 * Returns group name.
	 *
	 * @param  string $filename
	 * @return string or FALSE on failure
	 */
	public function getGroup($filename)
	{
		if (!is_file($filename)) {
			return false;
		}
		$info = posix_getpwuid(filegroup($filename));

		return $info["name"];
	}
}
