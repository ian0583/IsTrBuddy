<?
/**
 * Class for commonly used functions
 *
 * @author Sheldon Senseng
 * @copyright Sheldon Senseng <sheldonsenseng@gmail.com>
 * @version 0.1
 *
 */

class Core_Helper
{
	/**
	 * HTML Formatted print_r alias
	 *
	 * @param mixed $mixed
	 */
	public static function show($mixed)
	{
		echo '<pre>';
		print_r($mixed);
		echo '</pre>';
	}

	/**
	 * Sanitizes the given string to prevent SQL Injection and HTML Entities
	 *
	 * @param string $string
	 * @return string
	 */
	public static function sanitize($string)
	{
		return addslashes(urlencode($string));
	}

	/**
	 * Desanitizes the given string that was previously sanitized
	 *
	 * @param string $string
	 * @return string
	 */
	public static function desanitize($string)
	{
		return urldecode(stripslashes($string));
	}

	/**
	 * Converts a numerical Value to its string equivalent
	 *
	 * @param int $number
	 * @return string
	 */
	public static function numberToString($number)
	{
		if (($number < 0) || ($number > 999999999))
		{
			throw new Exception("Number is out of range");
		}

		$Gn = floor($number / 1000000);  /* Millions (giga) */
		$number -= $Gn * 1000000;
		$kn = floor($number / 1000);     /* Thousands (kilo) */
		$number -= $kn * 1000;
		$Hn = floor($number / 100);      /* Hundreds (hecto) */
		$number -= $Hn * 100;
		$Dn = floor($number / 10);       /* Tens (deca) */
		$n = $number % 10;               /* Ones */

		$res = "";

		if ($Gn)
		{
			$res .= Core_Helper::convert_number($Gn) . " Million";
		}

		if ($kn)
		{
			$res .= (empty($res) ? "" : " ") .
			Core_Helper::convert_number($kn) . " Thousand";
		}

		if ($Hn)
		{
			$res .= (empty($res) ? "" : " ") .
			Core_Helper::convert_number($Hn) . " Hundred";
		}

		$ones = array("", "One", "Two", "Three", "Four", "Five", "Six",
		"Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen",
		"Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen",
		"Nineteen");
		$tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty",
		"Seventy", "Eigthy", "Ninety");

		if ($Dn || $n)
		{
			if (!empty($res))
			{
				$res .= " and ";
			}

			if ($Dn < 2)
			{
				$res .= $ones[$Dn * 10 + $n];
			}
			else
			{
				$res .= $tens[$Dn];

				if ($n)
				{
					$res .= "-" . $ones[$n];
				}
			}
		}

		if (empty($res))
		{
			$res = "zero";
		}

		return $res;
	}

	/**
	 * Generates a random string to be used as a password
	 *
	 * @param int $length
	 * @return string
	 */
	public static function randomPassword($length = 8)
	{
		$vowels = 'aeuyAEUY';
		$consonants = 'bdghjmnpqrstvzBDGHJLMNPQRSTVWXZ';

		$password = '';
		for ($i = 0; $i < $length; $i++)
		{
			if ($i % 2 == 0)
			{
				$password .= $consonants[(rand() % strlen($consonants))];
			}
			else
			{
				$password .= $vowels[(rand() % strlen($vowels))];
			}
		}

		return $password;
	}

	/**
	 * Converts the amount in seconds to a H:i:s format
	 *
	 * @param int $ts
	 * @return string
	 */
	public static function secondsToTime($ts)
	{
		$ts = (int) $ts;

		$hours = floor($ts / 3600);

		$ts -= $hours * 3600;

		$minutes = floor($ts / 60);

		$ts -= $minutes * 60;

		$seconds = $ts;

		return $hours . ':' . $minutes . ':' . $seconds;
	}

	/**
	 * Pretty formats a JSON value
	 *
	 * @param string $json
	 * @return string
	 */
	public static function formatJson($json)
	{
		$tab = "  ";
		$new_json = "";
		$indent_level = 0;
		$in_string = false;

		$json_obj = json_decode($json);

		if($json_obj === false)
		return false;

		$json = json_encode($json_obj);
		$len = strlen($json);

		for($c = 0; $c < $len; $c++)
		{
			$char = $json[$c];
			switch($char)
			{
				case '{':
				case '[':
					if(!$in_string)
					{
						$new_json .= $char . "\n" . str_repeat($tab, $indent_level+1);
						$indent_level++;
					}
					else
					{
						$new_json .= $char;
					}
					break;
				case '}':
				case ']':
					if(!$in_string)
					{
						$indent_level--;
						$new_json .= "\n" . str_repeat($tab, $indent_level) . $char;
					}
					else
					{
						$new_json .= $char;
					}
					break;
				case ',':
					if(!$in_string)
					{
						$new_json .= ",\n" . str_repeat($tab, $indent_level);
					}
					else
					{
						$new_json .= $char;
					}
					break;
				case ':':
					if(!$in_string)
					{
						$new_json .= ": ";
					}
					else
					{
						$new_json .= $char;
					}
					break;
				case '"':
					if($c > 0 && $json[$c-1] != '\\')
					{
						$in_string = !$in_string;
					}
				default:
					$new_json .= $char;
					break;
			}
		}

		return $new_json;
	}

	/**
	 * Returns if a string (str) is a prefix of a given text (text)
	 *
	 * @param string $str, string $text
	 * @return boolean
	 */
	public static function isPrefix($str, $text)
	{
		$rest = substr($text, 0, strlen($str));

		if($rest == $str)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Returns the numerical permissions of a given file
	 *
	 * @param string $file
	 * @return string
	 */
	public function filePermissions($file)
	{
		return substr(decoct(fileperms($file)), -3) . '';
	}

	public static function updateSession($key, $data)
	{
		$_SESSION[APP_NAME][$key] = $data;
	}

	public static function getSession($key)
	{
		if (isset($_SESSION[APP_NAME][$key]))
		{
			return $_SESSION[APP_NAME][$key];
		}
		else
		{
			return null;
		}
	}

	public static function clearSession($key)
	{
		if (isset($_SESSION[APP_NAME][$key]))
		{
			unset($_SESSION[APP_NAME][$key]);
		}
	}

	public static function listFiles($path)
	{
		$dh = opendir($path);

		$files = array();
		while ($file = readdir($dh))
		{
			if (!in_array($file, array('.', '..', '.svn')))
			{
				if (is_dir($path . $file))
				{
					$files = array_merge($files, self::listFiles($path . $file . '/'));
				}
				else
				{
					$files[] = $path . $file;
				}
			}
		}

		return $files;
	}



}
