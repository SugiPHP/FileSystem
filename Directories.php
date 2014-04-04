<?php
/**
 * @package    SugiPHP
 * @subpackage FileSystem
 * @author     Plamen Popov <tzappa@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php (MIT License)
 */

namespace SugiPHP\FileSystem;

/**
 * Directories - Helper functions to ease directories specific operations.
 * Files operations are intentionally avoided.
 */
class Directories
{
	/**
	 * Determine if directory exists.
	 *
	 * @param  string  $dir Directory name
	 * @return boolean
	 */
	public function exists($dir)
	{
		return is_dir($dir);
	}

	/**
	 * Determine if the directory can be accessed
	 *
	 * @param  string  $dir Directory name
	 * @return boolean
	 */
	public function isReadable($dir)
	{
		return is_dir($dir) && is_readable($dir);
	}

	/**
	 * Determine if the directory is writable.
	 *
	 * @param  string  $dir Directory name
	 * @return boolean
	 */
	public function isWritable($dir)
	{
		return is_dir($dir) && is_writable($dir);
	}

	/**
	 * Recursively create directory.
	 *
	 * @param  string  $dir
	 * @param  integer $mode
	 * @return boolean
	 */
	public function mkdir($dir, $mode = 0777)
	{
		return @mkdir($dir, $mode, true);
	}

	/**
	 * Changes directory mode.
	 *
	 * @param  string  $dir Directory name
	 * @param  octal   $mode
	 * @return boolean TRUE on success or FALSE on failure.
	 */
	public function chmod($dir, $mode)
	{
		return is_dir($dir) && chmod($dir, $mode);
	}
}
