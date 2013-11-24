<?php
namespace Composer;

use Composer\Eclipse\BuildPath;

class BuildpathTest extends \PHPUnit_Framework_TestCase 
{
	private $filename;

	protected function setUp()
	{
		$this->filename = tempnam(".", __CLASS__);
	}


	public function testSave() 
	{
		// given

		$buildpath = new BuildPath();
		$buildpath->addContainterEntry("org.eclipse.php.core.LANGUAGE");
		$buildpath->addExternalSourceEntry("/home/rosiecki/composer-eclipse-script/source");
		$buildpath->addProjectEntry("/same-project");
		$buildpath->addSourceEntry("vendor/composer/composer/src");

		// when

		$buildpath->save($this->filename);

		// then

		$this->assertFileEquals(__DIR__."/valid-eclipse-buildpath.xml", $this->filename);

	}

	protected function tearDown()
	{
		unlink($this->filename);
	}

}