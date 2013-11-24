<?php

namespace Composer;

use Composer\Eclipse\Project;
use Composer\Eclipse\Path;
use Composer\Eclipse\Prefs;


class PrefsTest extends \PHPUnit_Framework_TestCase 
{	
	private $filename;
	
	protected function setUp()
	{
		$this->filename = tempnam(".", __CLASS__);
	}	
	
	
	public function testSave() 
	{	
		// given
		
		$prefs = new Prefs();
		$prefs->addIncludePath("/home/rosiecki/composer-eclipse-script/path01");
		$prefs->addIncludePath("/home/rosiecki/composer-eclipse-script/path02");
		$prefs->addIncludePath("/home/rosiecki/composer-eclipse-script/path03");
		
		// when
		
		$prefs->save($this->filename);
		
		// then
		
		$this->assertFileEquals(__DIR__."/valid-org.eclipse.php.core.prefs", $this->filename);		
		
	}

	protected function tearDown()
	{
		unlink($this->filename);
	}
	
}
