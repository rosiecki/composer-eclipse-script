<?php
namespace Devlab\Utils;

class ArrayUtils {
	
	public static function arrayValueRecursive(array $arr){
		$values = array();
		array_walk_recursive($arr, function($value) use(&$values){
			array_push($values, $value);
		});
		return $values;
	}
	
	public static function isNotEmpty($array) {
		return is_array($array) && empty($array) == false;
	}
	
	public static function normalize($array) {
		return self::isNotEmpty($array) ? $array : array();
	}
	
	
}