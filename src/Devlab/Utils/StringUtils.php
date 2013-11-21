<?php
namespace Devlab\Utils;


class StringUtils {

	public static function startsWith($haystack, $needle)
	{
		return $needle === "" || strpos($haystack, $needle) === 0;
	}
	

}