<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if (CModule::IncludeModule("blog"))
{
	$SORT_BY1 = (isset($SORT_BY1) ? $SORT_BY1 : "NAME");
	$SORT_ORDER1 = (isset($SORT_ORDER1) ? $SORT_ORDER1 : "ASC");
	$SORT_BY2 = (isset($SORT_BY2) ? $SORT_BY2 : "ID");
	$SORT_ORDER2 = (isset($SORT_ORDER2) ? $SORT_ORDER2 : "ASC");
	$COLS_COUNT = (IntVal($COLS_COUNT)>0 ? IntVal($COLS_COUNT) : 2);
	$SORT = Array($SORT_BY1=>$SORT_ORDER1, $SORT_BY2=>$SORT_ORDER2);
	$COUNT = (intval($GROUPS_COUNT)>0) ? Array("nTopCount" => intval($GROUPS_COUNT)) : false;
	$CACHE_TIME = IntVal($CACHE_TIME);
	$is404 = ($is404=='N') ? false: true;
	$arFilter = Array("SITE_ID"=>SITE_ID);
	$arSelectFields = false;
	//формируем кэш
	$cache = new CPHPCache;
	$cache_id = "blog_blog_groups_".serialize($arParams);
	$cache_path = "/".SITE_ID."/blog/blog_groups/";

	if ($CACHE_TIME > 0 && $cache->InitCache($CACHE_TIME, $cache_id, $cache_path))
	{
		$cache->Output();
	}
	else
	{
		if ($CACHE_TIME > 0)
			$cache->StartDataCache($CACHE_TIME, $cache_id, $cache_path);

		$Groups = Array();
		$index_old = 0;
		?><table border="0" cellpadding="4" cellspacing="0" width="75%">
			<?
			$dbGroups = CBlogGroup::GetList(
						$SORT, 
						$arFilter, 
						false, 
						$COUNT, 
						$arSelectFields);
			while($arGroups = $dbGroups->Fetch())
			{
				$dbBlog = CBlog::GetList(Array(), Array("GROUP_ID"=>$arGroups["ID"]), false, false, Array("ID", "GROUP_ID"));
				if($arBlog = $dbBlog->Fetch())
					$Groups[] = array("ID"=>$arGroups["ID"], "NAME"=>htmlspecialcharsex($arGroups["NAME"]));
			}

			$cnt = count($Groups);
			$row1 = ceil($cnt/$COLS_COUNT);
			$all = $cnt-$row1;
			for($i=1; $i<$COLS_COUNT; $i++)
			{
				if(($COLS_COUNT-$i)>1)
					${"row".($i+1)} = ceil($all/($COLS_COUNT-$i));
				else
					${"row".($i+1)} = $all;
				$all = $all - ${"row".($i+1)};
			}
			$showed = 0;
			for($j=0; $j<$row1; $j++)
			{
				?>
				<tr>
					<?
					for($k=0; $k<$COLS_COUNT; $k++)
					{
						if($k==0)
							$index = $j;
						else
							$index = $index_old+${'row'.$k};
							
						if($is404)
							$pathToGroup = "group/".$Groups[$index]['ID'].".php";
						else
							$pathToGroup = "group.php?group_id=".$Groups[$index]["ID"];
						?>
						<td nowrap><a href="<?=$pathToGroup?>"><img src="/bitrix/templates/.default/blog/images/folder.gif" width="17" height="17" border="0" align="absmiddle"></a>&nbsp;&nbsp;<font class="blogtext"><a href="<?=$pathToGroup?>"><?echo $Groups[$index]["NAME"]?></a>
						</font></td>
						<?
						$index_old = $index;
						$showed++;
						if($showed==$cnt)
							$k = $COLS_COUNT;
					}?>
				</tr>
				<?
			}
		?></table><?
		if ($CACHE_TIME > 0)
			$cache->EndDataCache(array());
	}
}
else
	echo ShowError(GetMessage("BLOG_GROUPS_NOT_INSTALL"));?>