<?php

namespace Nano\Cli;

class DocBlockParser {

	/**
	 * @return string
	 * @param string $string
	 */
	public static function parse($string) {
		$toParse = mb_subStr($string, 2, -2, 'UTF-8');
		$toParse = trim($toParse);
		$toParse = preg_replace('/^\s*\*[^@]/m', '', $toParse);
		$parts  = preg_split('/(@(?:description|param))\s*/', $toParse, null, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		$count  = count($parts);

		$result = array();
		for ($i = 0; $i < $count; ++$i) {
			$tag = strToLower(subStr($parts[$i], 1));
			if ($i + 1 >= $count) {
				continue;
			}
			if ('@' != $parts[$i + 1][0]) {
				$function     = 'parse' . ucFirst($tag);
				$item         = self::$function($parts[$i + 1]);
				$item['name'] = $tag;
				$result[]     = $item;
				++$i;
			}
		}

		return $result;
	}

	/**
	 * @return string[]
	 * @param string $value
	 */
	public static function parseDescription($value) {
		$result = array();
		$result['value'] = trim($value);
		return $result;
	}

	/**
	 * @return string[]
	 * @param string $value
	 */
	public static function parseParam($value) {
		list ($optional, $param, $description) = preg_split('/\s+/', $value, 3, PREG_SPLIT_NO_EMPTY);
		$result = array();
		$result['param']       = $param;
		$result['optional']    = $optional;
		$result['description'] = trim($description);
		return $result;
	}

}