<?php
/**
 * @package    SugiPHP
 * @subpackage FileSystem
 * @category   tests
 * @author     Plamen Popov <tzappa@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php (MIT License)
 */

namespace SugiPHP\FileSystem\Test;

use SugiPHP\FileSystem\Files as File;
use PHPUnit_Framework_TestCase;

class FilesTests extends PHPUnit_Framework_TestCase
{

	public static function setUpBeforeClass()
	{
		@define("TESTFILE", __DIR__.DIRECTORY_SEPARATOR."file.txt");
		@define("TESTFILE2", __DIR__.DIRECTORY_SEPARATOR."file2.txt");
	}

	public function tearDown()
	{
		@unlink(TESTFILE2);
		@unlink(TESTFILE);
	}

	public function testExists()
	{
		$file = new File();
		$this->assertFileNotExists(TESTFILE);
		$this->assertFalse($file->exists(TESTFILE));
		file_put_contents(TESTFILE, "Hello World");
		$this->assertFileExists(TESTFILE);
		$this->assertTrue($file->exists(TESTFILE));
		$this->assertFalse($file->exists(__DIR__)); // this is a path!
	}

	public function testIsReadable()
	{
		$file = new File();
		$this->assertTrue($file->isReadable(__FILE__));
		$this->assertFalse($file->isReadable(TESTFILE));
		file_put_contents(TESTFILE, "Hello World");
		$this->assertTrue($file->isReadable(TESTFILE));
	}

	public function testExtReturnsFileExtension()
	{
		$file = new File();
		$this->assertEquals("php", $file->ext(__FILE__));
		$this->assertEquals("", $file->ext(__DIR__));
		$this->assertFalse($file->ext(__DIR__));
	}

	public function testGet()
	{
		$file = new File();
		$this->assertNull($file->get(TESTFILE));
		$this->assertEquals("default", $file->get(TESTFILE, "default"));
		file_put_contents(TESTFILE, "Hello World");
		$this->assertEquals("Hello World", $file->get(TESTFILE));
		// using read as an alias
		$this->assertEquals("Hello World", $file->read(TESTFILE));
		// cannot get directory
		$this->assertNull($file->get(__DIR__));
	}

	public function testPut()
	{
		$file = new File();
		$this->assertEquals(11, $file->put(TESTFILE, "Hello World"));
		$this->assertFileExists(TESTFILE);
		$this->assertEquals("Hello World", file_get_contents(TESTFILE));
		// cannot put in directory
		$this->assertFalse($file->put(__DIR__, "test"));
		// using write as an alias of put
		$this->assertEquals(11, $file->write(TESTFILE, "Hello World"));
	}

	public function testPutOnWriteProtectedFiles()
	{
		$file = new File();
		file_put_contents(TESTFILE, "foo");
		chmod(TESTFILE, 0444);
		$this->assertFalse($file->put(TESTFILE, "Hello World"));
		$this->assertEquals("foo", file_get_contents(TESTFILE));
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testPutWithChmod()
	{
		$file = new File();
		$this->assertEquals(11, $file->put(TESTFILE, "Hello World", 0444));
		$this->assertEquals("Hello World", file_get_contents(TESTFILE));
		file_put_contents(TESTFILE, "foo");
	}

	public function testAppend()
	{
		$file = new File();
		file_put_contents(TESTFILE, "Hello");
		$this->assertEquals(6, $file->append(TESTFILE, " World"));
		$this->assertEquals("Hello World", file_get_contents(TESTFILE));
		$this->assertFalse($file->append(__DIR__, " World"));
	}

	public function testAppendOnWriteProtectedFiles()
	{
		$file = new File();
		file_put_contents(TESTFILE, "Hello");
		chmod(TESTFILE, 0444);
		$this->assertFalse($file->append(TESTFILE, " World"));
		$this->assertEquals("Hello", file_get_contents(TESTFILE));
	}

	public function testAppendOnNonExistingFile()
	{
		$file = new File();
		$this->assertEquals(11, $file->append(TESTFILE, "Hello World"));
		$this->assertEquals("Hello World", file_get_contents(TESTFILE));
	}

	public function testDelete()
	{
		$file = new File();
		$this->assertTrue($file->delete(TESTFILE));
		file_put_contents(TESTFILE, "Hello World");
		$this->assertTrue($file->delete(TESTFILE));
		$this->assertFalse(file_exists(TESTFILE));
	}

	public function testMtime()
	{
		$file = new File();
		$this->assertFalse($file->mtime(TESTFILE));
		file_put_contents(TESTFILE, "Hello World");
		$this->assertNotEmpty($file->mtime(TESTFILE));
		$this->assertInternalType("integer", $file->mtime(TESTFILE));
	}

	public function testGetUID()
	{
		$file = new File();
		// File does not exists
		$this->assertFalse($file->getUID(TESTFILE));
		file_put_contents(TESTFILE, "Hello World");
		$this->assertInternalType("integer", $file->getUID(TESTFILE));
	}

	public function testGetOwner()
	{
		$file = new File();
		// File does not exists
		$this->assertFalse($file->getOwner(TESTFILE));
		file_put_contents(TESTFILE, "Hello World");
		$this->assertNotEmpty($file->getOwner(TESTFILE));
		$this->assertInternalType("string", $file->getOwner(TESTFILE));
	}

	public function testGetGID()
	{
		$file = new File();
		// File does not exists
		$this->assertFalse($file->getGID(TESTFILE));
		file_put_contents(TESTFILE, "Hello World");
		$this->assertInternalType("integer", $file->getGID(TESTFILE));
	}

	public function testGetGroup()
	{
		$file = new File();
		// File does not exists
		$this->assertFalse($file->getGroup(TESTFILE));
		file_put_contents(TESTFILE, "Hello World");
		$this->assertNotEmpty($file->getGroup(TESTFILE));
		$this->assertInternalType("string", $file->getGroup(TESTFILE));
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testChmod()
	{
		$file = new File();
		file_put_contents(TESTFILE, "Hello World");
		$this->assertTrue($file->chmod(TESTFILE, 0444));
		file_put_contents(TESTFILE, "foo");
	}

	public function testChmodFailures()
	{
		$file = new File();
		$this->assertFalse($file->chmod(TESTFILE, 0444));
		$this->assertFalse($file->chmod(__DIR__, 0775));
	}

	public function testChown()
	{
		$file = new File();
		$this->assertFalse($file->chown(TESTFILE, $file->getUID(TESTFILE)));
		touch(TESTFILE);
		$this->assertTrue($file->chown(TESTFILE, $file->getUID(TESTFILE)));
		$this->assertTrue($file->chown(TESTFILE, $file->getOwner(TESTFILE)));
	}

	public function testChgrp()
	{
		$file = new File();
		$this->assertFalse($file->chown(TESTFILE, $file->getGID(TESTFILE)));
		touch(TESTFILE);
		$this->assertTrue($file->chown(TESTFILE, $file->getGID(TESTFILE)));
		$this->assertTrue($file->chown(TESTFILE, $file->getGroup(TESTFILE)));
	}

	public function testCopy()
	{
		$file = new File();
		// copy an unexisting file
		$this->assertFalse($file->copy(TESTFILE, TESTFILE2));
		// making source file
		$file->write(TESTFILE, "Hi there");
		// copy
		$this->assertTrue($file->copy(TESTFILE, TESTFILE2));
		// check new file exists
		$this->assertTrue(file_exists(TESTFILE2));
		// check contents
		$this->assertEquals("Hi there", $file->read(TESTFILE2));
		// change contents for original file
		$file->write(TESTFILE, "Something different");
		// copy it (no overwrite)
		$this->assertFalse($file->copy(TESTFILE, TESTFILE2));
		// check it's still old one
		$this->assertEquals("Hi there", $file->read(TESTFILE2));
		// overwrite it
		$this->assertTrue($file->copy(TESTFILE, TESTFILE2, true));
		// check the contents is new
		$this->assertEquals("Something different", $file->read(TESTFILE2));
	}

	public function testMove()
	{
		$file = new File();
		// move an unexisting file
		$this->assertFalse($file->move(TESTFILE, TESTFILE2));
		// making source file
		$file->write(TESTFILE, "Hi there");
		// move it
		$this->assertTrue($file->move(TESTFILE, TESTFILE2));
		// check the file is moved
		$this->assertTrue(file_exists(TESTFILE2));
		// check contents
		$this->assertEquals("Hi there", $file->read(TESTFILE2));
		// check source file is gone
		$this->assertFalse(file_exists(TESTFILE));
		// make a new source file
		$file->write(TESTFILE, "Something different");
		// move it (no overwrite)
		$this->assertFalse($file->move(TESTFILE, TESTFILE2));
		// check it's still old one
		$this->assertEquals("Hi there", $file->read(TESTFILE2));
		// move it (overwrite)
		$this->assertTrue($file->move(TESTFILE, TESTFILE2, true));
		// test it's a new
		$this->assertEquals("Something different", $file->read(TESTFILE2));
		// test original file is gone
		$this->assertFalse(file_exists(TESTFILE));
	}
}
