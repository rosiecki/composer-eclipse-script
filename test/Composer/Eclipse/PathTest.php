<?php

namespace Composer;

use Composer\Eclipse\Project;
use Composer\Eclipse\Path;


class PathTest extends \PHPUnit_Framework_TestCase 
{
	public function testIsVendorSource() 
	{		
		// given
		
		$source = "/home/rosiecki/composer-eclipse-script/vendor/composer"; 
		$vendorPath = "/home/rosiecki/composer-eclipse-script/vendor";
		
		$path = new Path($source, $vendorPath);
		
		// when
		
		$isVendorSource = $path->isVendorSource();
		
		// then
		
		$this->assertTrue($isVendorSource);
	}
	

	public function testIsNotVendorSource() 
	{	
		// given
	
		$source = "/home/rosiecki/composer-eclipse-script/src";
		$vendorPath = "/home/rosiecki/composer-eclipse-script/vendor";
	
		$path = new Path($source, $vendorPath);
	
		// when
	
		$isVendorSource = $path->isVendorSource();
	
		// then
	
		$this->assertFalse($isVendorSource);
	}
	

	public function testIsExists() 
	{
		// given
	
		$source = __DIR__;
		$vendorPath = "IGNORED_PATH";
	
		$path = new Path($source, $vendorPath);
	
		// when
	
		$isExists = $path->isExists();
	
		// then
	
		$this->assertTrue($isExists);
	}	
	
	public function testVendorize() 
	{
		// given
	
		$source = "/home/rosiecki/composer-eclipse-script/vendor/composer";
		$vendorPath = "/home/rosiecki/composer-eclipse-script/vendor";
	
		$path = new Path($source, $vendorPath);
	
		// when
	
		$vendorizedPath = $path->vendorize();
	
		// then
	
		$this->assertEquals("vendor/composer", $vendorizedPath);
	}
}