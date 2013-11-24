<?php


namespace Composer;

use Composer\Eclipse;
use Composer\Eclipse\EventWrapper;
use Composer\Script\Event;
use Composer\Eclipse\EventWrapperInterface;

class EclipseTest extends \PHPUnit_Framework_TestCase 
{	
	private $projectDir;
	
	protected function setUp()
	{
		$this->projectDir = dirname(dirname(__DIR__))."/".uniqid();
		
		mkdir($this->projectDir);
		mkdir($this->projectDir. "/.settings");
		mkdir($this->projectDir. "/vendor");
		mkdir($this->projectDir. "/vendor/phpunit");
	}	
		
	public function testEclipse() 
	{
		// given

 		$eventWrapper = $this->getMockBuilder('Composer\\Eclipse\\EventWrapper')->disableOriginalConstructor()->getMock();
 		
 		$this->record($eventWrapper, 'getProjectDir', $this->projectDir);
 		$this->record($eventWrapper, 'getPackageName', 'composer-eclipse-script');
 		$this->record($eventWrapper, 'getPackageVersion', '1.0.0');
 		$this->record($eventWrapper, 'getVendorDir', $this->projectDir. '/vendor');
 		$this->record($eventWrapper, 'getProjectExtra', array());
 		
		$eventWrapper->write("Generating file $this->projectDir/.project");
		$eventWrapper->write("Generating file $this->projectDir/.buildpath");
		$eventWrapper->write("Generating file $this->projectDir/.settings/org.eclipse.php.core.prefs");
		
		$includePaths = array('psr-0' => array("$this->projectDir/vendor/phpunit"), 'classmap' => array(), 'files' => array());
		$this->record($eventWrapper, 'getProjectIncludePaths', $includePaths);
		
		$eclipse = new Eclipse($eventWrapper);
		
		// when
		
		$eclipse->execute();
		
		// then
		
		$this->assertFileEquals(__DIR__."/valid-eclipse-project.xml", "$this->projectDir/.project");
		$this->assertFileEquals(__DIR__."/valid-eclipse-buildpath.xml", "$this->projectDir/.buildpath");
		$this->assertFileEquals(__DIR__."/valid-org.eclipse.php.core.prefs", "$this->projectDir/.settings/org.eclipse.php.core.prefs");

	}

	private function record($mock, $methodName, $returnValue) 
	{
		$mock->expects($this->any())->method($methodName)->will($this->returnValue($returnValue));
	}
	
	protected function tearDown()
	{
		unlink($this->projectDir. "/.buildpath");
		unlink($this->projectDir. "/.project");
		unlink($this->projectDir. "/.settings/org.eclipse.php.core.prefs");
		rmdir($this->projectDir. "/.settings");
		rmdir($this->projectDir. "/vendor/phpunit");
		rmdir($this->projectDir. "/vendor");
		rmdir($this->projectDir);
	}

}