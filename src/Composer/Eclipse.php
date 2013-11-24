<?php

namespace Composer;

use Composer\Script\Event;
use Composer\IO\IOInterface;
use Composer\Util\Filesystem;
use Composer\Composer;

use Composer\Eclipse\BuildPath;
use Composer\Eclipse\Project;
use Composer\Eclipse\Path;
use Composer\Eclipse\Prefs;

use Devlab\Utils\ArrayUtils;
use Devlab\Utils\StringUtils;
use Composer\Package\Package;
use Composer\Eclipse\EventWrapper;


class Eclipse {
	
	/**
	 * @var EventWrapper
	 */
	private $eventWrapper;
	
	/**
	 * @var string
	 */
	private $projectDir;
	
	/**
	 * @var string
	 */	
	private $settingsDir;
	
	public static function eclipse(Event $event)
	{		
		if($event->isDevMode()) {
		
			$eventWrapper = new EventWrapper($event);			
			$eclipse = new Eclipse($eventWrapper);
			$eclipse->execute();			
		}
	}
	
	
	public function __construct(EventWrapper $eventWrapper)
	{
		$this->eventWrapper = $eventWrapper;
		$this->projectDir = $eventWrapper->getProjectDir();
		$this->settingsDir = $this->projectDir . DIRECTORY_SEPARATOR.".settings";
	}
	
	public function execute() {

		$projectName = $this->eventWrapper->getPackageName() . "-". $this->eventWrapper->getPackageVersion();
	
		$this->setupPHPProject($projectName);	
		$this->setupPHPIncludePath($projectName);		
	}
	
	private function setupPHPProject($projectName) {

		$this->log("Generating file ".$this->projectDir . DIRECTORY_SEPARATOR.".project");		
		
		$project = new Project ( $projectName );
			
		$extra = $this->eventWrapper->getProjectExtra();

		foreach ($this->getBuilders($extra) as $builder) {
			$project->addBuildCommand($builder);
		}
		
		foreach ($this->getNatures($extra) as $nature) {
			$project->addNature($nature);
		}

		$project->save ( $this->projectDir . DIRECTORY_SEPARATOR.".project");
	}	


	private function getBuilders(array $extra)
	{
		if(isset($extra["eclipse"]["builders"]))
		{
			return ArrayUtils::normalize($extra["eclipse"]["builders"]);
		}
	
		return array
		(
			"org.eclipse.wst.validation.validationbuilder",
			"org.eclipse.dltk.core.scriptbuilder",
			"org.eclipse.wst.common.project.facet.core.builder"
		);
	}
	
	private function getNatures(array $extra)
	{
		if(isset($extra["eclipse"]["natures"]))
		{
			return ArrayUtils::normalize($extra["eclipse"]["natures"]);
		}

		return array
		(
			"org.eclipse.wst.common.project.facet.core.nature",
			"org.eclipse.php.core.PHPNature"
		);
	}	
	

	private function setupPHPIncludePath($projectName)
	{	
		$sources = $this->generateAbsoluteAutoloadPaths();		
		$paths = $this->transformToRelativePaths($sources);
				
		$this->setupBuildPath($paths);

		$this->setupPrefs($this->eventWrapper->getPackageName(), $paths);

	}
	
	private function setupBuildPath(array $paths) 
	{
		$this->log("Generating file ".$this->projectDir . DIRECTORY_SEPARATOR.".buildpath");
		
		$buildPath = new BuildPath();
		
		$buildPath->addContainterEntry("org.eclipse.php.core.LANGUAGE");
		
		foreach($paths as $source)
		{
			$buildPath->addSourceEntry($source);
		}
		
		$buildPath->save ( $this->projectDir . DIRECTORY_SEPARATOR.".buildpath");
		
	}

	private function setupPrefs($projectName, array $paths) 
	{		
		$settingsDir = $this->projectDir . DIRECTORY_SEPARATOR.".settings";

		$this->log("Generating file ".$settingsDir.DIRECTORY_SEPARATOR."org.eclipse.php.core.prefs");
		
		$prefs = new Prefs ();
		
		foreach($paths as $source) {
			$prefs->addIncludePath(DIRECTORY_SEPARATOR.$projectName.DIRECTORY_SEPARATOR.$source);
		}
		
		$this->eventWrapper->ensureDirectoryExists($settingsDir);
		
		$prefs->save ( $settingsDir.DIRECTORY_SEPARATOR."org.eclipse.php.core.prefs");		
	}	

	private function generateAbsoluteAutoloadPaths() 
	{ 	
		$map = $this->eventWrapper->getProjectIncludePaths();
		return ArrayUtils::arrayValueRecursive($map);
	}


	private function transformToRelativePaths(array $sources) {
		
		$paths = array();

		$vendorDir = $this->eventWrapper->getVendorDir();
		
		foreach ($sources as $source) {

			$path = new Path($source, $vendorDir);
			
			if($path->isExists()) {
				
				if($path->isVendorSource()){	

					array_push($paths, $path->vendorize());
				} else {
					array_push($paths, $source);
				}
			}
			
		}
		return $paths;
	}

	private function log($messages) 
	{
		$this->eventWrapper->write($messages);
	}
	

}