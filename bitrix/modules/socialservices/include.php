<?
$arClasses = array(
	"CSocServAuthManager" => "classes/general/authmanager.php",
	"CSocServUtil" => "classes/general/authmanager.php",
	"CSocServAuth" => "classes/general/authmanager.php",
	"CSocServFacebook" => "classes/general/facebook.php",
	"CFacebookInterface" => "classes/general/facebook.php",
	"CSocServLiveID" => "classes/general/liveid.php",
	"CSocServMyMailRu" => "classes/general/mailru.php",
	"CSocServOpenID" => "classes/general/openid.php",
	"CSocServYandex" => "classes/general/openid.php",
	"CSocServMailRu" => "classes/general/openid.php",
	"CSocServLivejournal" => "classes/general/openid.php",
	"CSocServLiveinternet" => "classes/general/openid.php",
	"CSocServBlogger" => "classes/general/openid.php",
	"CSocServRambler" => "classes/general/openid.php",
	"CSocServTwitter" => "classes/general/twitter.php",
	"CTwitterInterface" => "classes/general/twitter.php",
	"CSocServVKontakte" => "classes/general/vkontakte.php", 
	"CSocServGoogleOAuth" => "classes/general/google.php",
	"COpenIDClient" => "classes/general/openidclient.php",
);

CModule::AddAutoloadClasses("socialservices", $arClasses);
?>