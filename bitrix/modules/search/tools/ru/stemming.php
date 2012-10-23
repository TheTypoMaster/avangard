<?php
global $STEMMING_RU_VOWELS;
$STEMMING_RU_VOWELS = "ÀÅÈÎÓÛÝÞß";
global $STEMMING_RU_PERFECTIVE_GERUND;
$STEMMING_RU_PERFECTIVE_GERUND = "/(ÛÂØÈÑÜ|ÈÂØÈÑÜ|ßÂØÈÑÜ|ÀÂØÈÑÜ|ÛÂØÈ|ÈÂØÈ|ßÂØÈ|ÀÂØÈ|ÛÂ|ÈÂ|ßÂ|ÀÂ)$/".BX_UTF_PCRE_MODIFIER;
global $STEMMING_RU_REFLEXIVE;
$STEMMING_RU_REFLEXIVE=array("Ñß", "ÑÜ");
global $STEMMING_RU_ADJECTIVE;
$STEMMING_RU_ADJECTIVE=array("ÅÅ", "ÈÅ", "ÛÅ", "ÎÅ", "ÈÌÈ", "ÛÌÈ", "ÅÉ", "ÈÉ", "ÛÉ", "ÎÉ", "ÅÌ", "ÈÌ", "ÛÌ", "ÎÌ", "ÅÃÎ", "ÎÃÎ", "ÅÌÓ", "ÎÌÓ", "ÈÕ", "ÛÕ", "ÓÞ", "ÞÞ", "Àß", "ßß", "ÎÞ", "ÅÞ");
global $STEMMING_RU_PARTICIPLE_GR1;
$STEMMING_RU_PARTICIPLE_GR1=array("ÅÌ", "ÍÍ", "ÂØ", "ÞÙ", "Ù");
global $STEMMING_RU_PARTICIPLE_GR2;
$STEMMING_RU_PARTICIPLE_GR2=array("ÈÂØ", "ÛÂØ", "ÓÞÙ");
global $STEMMING_RU_ADJECTIVAL_GR1;
$STEMMING_RU_ADJECTIVAL_GR1=array();
global $STEMMING_RU_ADJECTIVAL_GR2;
$STEMMING_RU_ADJECTIVAL_GR2=array();
foreach($STEMMING_RU_ADJECTIVE as $i)
{
	foreach($STEMMING_RU_PARTICIPLE_GR1 as $j) $STEMMING_RU_ADJECTIVAL_GR1[]=$j.$i;
	foreach($STEMMING_RU_PARTICIPLE_GR2 as $j) $STEMMING_RU_ADJECTIVAL_GR2[]=$j.$i;
}
global $STEMMING_RU_ADJECTIVAL1;
usort($STEMMING_RU_ADJECTIVAL_GR1, "stemming_ru_sort");
$STEMMING_RU_ADJECTIVAL1="/([Àß])(".implode("|", $STEMMING_RU_ADJECTIVAL_GR1).")$/".BX_UTF_PCRE_MODIFIER;

global $STEMMING_RU_ADJECTIVAL2;
foreach($STEMMING_RU_ADJECTIVE as $i)
	$STEMMING_RU_ADJECTIVAL_GR2[]=$i;
usort($STEMMING_RU_ADJECTIVAL_GR2, "stemming_ru_sort");
$STEMMING_RU_ADJECTIVAL2="/(".implode("|", $STEMMING_RU_ADJECTIVAL_GR2).")$/".BX_UTF_PCRE_MODIFIER;

global $STEMMING_RU_VERB1;
$STEMMING_RU_VERB1="/([Àß])(ÍÍÎ|ÅÒÅ|ÉÒÅ|ÅØÜ|ËÀ|ÍÀ|ËÈ|ÅÌ|ËÎ|ÍÎ|ÅÒ|ÞÒ|ÍÛ|ÒÜ|É|Ë|Í)$/".BX_UTF_PCRE_MODIFIER;

global $STEMMING_RU_VERB_GR2;
$STEMMING_RU_VERB_GR2=array("ÈËÀ", "ÛËÀ", "ÅÍÀ", "ÅÉÒÅ", "ÓÉÒÅ", "ÈÒÅ", "ÈËÈ", "ÛËÈ", "ÅÉ", "ÓÉ", "ÈË", "ÛË", "ÈÌ", "ÛÌ", "ÅÍ", "ÈËÎ", "ÛËÎ", "ÅÍÎ", "ßÒ", "ÓÅÒ", "ÓÞÒ", "ÈÒ", "ÛÒ", "ÅÍÛ", "ÈÒÜ", "ÛÒÜ", "ÈØÜ", "ÓÞ", "Þ");
usort($STEMMING_RU_VERB_GR2, "stemming_ru_sort");
global $STEMMING_RU_VERB2;
$STEMMING_RU_VERB2="/(".implode("|", $STEMMING_RU_VERB_GR2).")$/".BX_UTF_PCRE_MODIFIER;
global $STEMMING_RU_NOUN;
$STEMMING_RU_NOUN="/(ÈßÌÈ|ÈßÕ|ÈÅÌ|ÈßÌ|ÀÌÈ|ßÌÈ|Üß|Èß|ÜÞ|ÈÞ|ßÕ|ÀÕ|ÎÌ|ÀÌ|ÅÌ|ßÌ|ÈÉ|ÎÉ|ÅÉ|ÈÅÉ|ÈÈ|ÅÈ|ÜÅ|ÈÅ|ÎÂ|ÅÂ|Þ|Ü|Û|Ó|Î|É|È|Å|ß|À)$/".BX_UTF_PCRE_MODIFIER;
function stemming_letter_ru()
{
	return "¸éöóêåíãøùçõúôûâàïðîëäæýÿ÷ñìèòüáþ¨ÉÖÓÊÅÍÃØÙÇÕÚÔÛÂÀÏÐÎËÄÆÝß×ÑÌÈÒÜÁÞ";
}
function stemming_ru_sort($a, $b)
{
	$al = strlen($a);
	$bl = strlen($b);
	if($al == $bl)
		return 0;
	elseif($al < $bl)
		return 1;
	else
		return -1;
}
function stemming_stop_ru($sWord)
{
	if(strlen($sWord) < 2)
		return false;
	static $stop_list = false;
	if(!$stop_list)
	{
		$stop_list = array (
			"QUOTE"=>0,"HTTP"=>0,"WWW"=>0,"RU"=>0,"IMG"=>0,"GIF"=>0,"ÁÅÇ"=>0,"ÁÛ"=>0,"ÁÛË"=>0,
			"ÁÛÒ"=>0,"ÂÀÌ"=>0,"ÂÀØ"=>0,"ÂÎ"=>0,"ÂÎÒ"=>0,"ÂÑÅ"=>0,"ÂÛ"=>0,"ÃÄÅ"=>0,"ÄÀ"=>0,
			"ÄÀÆ"=>0,"ÄËß"=>0,"ÄÎ"=>0,"ÅÃ"=>0,"ÅÑË"=>0,"ÅÑÒ"=>0,"ÅÙ"=>0,"ÆÅ"=>0,"ÇÀ"=>0,
			"ÈÇ"=>0,"ÈËÈ"=>0,"ÈÌ"=>0,"ÈÕ"=>0,"ÊÀÊ"=>0,"ÊÎÃÄ"=>0,"ÊÒÎ"=>0,"ËÈ"=>0,"ËÈÁ"=>0,
			"ÌÅÍ"=>0,"ÌÍÅ"=>0,"ÌÎ"=>0,"ÌÛ"=>0,"ÍÀ"=>0,"ÍÀÄ"=>0,"ÍÅ"=>0,"ÍÅÒ"=>0,"ÍÈ"=>0,
			"ÍÎ"=>0,"ÍÓ"=>0,"ÎÁ"=>0,"ÎÍ"=>0,"ÎÒ"=>0,"Î×ÅÍ"=>0,"ÏÎ"=>0,"ÏÎÄ"=>0,"ÏÐÈ"=>0,
			"ÏÐÎ"=>0,"ÑÀÌ"=>0,"ÑÅÁ"=>0,"ÑÂÎ"=>0,"ÒÀÊ"=>0,"ÒÀÌ"=>0,"ÒÅÁ"=>0,"ÒÎ"=>0,"ÒÎÆ"=>0,
			"ÒÎËÜÊ"=>0,"ÒÓÒ"=>0,"ÒÛ"=>0,"ÓÆ"=>0,"ÕÎÒ"=>0,"×ÅÃ"=>0,"×ÅÌ"=>0,"×ÒÎ"=>0,"×ÒÎÁ"=>0,
			"ÝÒ"=>0,"ÝÒÎÒ"=>0,
		);
		if(defined("STEMMING_STOP_RU"))
		{
			foreach(explode(",", STEMMING_STOP_RU) as $word)
			{
				$word = trim($word);
				if(strlen($word)>0)
					$stop_list[$word]=0;
			}
		}
	}
	return !array_key_exists($sWord, $stop_list);
}

function stemming_upper_ru($sText)
{
	return str_replace(array("¨"), array("Å"), ToUpper($sText, "ru"));
}

function stemming_ru($word, $flags = 0)
{
	global $STEMMING_RU_VOWELS;
	global $STEMMING_RU_PERFECTIVE_GERUND;
	global $STEMMING_RU_REFLEXIVE;
	global $STEMMING_RU_ADJECTIVE;
	global $STEMMING_RU_PARTICIPLE_GR2;
	global $STEMMING_RU_ADJECTIVAL1;
	global $STEMMING_RU_ADJECTIVAL2;
	global $STEMMING_RU_VERB1;
	global $STEMMING_RU_VERB_GR2;
	global $STEMMING_RU_VERB2;
	global $STEMMING_RU_NOUN;
	//There is a 33rd letter, ¸ (?), but it is rarely used, and we assume it is mapped into å (e).
	$word=str_replace("¨", "Å", $word);
	//Exceptions
	static $STEMMING_RU_EX = array("ÁÅÇÅ"=>true,"ÁÛËÜ"=>true,"ÌÅÍÞ"=>true,"ÃÐÀÍÀÒ"=>true,"ÃÐÀÍÈÒ"=>true,"ÈËÈ"=>true);
	if(isset($STEMMING_RU_EX[$word]))
		return $word;

	//HERE IS AN ATTEMPT TO STEM RUSSIAN SECOND NAMES BEGINS
	//http://www.gramma.ru/SPR/?id=2.8
	if($flags & 1)
	{
		if(preg_match("/(ÎÂ|ÅÂ)$/", $word))
		{
			return array(
				stemming_ru($word."À"),
				stemming_ru($word),
			);
		}
		if(preg_match("/(ÎÂ|ÅÂ)(À|Ó|ÛÌ|Å)$/", $word, $found))
		{
			return array(
				stemming_ru($word),
				stemming_ru(substr($word, 0, -strlen($found[2]))),
			);
		}
	}
	//HERE IS AN ATTEMPT TO STEM RUSSIAN SECOND NAMES ENDS

	//In any word, RV is the region after the first vowel, or the end of the word if it contains no vowel.
	//All tests take place in the the RV part of the word.
	$found=array();
	if(preg_match("/^(.*?[$STEMMING_RU_VOWELS])(.+)$/".BX_UTF_PCRE_MODIFIER, $word, $found))
	{
		$rv = $found[2];
		$word = $found[1];
	}
	else
	{
		return $word;
	}

	//Do each of steps 1, 2, 3 and 4.
	//Step 1: Search for a PERFECTIVE GERUND ending. If one is found remove it, and that is then the end of step 1.


	if(preg_match($STEMMING_RU_PERFECTIVE_GERUND, $rv, $found))
	{
		switch($found[0]) {
			case "ÀÂ":
			case "ÀÂØÈ":
			case "ÀÂØÈÑÜ":
			case "ßÂ":
			case "ßÂØÈ":
			case "ßÂØÈÑÜ":
				$rv = substr($rv, 0, 1-strlen($found[0]));
				break;
			default:
				$rv = substr($rv, 0, -strlen($found[0]));
		}
	}
	//Otherwise try and remove a REFLEXIVE ending, and then search in turn for
	// (1) an ADJECTIVE,
	// (2) a VERB or (3)
	// a NOUN ending.
	// As soon as one of the endings (1) to (3) is found remove it, and terminate step 1.
	else
	{
		$rv = preg_replace("/(Ñß|ÑÜ)$/".BX_UTF_PCRE_MODIFIER, "", $rv);
		//ADJECTIVAL
		if(preg_match($STEMMING_RU_ADJECTIVAL1, $rv, $found))
			$rv = substr($rv, 0, -strlen($found[2]));
		elseif(preg_match($STEMMING_RU_ADJECTIVAL2, $rv, $found))
			$rv = substr($rv, 0, -strlen($found[0]));
		elseif(preg_match($STEMMING_RU_VERB1, $rv, $found))
			$rv = substr($rv, 0, -strlen($found[2]));
		elseif(preg_match($STEMMING_RU_VERB2, $rv, $found))
			$rv = substr($rv, 0, -strlen($found[0]));
		else
			$rv = preg_replace($STEMMING_RU_NOUN, "", $rv);
	}

	//Step 2: If the word ends with è (i), remove it.
	if(substr($rv, -1) == "È")
		$rv = substr($rv, 0, -1);
	//Step 3: Search for a DERIVATIONAL ending in R2 (i.e. the entire ending must lie in R2), and if one is found, remove it.
	//R1 is the region after the first non-vowel following a vowel, or the end of the word if there is no such non-vowel.
	if(preg_match("/(ÎÑÒÜ|ÎÑÒ)$/".BX_UTF_PCRE_MODIFIER, $rv))
	{
		$R1=0;
		$rv_len = strlen($rv);
		while( ($R1<$rv_len) && (strpos($STEMMING_RU_VOWELS, substr($rv,$R1,1))!==false) )
			$R1++;
		if($R1 < $rv_len)
			$R1++;
		//R2 is the region after the first non-vowel following a vowel in R1, or the end of the word if there is no such non-vowel.
		$R2 = $R1;
		while( ($R2<$rv_len) && (strpos($STEMMING_RU_VOWELS, substr($rv,$R2,1))===false) )
			$R2++;
		while( ($R2<$rv_len) && (strpos($STEMMING_RU_VOWELS, substr($rv,$R2,1))!==false) )
			$R2++;
		if($R2 < $rv_len)
			$R2++;
		//"ÎÑÒÜ", "ÎÑÒ"
		if((substr($rv, -4) == "ÎÑÒÜ") && ($rv_len >= ($R2+4)))
			$rv = substr($rv, 0, $rv_len - 4);
		elseif((substr($rv, -3) == "ÎÑÒ") && ($rv_len >= ($R2+3)))
			$rv = substr($rv, 0, $rv_len - 3);
	}
	//Step 4: (1) Undouble í (n), or, (2) if the word ends with a SUPERLATIVE ending, remove it and undouble í (n), or (3) if the word ends ü (') (soft sign) remove it.
	$rv = preg_replace("/(ÅÉØÅ|ÅÉØ)$/".BX_UTF_PCRE_MODIFIER, "", $rv);
	$r = preg_replace("/ÍÍ$/".BX_UTF_PCRE_MODIFIER, "Í", $rv);
	if($r == $rv)
		$rv = preg_replace("/Ü$/".BX_UTF_PCRE_MODIFIER, "", $rv);
	else
		$rv = $r;

	return $word.$rv;
}
?>
