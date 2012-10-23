<?
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/favorites.php");

if(!class_exists("CFavorites")):
class CFavorites extends CAllFavorites
{
}
endif; //!class_exists("CFavorites")

class CUserOptions extends CAllUserOptions
{
}
?>