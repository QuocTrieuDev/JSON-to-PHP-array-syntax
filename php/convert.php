<?php

$GLOBALS["conf_tab"] = "    ";


INPUT_JSON_STRING:
$json = $f = "";
echo "Paste json string below, enter # charater to complete.\n\n#BEGIN\n";
// Input JSON string
while (true) {
	$f = trim(fgets(STDIN));
	if (strpos($f, "#") === strlen($f) - 1) {
		$f = substr($f, 0, -1);
		$json .= $f;
		break;
	}
	$json .= "$f\n";
}

$array = json_decode($json, true);
if ($array == null) {
	// Error message and re-entry
	echo "ERROR: " . json_last_error_msg() . "\n";
	echo "=====JSON INPUT=====\n" . $json . "\n=====END JSON INPUT=====\n\n";
	goto INPUT_JSON_STRING;
}

echo "\n=====RESULT=====\n";
echo toArraySyntax($array, $GLOBALS["conf_tab"]);

function toArraySyntax(array $array, $tabSize, $deepth = 0)
{
	/**
	 * Convert array  to php array syntax
	 * @param array $array Array to convert
	 * @param string $tabSize The padding for format syntax
	 * @param int $deepth Use for recursive process, perform to calculate the size of Padding 
	 * @return string as php array syntax
	 */
	$tab = str_repeat($tabSize, $deepth + 1);
	$preTab = str_repeat($tabSize, $deepth);;

	if (isAssoc($array)) {
		$output = "[\n";
		$i = 0;
		foreach ($array as $key => $value) {
			renderVar($key);
			if (is_array($value)) {
				$value = toArraySyntax($value, $tab, $deepth + 1);
			} else {
				renderVar($value);
			}

			$output .= $tab . "$key => $value";
			if (++$i != count($array)) { // Find the last element of an array
				$output .= ",\n";
			}
		}
		$output .= "\n" . $preTab . "]";
	} else {
		$output = "[\n";
		$i = 0;
		foreach ($array as $element) {
			if (is_array($element)) {
				$element = toArraySyntax($element, $tab, $deepth + 1);
			} else{
				renderVar($element);
			} 
			$output .=  $tab . $element;
			if (++$i != count($array)) { // Find the last element of an array
				$output .= ",\n";
			}
		}
		$output .= "\n" . $preTab . "]";
	}
	return $output;
}
function isAssoc(array $arr)
{
	if (array() === $arr) return false;
	return array_keys($arr) !== range(0, count($arr) - 1);
}
function renderVar(&$mix)
{
	if (is_string($mix)) {
		$mix = "\"" . addslashes($mix) . "\"";
	}
	return $mix;
}
