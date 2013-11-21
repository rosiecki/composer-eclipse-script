<?php

namespace Composer\Eclipse;

use \DOMDocument;
use \DOMElement;
use NetArt\Tools\Utils;
use NetArt\Tools\Utils\BooleanUtils;

class BuildPath {

	private $xml;

	private $buildpath;

	public function __construct()
	{
		$this->xml = new DOMDocument ( '1.0', 'utf-8' );
		$this->xml->preserveWhiteSpace = false;
		$this->xml->formatOutput = true;
		$this->buildpath = $this->xml->createElement ( 'buildpath' );
		$this->xml->appendChild($this->buildpath);
	}

	public function addProjectEntry($path) {
		$this->_addEntry(array('path'=>$path, 'kind'=>'proj', 'combineaccessrules'=>false));
	}

	public function addContainterEntry($path) {
		$this->_addEntry(array('path'=>$path, 'kind'=>'con'));
	}

	public function addSourceEntry($path) {
		$this->_addEntry(array('path'=>$path, 'kind'=>'src'));
	}

	public function addExternalSourceEntry($path) {
		$this->_addEntry(array('path'=>$path, 'kind'=>'lib', 'external'=>true));
	}

	private function _addEntry(array $attributes) {

		$buildpathentry = $this->xml->createElement ( 'buildpathentry' );

		$buildpathentry->setAttribute('kind', $attributes['kind']);
		$buildpathentry->setAttribute('path', $attributes['path']);

		self::setBooleanAttribute($buildpathentry, 'external', $attributes);
		self::setBooleanAttribute($buildpathentry, 'combineaccessrules', $attributes);

		$this->buildpath->appendChild ( $buildpathentry );

	}

	public function save($filename) {
		$this->xml->save ($filename);
	}

	private static function setBooleanAttribute(DOMElement $element, $name, array $array) {
		if(key_exists($name, $array)) {
			$element->setAttribute($name, BooleanUtils::toString($array[$name]));
		}
	}



}