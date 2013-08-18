<?php 

// This is an alternate get_plural, which has the all the plurals are defined in a file.
function get_plural_alternate($word)
{
	include_once "lang/en/lang_plural.inc";

	if (isset($__plural_desc[$word])) {
		return $__plural_desc[$word];
	}

	return "{$word}s";
}

function get_plural($string) {

    $string = strtolower($string);

    $combi=array("ba"=>"be",
        "ca"=>"che",
        "cchio"=>"cchie",
        "cia" => "ce",
        "da" => "de",
        "fa" => "fe",
        "ga" => "ghe",
        "glia" => "glie",
        "la" => "le",
        "ma" => "me",
        "na" => "ne",
        "pa" => "pe",
        "qua" => "que",
        "ra" => "re",
        "sa" => "se",
        "ta" => "te",
        "va" => "ve",
        "za" => "ze",
        "bo" => "bi",
        "occhio" => "occhi",
        "co" => "chi",
        "rco" => "rci",
        "vortice"=>"vortici",
        "rtico" => "rtici",
        "cio" => "ci",
        "fede" => "fedi",
        "do" => "di",
        "fo" => "fi",
        "go" => "gi",
        "go" => "ghi",
        "glio" => "gli",
        "arme" => "armi",
        "lo" => "li",
        "mo" => "mi",
        "one" => "oni",
        "rno" => "rni",
        "treno"=> "treni",
        "one" => "oni",
        "ino" => "ini",
        "eno" => "eni",
        "po" => "pi",
        "are" => "ari",
        "ccessorio" => "ccessori",
        "bro" => "bri",
        "re" => "ri",
        "so" => "si",
        "ista" => "isti",
        "to" => "ti",
        "zo" => "zi",
        "zio" => "zi",
	"gio" => "gi",
	"nio" => "ni",
	"rio" => "ri",
        "io" => "ii",
        "ereo" => "erei", 
        "neo" => "nei",
        "ne" => "ni",
        "ua" => "ue");
        

$plur_word = $string;

foreach ($combi as $singo=>$plur)


    {
        
        if (substr($string,strlen($singo)*-1) == $singo) {
            $plur_word =
substr($string,0,strlen($string)-strlen($singo)).$plur;
            break;
        }
        
        
    }

    return ucfirst($plur_word);

}

