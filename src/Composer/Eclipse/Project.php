<?php

namespace Composer\Eclipse;

use \DOMDocument;
use \DOMElement;

class Project {

	private $xml;

	private $buildSpec;

	private $natures;

	public function __construct($name)
	{
		$this->xml = new DOMDocument ( '1.0', 'utf-8' );
		$this->xml->preserveWhiteSpace = false;
		$this->xml->formatOutput = true;

		$projectDescription = $this->xml->createElement ( 'projectDescription' );

		$this->buildSpec = $this->xml->createElement ( 'buildSpec' );
		$this->natures = $this->xml->createElement ( 'natures' );

		$projectDescription->appendChild ( $this->xml->createElement ( 'name', $name ) );
		$projectDescription->appendChild ( $this->buildSpec );
		$projectDescription->appendChild ( $this->natures );

		$this->xml->appendChild ( $projectDescription );
	}

	public function addBuildCommand($value)
	{
		$buildCommand = $this->xml->createElement ( 'buildCommand' );
		$buildCommand->appendChild ( $this->xml->createElement ( 'name', $value ) );

		$this->buildSpec->appendChild ( $buildCommand );
	}

	public function addNature($value)
	{
		$this->natures->appendChild ( $this->xml->createElement ( 'nature', $value ) );
	}

	public function save($filename) {
		$this->xml->save ($filename);
	}
}