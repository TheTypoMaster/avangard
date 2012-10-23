<?
IncludeModuleLangFile(__FILE__);
/*
CModule::IncludeModule('security');
$flt = new CSecurityFilter;
$s = '';
$a = $flt->_teststr($s);
echo "<pre>",htmlspecialchars(print_r($a, true)),"</pre>";
*/
/*
CModule::IncludeModule('security');
$flt = new CSecurityXSSDetect;
$s=<<<EOT

place JS code here

EOT;
$v = 'and variable here';
$a = $flt->remove_quoted_strings($s);
echo "<pre>",htmlspecialchars(print_r($a, true)),"</pre>";
*/
class CSecurityFilter
{
	var $_tmp_dir = false;
	var $_filter_action = false;
	var $_filter_stop = false;
	var $_filter_log = false;
	var $_found_vars = array();

	var $_whitelist = false;
	var $_filters = false;
	var $_sql_filters = false;
	var $_sql_filters_keys = false;
	var $_sql_filters_values = false;
	var $_php_filters = false;
	var $_php_filters_keys = false;
	var $_php_filters_values = false;
	var $_blocked = false;
	var $quotes = array();

	function __construct($char = false)
	{
		return $this->CSecurityFilter($char);
	}

	function CSecurityFilter($char = false)
	{
		if($char === false)
			$char = " ";
		if(!$this->_tmp_dir)
			$this->_tmp_dir = $_SERVER["DOCUMENT_ROOT"]."/".COption::GetOptionString("main", "upload_dir", "upload");

		$this->_filter_action = COption::GetOptionString("security", "filter_action");
		$this->_filter_stop = COption::GetOptionString("security", "filter_stop");
		$this->_filter_log = COption::GetOptionString("security", "filter_log");

		$_M='(?:[\x09\x0a\x0d\\\\]*)';
		$_M3='(?:[\x09\x0a\x0d\\\\\s]*)';
		$_M2='(?:(?:[\x09\x0a\x0d\\\\\s]|(?:\/\*.*?\*\/))*)';

		$_Jj ="(?:j|(?:\\\\0*[64]a))";
		$_Ja ="(?:a|(?:\\\\0*[64]1))";
		$_Jb ="(?:b|(?:\\\\0*[64]2))";

		$_Jv ="(?:v|(?:\\\\0*[75]6))";
		$_Js ="(?:s|(?:\\\\0*[75]3))";
		$_Jc ="(?:c|(?:\\\\0*[64]3))";
		$_Jr ="(?:r|(?:\\\\0*[75]2))";
		$_Ji ="(?:i|(?:\\\\0*[64]9))";
		$_Jp ="(?:p|(?:\\\\0*[75]0))";
		$_Jt ="(?:t|(?:\\\\0*[75]4))";

		$_Je ="(?:e|(?:\\\\0*[64]5))";
		$_Jx ="(?:x|(?:\\\\0*[75]8))";
		$_Jo ="(?:o|(?:\\\\0*[64]f))";
		$_Jn ="(?:n|(?:\\\\0*[64]e))";

		$_Jm ="(?:m|(?:\\\\0*[64]d))";

		$_Jh ="(?:h|(?:\\\\0*[64]8))";

		$_Jgav ="(?:@|(?:\\\\0*40))";

		$_Jdd="(?:\\:|=|(?:\\\\0*3a)|(?:\\\\0*3d))";
		$_Jss="(?:\\(|(?:\\\\0*28))";

		$_Jvopr="(?:\\?|(?:\\\\0*3f))";
		$_Jgalka="(?:\\<|(?:\\\\0*3c))";

		$_WS_OPT = "[\\x00\\x09\\x0A\\x0B\\x0C\\x0D\\s\\\\]*"; //not modified
		$_WS_OPT2 = "(?:[\\x00\\x09\\x0A\\x0B\\x0C\\x0D\\s\\\\]|(?:\\/\\*.*?\\*\\/)|(?:\\/\\/.*[\\n\\r]))*";

		if(!$this->_whitelist)
		{
			$safe_replacement = md5(mt_rand());
			$this->_whitelist = array(
				//video player insertion
				array(
					'store_match' => '#(<script)(\\s+type="text/javascript"\\s+src="/bitrix/components/bitrix/player/wmvplayer/(silverlight|wmvplayer).js"[\\s/]*></script>)#s',
					'store_replacement' => '<'.$safe_replacement.'\\2',
					'restore_match' => '#<'.$safe_replacement.'#',
					'restore_replacement' => '<script',
				),
				array(
					'store_match' => '#(<script)(\\s+type\\s*=\\s*"text/javascript"\\s*>\\s*new\\s+jeroenwijering\\.Player\\(\\s*document\\.getElementById\\(\\s*"[a-zA-Z0-9_]+"\\s*\\)\\s*,\\s*"/bitrix/components/bitrix/player/wmvplayer/wmvplayer.xaml"\\s*,\\s*{\\s*(?:[a-zA-Z0-9_]+:\\s+"[a-zA-Z0-9/.]*?"[,\\s]*)*}\\);</script>)#s',
					'store_replacement' => '<'.$safe_replacement.'\\2',
					'restore_match' => '#<'.$safe_replacement.'#',
					'restore_replacement' => '<script',
				),
				array(
					'store_match' => '#(BX\\.WindowManager\\.)(\\d+\\.\\d+)#s',
					'store_replacement' => '_b_x_'.$safe_replacement.'\\2',
					'restore_match' => '#_b_x_'.$safe_replacement.'#',
					'restore_replacement' => 'BX.WindowManager.',
				),
				//AJAX part of the component
				array(
					'store_match' => '#sale\.location\.suggest#s',
					'store_replacement' => '_b_x2_'.$safe_replacement,
					'restore_match' => '#_b_x2_'.$safe_replacement.'#',
					'restore_replacement' => 'sale.location.suggest',
				),
				//more will come
			);
		}

		if(!$this->_filters)
		{
			$this->_filters = array(
				0 => array("\\1 * \\2", array(//space is not enought
				"/({$_Jb}{$_M}{$_Je}{$_M}{$_Jh}{$_M})({$_Ja}{$_M}{$_Jv}{$_M}{$_Ji}{$_M}{$_Jo}{$_M}{$_Jr}{$_WS_OPT}{$_Jdd})/is",
				"/({$_Jgav}{$_M}{$_Ji}{$_M}{$_Jm})({$_M}{$_Jp}{$_M}{$_Jo}{$_M}{$_Jr}{$_M}{$_Jt})/",
				"/({$_Jgalka}{$_Jvopr}{$_M}{$_Ji}{$_M})({$_Jm}{$_M}{$_Jp}{$_M}{$_Jo}{$_M}{$_Jr}{$_M}{$_Jt})/is",
				"/({$_Jj}{$_M3}{$_Ja}{$_M3}{$_Jv}{$_M3})({$_Ja}{$_M3}{$_Js}{$_M3}{$_Jc}{$_M3}{$_Jr}{$_M3}{$_Ji}{$_M3}{$_Jp}{$_M3}{$_Jt}{$_M3}{$_Jdd})/is",
				"/({$_Jv}{$_M3}{$_Jb}{$_M3})({$_Js}{$_M3}{$_Jc}{$_M3}{$_Jr}{$_M3}{$_Ji}{$_M3}{$_Jp}{$_M3}{$_Jt}{$_M3}{$_Jdd})/is",
				"/({$_Je}{$_M2}{$_Jx}{$_M2})({$_Jp}{$_M2}{$_Jr}{$_M2}{$_Je}{$_M2}{$_Js}{$_M2}{$_Js}{$_M2}{$_Ji}{$_M2}{$_Jo}{$_M2}{$_Jn}{$_M2}{$_Jss})/is",
				)),
				"" => array("\\1{$char}\\2", array(
				"/(['\\\"]C{$_M})(h{$_M}a{$_M}r{$_M}C{$_M}o{$_M}d{$_M}e{$_M}A{$_M}t['\\\"])/is",
				"/(['\\\"]f{$_M})(r{$_M}o{$_M}m{$_M}C{$_M}h{$_M}a{$_M}r{$_M}C{$_M}o{$_M}d{$_M}e['\\\"])/is",
				"/(['\\\"]t{$_M})(o{$_M}S{$_M}t{$_M}r{$_M}i{$_M}n{$_M}g['\\\"])/is",
				"/(['\\\"]s{$_M})(u{$_M}b{$_M}s{$_M}t{$_M}r['\\\"])/is",
				"/(['\\\"]c{$_M})(h{$_M}a{$_M}r{$_M}A{$_M}t['\\\"])/is",
				"/(['\\\"]w{$_M})(r{$_M}i{$_M}t{$_M}e['\\\"])/is",
				"/(['\\\"]g{$_M})(e{$_M}t{$_M}E{$_M}l{$_M}e{$_M}m{$_M}e{$_M}n{$_M}t{$_M}B{$_M}y{$_M}I{$_M}d['\\\"])/is",
				"/(['\\\"]S{$_M})(u{$_M}b{$_M}m{$_M}i{$_M}t['\\\"])/is",
				"/(['\\\"]r{$_M})(e{$_M}p{$_M}l{$_M}a{$_M}c{$_M}e['\\\"])/is",
				"/(['\\\"]f{$_M})(o{$_M}r{$_M}m{$_M}s['\\\"])/is",
				"/(['\\\"]c{$_M})(r{$_M}e{$_M}a{$_M}t{$_M}e{$_M}E{$_M}l{$_M}e{$_M}m{$_M}e{$_M}n{$_M}t['\\\"])/is",
				"/(['\\\"]R{$_M})(e{$_M}g{$_M}E{$_M}x{$_M}p['\\\"])/is",
				"/(S{$_M})(t{$_M}r{$_M}i{$_M}n{$_M}g{$_WS_OPT2}[\[\(])/is",
				"/(e{$_M})(v{$_M}a{$_M}l{$_WS_OPT2}[\[\(])/is",
				"/(F{$_M})(u{$_M}n{$_M}c{$_M}t{$_M}i{$_M}o{$_M}n{$_WS_OPT2}[\[\(])/is",
				"/(e{$_M})(s{$_M}c{$_M}a{$_M}p{$_M}e{$_WS_OPT2}[\[\(])/is",
				"/(u{$_M})(n{$_M}e{$_M}s{$_M}c{$_M}a{$_M}p{$_M}e{$_WS_OPT2}[\[\(])/is",
				"/(a{$_M})(l{$_M}e{$_M}r{$_M}t{$_WS_OPT2}[\[\(])/is",
				"/(r{$_M})(e{$_M}p{$_M}l{$_M}a{$_M}c{$_M}e{$_WS_OPT2}[\[\(])/is",
				"/(s{$_M})(e{$_M}t{$_M}T{$_M}i{$_M}m{$_M}e{$_M}o{$_M}u{$_M}t{$_WS_OPT2}[\[\(])/is",
				"/(\<{$_M}\!{$_M}D{$_M}O{$_M})(C{$_M}T{$_M}Y{$_M}P{$_M}E)/is",
				"/(\<{$_M}\!{$_M}E{$_M}N{$_M})(T{$_M}I{$_M}T{$_M}Y)/is",
				"/(p{$_M}h{$_M}p{$_M}V{$_M})(a{$_M}r{$_M}s)/is",
				"/(C{$_M}H{$_M}t{$_M}t{$_M})(p{$_M}R{$_M}e{$_M}q{$_M}u{$_M}e{$_M}s{$_M}t)/is",
				"/(j{$_M}s{$_M}A{$_M}j{$_M})(a{$_M}x)/is",
				"/(B{$_M}X)({$_WS_OPT2}[\.\(\[])/s",
				"/(w{$_M}i{$_M}n{$_M}d{$_M}o{$_M}w)({$_WS_OPT2}[\.\(\[])/is",
				"/([\=\(\:\?]{$_WS_OPT2}B{$_M}X\\W)/s",
				"/([\=\(\:\?]{$_WS_OPT2}S{$_M}t{$_M}r{$_M}i{$_M})(n{$_M}g)/s",
				"/([\=\(\:\?]{$_WS_OPT2}e{$_M}v{$_M}a{$_M}l)()/is",
				"/([\=\(\:\?]{$_WS_OPT2}F{$_M}u{$_M}n{$_M}c{$_M})(t{$_M}i{$_M}o{$_M}n)/is",
				"/([\=\(\:\?]{$_WS_OPT2}e{$_M}s{$_M}c{$_M}a{$_M})(p{$_M}e)/is",
				"/([\=\(\:\?]{$_WS_OPT2}u{$_M}n{$_M}e{$_M}s{$_M})(c{$_M}a{$_M}p{$_M}e)/is",
				"/([\=\(\:\?]{$_WS_OPT2}r{$_M}e{$_M}p{$_M}l{$_M})(a{$_M}c{$_M}e)/is",
				"/([\=\(\:\?]{$_WS_OPT2}s{$_M}e{$_M}t{$_M}T{$_M})(i{$_M}m{$_M}e{$_M}o{$_M}u{$_M}t)/is",
				"/([\=\(\:\?]{$_WS_OPT2}w{$_M}i{$_M}n{$_M}d{$_M})(o{$_M}w[^a-z])/is",
				"/([\=\(\:\?]{$_WS_OPT2}d{$_M}o{$_M}c{$_M}u{$_M})(m{$_M}e{$_M}n{$_M}t)(?![a-zA-Z0-9_])/s",
				"/(d{$_M}o{$_M}c{$_M}u{$_M}m{$_M}e{$_M}n{$_M}t)({$_WS_OPT2}[\(\[])/s",
				)),

				"<" => array("\\1{$char}\\2", array(
				"/(\<{$_M}s{$_M}c{$_M})(r{$_M}i{$_M}p{$_M}t)/is",
				"/(\<{$_M}x{$_M}:{$_M}s{$_M}c{$_M})(r{$_M}i{$_M}p{$_M}t)/is",
				"/(\<{$_M}a{$_M}p{$_M}p{$_M})(l{$_M}e{$_M}t)/is",
				"/(\<{$_M}e{$_M}m{$_M}b)(e{$_M}d)/is",
				"/(\<{$_M}s{$_M}t{$_M})(y{$_M}l{$_M}e)/is",
				"/(\<{$_M}f{$_M}r{$_M}a{$_M})(m{$_M}e)/is",
				"/(\<{$_M}i{$_M}f{$_M}r{$_M})(a{$_M}m{$_M}e)/is",
				"/(\<{$_M}f{$_M}o{$_M})(r{$_M}m)/is",
				//"/(\.{$_M}c{$_M}o{$_M})(o{$_M}k{$_M}i{$_M}e)/is",
				"/(\<{$_M}o{$_M}b{$_M})(j{$_M}e{$_M}c{$_M}t)/is",
				"/(\<{$_M}l{$_M}i{$_M})(n{$_M}k)/is",
				"/(\<{$_M}m{$_M}e{$_M}t)({$_M}a)/is",
				"/(\<{$_M}L{$_M}A{$_M}Y{$_M})(E{$_M}R)/is",
				"/(\<{$_M}h{$_M}t{$_M})(m{$_M}l)/is",
				"/(\<{$_M}x{$_M}m{$_M})(l)/is",
				"/(\<{$_M}b{$_M}a{$_M})(s{$_M}e)/is",
				)),

				"=" => array("\\1{$char}\\2", array(
				"/([\W]s{$_M}t{$_M})(y{$_M}l{$_M}e{$_WS_OPT}\=)(?!\\s*\"(\\s*[a-z-]+\\s*:\\s*([0-9a-z\\s%,.#-]+|rgb\\s*\\([0-9,\\s]+\\))\\s*;{0,1}){0,}\\s*\")(?!\\s*&quot;(\\s*[a-z-]+\\s*:\\s*([0-9a-z\\s%,.#-]+|rgb\\s*\\([0-9,\\s]+\\))\\s*;{0,1}){0,}\\s*&quot;)/is",
				"/(f{$_M}o{$_M}r{$_M})(m{$_M}a{$_M}c{$_M}t{$_M}i{$_M}o{$_M}n{$_WS_OPT}\=)/is",

				"/(o{$_M}n{$_M}A{$_M})(b{$_M}o{$_M}r{$_M}t{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}B{$_M})(l{$_M}u{$_M}r{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}C{$_M})(h{$_M}a{$_M}n{$_M}g{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}C{$_M})(l{$_M}i{$_M}c{$_M}k{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}D{$_M})(b{$_M}l{$_M}C{$_M}l{$_M}i{$_M}c{$_M}k{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}E{$_M})(r{$_M}r{$_M}o{$_M}r{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}F{$_M})(o{$_M}c{$_M}u{$_M}s{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}K{$_M})(e{$_M}y{$_M}D{$_M}o{$_M}w{$_M}n{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}K{$_M})(e{$_M}y{$_M}P{$_M}r{$_M}e{$_M}s{$_M}s{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}K{$_M})(e{$_M}y{$_M}U{$_M}p{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}L{$_M})(o{$_M}a{$_M}d{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}M{$_M})(o{$_M}u{$_M}s{$_M}e{$_M}D{$_M}o{$_M}w{$_M}n{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}M{$_M})(o{$_M}u{$_M}s{$_M}e{$_M}M{$_M}o{$_M}v{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}M{$_M})(o{$_M}u{$_M}s{$_M}e{$_M}O{$_M}u{$_M}t{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}M{$_M})(o{$_M}u{$_M}s{$_M}e{$_M}O{$_M}v{$_M}e{$_M}r{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}M{$_M})(o{$_M}u{$_M}s{$_M}e{$_M}U{$_M}p{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}M{$_M})(o{$_M}v{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}R{$_M})(e{$_M}s{$_M}e{$_M}t{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}R{$_M})(e{$_M}s{$_M}i{$_M}z{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}S{$_M})(e{$_M}l{$_M}e{$_M}c{$_M}t{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}S{$_M})(u{$_M}b{$_M}m{$_M}i{$_M}t{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}U{$_M})(n{$_M}l{$_M}o{$_M}a{$_M}d{$_WS_OPT}\=)/is",

				"/(o{$_M}n{$_M}m{$_M}o{$_M})(u{$_M}s{$_M}e{$_M}l{$_M}e{$_M}a{$_M}v{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}m{$_M}o{$_M}u{$_M})(s{$_M}e{$_M}e{$_M}n{$_M}t{$_M}e{$_M}r{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}s{$_M}e{$_M}l{$_M})(e{$_M}c{$_M}t{$_M}s{$_M}t{$_M}a{$_M}r{$_M}t{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}s{$_M}e{$_M}l{$_M})(e{$_M}c{$_M}t{$_M}e{$_M}n{$_M}d{$_WS_OPT}\=)/is",

				"/(o{$_M}n{$_M}a{$_M}f{$_M})(t{$_M}e{$_M}r{$_M}p{$_M}r{$_M}i{$_M}n{$_M}t{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}b{$_M}e{$_M})(f{$_M}o{$_M}r{$_M}e{$_M}p{$_M}r{$_M}i{$_M}n{$_M}t{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}b{$_M}e{$_M})(f{$_M}o{$_M}r{$_M}e{$_M}o{$_M}n{$_M}l{$_M}o{$_M}a{$_M}d{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}h{$_M}a{$_M})(s{$_M}c{$_M}h{$_M}a{$_M}n{$_M}g{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}m{$_M}e{$_M})(s{$_M}s{$_M}a{$_M}g{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}o{$_M}f{$_M})(f{$_M}l{$_M}i{$_M}n{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}o{$_M}n{$_M})(l{$_M}i{$_M}n{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}p{$_M}a{$_M})(g{$_M}e{$_M}h{$_M}i{$_M}d{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}p{$_M}a{$_M})(g{$_M}e{$_M}s{$_M}h{$_M}o{$_M}w{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}p{$_M}o{$_M})(p{$_M}s{$_M}t{$_M}a{$_M}t{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}r{$_M}e{$_M})(d{$_M}o{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}s{$_M}t{$_M})(o{$_M}r{$_M}a{$_M}g{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}u{$_M}n{$_M})(d{$_M}o{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}c{$_M}o{$_M})(n{$_M}t{$_M}e{$_M}x{$_M}t{$_M}m{$_M}e{$_M}n{$_M}u{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}f{$_M}o{$_M})(r{$_M}m{$_M}c{$_M}h{$_M}a{$_M}n{$_M}g{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}f{$_M}o{$_M})(r{$_M}m{$_M}i{$_M}n{$_M}p{$_M}u{$_M}t{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}i{$_M}n{$_M})(p{$_M}u{$_M}t{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}i{$_M}n{$_M})(v{$_M}a{$_M}l{$_M}i{$_M}d{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}d{$_M}r{$_M})(a{$_M}g{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}d{$_M}r{$_M})(a{$_M}g{$_M}e{$_M}n{$_M}d{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}d{$_M}r{$_M})(a{$_M}g{$_M}e{$_M}n{$_M}t{$_M}e{$_M}r{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}d{$_M}r{$_M})(a{$_M}g{$_M}l{$_M}e{$_M}a{$_M}v{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}d{$_M}r{$_M})(a{$_M}g{$_M}o{$_M}v{$_M}e{$_M}r{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}d{$_M}r{$_M})(a{$_M}g{$_M}s{$_M}t{$_M}a{$_M}r{$_M}t{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}d{$_M}r{$_M})(o{$_M}p{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}m{$_M}o{$_M})(u{$_M}s{$_M}e{$_M}w{$_M}h{$_M}e{$_M}e{$_M}l{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}s{$_M}c{$_M})(r{$_M}o{$_M}l{$_M}l{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}c{$_M}a{$_M})(n{$_M}p{$_M}l{$_M}a{$_M}y{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}c{$_M}a{$_M})(n{$_M}p{$_M}l{$_M}a{$_M}y{$_M}t{$_M}h{$_M}r{$_M}o{$_M}u{$_M}g{$_M}h{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}d{$_M}u{$_M})(r{$_M}a{$_M}t{$_M}i{$_M}o{$_M}n{$_M}c{$_M}h{$_M}a{$_M}n{$_M}g{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}e{$_M}m{$_M})(p{$_M}t{$_M}i{$_M}e{$_M}d{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}e{$_M}n{$_M})(d{$_M}e{$_M}d{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}l{$_M}o{$_M})(a{$_M}d{$_M}e{$_M}d{$_M}d{$_M}a{$_M}t{$_M}a{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}l{$_M}o{$_M})(a{$_M}d{$_M}e{$_M}d{$_M}m{$_M}e{$_M}t{$_M}a{$_M}d{$_M}a{$_M}t{$_M}a{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}l{$_M}o{$_M})(a{$_M}d{$_M}s{$_M}t{$_M}a{$_M}r{$_M}t{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}p{$_M}a{$_M})(u{$_M}s{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}p{$_M}l{$_M})(a{$_M}y{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}p{$_M}l{$_M})(a{$_M}y{$_M}i{$_M}n{$_M}g{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}p{$_M}r{$_M})(o{$_M}g{$_M}r{$_M}e{$_M}s{$_M}s{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}r{$_M}a{$_M})(t{$_M}e{$_M}c{$_M}h{$_M}a{$_M}n{$_M}g{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}r{$_M}e{$_M})(a{$_M}d{$_M}y{$_M}s{$_M}t{$_M}a{$_M}t{$_M}e{$_M}c{$_M}h{$_M}a{$_M}n{$_M}g{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}s{$_M}e{$_M})(e{$_M}k{$_M}e{$_M}d{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}s{$_M}e{$_M})(e{$_M}k{$_M}i{$_M}n{$_M}g{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}s{$_M}t{$_M})(a{$_M}l{$_M}l{$_M}e{$_M}d{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}s{$_M}u{$_M})(s{$_M}p{$_M}e{$_M}n{$_M}d{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}t{$_M}i{$_M})(m{$_M}e{$_M}u{$_M}p{$_M}d{$_M}a{$_M}t{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}v{$_M}o{$_M})(l{$_M}u{$_M}m{$_M}e{$_M}c{$_M}h{$_M}a{$_M}n{$_M}g{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}w{$_M}a{$_M})(i{$_M}t{$_M}i{$_M}n{$_M}g{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}t{$_M}i{$_M})(m{$_M}e{$_M}e{$_M}r{$_M}r{$_M}o{$_M}r{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}e{$_M}n{$_M})(d{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}b{$_M}e{$_M})(g{$_M}i{$_M}n{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}m{$_M}e{$_M})(d{$_M}i{$_M}a{$_M}c{$_M}o{$_M}m{$_M}p{$_M}l{$_M}e{$_M}t{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}m{$_M}e{$_M})(d{$_M}i{$_M}a{$_M}l{$_M}o{$_M}a{$_M}d{$_M}f{$_M}a{$_M}i{$_M}l{$_M}e{$_M}d{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}m{$_M}e{$_M})(d{$_M}i{$_M}a{$_M}s{$_M}l{$_M}i{$_M}p{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}r{$_M}e{$_M})(p{$_M}e{$_M}a{$_M}t{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}r{$_M}e{$_M})(s{$_M}u{$_M}m{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}r{$_M}e{$_M})(s{$_M}y{$_M}n{$_M}c{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}r{$_M}e{$_M})(v{$_M}e{$_M}r{$_M}s{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}s{$_M}c{$_M})(r{$_M}i{$_M}p{$_M}t{$_M}c{$_M}o{$_M}m{$_M}m{$_M}a{$_M}n{$_M}d{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}m{$_M}e{$_M})(d{$_M}i{$_M}a{$_M}e{$_M}r{$_M}r{$_M}o{$_M}r{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}o{$_M}u{$_M})(t{$_M}o{$_M}f{$_M}s{$_M}y{$_M}n{$_M}c{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}s{$_M}e{$_M})(e{$_M}k{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}s{$_M}y{$_M})(n{$_M}c{$_M}r{$_M}e{$_M}s{$_M}t{$_M}o{$_M}r{$_M}e{$_M}d{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}t{$_M}r{$_M})(a{$_M}c{$_M}k{$_M}c{$_M}h{$_M}a{$_M}n{$_M}g{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}u{$_M}r{$_M})(l{$_M}f{$_M}l{$_M}i{$_M}p{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}s{$_M}t{$_M})(a{$_M}r{$_M}t{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}a{$_M}c{$_M})(t{$_M}i{$_M}v{$_M}a{$_M}t{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}a{$_M}f{$_M})(t{$_M}e{$_M}r{$_M}u{$_M}p{$_M}d{$_M}a{$_M}t{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}b{$_M}e{$_M})(f{$_M}o{$_M}r{$_M}e{$_M}a{$_M}c{$_M}t{$_M}i{$_M}v{$_M}a{$_M}t{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}b{$_M}e{$_M})(f{$_M}o{$_M}r{$_M}e{$_M}c{$_M}o{$_M}p{$_M}y{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}b{$_M}e{$_M})(f{$_M}o{$_M}r{$_M}e{$_M}c{$_M}u{$_M}t{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}b{$_M}e{$_M})(f{$_M}o{$_M}r{$_M}e{$_M}d{$_M}e{$_M}a{$_M}c{$_M}t{$_M}i{$_M}v{$_M}a{$_M}t{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}b{$_M}e{$_M})(f{$_M}o{$_M}r{$_M}e{$_M}e{$_M}d{$_M}i{$_M}t{$_M}f{$_M}o{$_M}c{$_M}u{$_M}s{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}b{$_M}e{$_M})(f{$_M}o{$_M}r{$_M}e{$_M}p{$_M}a{$_M}s{$_M}t{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}b{$_M}e{$_M})(f{$_M}o{$_M}r{$_M}e{$_M}u{$_M}n{$_M}l{$_M}o{$_M}a{$_M}d{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}b{$_M}e{$_M})(f{$_M}o{$_M}r{$_M}e{$_M}u{$_M}p{$_M}d{$_M}a{$_M}t{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}b{$_M}o{$_M})(u{$_M}n{$_M}c{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}c{$_M}e{$_M})(l{$_M}l{$_M}c{$_M}h{$_M}a{$_M}n{$_M}g{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}c{$_M}o{$_M})(n{$_M}t{$_M}r{$_M}o{$_M}l{$_M}s{$_M}e{$_M}l{$_M}e{$_M}c{$_M}t{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}c{$_M}o{$_M})(p{$_M}y{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}c{$_M}u{$_M})(t{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}d{$_M}a{$_M})(t{$_M}a{$_M}a{$_M}v{$_M}a{$_M}i{$_M}l{$_M}a{$_M}b{$_M}l{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}d{$_M}a{$_M})(t{$_M}a{$_M}s{$_M}e{$_M}t{$_M}c{$_M}h{$_M}a{$_M}n{$_M}g{$_M}e{$_M}d{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}d{$_M}a{$_M})(t{$_M}a{$_M}s{$_M}e{$_M}t{$_M}c{$_M}o{$_M}m{$_M}p{$_M}l{$_M}e{$_M}t{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}d{$_M}e{$_M})(a{$_M}c{$_M}t{$_M}i{$_M}v{$_M}a{$_M}t{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}e{$_M}r{$_M})(r{$_M}o{$_M}r{$_M}u{$_M}p{$_M}d{$_M}a{$_M}t{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}f{$_M}i{$_M})(l{$_M}t{$_M}e{$_M}r{$_M}c{$_M}h{$_M}a{$_M}n{$_M}g{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}f{$_M}i{$_M})(n{$_M}i{$_M}s{$_M}h{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}f{$_M}o{$_M})(c{$_M}u{$_M}s{$_M}i{$_M}n{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}f{$_M}o{$_M})(c{$_M}u{$_M}s{$_M}o{$_M}u{$_M}t{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}h{$_M}a{$_M})(s{$_M}h{$_M}c{$_M}h{$_M}a{$_M}n{$_M}g{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}h{$_M}e{$_M})(l{$_M}p{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}l{$_M}a{$_M})(y{$_M}o{$_M}u{$_M}t{$_M}c{$_M}o{$_M}m{$_M}p{$_M}l{$_M}e{$_M}t{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}l{$_M}o{$_M})(s{$_M}e{$_M}c{$_M}a{$_M}p{$_M}t{$_M}u{$_M}r{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}m{$_M}o{$_M})(v{$_M}e{$_M}e{$_M}n{$_M}d{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}m{$_M}o{$_M})(v{$_M}e{$_M}s{$_M}t{$_M}a{$_M}r{$_M}t{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}m{$_M}s{$_M})(s{$_M}i{$_M}t{$_M}e{$_M}m{$_M}o{$_M}d{$_M}e{$_M}j{$_M}u{$_M}m{$_M}p{$_M}l{$_M}i{$_M}s{$_M}t{$_M}i{$_M}t{$_M}e{$_M}m{$_M}r{$_M}e{$_M}m{$_M}o{$_M}v{$_M}e{$_M}d{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}m{$_M}s{$_M})(t{$_M}h{$_M}u{$_M}m{$_M}b{$_M}n{$_M}a{$_M}i{$_M}l{$_M}c{$_M}l{$_M}i{$_M}c{$_M}k{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}p{$_M}a{$_M})(g{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}p{$_M}a{$_M})(s{$_M}t{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}p{$_M}r{$_M})(o{$_M}p{$_M}e{$_M}r{$_M}t{$_M}y{$_M}c{$_M}h{$_M}a{$_M}n{$_M}g{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}r{$_M}e{$_M})(s{$_M}i{$_M}z{$_M}e{$_M}e{$_M}n{$_M}d{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}r{$_M}e{$_M})(s{$_M}i{$_M}z{$_M}e{$_M}s{$_M}t{$_M}a{$_M}r{$_M}t{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}r{$_M}o{$_M})(w{$_M}e{$_M}n{$_M}t{$_M}e{$_M}r{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}r{$_M}o{$_M})(w{$_M}e{$_M}x{$_M}i{$_M}t{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}r{$_M}o{$_M})(w{$_M}s{$_M}d{$_M}e{$_M}l{$_M}e{$_M}t{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}r{$_M}o{$_M})(w{$_M}s{$_M}i{$_M}n{$_M}s{$_M}e{$_M}r{$_M}t{$_M}e{$_M}d{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}s{$_M}e{$_M})(l{$_M}e{$_M}c{$_M}t{$_M}i{$_M}o{$_M}n{$_M}c{$_M}h{$_M}a{$_M}n{$_M}g{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}s{$_M}t{$_M})(o{$_M}p{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}s{$_M}t{$_M})(o{$_M}r{$_M}a{$_M}g{$_M}e{$_M}c{$_M}o{$_M}m{$_M}m{$_M}i{$_M}t{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}t{$_M}i{$_M})(m{$_M}e{$_M}o{$_M}u{$_M}t{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}r{$_M}e{$_M})(a{$_M}d{$_M}y{$_M}s{$_M}t{$_M}a{$_M}t{$_M}e{$_M}c{$_M}h{$_M}a{$_M}n{$_M}g{$_M}e{$_M}d{$_WS_OPT}\=)/is",

				"/(o{$_M}n{$_M}s{$_M}e{$_M})(a{$_M}r{$_M}c{$_M}h{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}w{$_M}e{$_M})(b{$_M}k{$_M}i{$_M}t{$_M}f{$_M}u{$_M}l{$_M}l{$_M}s{$_M}c{$_M}r{$_M}e{$_M}e{$_M}n{$_M}c{$_M}h{$_M}a{$_M}n{$_M}g{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}z{$_M}o{$_M})(o{$_M}m{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}t{$_M}o{$_M})(u{$_M}c{$_M}h{$_M}s{$_M}t{$_M}a{$_M}r{$_M}t{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}t{$_M}o{$_M})(u{$_M}c{$_M}h{$_M}m{$_M}o{$_M}v{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}t{$_M}o{$_M})(u{$_M}c{$_M}h{$_M}e{$_M}n{$_M}d{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}t{$_M}o{$_M})(u{$_M}c{$_M}h{$_M}c{$_M}a{$_M}n{$_M}c{$_M}e{$_M}l{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}g{$_M}e{$_M})(s{$_M}t{$_M}u{$_M}r{$_M}e{$_M}s{$_M}t{$_M}a{$_M}r{$_M}t{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}g{$_M}e{$_M})(s{$_M}t{$_M}u{$_M}r{$_M}e{$_M}c{$_M}h{$_M}a{$_M}n{$_M}g{$_M}e{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}g{$_M}e{$_M})(s{$_M}t{$_M}u{$_M}r{$_M}e{$_M}e{$_M}n{$_M}d{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}w{$_M}e{$_M})(b{$_M}k{$_M}i{$_M}t{$_M}a{$_M}n{$_M}i{$_M}m{$_M}a{$_M}t{$_M}i{$_M}o{$_M}n{$_M}s{$_M}t{$_M}a{$_M}r{$_M}t{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}w{$_M}e{$_M})(b{$_M}k{$_M}i{$_M}t{$_M}a{$_M}n{$_M}i{$_M}m{$_M}a{$_M}t{$_M}i{$_M}o{$_M}n{$_M}e{$_M}n{$_M}d{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}w{$_M}e{$_M})(b{$_M}k{$_M}i{$_M}t{$_M}a{$_M}n{$_M}i{$_M}m{$_M}a{$_M}t{$_M}i{$_M}o{$_M}n{$_M}i{$_M}t{$_M}e{$_M}r{$_M}a{$_M}t{$_M}i{$_M}o{$_M}n{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}d{$_M}e{$_M})(v{$_M}i{$_M}c{$_M}e{$_M}o{$_M}r{$_M}i{$_M}e{$_M}n{$_M}t{$_M}a{$_M}t{$_M}i{$_M}o{$_M}n{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}w{$_M}e{$_M})(b{$_M}k{$_M}i{$_M}t{$_M}t{$_M}r{$_M}a{$_M}n{$_M}s{$_M}i{$_M}t{$_M}i{$_M}o{$_M}n{$_M}e{$_M}n{$_M}d{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}w{$_M}e{$_M})(b{$_M}k{$_M}i{$_M}t{$_M}b{$_M}e{$_M}g{$_M}i{$_M}n{$_M}f{$_M}u{$_M}l{$_M}l{$_M}s{$_M}c{$_M}r{$_M}e{$_M}e{$_M}n{$_WS_OPT}\=)/is",
				"/(o{$_M}n{$_M}w{$_M}e{$_M})(b{$_M}k{$_M}i{$_M}t{$_M}e{$_M}n{$_M}d{$_M}f{$_M}u{$_M}l{$_M}l{$_M}s{$_M}c{$_M}r{$_M}e{$_M}e{$_M}n{$_WS_OPT}\=)/is",

				)),

				":" => array("\\1{$char}\\2", array(
				"/(u{$_M}r{$_M}n{$_M2}\:{$_M2}s{$_M})(c{$_M}h{$_M}e{$_M}m{$_M}a{$_M}s{$_M}\-{$_M}m{$_M}i{$_M}c{$_M}r{$_M}o{$_M}s{$_M}o{$_M}f{$_M}t{$_M}\-{$_M}c{$_M}o{$_M}m{$_M2}\:)/",
				"/(d{$_M}a{$_M}t{$_M})(a{$_M}\:)/is",
				)),

				"-" => array("\\1{$char}\\2", array(
				"/(\-{$_M}m{$_M}o{$_M}z{$_M}\-{$_M}b{$_M}i{$_M})(n{$_M}d{$_M}i{$_M}n{$_M}g{$_M}{$_WS_OPT}\:{$_WS_OPT}{$_M}u{$_M}r{$_M}l)/is",
				)),

				"(" => array("\\1{$char}\\2", array(
				"/(f{$_M}r{$_M}o{$_M}m)({$_M}c{$_M}h{$_M}a{$_M}r{$_M}c{$_M}o{$_M}d{$_M}e{$_M3}\()/",
				"/(u{$_M}n{$_M}e{$_M})(s{$_M}c{$_M}a{$_M}p{$_M}e{$_M3}\()/",
				)),

				"." => array("\\1{$char}\\2", array(
				"/(\.$_WS_OPT2)(c{$_M}o{$_M}o{$_M}k{$_M}i{$_M}e)/is",
				"/(\.$_WS_OPT2)(l{$_M}o{$_M}c{$_M}a{$_M}t{$_M}i{$_M}o{$_M}n)(?![a-zA-Z0-9])/s",
				"/(\.$_WS_OPT2)(v{$_M}a{$_M}l{$_M}u{$_M}e)/is",
				"/(\.$_WS_OPT2)(s{$_M}o{$_M}u{$_M}r{$_M}c{$_M}e)/is",
				"/(\.$_WS_OPT2)(t{$_M}e{$_M}x{$_M}t{$_WS_OPT2}[\\[\\(\\)])/s",
				"/(\.$_WS_OPT2)(C{$_M}h{$_M}a{$_M}r{$_M}C{$_M}o{$_M}d{$_M}e{$_M}A{$_M}t{$_WS_OPT2}[\(\[]\.)/is",
				"/(\.$_WS_OPT2)(f{$_M}r{$_M}o{$_M}m{$_M}C{$_M}h{$_M}a{$_M}r{$_M}C{$_M}o{$_M}d{$_M}e{$_WS_OPT2}[\(\[]\.)/is",
				"/(\.$_WS_OPT2)(t{$_M}o{$_M}S{$_M}t{$_M}r{$_M}i{$_M}n{$_M}g{$_WS_OPT2}[\(\[]\.)/is",
				"/(\.$_WS_OPT2)(s{$_M}u{$_M}b{$_M}s{$_M}t{$_M}r{$_WS_OPT2}[\(\[]\.)/is",
				"/(\.$_WS_OPT2)(c{$_M}h{$_M}a{$_M}r{$_M}A{$_M}t{$_WS_OPT2}[\(\[]\.)/is",
				"/(\.$_WS_OPT2)(w{$_M}r{$_M}i{$_M}t{$_M}e{$_WS_OPT2}[\(\[]\.)/is",
				"/(\.$_WS_OPT2)(g{$_M}e{$_M}t{$_M}E{$_M}l{$_M}e{$_M}m{$_M}e{$_M}n{$_M}t{$_M}B{$_M}y{$_M}I{$_M}d{$_WS_OPT2}[\(\[]\.)/is",
				"/(\.$_WS_OPT2)(S{$_M}u{$_M}b{$_M}m{$_M}i{$_M}t{$_WS_OPT2}[\(\[]\.)/is",
				"/(\.$_WS_OPT2)(r{$_M}e{$_M}p{$_M}l{$_M}a{$_M}c{$_M}e{$_WS_OPT2}[\(\[]\.)/is",
				"/(\.$_WS_OPT2)(f{$_M}o{$_M}r{$_M}m{$_M}s{$_WS_OPT2}[\(\[]\.)/is",
				"/(\.$_WS_OPT2)(c{$_M}r{$_M}e{$_M}a{$_M}t{$_M}e{$_M}E{$_M}l{$_M}e{$_M}m{$_M}e{$_M}n{$_M}t{$_WS_OPT2}[\(\[]\.)/is",
				"/(\.$_WS_OPT2)(R{$_M}e{$_M}g{$_M}E{$_M}x{$_M}p{$_WS_OPT2}[\(\[]\.)/is",
				))

			);
		}

		$sql_space = "(?:[\\x00-\\x20\(\)\'\"\`*@\+\-\.~\\\efd!\d]|(?:\\/\\*.*?\\*\\/)|(?:--.*?[\\n\\r])|(?:\\/\\*!\d*)|(?:\\*\\/))+";
		$sql_functions_space="[\\x00-\\x20]*";

		if(!$this->_sql_filters)
		{
			global $DBType;
			$this->_sql_filters = array(
				"/(uni)(on{$sql_space}.+{$sql_space}sel)(ect)/is" => "\\1{$char}\\2{$char}\\3",
				"/(uni)(on{$sql_space}sel)(ect)/is" => "\\1{$char}\\2{$char}\\3",

				"/(sel)(ect{$sql_space}.+{$sql_space}fr)(om)/is" => "\\1{$char}\\2{$char}\\3",
				"/(sel)(ect{$sql_space}fr)(om)/is" => "\\1{$char}\\2{$char}\\3",
				"/(fr)(om{$sql_space}.+{$sql_space}wh)(ere)/is" => "\\1{$char}\\2{$char}\\3",

				"/(alt)(er)({$sql_space})(database|table|function|procedure|server|event|view|index)/is" => "\\1{$char}\\2\\3\\4",
				"/(cre)(ate)({$sql_space})(database|table|function|procedure|server|event|view|index)/is" => "\\1{$char}\\2\\3\\4",
				"/(dr)(op)({$sql_space})(database|table|function|procedure|server|event|view|index)/is" => "\\1{$char}\\2\\3\\4",

				"/(upd)(ate{$sql_space}.+{$sql_space}se)(t)/is" => "\\1{$char}\\2{$char}\\3",
				"/(ins)(ert{$sql_space}.+{$sql_space}val)(ue)/is" => "\\1{$char}\\2{$char}\\3",
				"/(ins)(ert{$sql_space}.+{$sql_space}se)(t)/is" => "\\1{$char}\\2{$char}\\3",
				"/(i)(nto{$sql_space}out)(file)/is" => "\\1{$char}\\2{$char}\\3",
				"/(i)(nto{$sql_space}dump)(file)/is" => "\\1{$char}\\2{$char}\\3",

				"/(ins)(ert{$sql_space}.+{$sql_space}sele)(ct)/is" => "\\1{$char}\\2{$char}\\3",
				"/(ins)(ert{$sql_space}in)(to)/is" => "\\1{$char}\\2{$char}\\3",
				"/(ins)(ert{$sql_space}.+{$sql_space}in)(to)/is" => "\\1{$char}\\2{$char}\\3",

				"/(load_)(file{$sql_functions_space}\()/is" => "\\1{$char}\\2",

				"/(fr)(om.+lim)(it)/is" => "\\1{$char}\\2{$char}\\3",

				"/(ben)(chmark{$sql_functions_space}\()/is" => "\\1{$char}\\2",
				"/(sl)(eep{$sql_functions_space}\()/is" => "\\1{$char}\\2",
				"/(us)(er{$sql_functions_space}\()/is" => "\\1{$char}\\2",
				"/(ver)(sion{$sql_functions_space}\()/is" => "\\1{$char}\\2",
				"/(dat)(abase{$sql_functions_space}\()/is" => "\\1{$char}\\2",
				"/(sche)(ma{$sql_functions_space}\()/is" => "\\1{$char}\\2",
				"/(sub)(string{$sql_functions_space}\()/is" => "\\1{$char}\\2",
			);

			$dbt = strtolower($DBType);
			if($dbt === 'mssql')
			{
				$this->_sql_filters += array(
					"/({$sql_space}[sx]p)(_\w+{$sql_functions_space}[\(\[])/" => "\\1{$char}\\2",
					"/(ex)(ec{$sql_functions_space}\()/is"=>"\\1{$char}\\2",
					"/(ex)(ecute{$sql_functions_space}\()/is"=>"\\1{$char}\\2",
					"/([\\x00-\\x20;]ex)(ec.+[sx]p)(_\w+)/is" => "\\1{$char}\\2{$char}\\3",
				);
			}
			elseif($dbt === 'oracle')
			{
				$this->_sql_filters += array(
					"/(ex)(ecute{$sql_space}.+{$sql_space}imme)(diate)/is" => "\\1{$char}\\2{$char}\\3",
					"/(ex)(ecute{$sql_space}imme)(diate)/is" => "\\1{$char}\\2{$char}\\3",
				);
			}
		}

		$this->_sql_filters_keys = array_keys($this->_sql_filters);
		$this->_sql_filters_values = array_values($this->_sql_filters);

		if(!$this->_php_filters)
		{
			$this->_php_filters = array();
			$this->_php_filters["/(\\.)(\\.[\\\\\/])/is"] = "\\1{$char}\\2"; //directory up, ../
			if(
				(!defined("PHP_OS"))
				|| (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
			)
				$this->_php_filters["/[\\.\\/\\\\\\x20\\x22\\x3c\\x3e\\x5c]{30,}/"] = " X ";
			else
				$this->_php_filters["/[\\.\\/\\\\]{30,}/"] = " X ";
		}

		$this->_php_filters_keys = array_keys($this->_php_filters);
		$this->_php_filters_values = array_values($this->_php_filters);

	}

	/*
	Function is used in regular expressions in order to decode characters presented as &#123;
	*/
	function _decode_cb($in)
	{
		$ad = $in[2];
		if($ad == ';')
			$ad="";
		$num = intval($in[1]);
		return chr($num).$ad;
	}

	/*
	Function is used in regular expressions in order to decode characters presented as  &#xAB;
	*/
	function _decode_cb_hex($in)
	{
		$ad = $in[2];
		if($ad==';')
			$ad="";
		$num = intval(hexdec($in[1]));
		return chr($num).$ad;
	}

	/*
	Decodes string from html codes &#***;
	One pass!
	-- Decode only a-zA-Z:().=, because only theese are used in filters
	*/
	function _decode($str)
	{
		$str= preg_replace_callback("/\&\#(\d+)([^\d])/is", array("CSecurityFilter", "_decode_cb"), $str);
		return preg_replace_callback("/\&\#x([\da-f]+)([^\da-f])/is", array("CSecurityFilter", "_decode_cb_hex"), $str);
	}

	function _log($str, $var_name, $type)
	{
		$this->_found_vars[$var_name] = $str;
		CEventLog::Log("SECURITY", "SECURITY_FILTER_".$type, "security", $var_name, "==".base64_encode($str));
	}

	function _block($ip)
	{
		static $blocked = array();

		if($this->_usercanexcept())
			return;

		if(!array_key_exists($ip, $blocked))
		{
			$rule = new CSecurityIPRule;

			CTimeZone::Disable();
			$rule->Add(array(
				"RULE_TYPE" => "A",
				"ACTIVE" => "Y",
				"ADMIN_SECTION" => "Y",
				"NAME" => GetMessage("SECURITY_FILTER_IP_RULE", array("#IP#" => $ip)),
				"ACTIVE_FROM" => ConvertTimeStamp(false, "FULL"),
				"ACTIVE_TO" => ConvertTimeStamp(time()+COption::GetOptionInt("security", "filter_duration")*60, "FULL"),
				"INCL_IPS" => array($ip),
				"INCL_MASKS" => array("*"),
			));
			CTimeZone::Enable();

			$blocked[$ip] = true;
			$this->_blocked = true;
		}
	}

	/*
	PHP Injection detection
	*/
	function _dostrphp(&$str, $var_name)
	{
		if($this->_filter_action === "clear" || $this->_filter_action === "none")
		{
			foreach($this->_php_filters_keys as $flt)
			{
				if(preg_match($flt, $str))
				{
					if($this->_filter_stop === "Y")
						$this->_block($_SERVER["REMOTE_ADDR"]);
					if($this->_filter_log === "Y")
						$this->_log($str, $var_name, "PHP");
					if($this->_filter_action === "clear")
						$str = "";
					return 1;
				}
			}
		}
		else
		{
			$str2 = preg_replace($this->_php_filters_keys, $this->_php_filters_values, $str);
			if($str2 <> $str)
			{
				if($this->_filter_stop === "Y")
					$this->_block($_SERVER["REMOTE_ADDR"]);
				if($this->_filter_log === "Y")
					$this->_log($str, $var_name, "PHP");
				$str = $str2;
				return 1;
			}
		}
		return 0;
	}

	/*
	SQL Injection detection
	*/
	function _dostrsql(&$str, $var_name)
	{
		if($this->_filter_action === "clear" || $this->_filter_action === "none")
		{
			foreach($this->_sql_filters_keys as $flt)
			{
				if(preg_match($flt, $str))
				{
					if($this->_filter_stop === "Y")
						$this->_block($_SERVER["REMOTE_ADDR"]);
					if($this->_filter_log === "Y")
						$this->_log($str, $var_name, "SQL");
					if($this->_filter_action === "clear")
						$str = "";
					return 1;
				}
			}
		}
		else
		{
			$str2 = "";
			$strX = $str;
			while($str2 <> $strX)
			{
				$str2 = $strX;
				$strX = preg_replace($this->_sql_filters_keys, $this->_sql_filters_values, $str2);
			}

			if($str2 <> $str)
			{
				if($this->_filter_stop === "Y")
					$this->_block($_SERVER["REMOTE_ADDR"]);
				if($this->_filter_log === "Y")
					$this->_log($str, $var_name, "SQL");
				$str = $str2;
				$this->_dostrphp($str, $var_name);
				return 1;
			}
		}
		return $this->_dostrphp($str, $var_name);
	}

	function _teststr($str)
	{
		$arResult = array(
			"INPUT" => $str,
		);

		if(preg_match("/^[A-Za-z0-9_.,-]*$/", $str))
		{
			$arResult["NOT_FOUND"] = 1;
			return $arResult;
		}

		if(
			preg_match("/[(){}\\[\\]=+&%]/", $str)
			&& !preg_match("/^[a-zA-Z0-9_\\/?=&-]*$/", $str)
		)
		{

			//NEW PRE filters
			//$RETURN = 0;
			//$strNEW = $this->_prefilter($str, true);
			//if($strNEW <> $str)
			//	$arResult["PREFILTER"] = $strNEW;

			/*white list start*/
			$str1="";
			$strY=$str;
			while($str1 <> $strY)
			{
				$str1 = $strY;
				foreach($this->_whitelist as $arWhiteListElement)
					$strY = preg_replace($arWhiteListElement["store_match"], $arWhiteListElement["store_replacement"], $strY);
			}
			$str1="";
			/*white list end*/

			while($str1 <> $strY)
			{
				$str1 = $strY;
				$strY = $this->_decode($strY);
				$strY = str_replace("\x00", "", $strY);
				$strY = preg_replace("/\&\#0+(;|([^\d;]))/is", "\\2", $strY);
				$strY = preg_replace("/\&\#x0+(;|([^\da-f;]))/is", "\\2", $strY);
			}

			$arResult["DECODE"] = $str1;

			foreach($this->_filters as $ch => $filters)
			{
				if($ch === '' || $ch === 0 || strpos($str1, $ch) !== false)
				{
					foreach($filters[1] as $flt)
					{
						if(preg_match($flt, $str1, $match))
						{
							$arResult["XSS_FOUND"] = array(
								"INDEX" => $i,
								"FILTER" => $flt,
								"MATCH" => $match,
							);
							return $arResult;
						}
					}
				}
			}

			/*white list start*/
			foreach($this->_whitelist as $arWhiteListElement)
				$str1 = preg_replace($arWhiteListElement["restore_match"], $arWhiteListElement["restore_replacement"], $str1);
			$arResult["WHITELIST_RESTORED"] = $str1;
			/*white list end*/
		}

		foreach($this->_sql_filters_keys as $flt)
		{
			if(preg_match($flt, $str1, $match))
			{
				$arResult["SQL_FOUND"] = array(
					"FILTER" => $flt,
					"MATCH" => $match,
				);
				return $arResult;
			}
		}

		foreach($this->_php_filters_keys as $flt)
		{
			if(preg_match($flt, $str1, $match))
			{
				$arResult["PHP_FOUND"] = array(
					"FILTER" => $flt,
					"MATCH" => $match,
				);
				return $arResult;
			}
		}

		$arResult["NOT_FOUND"] = true;
		return $arResult;
	}


	function _prefilter($str)
	{
		$str = preg_replace("/(\\\\[xub]?)([0-9a-f]{2,})/i", "\\1 \\2", $str);
		$str = preg_replace("/(\\&x?)([0-9a-f]{2,};)/i", "\\1 \\2", $str);

		$char="(){}[]:+-.'\"\$_\\<>";

		$strs = str_split($str, 30);
		$stro = "";
		foreach($strs as $s)
		{
			$l = strlen($s);
			$c = 0;
			for($i = 0; $i < $l; $i++)
			{
				if(strstr($char, $s[$i]))
					$c++;
			}

			if($c > 6)
				$s = preg_replace("/([".preg_quote($char, "/")."])/", "\\1 ", $s);

			$stro .= $s;
		}

		return $stro;
	}


	/*
	XSS injection detection.
	Also calls SQL injection detection function.
	*/
	function _dostr(&$str, $var_name)
	{
		if(preg_match("/^[A-Za-z0-9_.,-]*$/", $str))
			return 0;

		if(!preg_match("/[(){}\\[\\]=+&%<>]/", $str))
			return $this->_dostrsql($str, $var_name);

		if(preg_match("/^[a-zA-Z0-9_\\/?=&-]*$/", $str))
			return $this->_dostrsql($str, $var_name);

		//NEW PRE filters
		$RETURN = 0;
/*
		$strNEW = $this->_prefilter($str);
		if($strNEW <> $str)
		{
			if($this->_filter_action === "clear" || $this->_filter_action === "none")
			{
				if($this->_filter_stop === "Y")
					$this->_block($_SERVER["REMOTE_ADDR"]);
				if($this->_filter_log === "Y")
					$this->_log($str, $var_name, "XSS");
				if($this->_filter_action === "clear")
					$str = "";

			}
			else
			{
				$str=$strNEW;
				if($this->_filter_stop === "Y")
					$this->_block($_SERVER["REMOTE_ADDR"]);
				if($this->_filter_log === "Y")
					$this->_log($str, $var_name, "XSS");
			}
			$RETURN = 1;
		}
*/
		/*white list start*/
		$str1="";
		$strY=$str;
		while($str1 <> $strY)
		{
			$str1 = $strY;
			foreach($this->_whitelist as $arWhiteListElement)
				$strY = preg_replace($arWhiteListElement["store_match"], $arWhiteListElement["store_replacement"], $strY);
		}
		$str1="";
		/*white list end*/

		while($str1 <> $strY)
		{
			$str1 = $strY;
			$strY = $this->_decode($strY);
			$strY = str_replace("\x00", "", $strY);
			$strY = preg_replace("/\&\#0+(;|([^\d;]))/is", "\\2", $strY);
			$strY = preg_replace("/\&\#x0+(;|([^\da-f;]))/is", "\\2", $strY);
		}

		if($this->_filter_action === "clear" || $this->_filter_action === "none")
		{
			foreach($this->_filters as $ch => $filters)
			{
				if($ch === '' || $ch === 0 || strpos($str1, $ch) !== false)
				{
					foreach($filters[1] as $flt)
					{
						if(preg_match($flt, $str1))
						{
							if($this->_filter_stop === "Y")
								$this->_block($_SERVER["REMOTE_ADDR"]);
							if($this->_filter_log === "Y")
								$this->_log($str, $var_name, "XSS");
							if($this->_filter_action === "clear")
								$str = "";
							return 1;
						}
					}
				}
			}
		}
		else
		{
			$str2 = "";
			$strX = $str1;
			while($str2 <> $strX)
			{
				foreach($this->_filters as $ch => $filters)
				{
					if($ch === '' || $ch === 0 || strpos($strX, $ch) !== false)
					{
						$str2 = $strX;
						$strX = preg_replace($filters[1], $filters[0], $str2);
					}
				}
			}

			if($str2 <> $str1)
			{
				if($this->_filter_stop === "Y")
					$this->_block($_SERVER["REMOTE_ADDR"]);
				if($this->_filter_log === "Y")
					$this->_log($str, $var_name, "XSS");

				/*white list start*/
				foreach($this->_whitelist as $arWhiteListElement)
					$str2 = preg_replace($arWhiteListElement["restore_match"], $arWhiteListElement["restore_replacement"], $str2);
				/*white list end*/

				$str = $str2;
				$this->_dostrsql($str, $var_name);
				return 1;
			}
		}


		return $this->_dostrsql($str, $var_name) || $RETURN ;
	}

	function TestXSS($str, $action = 'clear') /*'replace'*/
	{
		$str1="";
		$strY=$str;
		while($str1 <> $strY)
		{
			$str1 = $strY;
			$strY = $this->_decode($strY);
			$strY = str_replace("\x00", "", $strY);
			$strY = preg_replace("/\&\#0+(;|([^\d;]))/is", "\\2", $strY);
			$strY = preg_replace("/\&\#x0+(;|([^\da-f;]))/is", "\\2", $strY);
		}

		if($action === "replace")
		{
			$str2 = "";
			$strX = $str1;
			while($str2 <> $strX)
			{
				foreach($this->_filters as $ch => $filters)
				{
					if($ch === '' || $ch === 0 || strpos($strX, $ch) !== false)
					{
						$str2 = $strX;
						$strX = preg_replace($filters[1], $filters[0], $str2);
					}
				}
			}

			if($str2 <> $str1)
				return $str2;
		}
		else
		{
			foreach($this->_filters as $ch => $filters)
			{
				if($ch === '' || $ch === 0 || strpos($str1, $ch) !== false)
				{
					foreach($filters[1] as $flt)
						if(preg_match($flt, $str1))
							return "";
				}
			}
		}

		return $str;
	}

	/*
	Calls detection function on array keys and values
	*/
	function _doarray(&$ar, $var_name)
	{
		$ret=0;
		if(!is_array($ar)) return;
		foreach($ar as $k=>$v)
		{
			if(is_array($v))
			{
				$k1=$k;
				$ret+=$this->_dostr($k1, $var_name.'["'.$k1.'"]');

				if($k<>$k1)
				{
					unset($ar[$k]);
					$ar[$k1]=$v;
				}
				$ret+=$this->_doarray($ar[$k1], $var_name.'["'.$k1.'"]');
			}
			else
			{
				$k1=$k;
				$ret+=$this->_dostr($k1, $var_name.'["'.$k1.'"]');

				if($k<>$k1)
				{
					unset($ar[$k]);
					$ar[$k1]=$v;
				}
				$ret+=$this->_dostr($ar[$k1], $var_name.'["'.$k1.'"]');
			}
		}
		return $ret;
	}

	function _fixtmpnames(&$ar)
	{
		if(is_array($ar))
		{
			foreach($ar as $k=>$v)
			{
				$this->_fixtmpnames($ar[$k]);
			}
		}
		else
		{
			$ar=preg_replace("/[^a-z0-9]/i", "", $ar);
			$dir=$this->_tmp_dir;
			$ar=$dir."/MYFILTR_".$ar;
		}
	}

	function _fixsize(&$tmpname, &$size, &$error)
	{
		if(is_array($tmpname))
		{
			foreach($tmpname as $k=>$v)
			{
				$this->_fixsize($tmpname[$k], $size[$k], $error[$k]);
			}
		}
		else
		{
			if(file_exists($tmpname))
			{
				$size=filesize($tmpname);
				$error=0;
			}
		}
	}

	function _initfiles()
	{
		if(is_array($_POST['__SECFILTER_FILES']) && sizeof($_POST['__SECFILTER_FILES'] >0))
		{
			foreach($_POST['__SECFILTER_FILES'] as $k=>$v)
			{
				$nk=$k;
				$this->_fixtmpnames($_POST['__SECFILTER_FILES'][$k]['tmp_name']);
				$this->_fixsize($_POST['__SECFILTER_FILES'][$k]['tmp_name'], $_POST['__SECFILTER_FILES'][$k]['size'], $_POST['__SECFILTER_FILES'][$k]['error']);
				$_FILES=$_POST['__SECFILTER_FILES'];
			}
		}

		unset($_POST['__SECFILTER_FILES']);
	}

	function _returnfilesar($fname, $index, $tmpname, $name, $type)
	{
		$dir=$this->_tmp_dir;

		if(!is_array($tmpname))
		{
			$newtmpname=md5(uniqid(rand(), true));

			$ret="";

			if(move_uploaded_file($tmpname, $dir."/MYFILTR_".$newtmpname))
			{
				$ret.="
					<input type=hidden name=\"__SECFILTER_FILES[".htmlspecialchars($fname)."][tmp_name]".htmlspecialchars($index)."\" value=\"".htmlspecialchars($newtmpname)."\">
					<input type=hidden name=\"__SECFILTER_FILES[".htmlspecialchars($fname)."][name]".htmlspecialchars($index)."\" value=\"".htmlspecialchars($name)."\">
					<input type=hidden name=\"__SECFILTER_FILES[".htmlspecialchars($fname)."][type]".htmlspecialchars($index)."\" value=\"".htmlspecialchars($type)."\">
				";
			}

		}
		else
		{
			foreach($tmpname as $k=>$v)
			{
				$ret.=$this->_returnfilesar($fname, $index."[$k]", $tmpname[$k], $name[$k], $type[$k]);
			}
		}
		return $ret;
	}

	function _returnfiles()
	{
		global $_UNSECURE;

		$ret="";

		if(is_array($_UNSECURE['_FILES']) && sizeof($_UNSECURE[_FILES])>0)
		{

			foreach($_UNSECURE['_FILES'] as $k=>$v)
			{
				$ret.=$this->_returnfilesar($k, "", $v['tmp_name'], $v['name'], $v['type']);
			}
		}
		return $ret;
	}

	/*
	Show hidden in order to "repost"
	*/
	function _returnhiddens($ar, $prefix)
	{
		$ret="";
		foreach($ar as $k=>$v)
		{
			if(is_array($v))
			{
				if(empty($prefix))
				{
					$ret.=$this->_returnhiddens($v, htmlspecialchars($k));
				}
				else
				{
					$ret.=$this->_returnhiddens($v, $prefix."[".htmlspecialchars($k)."]");
				}
			}
			else
			{
				if(empty($prefix))
				{
					$ret.="<input type=hidden name=\"".htmlspecialchars($k)."\" value=\"".htmlspecialchars($v)."\">\r\n";
				}
				else
				{
					$ret.="<input type=hidden name=\"{$prefix}[".htmlspecialchars($k)."]\" value=\"".htmlspecialchars($v)."\">\r\n";
				}
			}
		}

		return $ret;
	}

	/*
	Returns 1 for users who can submit dangerous code
	*/
	function _usercanexcept()
	{
		global $USER;
		return $USER->CanDoOperation('security_filter_bypass');
	}

	function _cleartmpfiles()
	{
		if (is_dir($this->_tmp_dir))
		{
			if ($dh = opendir($this->_tmp_dir))
			{
				while (($file = readdir($dh)) !== false)
				{
					if(preg_match("/^MYFILTR_/", $file) && filemtime($this->_tmp_dir."/".$file)<=time()-86400  )
					{
						@unlink($this->_tmp_dir."/".$file);
					}
				}
				closedir($dh);
			}
		}
	}

	/*
	Main filtering loop
	also sets up global vars
	GET POST COOKIE and some $_SERVER keys
	*/
	function _do()
	{
		global $HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_COOKIE_VARS, $HTTP_REQUEST_VARS, $_UNSECURE;

		$this->_cleartmpfiles();

		if(CSecurityFilterMask::Check(SITE_ID, $_SERVER["REQUEST_URI"]))
			return 1;

		$noprocess = array(
			"_GET" => 1,
			"_POST" => 1,
			"_SERVER" => 1,
			"_ENV" => 1,
			"_COOKIE" => 1,
			"_SERVER" => 1,
			"_POST" => 1,
			"_FILES" => 1,
			"_REQUEST" => 1,
			"_SESSION" => 1,
			"GLOBALS" => 1,
			"HTTP_GET_VARS" => 1,
			"HTTP_POST_VARS" => 1,
			"HTTP_SERVER_VARS" => 1,
			"HTTP_ENV_VARS" => 1,
			"HTTP_COOKIE_VARS" => 1,
			"HTTP_SERVER_VARS" => 1,
			"HTTP_POST_VARS" => 1,
			"HTTP_FILES_VARS" => 1,
			"HTTP_REQUEST_VARS" => 1,
			"HTTP_SESSION_VARS" => 1,
			"GLOBALS" => 1,
			"php_errormsg" => 1,
			"HTTP_RAW_POST_DATA" => 1,
			"http_response_header" => 1,
			"argc" => 1,
			"argv" => 1,
			"DOCUMENT_ROOT" => 1,
			"_UNSECURE" => 1,
			"__SECFILTER_FILES" => 1,
		);

		if((!empty($_POST['____SECFILTER_ACCEPT_JS'])) || (!empty($_POST['____SECFILTER_CONVERT_JS'])))
		{
			$this->_initfiles();
		}

		if($this->_usercanexcept())
		{
			if(
				($_SERVER["REQUEST_METHOD"] === "POST")
				&& check_bitrix_sessid()
				&& empty($_POST['____SECFILTER_CONVERT_JS'])
			)
			{
				return 1;
			}
		}

		if(!is_array($_UNSECURE))
			$_UNSECURE = array();
		$_UNSECURE['GLOBALS'] = $GLOBALS;
		$_UNSECURE['_GET'] = $_GET;
		if(!isset($_UNSECURE['_POST']))
			$_UNSECURE['_POST'] = $_POST;
		$MY_POST = $_POST;
		$_UNSECURE['_SERVER'] = $_SERVER;
		$_UNSECURE['_COOKIE'] = $_COOKIE;
		$_UNSECURE['_FILES'] = $_FILES;

		//Do not touch those variables who did not come from REQUEST
		foreach($_REQUEST as $k=>$v)
			if(($v === $GLOBALS[$k]) && !array_key_exists($k, $noprocess))
				unset($GLOBALS[$k]);

		$c=0;

		$c+=$this->_doarray($_GET, '$_GET');
		$c+=$this->_doarray($_POST, '$_POST');
		$c+=$this->_doarray($_COOKIE, '$_COOKIE');
		$c+=$this->_doarray($_FILES, '$_FILES');

		foreach($_SERVER as $k=>$v)
		{
			if(strpos($k, "HTTP_")===0)
			{
				$k1=$k;
				$c+=$this->_dostr($k1, '$_SERVER["'.$k1.'"]');

				if($k<>$k1)
				{
					unset($_SERVER[$k]);
					$_SERVER[$k1]=$v;
				}
				$c+=$this->_dostr($_SERVER[$k1], '$_SERVER["'.$k1.'"]');
			}
		}
		$c+=$this->_dostr($_SERVER["QUERY_STRING"], '$_SERVER["QUERY_STRING"]');
		$c+=$this->_dostr($_SERVER["REQUEST_URI"], '$_SERVER["REQUEST_URI"]');
		$c+=$this->_dostr($_SERVER["SCRIPT_URL"], '$_SERVER["SCRIPT_URL"]');
		$c+=$this->_dostr($_SERVER["SCRIPT_URI"], '$_SERVER["SCRIPT_URI"]');

		$_REQUEST = $_GET;
		foreach($_POST as $k => $v)
			$_REQUEST[$k] = $v;
		foreach($_COOKIE as $k => $v)
			$_REQUEST[$k] = $v;

		$HTTP_GET_VARS=$_GET;
		$HTTP_POST_VARS=$_POST;
		$HTTP_COOKIE_VARS=$_COOKIE;
		$HTTP_REQUEST_VARS=$_REQUEST;
		foreach($_REQUEST as $k=>$v)
			if(!array_key_exists($k, $noprocess) && empty($GLOBALS[$k]))
				$GLOBALS[$k]=$v;

		if(
			$c > 0
			&& $this->_usercanexcept()
		)
		{

			if($this->_filter_action === "none")
				return 1;

			if(empty($_POST['____SECFILTER_CONVERT_JS']))
			{

				//This shows alert text when:
				if(
					//intranet tasks folder created
					($_GET["bx_task_action_request"] == "Y" && $_GET["action"] == "folder_edit")
					//or create ticket with wizard
					|| ($_POST['AJAX_CALL'] == "Y" && $_GET['show_wizard'] == "Y")
					//or by bitrix:search.title
					|| ($_POST['ajax_call'] == "y" && !empty($_POST['q']))
					//or by constant defined on the top of the page
					|| defined('BX_SECURITY_SHOW_MESSAGE')
				)
				{
					echo "[WAF] ".GetMessage("SECURITY_FILTER_FORM_SUB_TITLE")." ".GetMessage("SECURITY_FILTER_FORM_TITLE").".";
					die();
				}


		?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?echo LANG_CHARSET?>" />
<title><?echo GetMessage("SECURITY_FILTER_FORM_TITLE")?></title>
<link rel="stylesheet" type="text/css" href="/bitrix/themes/.default/adminstyles.css" />
<link rel="stylesheet" type="text/css" href="/bitrix/themes/.default/404.css" />
</head>
<body>
<script>if(document.location!=top.location)top.location=document.location;</script>
<style>
	div.description td { font-family:Verdana,Arial,sans-serif; font-size:70%;  border: 1px solid #BDC6E0; padding:3px; background-color: white; }
	div.description table { border-collapse:collapse; }
	div.description td.head { background-color:#E6E9F4; }
</style>

<div class="error-404">
<table class="error-404" border="0" cellpadding="0" cellspacing="0" align="center">
	<tbody><tr class="top">
		<td class="left"><div class="empty"></div></td>
		<td><div class="empty"></div></td>
		<td class="right"><div class="empty"></div></td>
	</tr>
	<tr>
		<td class="left"><div class="empty"></div></td>
		<td class="content">
			<div class="title">
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td><div class="icon"></div></td>
						<td><?echo GetMessage("SECURITY_FILTER_FORM_SUB_TITLE")?></td>
					</tr>
				</table>
			</div>
			<div class="description">
				<?echo GetMessage("SECURITY_FILTER_FORM_MESSAGE")?><br /><br />
				<table cellpadding="0" cellspacing="0" witdh="100%">
					<tr>
						<td class="head" align="center"><?echo GetMessage("SECURITY_FILTER_FORM_VARNAME")?></td>
						<td class="head" align="center"><?echo GetMessage("SECURITY_FILTER_FORM_VARDATA")?></td>
					</tr>
					<?foreach($this->_found_vars as $var_name => $str):?>
					<tr valign="top">
						<td><?echo htmlspecialchars($var_name)?></td>
						<td><?echo htmlspecialchars($str)?></td>
					</tr>
					<?endforeach?>
				</table><br />
				<form method="POST" <?if(defined('POST_FORM_ACTION_URI')):?> action="<?echo POST_FORM_ACTION_URI?>" <?endif?>>
					<?echo $this->_returnhiddens($MY_POST, "");?>
					<?echo $this->_returnfiles();?>
					<?echo bitrix_sessid_post();?>
					<input type="submit" name='____SECFILTER_ACCEPT_JS' value="<?echo GetMessage('SECURITY_FILTER_FORM_ACCEPT')?>" />
					<input type="submit" name='____SECFILTER_CONVERT_JS' value="<?echo GetMessage('SECURITY_FILTER_FORM_CONVERT')?>" />
				</form>
			</div>
		</td>
		<td class="right"><div class="empty"></div></td>
	</tr>
	<tr class="bottom">
		<td class="left"><div class="empty"></div></td>
		<td><div class="empty"></div></td>
		<td class="right"><div class="empty"></div></td>
	</tr>
</tbody></table>
</div>
</body>
</html>
		<?
				die();
			}
		}
		elseif(
			$c > 0
			&& $this->_blocked
			&& CSecurityIPRule::IsActive()
		)
		{
			CSecurityIPRule::OnPageStart(true);
		}
	}

	function OnBeforeProlog()
	{
		$filter = new CSecurityFilter;
		$filter->_do();
	}

	function IsActive()
	{
		$bActive = false;
		$rsEvents = GetModuleEvents("main", "OnBeforeProlog");
		while($arEvent = $rsEvents->Fetch())
		{
			if(
				$arEvent["TO_MODULE_ID"] == "security"
				&& $arEvent["TO_CLASS"] == "CSecurityFilter"
			)
			{
				$bActive = true;
				break;
			}
		}
		return $bActive;
	}

	function SetActive($bActive = false)
	{
		if($bActive)
		{
			if(!CSecurityFilter::IsActive())
			{
				RegisterModuleDependences("main", "OnBeforeProlog", "security", "CSecurityFilter", "OnBeforeProlog", "1");
				RegisterModuleDependences("main", "OnEndBufferContent", "security", "CSecurityXSSDetect", "OnEndBufferContent", 9999);
			}
		}
		else
		{
			if(CSecurityFilter::IsActive())
			{
				UnRegisterModuleDependences("main", "OnBeforeProlog", "security", "CSecurityFilter", "OnBeforeProlog");
				UnRegisterModuleDependences("main", "OnEndBufferContent", "security", "CSecurityXSSDetect", "OnEndBufferContent");
			}
		}
	}

	function GetAuditTypes()
	{
		return array(
			"SECURITY_FILTER_SQL" => "[SECURITY_FILTER_SQL] ".GetMessage("SECURITY_FILTER_SQL"),
			"SECURITY_FILTER_XSS" => "[SECURITY_FILTER_XSS] ".GetMessage("SECURITY_FILTER_XSS"),
			"SECURITY_FILTER_XSS2" => "[SECURITY_FILTER_XSS] ".GetMessage("SECURITY_FILTER_XSS"),
			"SECURITY_FILTER_PHP" => "[SECURITY_FILTER_PHP] ".GetMessage("SECURITY_FILTER_PHP"),
			"SECURITY_REDIRECT" => "[SECURITY_REDIRECT] ".GetMessage("SECURITY_REDIRECT"),
		);
	}
}

class CSecurityFilterMask
{
	function Update($arMasks)
	{
		global $DB, $CACHE_MANAGER;

		if(is_array($arMasks))
		{
			$res = $DB->Query("DELETE FROM b_sec_filter_mask", false, "File: ".__FILE__."<br>Line: ".__LINE__);
			if($res)
			{
				$arLikeSearch = array("?", "*", ".");
				$arLikeReplace = array("_",  "%", "\\.");
				$arPregSearch = array("\\", ".",  "?", "*",   "'");
				$arPregReplace = array("/",  "\.", ".", ".*?", "\'");

				$added = array();
				$i = 10;
				foreach($arMasks as $arMask)
				{
					$site_id = trim($arMask["SITE_ID"]);
					if($site_id == "NOT_REF")
						$site_id = "";

					$mask = trim($arMask["MASK"]);
					if($mask && !array_key_exists($mask, $added))
					{
						$arMask = array(
							"SORT" => $i,
							"FILTER_MASK" => $mask,
							"LIKE_MASK" => str_replace($arLikeSearch, $arLikeReplace, $mask),
							"PREG_MASK" => str_replace($arPregSearch, $arPregReplace, $mask),
						);
						if($site_id)
							$arMask["SITE_ID"] = $site_id;

						$DB->Add("b_sec_filter_mask", $arMask);
						$i += 10;
						$added[$mask] = true;
					}
				}

				if(CACHED_b_sec_filter_mask !== false)
					$CACHE_MANAGER->CleanDir("b_sec_filter_mask");

			}
		}

		return true;
	}

	function GetList()
	{
		global $DB;
		$res = $DB->Query("SELECT SITE_ID,FILTER_MASK from b_sec_filter_mask ORDER BY SORT");
		return $res;
	}

	function Check($site_id, $uri)
	{
		global $DB, $CACHE_MANAGER;
		$bFound = false;

		if(CACHED_b_sec_filter_mask !== false)
		{
			$cache_id = "b_sec_filter_mask";
			if($CACHE_MANAGER->Read(CACHED_b_sec_filter_mask, $cache_id, "b_sec_filter_mask"))
			{
				$arMasks = $CACHE_MANAGER->Get($cache_id);
			}
			else
			{
				$arMasks = array();

				$rs = $DB->Query("SELECT * FROM b_sec_filter_mask ORDER BY SORT");
				while($ar = $rs->Fetch())
				{
					$site_id = $ar["SITE_ID"]? $ar["SITE_ID"]: "-";
					$arMasks[$site_id][$ar["SORT"]] = $ar["PREG_MASK"];
				}

				$CACHE_MANAGER->Set($cache_id, $arMasks);
			}

			if(is_array($arMasks["-"]))
			{
				foreach($arMasks["-"] as $mask)
				{
					if(preg_match("#^".$mask."$#", $uri))
					{
						$bFound = true;
						break;
					}
				}
			}

			if(!$bFound && array_key_exists($site_id, $arMasks))
			{
				foreach($arMasks[$site_id] as $mask)
				{
					if(preg_match("#^".$mask."$#", $uri))
					{
						$bFound = true;
						break;
					}
				}
			}

		}
		else
		{
			$rs = $DB->Query("
				SELECT m.*
				FROM
					b_sec_filter_mask m
				WHERE
					(m.SITE_ID IS NULL AND '".$DB->ForSQL($uri)."' like m.LIKE_MASK)
					OR (m.SITE_ID = '".$DB->ForSQL($site_id)."' AND '".$DB->ForSQL($uri)."' like m.LIKE_MASK)
			");
			if($rs->Fetch())
				$bFound = true;
		}

		return $bFound;
	}
}

class CSecurityXSSDetect
{
	private $in;
	private $_filter_action = false;
	private $_filter_log = false;

	function __construct()
	{
		$this->_filter_action = COption::GetOptionString("security", "filter_action");
		$this->_filter_log = COption::GetOptionString("security", "filter_log");
	}

	function log($var_name, $str, $script)
	{
		if(defined("ANTIVIRUS_CREATE_TRACE"))
			$this->CreateTrace($var_name, $str, $script);
		CEventLog::Log("SECURITY", "SECURITY_FILTER_XSS2", "security", $var_name, "==".base64_encode($str));
	}

	function CreateTrace($var_name, $str, $script)
	{
		$cache_id = md5($var_name.'|'.$str);
		$fn = $_SERVER["DOCUMENT_ROOT"]."/bitrix/cache/virus.db/".$cache_id.".flt";
		if(!file_exists($fn))
		{
			CheckDirPath($fn);
			$f = fopen($fn, "wb");

			fwrite($f, $var_name.": ".$str);
			fwrite($f, "\n------------\n".$script);
			fwrite($f, "\n------------------------------\n\$_SERVER:\n");
			foreach($_SERVER as $k=>$v)
				fwrite($f, $k." = ".$v."\n");

			fclose($f);
			@chmod($fn, BX_FILE_PERMISSIONS);
		}
	}

	function _filter(&$str, $in, $var_name = '')
	{
		if(is_array($in))
		{
			foreach($in as $k => $v)
				$this->_filter($str, $v, $var_name==''? $k: $var_name.'["'.$k.'"]');
		}
		else
		{
			if(strlen($in) < 5)
				return $str;//too short
			if(preg_match("/^[^,;\'\"+\-*\/\{\}\[\]\(\)&\\|=\\\\]*$/", $in))
				return $str;//there is no potantially dangerous code

			$in = str_replace(chr(0), "", $in);
			if((strpos($in, '\'') !== false) || (strpos($in, '"') !== false))
			{
				$in_html=htmlspecialchars($in);
				if(stristr($str, $in) !== false)
				{
					if($this->_filter_log === "Y")
						$this->log($var_name, $in, $str);

					if($this->_filter_action !== "none")
						$str = "";
				}
				elseif(stristr($str, $in_html) !== false)
				{
					if($this->_filter_log === "Y")
						$this->log($var_name, $in, $str);

					if($this->_filter_action !== "none")
						$str = "";
				}
			}
			else
			{
				$str_cl = $this->remove_quoted_strings($str);
				$in_html = htmlspecialchars($in);
				if(stristr($str_cl, $in) !== false)
				{
					if($this->_filter_log === "Y")
						$this->log($var_name, $in, $str);

					if($this->_filter_action !== "none")
						$str = "";
				}
				elseif(stristr($str_cl, $in_html) !== false)
				{
					if($this->_filter_log === "Y")
						$this->log($var_name, $in, $str);

					if($this->_filter_action !== "none")
						$str = "";
				}
			}
		}

		return $str;
	}

	function remove_quoted_strings($str)
	{
		$this->quotes = array();
		$res = "";
		$a = preg_split('/(\'|"|\\/\\*|\\*\\/|\\/\\/|\\n|string\\.replace\\(\\/)/', $str, -1, PREG_SPLIT_DELIM_CAPTURE);
		$c = count($a);
		$i = 0;
		while($i < $c)
		{
			if($a[$i] == '\'')
			{
				$res .= $a[$i];
				$quote = '';
				while((++$i) < $c)
				{
					if($a[$i] === '\'')
					{
						if(preg_match('/(\\\\+)$/', $a[$i-1], $m))
						{
							if((strlen($m[1]) % 2) == 0) //non even slashes
								break;
						}
						else
							break;
					}
					$quote .= $a[$i];
				}
				$this->quotes[] = $quote;
			}
			elseif($a[$i] == '"')
			{
				$res .= $a[$i];
				$quote = '';
				while((++$i) < $c)
				{
					if($a[$i] === '"')
					{
						if(preg_match('/(\\\\+)$/', $a[$i-1], $m))
						{
							if((strlen($m[1]) % 2) == 0) //non even slashes
								break;
						}
						else
							break;
					}
					$quote .= $a[$i];
				}
				$this->quotes[] = $quote;
			}
			elseif($a[$i] == '//')
			{
				//single line comment
				while((++$i) < $c)
				{
					if($a[$i] === "\n")
						break;
				}
				continue;
			}
			elseif($a[$i] === '/*')
			{
				while((++$i) < $c)
				{
					if($a[$i] === '*/')
						break;
				}
				continue;
			}
			elseif($a[$i] === 'string.replace(/') //regexp
			{
				$res .= $a[$i];
				while((++$i) < $c)
				{
					if(preg_match('/^(.*)(\\\\*)(\\/.*)$/', $a[$i], $m))
					{
						if((strlen($m[2]) % 2) == 0) //non even slashes
						{
							$a[$i] = $m[3];
							break;
						}
					}
				}
				continue;
			}

			$res .= $a[$i];
			$i++;
		}

		return $res;
	}

	function filter_cb($strs)
	{
		return $strs[1].$this->_filter($strs[2], $this->in).$strs[3];
	}

	function safeize($str)
	{
		$pcre_backtrack_limit = intval(ini_get("pcre.backtrack_limit"));
		$text_len = defined("BX_UTF")? mb_strlen($str, 'latin1'): strlen($str);
		$text_len = $text_len*2;
		if($pcre_backtrack_limit < $text_len)
			@ini_set("pcre.backtrack_limit", $text_len);

		return preg_replace_callback("/(<script[^>]*>)(.*?)(<\\/script[^>]*>)/is", array($this, "filter_cb"), $str);
	}

	function OnEndBufferContent(&$content)
	{
		$av = new CSecurityXSSDetect;
		$av->in = array(
			"\$_GET" => $_GET,
			"\$_POST" => $_POST,
			"\$_COOKIE" => $_COOKIE,
		);
		$content = $av->safeize($content);
	}
}
?>