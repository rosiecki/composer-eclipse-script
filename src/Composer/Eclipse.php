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



class Eclipse {
	
	private $composer;
	
	private $filesystem;

	private $package;

	private $projectDir;
	
	private $settingsDir;
	
	private $io;
	
	public static function eclipse(Event $event)
	{		
		if($event->isDevMode()) {
		
			$composer = $event->getComposer();
		
			$io = $event->getIO();
			
			$eclipse = new Eclipse($composer, $io);
			$eclipse->execute();			
		}
	}
	
	private function __construct(Composer $composer, IOInterface $io)
	{
		$this->composer = $composer;
		$this->io = $io;
		$this->package = $this->composer->getPackage();
		$this->filesystem = new Filesystem();

		$this->projectDir = $this->filesystem->normalizePath(getcwd());
		$this->settingsDir = $this->projectDir . DIRECTORY_SEPARATOR.".settings";

	}
	
	private function execute() {
	
		$projectName = $this->package->getName() . "-". $this->package->getVersion();
	
		$this->setupPHPProject($projectName);	
		$this->setupPHPIncludePath($projectName);		
	}
	
	private function setupPHPProject($projectName) {

		$this->io->write("Generating file ".$this->projectDir . DIRECTORY_SEPARATOR.".project");		
		
		$project = new Project ( $projectName );
			
		$extra = $this->composer->getPackage()->getExtra();

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

		$this->setupPrefs($this->package->getName(), $paths);

	}
	
	private function setupBuildPath(array $paths) 
	{
		$this->io->write("Generating file ".$this->projectDir . DIRECTORY_SEPARATOR.".buildpath");
		
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

		$this->io->write("Generating file ".$settingsDir.DIRECTORY_SEPARATOR."org.eclipse.php.core.prefs");
		
		$prefs = new Prefs ();
		
		foreach($paths as $source) {
			$prefs->addIncludePath(DIRECTORY_SEPARATOR.$projectName.DIRECTORY_SEPARATOR.$source);
		}
		
		$this->filesystem->ensureDirectoryExists($settingsDir);
		
		$prefs->save ( $settingsDir.DIRECTORY_SEPARATOR."org.eclipse.php.core.prefs");
		
	}	

	private function generateAbsoluteAutoloadPaths() 
	{ 	
		$generator = $this->composer->getAutoloadGenerator();
		$packages = $this->composer->getRepositoryManager()->getLocalRepository()->getCanonicalPackages();
		$packageMap = $generator->buildPackageMap($this->composer->getInstallationManager(), $this->package, $packages);
		$map = $generator->parseAutoloads($packageMap, $this->package);
		return ArrayUtils::arrayValueRecursive($map);
	}



	private function transformToRelativePaths(array $sources) {
		
		$paths = array();

		$vendorPath = $this->filesystem->normalizePath(realpath($this->composer->getConfig()->get('vendor-dir')));
		
		foreach ($sources as $source) {

			$path = new Path($source, $vendorPath);
			
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

	

}