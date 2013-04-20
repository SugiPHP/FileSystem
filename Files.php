<?php
/**
 * @package    SugiPHP
 * @subpackage FileSystem
 * @author     Plamen Popov <tzappa@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php (MIT License)
 */

namespace SugiPHP\FileSystem;

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
	 * Changes file mode
	 *
	 * @param  string $filename
	 * @param  octal $mode
	 * @return boolean TRUE on success or FALSE on failure. 
	 */
	public function chmod($filename, $mode)
	{
		// intentionally check $filename is a file not a path since chmod works also on paths
		return /*preg_match('@^0[0-7]{3}$@', $mode) and*/ is_file($filename) and chmod($filename, $mode);
	}

	/**
	 * Gets last modification time of the file
	 *
	 * @param  string $filename
	 * @return integer or FALSE on failure (e.g. file does not exists)
	 */
	public function modified($filename)
	{
		return @filemtime($filename);
	}

	/** 
	 * Extracts file extension from the name of the file.
	 * Note that the function will return extension even 
	 * if the file doesn't exists or it is actually a directory!
	 *
	 * @param  string $filename
	 * @return string
	 */
	public function ext($filename)
	{
		return pathinfo($filename, PATHINFO_EXTENSION);
	}

	/**
	 * Deletes a file.
	 * If the file does not exists returns true
	 * 
	 * @param  string $filename
	 * @return boolean
	 */
	public function delete($filename) {
		return is_file($filename) ? @unlink($filename) : true;
	}
}
