<?php
namespace Devlab\Utils;


class BooleanUtils {

 public static function toString($bool, $trueString = 'true', $falseString = 'false', $nullString = "[NULL]") 
 {
    if (is_null($bool) || !is_bool($bool)) {
      return $nullString;
    }
    return $bool ? $trueString : $falseString;
  }
	

}