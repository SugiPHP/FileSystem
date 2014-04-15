<?php
/**
 * @package    SugiPHP
 * @subpackage FileSystem
 * @category   tests
 * @author     Plamen Popov <tzappa@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php (MIT License)
 */

use SugiPHP\FileSystem\Directories as Dir;

class DirsTest extends PHPUnit_Framework_TestCase
{

	public static function setUpBeforeClass()
	{
		@define("TESTDIR", __DIR__.DIRECTORY_SEPARATOR."testdir");
		@define("TESTDIRSLASH", __DIR__.DIRECTORY_SEPARATOR."testdirslash".DIRECTORY_SEPARATOR);
		@define("TESTFILE", __DIR__.DIRECTORY_SEPARATOR."testfile");
	}

	public function tearDown()
	{
		@unlink(TESTFILE);
		@rmdir(TESTDIR);
		@rmdir(TESTDIRSLASH);
	}

	public function testExists()
	{
		$dir = new Dir();
		$this->assertFileNotExists(TESTDIR);
		$this->assertFalse($dir->exists(TESTDIR));
		mkdir(TESTDIR);
		$this->assertFileExists(TESTDIR);
		$this->assertTrue($dir->exists(TESTDIR));
	}

	public function testExistsWithSlash()
	{
		$dir = new Dir();
		$this->assertFileNotExists(TESTDIRSLASH);
		$this->assertFalse($dir->exists(TESTDIRSLASH));
		mkdir(TESTDIRSLASH);
		$this->assertFileExists(TESTDIRSLASH);
		$this->assertTrue($dir->exists(TESTDIRSLASH));
	}

	public function testExistsForFileReturnsFalse()
	{
		touch(TESTFILE);
		$dir = new Dir();
		$this->assertFalse($dir->exists(TESTFILE));
	}

	public function testIsReadable()
	{
		$dir = new Dir();
		// directory does not exists
		$this->assertFalse($dir->isReadable(TESTDIR));
		// making a directory
		mkdir(TESTDIR);
		// checking readability
		$this->assertTrue($dir->isReadable(TESTDIR));
		// making it not readable
		chmod(TESTDIR, 0100);
		// checking readability
		$this->assertFalse($dir->isReadable(TESTDIR));
	}

	public function testIsWritable()
	{
		$dir = new Dir();
		// directory does not exists
		$this->assertFalse($dir->isWritable(TESTDIR));
		// making a directory
		mkdir(TESTDIR);
		// checking readability
		$this->assertTrue($dir->isWritable(TESTDIR));
		// making it not readable
		chmod(TESTDIR, 0500);
		// checking is not writable
		$this->assertFalse($dir->isWritable(TESTDIR));
	}

	public function testMkDir()
	{
		$dir = new Dir();
		// directory does not exists
		$this->assertFalse(is_dir(TESTDIR));
		$this->assertTrue($dir->mkdir(TESTDIR));
		$this->assertTrue(is_dir(TESTDIR));
		// if the directory is already created return TRUE
		$this->assertTrue($dir->mkdir(TESTDIR));
	}

	public function testMkDirRecursively()
	{
		$d = __DIR__.DIRECTORY_SEPARATOR."levelone".DIRECTORY_SEPARATOR."secondlevel";
		$dir = new Dir();
		// directory does not exists
		$this->assertFalse(is_dir($d));
		$dir->mkdir($d);
		$this->assertTrue(is_dir($d));
		rmdir($d);
		rmdir(dirname($d));
	}

	public function testChmod()
	{
		$dir = new Dir();
		mkdir(TESTDIR);
		// checking readability
		$this->assertTrue($dir->isReadable(TESTDIR));
		// changing mode
		$this->assertTrue($dir->chmod(TESTDIR, 0100));
		// checking readability
		$this->assertFalse($dir->isReadable(TESTDIR));
	}

	// public function testChmodFailures()
	// {
	// 	$file = new Files;
	// 	$this->assertFalse($file->chmod(TESTFILE, 0444));
	// 	$this->assertFalse($file->chmod(__DIR__, 0775));
	// }

	//
	// public function testGet()
	// {
	// 	$file = new Files;
	// 	$this->assertNull($file->get(TESTFILE));
	// 	$this->assertEquals("default", $file->get(TESTFILE, "default"));
	// 	file_put_contents(TESTFILE, "Hello World");
	// 	$this->assertEquals("Hello World", $file->get(TESTFILE));
	// 	$this->assertNull($file->get(__DIR__)); // cannot get directory
	// }

	// public function testPut()
	// {
	// 	$file = new Files;
	// 	$this->assertEquals(11, $file->put(TESTFILE, "Hello World"));
	// 	$this->assertFileExists(TESTFILE);
	// 	$this->assertEquals("Hello World", file_get_contents(TESTFILE));
	// 	$this->assertFalse($file->put(__DIR__, "test")); // cannot put in directory
	// }

	// public function testPutOnWriteProtectedFiles()
	// {
	// 	$file = new Files;
	// 	file_put_contents(TESTFILE, "foo");
	// 	chmod(TESTFILE, 0444);
	// 	$this->assertFalse($file->put(TESTFILE, "Hello World"));
	// 	$this->assertEquals("foo", file_get_contents(TESTFILE));
	// }

	// public function testAppend()
	// {
	// 	$file = new Files;
	// 	file_put_contents(TESTFILE, "Hello");
	// 	$this->assertEquals(6, $file->append(TESTFILE, " World"));
	// 	$this->assertEquals("Hello World", file_get_contents(TESTFILE));
	// 	$this->assertFalse($file->append(__DIR__, " World"));
	// }


	// public function testAppendOnWriteProtectedFiles()
	// {
	// 	$file = new Files;
	// 	file_put_contents(TESTFILE, "Hello");
	// 	chmod(TESTFILE, 0444);
	// 	$this->assertFalse($file->append(TESTFILE, " World"));
	// 	$this->assertEquals("Hello", file_get_contents(TESTFILE));
	// }

	// public function testAppendOnNonExistingFile()
	// {
	// 	$file = new Files;
	// 	$this->assertEquals(6, $file->append(TESTFILE, " World"));
	// 	$this->assertEquals(" World", file_get_contents(TESTFILE));
	// }

	// public function testDelete()
	// {
	// 	$file = new Files;
	// 	$this->assertTrue($file->delete(TESTFILE));
	// 	file_put_contents(TESTFILE, "Hello World");
	// 	$this->assertTrue($file->delete(TESTFILE));
	// 	$this->assertFalse(file_exists(TESTFILE));
	// }


	// /**
	//  * @expectedException PHPUnit_Framework_Error
	//  */
	// public function testPutWithChmod()
	// {
	// 	$file = new Files;
	// 	$this->assertEquals(11, $file->put(TESTFILE, "Hello World", 0444));
	// 	$this->assertEquals("Hello World", file_get_contents(TESTFILE));
	// 	file_put_contents(TESTFILE, "foo");
	// }
}
