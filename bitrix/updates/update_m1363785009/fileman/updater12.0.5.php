<?
if(IsModuleInstalled('fileman'))
{
	$updater->CopyFiles("install/admin", "admin");
	$updater->CopyFiles("install/js", "js");
}
if($updater->CanUpdateKernel())
{
	$arToDelete = array(
		"modules/fileman/install/js/fileman/light_editor/le_table.js",
	);
	foreach($arToDelete as $file)
		CUpdateSystem::DeleteDirFilesEx($_SERVER["DOCUMENT_ROOT"].$updater->kernelPath."/".$file);
}
?>
