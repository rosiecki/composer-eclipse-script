<?php
namespace Composer\Eclipse;

use Composer\Script\Event;
use Composer\Util\Filesystem;
use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\Package;

class EventWrapper 
{
	/**
	 * @var Composer
	 */
	private $composer;

	/**
	 * @var Filesystem
	 */
	private $filesystem;

	/**
	 * @var Package
	 */
	private $package;

	/**
	 * @var IOInterface
	 */
	private $io;

	public function __construct(Event $event)
	{
		$this->composer = $event->getComposer();
		$this->io = $event->getIO();
		$this->package = $this->composer->getPackage();
		$this->filesystem = new Filesystem();
	}

	public function getProjectDir()
	{
		return $this->filesystem->normalizePath(getcwd());
	}

	public function getVendorDir()
	{
		return $this->filesystem->normalizePath(realpath($this->composer->getConfig()->get('vendor-dir')));
	}

	public function getPackageName()
	{
		return $this->package->getName();
	}

	public function getPackageVersion()
	{
		return $this->package->getVersion();
	}

	/**
	 * @return array
	 */
	public function getProjectExtra()
	{
		return $this->composer->getPackage()->getExtra();
	}

	/**
	 * array('psr-0' => array(), 'classmap' => array(), 'files' => array())
	 * 
	 * @return array
	 */
	public function getProjectIncludePaths()
	{
		$generator = $this->composer->getAutoloadGenerator();
		$packages = $this->composer->getRepositoryManager()->getLocalRepository()->getCanonicalPackages();
		$packageMap = $generator->buildPackageMap($this->composer->getInstallationManager(), $this->package, $packages);
		$map = $generator->parseAutoloads($packageMap, $this->package);
		return $map;
	}

	public function ensureDirectoryExists($directory)
	{
		$this->filesystem->ensureDirectoryExists($directory);
	}


	public function write($messages)
	{
		$this->io->write($messages);
	}

}
