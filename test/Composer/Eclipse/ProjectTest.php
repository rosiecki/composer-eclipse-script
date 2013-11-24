<?php

namespace Composer;

use Composer\Eclipse\Project;


class ProjectTest extends \PHPUnit_Framework_TestCase 
{
	private $filename;
	
 	protected function setUp()
    {    	
    	$this->filename = tempnam(".", __CLASS__);
    }
	
	
	public function test() 
	{
		// given
		
		$project = new Project("composer-eclipse-script");
		
		// when
		
		$project->addBuildCommand("org.eclipse.wst.validation.validationbuilder");
		$project->addBuildCommand("org.eclipse.dltk.core.scriptbuilder");
		$project->addBuildCommand("org.eclipse.wst.common.project.facet.core.builder");
		
		$project->addNature("org.eclipse.wst.common.project.facet.core.nature");
		$project->addNature("org.eclipse.php.core.PHPNature");

		$project->save($this->filename);

		// then
		
		$this->assertFileEquals(__DIR__."/valid-eclipse-project.xml", $this->filename);				
	}

	protected function tearDown()
	{
		unlink($this->filename);
	}	
	
}