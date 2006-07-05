<?php /* $Id */
//Copyright (C) 2006 WeBRainstorm S.r.l. (ask@webrainstorm.it)
//
//This program is free software; you can redistribute it and/or
//modify it under the terms of the GNU General Public License
//as published by the Free Software Foundation; either version 2
//of the License, or (at your option) any later version.
//
//This program is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details.

function phonebook_list() {
	require_once('common/php-asmanager.php');
	global $amp_conf;

	$astman = new AGI_AsteriskManager();
	if ($res = $astman->connect("127.0.0.1", $amp_conf["AMPMGRUSER"] , $amp_conf["AMPMGRPASS"])) {
		$list = $astman->database_show();
		foreach ($list as $k => $v)	{
			if (substr($k, 1, 7) == 'cidname')
			$numbers[substr($k, 9)] = $v ;
		}

		if (is_array($numbers))
			natcasesort($numbers);

		return $numbers;
	} else {
		fatal("Cannot connect to Asterisk Manager with ".$amp_conf["AMPMGRUSER"]."/".$amp_conf["AMPMGRPASS"]);
	}

}

function phonebook_del($number){
	require_once('common/php-asmanager.php');
	global $amp_conf;

	$astman = new AGI_AsteriskManager();
	if ($res = $astman->connect("127.0.0.1", $amp_conf["AMPMGRUSER"] , $amp_conf["AMPMGRPASS"])) {
		$astman->database_del("cidname",$number);
	} else {
		fatal("Cannot connect to Asterisk Manager with ".$amp_conf["AMPMGRUSER"]."/".$amp_conf["AMPMGRPASS"]);
	}
}

function phonebook_empty(){
	require_once('common/php-asmanager.php');
	global $amp_conf;

	$astman = new AGI_AsteriskManager();
	if ($res = $astman->connect("127.0.0.1", $amp_conf["AMPMGRUSER"] , $amp_conf["AMPMGRPASS"])) {
		$astman->database_deltree("cidname");
	} else {
		fatal("Cannot connect to Asterisk Manager with ".$amp_conf["AMPMGRUSER"]."/".$amp_conf["AMPMGRPASS"]);
	}
}

function phonebook_add($number, $name){
	require_once('common/php-asmanager.php');
	global $amp_conf;

	if(!phonebook_chk($number))
		return false;

  $astman = new AGI_AsteriskManager();
	if ($res = $astman->connect("127.0.0.1", $amp_conf["AMPMGRUSER"] , $amp_conf["AMPMGRPASS"])) {
		$astman->database_put("cidname",$number, '"'.$name.'"');
	} else {
		fatal("Cannot connect to Asterisk Manager with ".$amp_conf["AMPMGRUSER"]."/".$amp_conf["AMPMGRPASS"]);
	}
}


// TODO: ensures post vars is valid
function phonebook_chk($post){
	return true;
}

function fgetcsvfromline ($line, $columnCount, $delimiterChar = ';', $enclosureChar = '"') {
    $regExpSpecialChars = array (
        "|" => "\\|",
        "&" => "\\&",
        "$" => "\\$",
        "(" => "\\(",
        ")" => "\\)",
        "^" => "\\^",
        "[" => "\\[",
        "]" => "\\]",
        "{" => "\\{",
        "}" => "\\}",
        "." => "\\.",
        "*" => "\\*",
        "\\" => "\\\\",
        "/" => "\\/"
    );

    $matches = array();
    $delimiterChar = strtr($delimiterChar, $regExpSpecialChars);
    $enclosureChar = strtr($enclosureChar, $regExpSpecialChars);

    $regExp = "/^";
    for ($i = 0; $i < $columnCount; $i++) {
        $regExp .= '('.$enclosureChar.'?)(.*)\\'.(2*$i + 1).$delimiterChar; // construct the regular expression
    }
    $regExp = substr($regExp, 0, (strlen($regExp) - strlen($delimiterChar)))."/"; // format the regular expression

    if (preg_match($regExp, $line, $matches)) {
        $result = array();
        for ($i = 1; $i < count($matches)/2; $i++) {
            if (strlen($matches[2*$i]) < 1)
              $matches[2*$i] = "";
            $result[$i] = $matches[2*$i]; // get only the fields but not the delimiters
        }
        return $result;
    }
    return FALSE;
}

?>
