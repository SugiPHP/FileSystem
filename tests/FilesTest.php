<?php
/**
 * @package    SugiPHP
 * @subpackage FileSystem
 * @category   tests
 * @author     Plamen Popov <tzappa@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php (MIT License)
 */

namespace SugiPHP\FileSystem;

class FilesTests extends \PHPUnit_Framework_TestCase
{

	public static function setUpBeforeClass()
	{
		@define("TESTFILE", __DIR__."/file.txt");
	}

	public function tearDown()
	{
		@unlink(TESTFILE);
	}

	public function testExists()
	{
		$file = new Files;
		$this->assertFileNotExists(TESTFILE);
		$this->assertFalse($file->exists(TESTFILE));
		file_put_contents(TESTFILE, "Hello World");
		$this->assertFileExists(TESTFILE);
		$this->assertTrue($file->exists(TESTFILE));
		$this->assertFalse($file->exists(__DIR__)); // this is a path!
	}

	public function testIsReadable()
	{
		$file = new Files;
		$this->assertTrue($file->isReadable(__FILE__));
		$this->assertFalse($file->isReadable(TESTFILE));
		file_put_contents(TESTFILE, "Hello World");
		$this->assertTrue($file->isReadable(TESTFILE));
	}

	public function testExtReturnsFileExtension()
	{
		$file = new Files;
		$this->assertEquals("php", $file->ext(__FILE__));
		$this->assertEquals("php2", $file->ext(__FILE__."2"));
		$this->assertEquals("", $file->ext(__DIR__));
	}

	public function testGet()
	{
		$file = new Files;
		$this->assertNull($file->get(TESTFILE));
		$this->assertEquals("default", $file->get(TESTFILE, "default"));
		file_put_contents(TESTFILE, "Hello World");
		$this->assertEquals("Hello World", $file->get(TESTFILE));
		$this->assertNull($file->get(__DIR__)); // cannot get directory
	}

	public function testPut()
	{
		$file = new Files;
		$this->assertEquals(11, $file->put(TESTFILE, "Hello World"));
		$this->assertFileExists(TESTFILE);
		$this->assertEquals("Hello World", file_get_contents(TESTFILE));
		$this->assertFalse($file->put(__DIR__, "test")); // cannot put in directory
	}

	public function testPutOnWriteProtectedFiles()
	{
		$file = new Files;
		file_put_contents(TESTFILE, "foo");
		chmod(TESTFILE, 0444);
		$this->assertFalse($file->put(TESTFILE, "Hello World"));
		$this->assertEquals("foo", file_get_contents(TESTFILE));
	}

	public function testAppend()
	{
		$file = new Files;
		file_put_contents(TESTFILE, "Hello");
		$this->assertEquals(6, $file->append(TESTFILE, " World"));
		$this->assertEquals("Hello World", file_get_contents(TESTFILE));
		$this->assertFalse($file->append(__DIR__, " World"));
	}


	public function testAppendOnWriteProtectedFiles()
	{
		$file = new Files;
		file_put_contents(TESTFILE, "Hello");
		chmod(TESTFILE, 0444);
		$this->assertFalse($file->append(TESTFILE, " World"));
		$this->assertEquals("Hello", file_get_contents(TESTFILE));
	}

	public function testAppendOnNonExistingFile()
	{
		$file = new Files;
		$this->assertEquals(6, $file->append(TESTFILE, " World"));
		$this->assertEquals(" World", file_get_contents(TESTFILE));
	}

	public function testDelete()
	{
		$file = new Files;
		$this->assertTrue($file->delete(TESTFILE));
		file_put_contents(TESTFILE, "Hello World");
		$this->assertTrue($file->delete(TESTFILE));
		$this->assertFalse(file_exists(TESTFILE));
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */	
	public function testChmod()
	{
		$file = new Files;
		file_put_contents(TESTFILE, "Hello World");
		$this->assertTrue($file->chmod(TESTFILE, 0444));
		file_put_contents(TESTFILE, "foo");
	}

	public function testChmodFailures()
	{
		$file = new Files;
		$this->assertFalse($file->chmod(TESTFILE, 0444));
		$this->assertFalse($file->chmod(__DIR__, 0775));
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */	
	public function testPutWithChmod()
	{
		$file = new Files;
		$this->assertEquals(11, $file->put(TESTFILE, "Hello World", 0444));
		$this->assertEquals("Hello World", file_get_contents(TESTFILE));
		file_put_contents(TESTFILE, "foo");
	}
}
