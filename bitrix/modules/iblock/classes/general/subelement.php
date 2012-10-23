<?
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/interface/admin_lib.php");
IncludeModuleLangFile(__FILE__);

class CAdminSubSorting extends CAdminSorting
{
	var $list_url;

	function CAdminSubSorting($table_id, $by_initial=false, $order_initial=false, $by_name="by", $ord_name="order", $list_url)
	{
		$this->by_name = $by_name;
		$this->ord_name = $ord_name;
		$this->table_id = $table_id;
		$this->by_initial = $by_initial;
		$this->order_initial = $order_initial;

		$this->list_url = $list_url;
		if ('' == $this->list_url)
			$this->list_url = $GLOBALS["APPLICATION"]->GetCurPage();
//TODO: need parameters in url for md5?
		$uniq = md5($this->list_url);

		$aOptSort = array();
		if(isset($GLOBALS[$this->by_name]))
			$_SESSION["SESS_SORT_BY"][$uniq] = $GLOBALS[$this->by_name];
		elseif(isset($_SESSION["SESS_SORT_BY"][$uniq]))
			$GLOBALS[$this->by_name] = $_SESSION["SESS_SORT_BY"][$uniq];
		else
		{
			$aOptSort = CUserOptions::GetOption("list", $this->table_id, array("by"=>$by_initial, "order"=>$order_initial));
			if(!empty($aOptSort["by"]))
				$GLOBALS[$this->by_name] = $aOptSort["by"];
			elseif($by_initial !== false)
				$GLOBALS[$this->by_name] = $by_initial;
		}

		if(isset($GLOBALS[$this->ord_name]))
			$_SESSION["SESS_SORT_ORDER"][$uniq] = $GLOBALS[$this->ord_name];
		elseif(isset($_SESSION["SESS_SORT_ORDER"][$uniq]))
			$GLOBALS[$this->ord_name] = $_SESSION["SESS_SORT_ORDER"][$uniq];
		else
		{
			if(empty($aOptSort["order"]))
				$aOptSort = CUserOptions::GetOption("list", $this->table_id, array("order"=>$order_initial));
			if(!empty($aOptSort["order"]))
				$GLOBALS[$this->ord_name] = $aOptSort["order"];
			elseif($order_initial !== false)
				$GLOBALS[$this->ord_name] = $order_initial;
		}
	}

	function Show($text, $sort_by, $alt_title = false)
	{
		$ord = "asc";
		$class = "";
		$title = GetMessage("admin_lib_sort_title")." ".($alt_title?$alt_title:$text);

		if(strtolower($GLOBALS[$this->by_name]) == strtolower($sort_by))
		{
			if(strtolower($GLOBALS[$this->ord_name]) == "desc")
			{
				$class = " down";
				$title .= " ".GetMessage("admin_lib_sort_down");
			}
			else
			{
				$class = " up";
				$title .= " ".GetMessage("admin_lib_sort_up");
				$ord = "desc";
			}
		}

		$path = $this->list_url;
		$sep = (false === strpos($path,'?') ? '?' : '&');

		$url = $path.$sep.$this->by_name."=".$sort_by."&".$this->ord_name."=".($class <> ""? $ord:"");

		echo '
<table cellspacing="0" class="subsorting" onClick="'.$this->table_id.'.Sort(\''.htmlspecialchars(CUtil::addslashes($url)).'\', '.($class <> ""? "false" : "true").', arguments);" title="'.$title.'">
<tr>
<td>'.$text.'</td>
<td class="sign'.$class.'"><div class="empty"></div></td>
</tr>
</table>
';
	}
}

class CAdminSubList extends CAdminList
{
/*
 *	list_url - string with params or array:
 *		LINK
 *		PARAMS (array key => value)
 */
	var $strListUrl = '';	// add
	var $strListUrlParams = ''; // add
	var $arListUrlParams = array(); // add
	var $boolNew = false; // add
	var $arFieldNames = array(); // add
	var $arHideHeaders = array(); // add

	function CAdminSubList($table_id, $sort=false,$list_url,$arHideHeaders = false)
	{
		$this->CAdminList($table_id,$sort);

		$this->strListUrlParams = '';
		$this->arListUrlParams = array();

		if ((true == is_array($list_url)) && (true == isset($list_url['LINK'])))
		{
			$this->strListUrl = $list_url['LINK'];
			$this->__ParseListUrl(true);
			if (true == isset($list_url['PARAMS']))
				$this->__SetListUrlParams($list_url['PARAMS']);
		}
		else
		{
			$this->strListUrl = $list_url;
			$this->__ParseListUrl(true);
		}
		if ('' == $this->strListUrl)
		{
			$this->strListUrl = $GLOBALS["APPLICATION"]->GetCurPageParam();
			$this->__ParseListUrl(true);
		}

		$this->SetBaseFieldNames();
		if ((true == is_array($arHideHeaders)) && (false == empty($arHideHeaders)))
		{
			$this->arHideHeaders = $arHideHeaders;
		}
	}

	function GetListUrl($boolFull = false)
	{
		return $this->strListUrl.(true == $boolFull && '' != $this->strListUrlParams ? '?'.$this->strListUrlParams : '');
	}

	function __UpdateListUrlParams()
	{
		$this->strListUrlParams = '';
		if (false == empty($this->arListUrlParams))
		{
			foreach ($this->arListUrlParams as $key => $value)
				$this->strListUrlParams .= $key.'='.$value.'&';
			$this->strListUrlParams = substr($this->strListUrlParams,0,-1);
		}
	}

	function __ClearListUrlParams()
	{
		$this->arListUrlParams = array();
		$this->strListUrlParams = '';
	}

	function __AddListUrlParams($strKey,$strValue)
	{
		if ('' != $strKey)
		{
			$this->arListUrlParams[$strKey] = $strValue;
			$this->__UpdateListUrlParams();
		}
	}

	function __DeleteListUrlParams($mxKey)
	{
		if (true == is_array($mxKey))
		{
			foreach ($mxKey as $value)
				if (('' != $value) && (true == array_key_exists($value,$this->arListUrlParams)))
					unset($this->arListUrlParams[$value]);
		}
		elseif (('' != $mxKey) && (true == array_key_exists($mxKey,$this->arListUrlParams)))
		{
			unset($this->arListUrlParams[$mxKey]);
		}
		$this->__UpdateListUrlParams();
	}

	function __SetListUrlParams($mxParams,$boolClear = false)
	{
		if (true == $boolClear)
			$this->arListUrlParams = array();
		if (false == is_array($mxParams))
		{
			$arParams = array();
			parse_str($mxParams,$arParams);
			$mxParams = (true == is_array($arParams) ? $arParams : array());
		}
		foreach ($mxParams as $key => $value)
			if ('' != $key)
				$this->arListUrlParams[$key] = $value;

		$this->__UpdateListUrlParams();
	}

	function __ParseListUrl($boolClear = false)
	{
		$mxPos = strpos($this->strListUrl,'?');
		if (false !== $mxPos)
		{
			$this->__SetListUrlParams(substr($this->strListUrl,$mxPos+1),$boolClear);
			$this->strListUrl = substr($this->strListUrl,0,$mxPos);
		}
	}

	function AddHideHeader($strID)
	{
		$strID = trim($strID);
		if ('' != $strID)
		{
			if (false == in_array($strID,$this->arHideHeaders))
				$this->arHideHeaders[] = $strID;
		}
	}

	//id, name, content, sort, default
	function AddHeaders($aParams)
	{
		if($_REQUEST['showallcol'])
			$_SESSION['SHALL'] = ($_REQUEST['showallcol']=='Y');

		$aOptions = CUserOptions::GetOption("list", $this->table_id, array());

		$aColsTmp = explode(",", $aOptions["columns"]);
		$aCols = array();
		foreach($aColsTmp as $col)
			if (('' != trim($col)) && (false == in_array($col,$this->arHideHeaders)))
				$aCols[] = trim($col);

		$bEmptyCols = empty($aCols);
		foreach($aParams as $param)
		{
			if (false == in_array($param["id"],$this->arHideHeaders))
			{
				$this->aHeaders[$param["id"]] = $param;
				if($_SESSION['SHALL'] || ($bEmptyCols && $param["default"]==true) || in_array($param["id"], $aCols))
					$this->arVisibleColumns[] = $param["id"];
			}
		}

		if($_REQUEST["mode"] == "subsettings")
		{
			$aAllCols = array();
			foreach($this->aHeaders as $i=>$header)
				$aAllCols[$i] = $header;
		}

		if(!$bEmptyCols)
		{
			foreach($aCols as $i=>$col)
				if($this->aHeaders[$col] <> "")
					$this->aHeaders[$col]["__sort"] = $i;
			uasort($this->aHeaders, create_function('$a, $b', 'if($a["__sort"] == $b["__sort"]) return 0; return ($a["__sort"] < $b["__sort"])? -1 : 1;'));
		}

		if($_REQUEST["mode"] == "subsettings")
			$this->ShowSettings($aAllCols, $aCols, $aOptions);
	}

	function AddVisibleHeaderColumn($id)
	{
		if (!in_array($id, $this->arVisibleColumns) && false == in_array($strID,$this->arHideHeaders))
			$this->arVisibleColumns[] = $id;
	}

	function AddAdminContextMenu($aContext=array(), $bShowExcel=true, $bShowSettings=true)
	{
		$bNeedSep = (count($aContext)>0);
		if($bShowSettings)
		{
			if($bNeedSep)
			{
				$aContext[] = array("SEPARATOR"=>true);
				$bNeedSep = false;
			}
/*			$link = DeleteParam(array("mode"));
			$link = $GLOBALS["APPLICATION"]->GetCurPage()."?mode=settings".($link <> ""? "&".$link:""); */
			$this->__AddListUrlParams('mode','subsettings');
			$aContext[] = array(
				"TEXT"=>GetMessage("admin_lib_context_sett"),
				"TITLE"=>GetMessage("admin_lib_context_sett_title"),
				"LINK"=>"javascript:".$this->table_id.".ShowSettings('".urlencode($this->GetListUrl(true))."')",
				"ICON"=>"btn_sub_settings",
			);
			$this->__DeleteListUrlParams('mode');
		}
		if($bShowExcel)
		{
			if($bNeedSep)
				$aContext[] = array("SEPARATOR"=>true);
/*			$link = DeleteParam(array("mode"));
			$link = $GLOBALS["APPLICATION"]->GetCurPage()."?mode=excel".($link <> ""? "&".$link:""); */
			$this->__AddListUrlParams('mode','excel');
			$aContext[] = array(
				"TEXT"=>"Excel",
				"TITLE"=>GetMessage("admin_lib_excel"),
				"LINK"=>htmlspecialchars($this->GetListUrl(true)),
				"ICON"=>"btn_sub_excel",
			);
			$this->__DeleteListUrlParams('mode');
		}
		if(count($aContext)>0)
			$this->context = new CAdminSubContextMenu($aContext);
	}

	function ActionAjaxReload($url)
	{
		if(strpos($url, "lang=")===false)
		{
			if(strpos($url, "?")===false)
				$url .= '?';
			else
				$url .= '&';
			$url .= 'lang='.LANGUAGE_ID;
		}

		return $this->table_id.".GetAdminList('".CUtil::JSEscape($url)."');";
	}

	function GroupAction()
	{
		//AddMessage2Log("GroupAction");
		if(!empty($_REQUEST['action_button']))
			$_REQUEST['action'] = $_REQUEST['action_button'];

		if(!isset($_REQUEST['action']) || !check_bitrix_sessid())
			return false;

		//AddMessage2Log("GroupAction = ".$_REQUEST['action']." & ".($this->bCanBeEdited?'bCanBeEdited':'ne'));
		if($_REQUEST['action_button']=="edit")
		{
			if(isset($_REQUEST['SUB_ID']))
			{
				if(!is_array($_REQUEST['SUB_ID']))
					$arID = Array($_REQUEST['SUB_ID']);
				else
					$arID = $_REQUEST['SUB_ID'];

				$this->arEditedRows = $arID;
				$this->bEditMode = true;
			}
			return false;
		}

		//AddMessage2Log("GroupAction = X");
		$arID = Array();
		if($_REQUEST['action_target']!='selected')
		{
			if(!is_array($_REQUEST['SUB_ID']))
				$arID = Array($_REQUEST['SUB_ID']);
			else
				$arID = $_REQUEST['SUB_ID'];
		}
		else
			$arID = Array('');

		return $arID;
	}

	function ActionPost($url = false)
	{
		return $this->table_id.".FormSubmit();";
	}

	function ActionDoGroup($id, $action_id, $add_params='')
	{
		$strParams = "SUB_ID=".urlencode($id)
			."&action=".urlencode($action_id)
			."&lang=".urlencode(LANGUAGE_ID)
			."&".bitrix_sessid_get()
			.($add_params<>""? "&".$add_params: "")
		;
		$strUrl = $this->GetListUrl(true).('' != $this->strListUrlParams ? '&' : '?').$strParams;
		return $this->table_id.".GetAdminList('".CUtil::JSEscape($strUrl)."');";
	}

	function &AddRow($id = false, $arRes = Array(), $link = false, $title = false, $boolBX = false)
	{
		$row = new CAdminSubListRow($this->aHeaders, $this->table_id);
		$row->id = $id;
		$row->arRes = $arRes;
		$row->link = $link;
		$row->title = $title;
		$row->pList = &$this;
		$row->boolBX = $boolBX;

		if($id)
		{
			if($this->bEditMode && in_array($id, $this->arEditedRows))
				$row->bEditMode = true;
			elseif(in_array($id, $this->arUpdateErrorIDs))
				$row->bEditMode = true;
		}

		$this->aRows[] = &$row;
		return $row;
	}

	function Display()
	{
		global $APPLICATION;

/*		$db_events = GetModuleEvents("main", "OnAdminSubListDisplay");
		while($arEvent = $db_events->Fetch())
			ExecuteModuleEventEx($arEvent, array(&$this)); */

/*		if($this->context)
			$this->context->Show(); */

		echo '<div id="form_'.$this->table_id.'">';

		if($this->bEditMode && !$this->bCanBeEdited)
			$this->bEditMode = false;

		$errmsg = '';
		for($i=0; $i<count($this->arFilterErrors); $i++)
			$errmsg .= ($errmsg<>''?'<br>':'').$this->arFilterErrors[$i];
		for($i=0; $i<count($this->arUpdateErrors); $i++)
			$errmsg .= ($errmsg<>''?'<br>':'').$this->arUpdateErrors[$i][0];
		for($i=0; $i<count($this->arGroupErrors); $i++)
			$errmsg .= ($errmsg<>''?'<br>':'').$this->arGroupErrors[$i][0];
		if($errmsg<>'')
			CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("admin_lib_error"), "DETAILS"=>$errmsg, "TYPE"=>"ERROR"));

		$successMessage = '';
		for ($i = 0, $cnt = count($this->arActionSuccess); $i < $cnt; $i++)
			$successMessage .= ($successMessage != '' ? '<br>' : '').$this->arActionSuccess[$i];
		if ($successMessage != '')
			CAdminMessage::ShowMessage(array("MESSAGE" => GetMessage("admin_lib_success"), "DETAILS" => $successMessage, "TYPE" => "OK"));

		if($this->sContent!==false)
		{
			echo $this->sContent;
			echo '</div>';
			return;
		}

		echo $this->sPrologContent;

		//!!! insert filter's hiddens
		echo bitrix_sessid_post();
		echo $this->sNavText;
		echo '<table cellspacing="0" class="sublist" id="'.$this->table_id.'">';

		$bShowSelectAll = (count($this->arActions)>0 || $this->bCanBeEdited);
		$this->bShowActions = false;
		foreach($this->aRows as $row)
		{
			if(!empty($row->aActions))
			{
				$this->bShowActions = true;
				break;
			}
		}

		echo '<tr class="gutter">';
		if($bShowSelectAll)
			echo '<td><div class="empty"></div></td>';
		if($this->bShowActions)
			echo '<td><div class="empty"></div></td>';
		foreach($this->aHeaders as $column_id=>$header)
			if(in_array($column_id, $this->arVisibleColumns))
				echo '<td><div class="empty"></div></td>';
		echo '</tr>';
		echo '<tr class="head">';

		$colSpan = 0;
		if($bShowSelectAll)
		{
			echo '<td><input type="checkbox" name="" id="'.$this->table_id.'_check_all" value="" title="'.GetMessage("admin_lib_list_check_all").'" '.($this->bEditMode ? 'disabled' : 'onClick="'.$this->table_id.'.SelectAllRows(this);"').'></td>';
			$colSpan++;
		}
		if($this->bShowActions)
		{
			echo '<td title="'.GetMessage("admin_lib_list_act").'"><div class="action"></div></td>';
			$colSpan++;
		}
		foreach($this->aHeaders as $column_id=>$header)
		{
			if(!in_array($column_id, $this->arVisibleColumns))
				continue;

			echo '<td>';
			if($this->sort && !empty($header["sort"]))
				echo $this->sort->Show($header["content"], $header["sort"], $header["title"]);
			else
				echo $header["content"];
			echo '</td>';
			$colSpan++;
		}
		echo '</tr>';

		if(!empty($this->aRows))
		{
			foreach($this->aRows as $row)
			{
				$row->Display();
				$arRowFields = $row->GetFieldNames();
			}
		}
		elseif(!empty($this->aHeaders))
			echo '<tr><td colspan="'.$colSpan.'">'.GetMessage("admin_lib_no_data").'</td></tr>';

		echo '</table>';

		if(!empty($this->aFooter))
		{
			echo '
<table cellpadding="0" cellspacing="0" border="0" class="sublistfooter">
	<tr>
';
			$n = count($this->aFooter);
			for($i=0; $i<$n; $i++)
				echo '<td'.($i==0? ' class="sublf left"':' class="sublf"').'>'.$this->aFooter[$i]["title"].' <span'.($this->aFooter[$i]["counter"]===true? ' id="'.$this->table_id.'_selected_span"':'').'>'.$this->aFooter[$i]["value"].'</span></td>';
			echo '
		<td class="sublf right">&nbsp;</td>
	</tr>
</table>
';
		}
		echo $this->sNavText;
		$this->ShowActionTable();

		echo $this->sEpilogContent;
		echo '</div>';
	}

	function AddGroupActionTable($arActions, $arParams=array())
	{
		//array("action"=>"text", ...)
		//OR array(array("action" => "custom JS", "value" => "action", "type" => "button", "title" => "", "name" => ""), ...)
		$this->arActions = $arActions;
		//array("disable_action_target"=>true, "select_onchange"=>"custom JS")
		$this->arActionsParams = $arParams;
	}

	function ShowActionTable()
	{
		global $APPLICATION;
		if(count($this->arActions)<=0 && !$this->bCanBeEdited)
			return;

		echo '
<div class="submultiaction">
<input type="hidden" name="action_button" id="'.$this->table_id.'_action_button" value="">
<table cellpadding="0" cellspacing="0" border="0" class="submultiaction">
	<tr class="top"><td class="left"><div class="empty"></div></td><td><div class="empty"></div></td><td class="right"><div class="empty"></div></td></tr>
	<tr>
		<td class="left"><div class="empty"></div></td>
		<td class="content">
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
';

		if($this->bEditMode || count($this->arUpdateErrorIDs)>0)
		{
			echo '
		<td>
			<input type="button" name="save_sub" id="'.$this->table_id.'_save_sub_button" value="'.GetMessage("admin_lib_list_edit_save").'" title="'.GetMessage("admin_lib_list_edit_save_title").'" onclick="'.$this->table_id.'.ExecuteFormAction(\''.$this->table_id.'_save_sub_button\');">
			<input type="button" name="cancel_sub" id="'.$this->table_id.'_cancel_sub_button" value="'.GetMessage("admin_lib_list_edit_cancel").'" title="'.GetMessage("admin_lib_list_edit_cancel_title").'" onclick="'.$this->ActionAjaxReload($this->GetListUrl(true)).'">
		</td>
';
		}
		else
		{
			$bNeedSep = false;
			if($this->arActionsParams["disable_action_target"] <> true)
			{
				echo '
		<td>
			<input title="'.GetMessage("admin_lib_list_edit_for_all").'" type="checkbox" name="action_sub_target" id="'.$this->table_id.'_action_sub_target" value="selected" onclick="if(this.checked && !confirm(\''.GetMessage("admin_lib_list_edit_for_all_warn").'\')) {this.checked=false;} '.$this->table_id.'.EnableActions();">
		</td>
		<td><label title="'.GetMessage("admin_lib_list_edit_for_all").'" for="'.$this->table_id.'_action_sub_target">'.GetMessage("admin_lib_list_for_all").'</label></td>
';
				$bNeedSep = true;
			}

			if($this->bCanBeEdited)
			{
				if($bNeedSep)
					echo '<td><div class="separator"></div></td>';
				$bNeedSep = true;
				echo '
		<td><a href="javascript:void(0);" hidefocus="true" onClick="this.blur();if('.$this->table_id.'.IsActionEnabled(\'edit\')){document.getElementById(\''.$this->table_id.'_action_button\').value=\'edit\'; '.htmlspecialchars($this->ActionPost()).'}" title="'.GetMessage("admin_lib_list_edit").'" class="context-button icon action-edit-button-dis" id="'.$this->table_id.'_action_edit_button"></a></td>
';
			}

			$list = "";
			$buttons = "";
			$html = "";
			foreach($this->arActions as $k=>$v)
			{
				if($k === "delete")
				{
					if($bNeedSep && !$this->bCanBeEdited)
						echo '
		<td><div class="separator"></div></td>
';
					$bNeedSep = true;
					echo '
		<td><a href="javascript:void(0);" hidefocus="true" onClick="this.blur();if('.$this->table_id.'.IsActionEnabled() && confirm((document.getElementById(\''.$this->table_id.'_action_sub_target\') && document.getElementById(\''.$this->table_id.'_action_sub_target\').checked? \''.GetMessage("admin_lib_list_del").'\':\''.GetMessage("admin_lib_list_del_sel").'\'))) {document.getElementById(\''.$this->table_id.'_action_button\').value=\'delete\'; '.htmlspecialchars($this->ActionPost()).'}" title="'.GetMessage("admin_lib_list_del_title").'" class="context-button icon action-delete-button-dis" id="'.$this->table_id.'_action_delete_button"></a></td>
';
				}
				else
				{
					if(is_array($v))
					{
						if($v["type"] == "button")
							$buttons .= '<td><input type="button" name="" value="'.htmlspecialchars($v['name']).'" onclick="'.(!empty($v["action"])? str_replace("\"", "&quot;", $v['action']) : 'document.getElementById(\''.$this->table_id.'_action_button\').=\''.htmlspecialchars($v["value"]).'\'; '.htmlspecialchars($this->ActionPost()).'').'" title="'.htmlspecialchars($v["title"]).'"></td>';
						elseif($v["type"] == "html")
							$html .= '<td>'.$v["value"].'</td>';
						else
							$list .= '<option value="'.htmlspecialchars($v['value']).'"'.($v['action']?' custom_action="'.str_replace("\"", "&quot;", $v['action']).'"':'').'>'.htmlspecialcharsex($v['name']).'</option>';
					}
					else
						$list .= '<option value="'.htmlspecialchars($k).'">'.htmlspecialcharsex($v).'</option>';
				}
			}

			if($buttons <> "")
			{
				if($bNeedSep)
					echo '<td><div class="separator"></div></td>';
				$bNeedSep = true;
				echo $buttons;
			}

			if($list <> "")
			{
				if($bNeedSep)
					echo '<td><div class="separator"></div></td>';
				echo '
		<td>
			<select name="action" id="'.$this->table_id.'_action"'.($this->arActionsParams["select_onchange"] <> ""? ' onchange="'.htmlspecialchars($this->arActionsParams["select_onchange"]).'"':'').' disabled>
				<option value="">'.GetMessage("admin_lib_list_actions").'</option>
				'.$list.'
			</select>
		</td>'.
		$html.'
		<td><input type="button" name="apply_sub" id="'.$this->table_id.'_apply_sub_button" value="'.GetMessage("admin_lib_list_apply").'" onclick="'.$this->table_id.'.ExecuteFormAction(\''.$this->table_id.'_apply_sub_button\');" disabled></td>
';
			}
		}
		echo '
				</tr>
			</table>
		</td>
		<td class="right"><div class="empty"></div></td>
	</tr>
	<tr class="bottom"><td class="left"><div class="empty"></div></td><td><div class="empty"></div></td><td class="right"><div class="empty"></div></td></tr>
</table>
</div>
';
	}

	function DisplayList($boolFlag = true)
	{
		global $APPLICATION;
		$menu = new CAdminPopup($this->table_id."_menu", $this->table_id."_menu",false,array('zIndex' => 4000));
		$menu->Show();

		if($this->context)
			$this->context->Show();
		echo '<div id="'.$this->table_id.'_result_div">';
		$this->Display();
		echo '</div>';

		$tbl = CUtil::JSEscape($this->table_id);
		echo '
<script type="text/javascript">
var '.$this->table_id.'= new JCAdminSubList("'.$tbl.'","'.$this->GetListUrl(true).'");
'.$this->table_id.'.InitTable();
/*jsAdminChain.AddItems("'.$tbl.'_navchain_div"); */
jsUtils.addEvent(window, "unload", function(){'.$this->table_id.'.Destroy(true);});

function ReloadOffers()
{
	'.$this->ActionAjaxReload($this->GetListUrl(true)).'
}
</script>
';
	}

	function CreateChain()
	{
		return new CAdminChain($this->table_id."_navchain_div", false);
	}

	function ShowChain($chain)
	{
		$this->BeginPrologContent();
		$chain->Show();
		$this->EndPrologContent();
	}

	function CheckListMode()
	{
		if($_REQUEST["mode"]=='list' || $_REQUEST["mode"]=='frame')
		{
			ob_start();
			$this->Display();
			$string = ob_get_contents();
			ob_end_clean();

			if($_REQUEST["mode"]=='frame')
			{
				echo '<html><head>';
//				echo $GLOBALS["adminPage"]->ShowScript();
				echo '</head><body>
<div id="'.$this->table_id.'_result_frame_div">'.$string.'</div>
<script>
';
				if($this->bEditMode || count($this->arUpdateErrorIDs)>0)
				{
					echo $this->table_id.'.DeActivateMainForm();';
				}
				else
				{
					echo $this->table_id.'.ActivateMainForm();';
				}
				if($this->onLoadScript)
					echo 'w.eval(\''.CUtil::JSEscape($this->onLoadScript).'\');';
				echo '</script></body></html>';
			}
			else
			{
				if($this->onLoadScript)
					echo "<script type=\"text/javascript\">".$this->onLoadScript."</script>";
				echo $string;
			}
			define("ADMIN_AJAX_MODE", true);
			require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin_after.php");
			die();
		}
		elseif($_REQUEST["mode"]=='excel')
		{
			header("Content-Type: application/vnd.ms-excel");
			header("Content-Disposition: filename=".basename($GLOBALS["APPLICATION"]->GetCurPage(), ".php").".xls");
			$this->DisplayExcel();
			require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin_after.php");
			die();
		}
	}

	function SetBaseFieldNames()
	{
		$this->arFieldNames = array(
			array(
				'NAME' => 'SESSID',
				'TYPE' => 'HIDDEN',
			),
		);
	}

	function AddListFieldNames()
	{
		$this->arFieldNames[] = array(
			'NAME' => 'ACTION_BUTTON',
			'TYPE' => 'HIDDEN',
		);
		$this->arFieldNames[] = array(
			'NAME' => 'SUB_ID[]',
			'TYPE' => 'CHECKBOX',
		);
	}

	function SetListFieldNames($boolClear = true)
	{
		$boolClear = (true == $boolClear ? true: false);
		if (true == $boolClear)
			$this->SetBaseFieldNames();
		$this->AddListFieldNames();
	}

	function DeleteFieldNames($arList = array())
	{
		if (false == is_array($arList))
			$arList = array();
		if (false == empty($arList))
		{
			$arTempo = array();
			foreach ($this->arFieldNames as $arName)
			{
				if (false == in_array($arName['NAME'],$arList))
				{
					$arTempo[] = $arName;
				}
			}
			$this->arFieldNames = $arTempo;
			unset($arTempo);
		}
	}

	function GetListFieldNames()
	{
		return $this->arFieldNames;
	}

	function AddFieldNames($strFieldName, $strFieldType)
	{

	}
}

class CAdminSubListRow extends CAdminListRow
{
	var $arFieldNames = array(); //add
	var $boolBX = false; // add

	function CAdminSubListRow(&$aHeaders, $table_id)
	{
/*		$this->aHeaders = $aHeaders;
		$this->aHeadersID = array_keys($aHeaders);
		$this->table_id = $table_id; */
		parent::CAdminListRow($aHeaders, $table_id);
	}

	function Display()
	{
		$sDefAction = $sDefTitle = "";
		if(!$this->bEditMode)
		{
			if(!empty($this->link))
			{
				if (true == $this->boolBX)
					$sDefAction = "(new BX.CAdminDialog({
			    'content_url': '".$this->link.(!(defined('BX_PUBLIC_MODE') && BX_PUBLIC_MODE == 1) ? '&bxsku=Y' : '')."&bxpublic=Y',
			    'content_post': 'from_module=iblock',
				'draggable': true,
				'resizable': true,
				'buttons': [BX.CAdminDialog.btnSave, BX.CAdminDialog.btnCancel]
			})).Show();";
				else
					$sDefAction = "jsUtils.Redirect([], '".CUtil::addslashes($this->link)."');";
				$sDefTitle = $this->title;
			}
			else
			{
				foreach($this->aActions as $action)
					if($action["DEFAULT"] == true)
					{
						//$sDefAction = htmlspecialchars($action["ACTION"]);
				if (true == $this->boolBX)
					$sDefAction = "(new BX.CAdminDialog({
			    'content_url': '".CUtil::addslashes($action["ACTION"])."',
			    'content_post': '".(!(defined('BX_PUBLIC_MODE') && BX_PUBLIC_MODE == 1) ? '&bxsku=Y' : '')."&bxpublic=Y&from_module=iblock',
				'draggable': true,
				'resizable': true,
				'buttons': [BX.CAdminDialog.btnSave, BX.CAdminDialog.btnCancel]
			})).Show();";
				else
					$sDefAction = htmlspecialchars($action["ACTION"]);
						$sDefTitle = (!empty($action["TITLE"])? $action["TITLE"]:$action["TEXT"]);
						break;
					}
			}
		}

		$sMenuItems = "";
		if(!empty($this->aActions))
			$sMenuItems = htmlspecialchars(CAdminPopup::PhpToJavaScript($this->aActions));

		$aUserOpt = CUserOptions::GetOption("global", "settings");
		echo '<tr'.($this->aFeatures["footer"] == true? ' class="footer"':'').($sMenuItems <> "" && $aUserOpt["context_menu"]<>"N"? ' oncontextmenu="return '.$sMenuItems.';"':'').($sDefAction <> ""? ' ondblclick="'.$sDefAction.'"'.(!empty($sDefTitle)? ' title="'.GetMessage("admin_lib_list_double_click").' '.$sDefTitle.'"':''):'').'>';

		if(count($this->pList->arActions)>0 || $this->pList->bCanBeEdited)
			if ($this->pList->bEditMode)
				echo '<td>&nbsp;</td>';
			else
				echo '<td><input class="checkid" type="checkbox" name="SUB_ID[]" value="'.$this->id.'" title="'.GetMessage("admin_lib_list_check").'"'.($this->bReadOnly? ' disabled':'').'></td>';

		if($this->pList->bShowActions)
		{
			if(!empty($this->aActions))
			{
				echo '
	<td align="center">
		<table cellspacing="0">
			<tr>
				<td><a href="javascript:void(0);" hidefocus="true" onclick="this.blur();'.$this->table_id."_menu".'.ShowMenu(this, '.$sMenuItems.');return false;" title="'.GetMessage("admin_lib_list_actions_title").'" class="action context-button icon">'.'<img src="'.ADMIN_THEMES_PATH.'/'.ADMIN_THEME_ID.'/images/arr_down.gif" class="arrow" alt=""></a></td>
			</tr>
		</table>
	</td>
	';
			}
			else
				echo '<td>&nbsp;</td>';
		}

		$bVarsFromForm = ($this->bEditMode && is_array($this->pList->arUpdateErrorIDs) && in_array($this->id, $this->pList->arUpdateErrorIDs));
		foreach($this->aHeaders as $id=>$header_props)
		{
			if(!in_array($id, $this->pList->arVisibleColumns))
				continue;

			$field = $this->aFields[$id];
			if($this->bEditMode && isset($field["edit"]))
			{
				if($bVarsFromForm && $_REQUEST["FIELDS"])
					$val = $_REQUEST["FIELDS"][$this->id][$id];
				else
					$val = $this->arRes[$id];

				$val_old = $this->arRes[$id];

				echo '<td'.($header_props['align']?' align="'.$header_props['align'].'"':'').($header_props['valign']?' valign="'.$header_props['valign'].'"':'').'>';
				if(is_array($val_old))
				{
					foreach($val_old as $k=>$v)
						echo '<input type="hidden" name="FIELDS_OLD['.htmlspecialchars($this->id).']['.htmlspecialchars($id).']['.htmlspecialchars($k).']" value="'.htmlspecialchars($v).'">';
				}
				else
				{
					echo '<input type="hidden" name="FIELDS_OLD['.htmlspecialchars($this->id).']['.htmlspecialchars($id).']" value="'.htmlspecialchars($val_old).'">';
				}
				switch($field["edit"]["type"])
				{
					case "checkbox":
						echo '<input type="hidden" name="FIELDS['.htmlspecialchars($this->id).']['.htmlspecialchars($id).']" value="N">';
						echo '<input type="checkbox" name="FIELDS['.htmlspecialchars($this->id).']['.htmlspecialchars($id).']" value="Y"'.($val=='Y'?' checked':'').'>';
						break;
					case "select":
						echo '<select name="FIELDS['.htmlspecialchars($this->id).']['.htmlspecialchars($id).']"'.$this->__AttrGen($field["edit"]["attributes"]).'>';
						foreach($field["edit"]["values"] as $k=>$v)
							echo '<option value="'.htmlspecialchars($k).'" '.($k==$val?' selected':'').'>'.htmlspecialcharsex($v).'</option>';
						echo '</select>';
						break;
					case "input":
						if(!$field["edit"]["attributes"]["size"])
							$field["edit"]["attributes"]["size"] = "10";
						echo '<input type="text" '.$this->__AttrGen($field["edit"]["attributes"]).' name="FIELDS['.htmlspecialchars($this->id).']['.htmlspecialchars($id).']" value="'.htmlspecialchars($val).'">';
						break;
					case "calendar":
						if(!$field["edit"]["attributes"]["size"])
							$field["edit"]["attributes"]["size"] = "10";
						echo '<span style="white-space:nowrap;"><input type="text" '.$this->__AttrGen($field["edit"]["attributes"]).' name="FIELDS['.htmlspecialchars($this->id).']['.htmlspecialchars($id).']" value="'.htmlspecialchars($val).'">';
						echo CAdminCalendar::Calendar('FIELDS['.htmlspecialchars($this->id).']['.htmlspecialchars($id).']').'</span>';
						break;
					default:
						echo $field["edit"]['value'];
				}
				echo '</td>';
			}
			else
			{
				if(!is_array($this->arRes[$id]))
					$val = trim($this->arRes[$id]);
				else
					$val = $this->arRes[$id];
				switch($field["view"]["type"])
				{
					case "checkbox":
						if($val=='Y')
							$val = GetMessage("admin_lib_list_yes");
						else
							$val = GetMessage("admin_lib_list_no");
						break;
					case "select":
						if($field["edit"]["values"][$val])
							$val = $field["edit"]["values"][$val];
						break;
				}
				if($field["view"]['type']=='html')
					$val = $field["view"]['value'];
				else
					$val = htmlspecialcharsex($val);

				echo '<td'.($header_props['align']?' align="'.$header_props['align'].'"':'').($header_props['valign']?' valign="'.$header_props['valign'].'"':'').'>';
				echo ((string)$val <> ""? $val:'&nbsp;');
				if($field["edit"]["type"] == "calendar")
					echo CAdminCalendar::ShowScript();
				echo '</td>';
			}
		}
		echo '</tr>';
	}

	function AddFieldNames($strFieldName,$strFieldType = 'HIDDEN')
	{
		if (0 < strlen($strFieldName))
		{
			if (false == isset($this->arFieldNames[$strFieldName]))
			{
				if (0 == strlen($strFieldType))
					$strFieldType = 'HIDDEN';
				$this->arFieldNames[$strFieldName] = ToUpper($strFieldType);
			}
		}
	}

	function GetFieldNames()
	{
		return $this->arFieldNames;
	}
}

class CAdminSubContextMenu extends CAdminContextMenu
{
	function CAdminSubContextMenu($items)
	{
		//array(
		//	array("NEWBAR"=>true),
		//	array("SEPARATOR"=>true),
		//	array("HTML"=>""),
		//	array("TEXT", "ICON", "TITLE", "LINK", "LINK_PARAM"),
		//	array("TEXT", "ICON", "TITLE", "MENU"=>array(array("SEPARATOR"=>true, "ICON", "TEXT", "TITLE", "ACTION"), ...)),
		//	...
		//)
		$this->CAdminContextMenu($items);
	}

	function Show()
	{
		$db_events = GetModuleEvents("main", "OnAdminSubContextMenuShow");
		while($arEvent = $db_events->Fetch())
			ExecuteModuleEventEx($arEvent, array(&$this->items));

		echo '<div class="subcontextmenu">
<table cellpadding="0" cellspacing="0" border="0" class="subcontextmenu">
';
		$bFirst = true;
		$bWasSeparator = false;
		$bWasPopup = false;
		foreach($this->items as $item)
		{
			if(!empty($item["NEWBAR"]))
				$this->EndBar();
			if($bFirst || !empty($item["NEWBAR"]))
			{
				$this->BeginBar();
				$bWasSeparator = true;
			}
			if(!empty($item["NEWBAR"]))
				continue;
			if(!empty($item["SEPARATOR"]))
			{
				echo '<td><div class="section-separator"></div></td>';
				$bWasSeparator = true;
			}
			else
			{
				if(!$bWasSeparator)
				{
					echo '<td><div class="separator"></div></td>';
				}
				if(!empty($item["MENU"]))
				{
					$bWasPopup = true;
					$sMenuUrl = "jsToolBar_popup.ShowMenu(this, ".htmlspecialchars(CAdminPopup::PhpToJavaScript($item["MENU"])).");";
					echo '<td><a href="javascript:void(0);" hidefocus="true" onClick="this.blur();'.$sMenuUrl.'return false;" title="'.$item["TITLE"].'" class="context-button'.(!empty($item["ICON"])? ' icon" id="'.$item["ICON"].'"':'"').'>'.$item["TEXT"].'<img src="'.ADMIN_THEMES_PATH.'/'.ADMIN_THEME_ID.'/images/arr_down.gif" class="arrow" alt=""></a></td>';
				}
				elseif($item["HTML"] <> "")
				{
					echo '<td>'.$item["HTML"].'</td>';
				}
				else
				{
					echo '<td><a href="'.htmlspecialchars(htmlspecialcharsback($item["LINK"])).'" hidefocus="true" title="'.$item["TITLE"].'" '.$item["LINK_PARAM"].' class="context-button'.(!empty($item["ICON"])? ' icon" id="'.$item["ICON"].'"':'"').'>'.$item["TEXT"].'</a></td>';
				}
				$bWasSeparator = false;
			}
			$bFirst = false;
		}

		$this->EndBar();

		echo '
<tr class="bottom-all">
<td class="left"><div class="empty"></div></td>
<td><div class="empty"></div></td>
<td class="right"><div class="empty"></div></td>
</tr>
</table>
';
		if($bWasPopup)
		{
			$menu = new CAdminPopup("jsToolBar_popup", "adminToolbarMenu",false,array('zIndex' => 4000));
			$menu->Show();

		}
		echo '
</div>
';
	}

	function BeginBar()
	{
		echo '
<tr class="top">
<td class="left"><div class="empty"></div></td>
<td><div class="empty"></div></td>
<td class="right"><div class="empty"></div></td>
</tr>
<tr>
<td class="left"><div class="empty"></div></td>
<td class="subcontent">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td><div class="section-separator first"></div></td>
';
	}

	function EndBar()
	{
		echo '
</tr>
</table>
</td>
<td class="right"><div class="empty"></div></td>
</tr>
<tr class="bottom">
<td class="left"><div class="empty"></div></td>
<td><div class="empty"></div></td>
<td class="right"><div class="empty"></div></td>
</tr>
';
	}
}

class CAdminSubForm extends CAdminForm
{
/*
 *	list_url - string with params or array:
 *		LINK
 *		PARAMS (array key => value)
 *		POST_PARAMS (array key => value)
 */
	var $strListUrl = '';	// add
	var $strListUrlParams = ''; // add
	var $arListUrlParams = array(); // add
	var $strListPostParams = '';
	var $arListPostParams = array();
	var $boolShowSettings = false;

	function CAdminSubForm($name, $tabs, $bCanExpand = true, $bDenyAutosave = false, $list_url = '', $boolShowSettings = false)
	{
		global $APPLICATION;

		parent::CAdminForm($name, $tabs, $bCanExpand, $bDenyAutosave);

		$this->boolShowSettings = (true == $boolShowSettings);

		$this->strListUrlParams = '';
		$this->arListUrlParams = array();

		if ((true == is_array($list_url)) && (true == isset($list_url['LINK'])))
		{
			$this->strListUrl = $list_url['LINK'];
			$this->__ParseListUrl(true);
			if (true == isset($list_url['PARAMS']))
				$this->__SetListUrlParams($list_url['PARAMS']);
		}
		else
		{
			$this->strListUrl = $list_url;
			$this->__ParseListUrl(true);
		}
		if ('' == $this->strListUrl)
		{
			$this->strListUrl = $APPLICATION->GetCurPageParam();
			$this->__ParseListUrl(true);
		}

		if (true == is_array($list_url) && !empty($list_url['POST_PARAMS']))
		{
			$this->__SetListPostParams($list_url['POST_PARAMS'],true);
		}
	}

	public function GetListUrl($boolFull = false)
	{
		return $this->strListUrl.(true == $boolFull && '' != $this->strListUrlParams ? '?'.$this->strListUrlParams : '');
	}

	public function GetListPostParams($boolType = false, $boolJS = false)
	{
		$boolType = (true == $boolType ? true : false);
		if ($boolType)
		{
			$boolJS = (true == $boolJS ? true : false);
			return ($boolJS ? CUtil::PhpToJsObject($this->arListPostParams) : $this->arListPostParams);
		}
		else
		{
			return $this->strListPostParams;
		}
	}

	private function __UpdateListUrlParams()
	{
		$this->strListUrlParams = '';
		if (false == empty($this->arListUrlParams))
		{
			foreach ($this->arListUrlParams as $key => $value)
				$this->strListUrlParams .= $key.'='.$value.'&';
			$this->strListUrlParams = substr($this->strListUrlParams,0,-1);
		}
	}

	private function __ClearListUrlParams()
	{
		$this->arListUrlParams = array();
		$this->strListUrlParams = '';
	}

	private function __AddListUrlParams($strKey,$strValue)
	{
		if ('' != $strKey)
		{
			$this->arListUrlParams[$strKey] = $strValue;
			$this->__UpdateListUrlParams();
		}
	}

	private function __DeleteListUrlParams($mxKey)
	{
		if (true == is_array($mxKey))
		{
			foreach ($mxKey as $value)
				if (('' != $value) && (true == array_key_exists($value,$this->arListUrlParams)))
					unset($this->arListUrlParams[$value]);
		}
		elseif (('' != $mxKey) && (true == array_key_exists($mxKey,$this->arListUrlParams)))
		{
			unset($this->arListUrlParams[$mxKey]);
		}
		$this->__UpdateListUrlParams();
	}

	private function __SetListUrlParams($mxParams,$boolClear = false)
	{
		if (true == $boolClear)
			$this->arListUrlParams = array();

		if (!is_array($mxParams))
		{
			$arParams = array();
			parse_str($mxParams,$arParams);
			$mxParams = (true == is_array($arParams) ? $arParams : array());
		}
		foreach ($mxParams as $key => $value)
			if ('' != $key)
				$this->arListUrlParams[$key] = $value;

		$this->__UpdateListUrlParams();
	}

	private function __ParseListUrl($boolClear = false)
	{
		$mxPos = strpos($this->strListUrl,'?');
		if (false !== $mxPos)
		{
			$this->__SetListUrlParams(substr($this->strListUrl,$mxPos+1),$boolClear);
			$this->strListUrl = substr($this->strListUrl,0,$mxPos);
		}
	}

	private function __UpdateListPostParams()
	{
		$this->strListPostParams = '';
		if (false == empty($this->arListPostParams))
		{
			foreach ($this->arListPostParams as $key => $value)
				$this->strListPostParams .= $key.'='.$value.'&';
			$this->strListPostParams = substr($this->strListPostParams,0,-1);
		}
	}

	private function __ClearListPostParams()
	{
		$this->arListPostParams = array();
		$this->strListPostParams = '';
	}

	private function __AddListPostParams($strKey,$strValue)
	{
		if ('' != $strKey)
		{
			$this->arListPostParams[$strKey] = $strValue;
			$this->__UpdateListPostParams();
		}
	}

	private function __DeleteListPostParams($mxKey)
	{
		if (true == is_array($mxKey))
		{
			foreach ($mxKey as $value)
				if (('' != $value) && (true == array_key_exists($value,$this->arListPostParams)))
					unset($this->arListPostParams[$value]);
		}
		elseif (('' != $mxKey) && (true == array_key_exists($mxKey,$this->arListPostParams)))
		{
			unset($this->arListPostParams[$mxKey]);
		}
		$this->__UpdateListPostParams();
	}

	private function __SetListPostParams($mxParams,$boolClear)
	{
		if (true == $boolClear)
			$this->arListPostParams = array();
		if (!is_array($mxParams))
		{
			$arParams = array();
			parse_str($mxParams,$arParams);
			$mxParams = $arParams;
		}
		foreach ($mxParams as $key => $value)
			if ('' != $key)
				$this->arListPostParams[$key] = $value;

		$this->__UpdateListPostParams();
	}

	public function ShowSettings()
	{
		global $APPLICATION;

		$arSubSystemTabsFields = array();
		foreach($this->arSystemTabs as $arTab)
		{
			if(!array_key_exists("CUSTOM", $arTab) || $arTab["CUSTOM"] !== "Y")
			{
				$arSubSystemTabsFields[$arTab["DIV"]] = array();
				if(is_array($arTab["FIELDS"]))
					foreach($arTab["FIELDS"] as $i => $arField)
						$arSubSystemTabsFields[$arTab["DIV"]][$arField["id"]] = $arField["id"];
			}
		}

		$arSubSystemTabs = array();
		foreach($this->arSystemTabs as $arTab)
		{
			if(!array_key_exists("CUSTOM", $arTab) || $arTab["CUSTOM"] !== "Y")
			{
				$arSubSystemTabs[$arTab["DIV"]] = $arTab["TAB"];
			}
		}

		$arSubSystemFields = array();
		foreach($this->arSystemTabs as $arTab)
		{
			if(!array_key_exists("CUSTOM", $arTab) || $arTab["CUSTOM"] !== "Y")
			{
				if(is_array($arTab["FIELDS"]))
				{
					foreach($arTab["FIELDS"] as $arField)
					{
						$id = htmlspecialchars($arField["id"]);
						$label = htmlspecialchars(rtrim(trim($arField["content"]), " :"));
						if($arField["delimiter"])
							$arSubSystemFields[$id] = "--".$label;
						else
							$arSubSystemFields[$id] = ($arField["required"]? "*": "&nbsp;&nbsp;").$label;
					}
				}
			}
		}

		$arAvailableTabs = $arSubSystemTabs;
		$arAvailableFields = $arSubSystemFields;

		$arCustomFields = array();
		foreach($this->tabs as $arTab)
		{
			if(!array_key_exists("CUSTOM", $arTab) || $arTab["CUSTOM"] !== "Y")
			{
				$ar = array(
					"TAB" => $arTab["TAB"],
					"FIELDS" => array(),
				);
				if(is_array($arTab["FIELDS"]))
				{
					foreach($arTab["FIELDS"] as $arField)
					{
						$id = htmlspecialchars($arField["id"]);
						$label = htmlspecialchars(rtrim(trim($arField["content"]), " :"));
						if($arField["delimiter"])
							$ar["FIELDS"][$id] = "--".$label;
						else
							$ar["FIELDS"][$id] = ($arField["required"]? "*": "&nbsp;&nbsp;").$label;
						unset($arAvailableFields[$id]);
					}
				}
				$arCustomFields[$arTab["DIV"]] = $ar;
				unset($arAvailableTabs[$arTab["DIV"]]);
			}
		}
?>
<script>

var arSubSystemTabsFields = <? echo CUtil::PhpToJSObject($arSubSystemTabsFields); ?>;
var arSubSystemTabs = <? echo CUtil::PhpToJSObject($arSubSystemTabs); ?>;
var arSubSystemFields = <? echo CUtil::PhpToJSObject($arSubSystemFields); ?>;

function OnSubAdd(id)
{
	var frm=document.form_settings;
	if(id == 'subtabs_add')
	{
		var oSelect = document.getElementById('selected_tabs');
		if(oSelect)
		{
			var name = prompt('<?echo GetMessage("admin_lib_sett_tab_prompt")?>', '<?echo GetMessage("admin_lib_sett_tab_default_name")?>');
			if(name && name.length > 0)
			{
				var n = oSelect.length;
				var c = 0;
				var found = true;
				while(found)
				{
					c++;
					found = false;
					for(var i=0; i<n; i++)
						if(oSelect[i].value == 'cedit'+c)
							found = true;
				}
				jsSelectUtils.addNewOption('selected_tabs', 'cedit'+c, name, false);
				var td = document.getElementById('selected_fields');
				var newSelect = document.createElement('SPAN');
				td.appendChild(newSelect);
				newSelect.innerHTML = '<select style="display:none" class="select" name="selected_fields[cedit' + c + ']" id="selected_fields[cedit' + c + ']" size="12" multiple onchange="SubSync();"></select>';
				jsSelectUtils.selectOption('selected_tabs', 'cedit'+c);
			}
		}
	}
	if(id == 'subtabs_copy')
	{
		var oSelectFrom = document.getElementById('available_tabs');
		var oSelectTo = document.getElementById('selected_tabs');
		if(oSelectFrom && oSelectTo)
		{
			var n = oSelectFrom.length;
			var k = oSelectTo.length;
			var c = 0;
			for(var i=0; i<n; i++)
				if(oSelectFrom[i].selected)
				{
					var found = false;
					for(var j=0; j<k; j++)
						if(oSelectTo[j].value == oSelectFrom[i].value)
							found = true;
					if(!found)
					{
						var td = document.getElementById('selected_fields');
						var newSelect = document.createElement('SPAN');
						var newID = 'selected_fields[' + oSelectFrom[i].value + ']';
						td.appendChild(newSelect);
						newSelect.innerHTML = '<select style="display:none" class="select" name="' + newID + '" id="' + newID + '" size="12" multiple onchange="SubSync();"></select>';

						jsSelectUtils.addNewOption('selected_tabs', oSelectFrom[i].value, oSelectFrom[i].text, false);
						jsSelectUtils.selectAllOptions('available_fields');
						jsSelectUtils.addSelectedOptions(document.getElementById('available_fields'), newID);

						jsSelectUtils.selectOption('selected_tabs', oSelectFrom[i].value);

					}
				}
		}
	}
	if(id == 'subfields_add')
	{
		var oSelect = document.getElementById('selected_tabs');
		var prefix = '';
		if(oSelect)
		{
			for(var i = 0; i < oSelect.length; i++)
				if(oSelect[i].selected)
					prefix = oSelect[i].value;
		}

		oSelect = GetSubFieldsActiveSelect();
		if(oSelect)
		{
			var name = prompt('<?echo GetMessage("admin_lib_sett_sec_prompt")?>', '<?echo GetMessage("admin_lib_sett_sec_default_name")?>');
			if(name && name.length > 0)
			{
				var n = oSelect.length;
				var c = 0;
				var found = true;
				while(found)
				{
					c++;
					found = false;
					for(var i=0; i<n; i++)
						if(oSelect[i].value == prefix+'_csection'+c)
							found = true;
				}
				jsSelectUtils.addNewOption(oSelect.id, prefix+'_csection'+c, '--'+name, false);
				jsSelectUtils.selectOption(oSelect.id, prefix+'_csection'+c);
			}
		}
	}
	if(id == 'subfields_copy')
	{
		var oSelectFrom = document.getElementById('available_fields');
		var oSelectTo = GetSubFieldsActiveSelect();
		if(oSelectFrom && oSelectTo && !oSelectTo.disabled)
		{
			//find last selected item in selected_subfields
			var i, last = oSelectTo.length - 1;
			for(i = 0; i < oSelectTo.length; i++)
			{
				if(oSelectTo[i].selected)
					last = i;
			}
			//Delete all after last selected
			var tail = new Array;
			for(i = oSelectTo.length - 1; i > last; i--)
			{
				var newoption = new Option(oSelectTo[i].text, oSelectTo[i].value, false, false);
				newoption.innerHTML = oSelectTo[i].innerHTML;
				tail[tail.length] = newoption;
				oSelectTo.remove(i);
			}
			//Deselect all selected_subfields
			for(i = 0; i < oSelectTo.length; i++)
				if(oSelectTo[i].selected)
					oSelectTo[i].selected = false;
			//Add new options
			var sel_count = 0, sel_value = '';
			for(i = 0; i < oSelectFrom.length; i++)
			{
				if(oSelectFrom[i].selected)
				{
					jsSelectUtils.addNewOption(oSelectTo.id, oSelectFrom[i].value, oSelectFrom[i].text, false);
					oSelectTo[oSelectTo.length - 1].selected = true;
					sel_count++;
					if(i < (oSelectFrom.length - 1))
						sel_value = oSelectFrom[i+1].value;
					else
						sel_value = '';
//					else if(i > 0)
//							sel_value = oSelectFrom[i-1].value;
				}
			}
			//Append selected_subfields tail
			var n = oSelectTo.length;
			for(i = tail.length - 1; i >= 0; i--)
			{
				oSelectTo[n] = tail[i];
				n++;
			}
			if((sel_count == 1) && sel_value)
				jsSelectUtils.selectOption(oSelectFrom.id, sel_value);
		}
	}
	SubSync();
}
function OnSubDelete(id)
{
	if(id == 'subtabs_delete')
	{
		var selected_subtabs = document.getElementById('selected_tabs');
		for(var i = 0; i < selected_subtabs.length; i++)
		{
			if(selected_subtabs[i].selected)
			{
				var selected_subfields = document.getElementById('selected_fields[' + selected_subtabs[i].value + ']');
				var p = selected_subfields.parentNode;
				p.removeChild(selected_subfields);
			}
		}

		jsSelectUtils.deleteSelectedOptions(selected_subtabs.id);
		//For Opera deselect options
		jsSelectUtils.selectOption(selected_subtabs.id, '');
	}
	if(id == 'subfields_delete')
	{
		var selected_subfields = GetSubFieldsActiveSelect();
		if(selected_subfields)
		{
			jsSelectUtils.deleteSelectedOptions(selected_subfields.id);
			//For Opera deselect options
			jsSelectUtils.selectOption(selected_subfields.id, '');
		}
	}
	SubSync();
}


function SubSync()
{
	var i,j,n,found;
	var available_subtabs = document.getElementById('available_tabs');
	var available_subfields = document.getElementById('available_fields');
	var selected_subtabs = document.getElementById('selected_tabs');

	//1 available_subtabs
	//1.1 Save selection
	var available_subtabs_selection = '';
	for(i = 0; i < available_subtabs.length; i++)
		if(available_subtabs[i].selected)
			available_subtabs_selection = available_subtabs[i].value;
/*
	//1.2 Clear list
	jsSelectUtils.selectAllOptions(available_subtabs.id);
	jsSelectUtils.deleteSelectedOptions(available_subtabs.id);
	//1.3 Fill list with missed tabs
	n = 0;
	for(available_tab in arSubSystemTabs)
	{
		found = false;
		for(i = 0; i < selected_subtabs.length; i++)
			if(selected_subtabs[i].value == available_tab)
				found = true;
		if(!found)
		{
			var newoption = new Option(arSubSystemTabs[available_tab], available_tab, false, false);
			available_subtabs.options[n] = newoption;
			available_subtabs.options[n].innerHTML = arSubSystemTabs[available_tab];
			n++;
		}
	}
	//1.4 Set selection
	if(available_subtabs_selection)
		for(i = 0; i < available_subtabs.length; i++)
			if(available_subtabs[i].value == available_subtabs_selection)
				available_subtabs[i].selected = true;
*/
	//2 available_subfields
	//2.1 Save selection
	var available_subfields_selection = new Object;
	for(i = 0; i < available_subfields.length; i++)
	{
		if(available_subfields[i].selected)
			available_subfields_selection[available_subfields[i].value] = available_subfields[i].value;
	}
	//2.2 Clear list
	jsSelectUtils.selectAllOptions(available_subfields.id);
	jsSelectUtils.deleteSelectedOptions(available_subfields.id);
	//2.3 Fill list with fields missed
	if(available_subtabs_selection)
	{
		var all_selected_subfields = new Object;
		for(i = 0; i < selected_subtabs.length; i++)
		{
			var selected_subfields = document.getElementById('selected_fields[' + selected_subtabs[i].value + ']');
			for(j = 0; j < selected_subfields.length; j++)
				all_selected_subfields[selected_subfields[j].value] = selected_subfields[j].value;
		}
		n = 0;
		for(available_field in arSubSystemTabsFields[available_subtabs_selection])
		{
			if(!all_selected_subfields[available_field])
			{
				var newoption = new Option(arSubSystemFields[available_field], available_field, false, false);
				available_subfields.options[n] = newoption;
				available_subfields.options[n].innerHTML = arSubSystemFields[available_field];
				n++;
			}
		}
		//2.4 Set selection
		for(i = 0; i < available_subfields.length; i++)
			if(available_subfields_selection[available_subfields[i].value])
				available_subfields[i].selected = true;
	}

	//3 selected_subtabs

	//4 selected_subfields
	found = false;
	for(i = 0; i < selected_subtabs.length; i++)
	{
		var selected_subfields = document.getElementById('selected_fields[' + selected_subtabs[i].value + ']');
		if(selected_subtabs[i].selected)
		{
			selected_subfields.style.display = 'block';
			found = true;
		}
		else
		{
			selected_subfields.style.display = 'none';
		}
	}
	if(found)
		document.getElementById('selected_fields[undef]').style.display = 'none';
	else
		document.getElementById('selected_fields[undef]').style.display = 'block';

	//5 disable and enable buttons
	//5.0 calculate selections counters
	var selected_subtabs_count = 0;
	for(i = 0; i < selected_subtabs.length; i++)
		if(selected_subtabs[i].selected)
			selected_subtabs_count++;
	var available_subtabs_count = 0;
	for(i = 0; i < available_subtabs.length; i++)
		if(available_subtabs[i].selected)
			available_subtabs_count++;
	//subtabs_delete enabled if selected_subtabs have selection
	document.getElementById('subtabs_delete').disabled = selected_subtabs_count <= 0;
	//subtabs_copy enabled if available_subtabs have selection and this selection does not exists in
	//		selected fields
	if(available_subtabs_count <= 0)
	{
		document.getElementById('subtabs_copy').disabled = true;
	}
	else
	{
		found = false;
		for(i = 0; i < selected_subtabs.length; i++)
			if(selected_subtabs[i].value == available_subtabs_selection)
				found = true;
		document.getElementById('subtabs_copy').disabled = found;
	}
	//subtabs_up enabled if selected_subtabs have selection
	document.getElementById('subtabs_up').disabled = selected_subtabs_count <= 0;
	//subtabs_down enabled if selected_subtabs have selection
	document.getElementById('subtabs_down').disabled = selected_subtabs_count <= 0;
	//subtabs_rename enabled if selected_subtabs have one item selected
	document.getElementById('subtabs_rename').disabled = selected_subtabs_count != 1;
	//subtabs_add always selected
	document.getElementById('subtabs_add').disabled = false;

	var selected_subfields_count = 0;
	for(i = 0; i < selected_subtabs.length; i++)
	{
		if(selected_subtabs[i].selected)
		{
			var selected_subfields = document.getElementById('selected_fields[' + selected_subtabs[i].value + ']');
			for(j = 0; j < selected_subfields.length; j++)
				if(selected_subfields[j].selected)
					selected_subfields_count++;
		}
	}
	var available_subfields_count = 0;
	for(i = 0; i < available_subfields.length; i++)
		if(available_subfields[i].selected)
			available_subfields_count++;
	//subfields_delete enabled if selected_subfields have selection
	document.getElementById('subfields_delete').disabled = selected_subfields_count <= 0;
	//subfields_copy enabled if available_subfields have selection and at least one tab selected
	document.getElementById('subfields_copy').disabled = available_subfields_count <= 0 || selected_subtabs_count <= 0;
	//subfields_up enabled if selected_subfields have selection
	document.getElementById('subfields_up').disabled = selected_subfields_count <= 0;
	//subfields_down enabled if selected_subfields have selection
	document.getElementById('subfields_down').disabled = selected_subfields_count <= 0;
	//subfields_rename enabled if selected_subfields have one item selected
	document.getElementById('subfields_rename').disabled = selected_subfields_count != 1;
	//subfields_add enabled if selected_subtabs have one item selected
	document.getElementById('subfields_add').disabled = selected_subtabs_count != 1;

	var arFields = new Object;
	for(var name in arSubSystemFields)
		arFields[name] = arSubSystemFields[name];
	for(i = 0; i < selected_subtabs.length; i++)
	{
		selected_subfields = document.getElementById('selected_fields[' + selected_subtabs[i].value + ']');
		for(j = 0; j < selected_subfields.length; j++)
			delete arFields[selected_subfields[j].value];
	}
	var save_button = document.getElementById('save_settings');
	save_button.disabled = false;
	for(var name in arFields)
	{
		if(arFields[name].substring(0,1) == "*")
			save_button.disabled = true;
	}
}

function SyncAvailableFields()
{
	var oSelect = document.getElementById('available_tabs');
	if(oSelect)
	{
		var k = oSelect.length;
		for(var i=0; i<k; i++)
		{
			oFieldsSelect = document.getElementById('available_fields');
			if(oFieldsSelect)
			{
				jsSelectUtils.selectAllOptions(oFieldsSelect.id);
				jsSelectUtils.deleteSelectedOptions(oFieldsSelect.id);
				if(oSelect[i].selected)
				{
					var n = 0;
					for(var field_id in arSubSystemTabsFields[oSelect[i].value])
					{
						var newoption = new Option(arSubSystemFields[field_id], field_id, false, false);
						oFieldsSelect.options[n]=newoption;
						oFieldsSelect.options[n].innerHTML = arSubSystemFields[field_id];
						n++;
					}
				}
			}
		}
	}
}

function GetSubFieldsActiveSelect()
{
	var oFieldsSelect;
	var oSelect = document.getElementById('selected_tabs');
	if(oSelect)
	{
		var k = oSelect.length;
		for(var i=0; i<k; i++)
		{
			oFieldsSelect = document.getElementById('selected_fields[' + oSelect[i].value + ']');
			if(oFieldsSelect && oFieldsSelect.style.display == 'block')
				return oFieldsSelect;
		}
	}
	return false;
}

function OnSubRename(id)
{
	var frm=document.form_subsettings;
	if(id == 'subtabs_rename')
	{
		var oSelect = document.getElementById('selected_tabs');
		if(oSelect)
		{
			var n = oSelect.length;
			var c = 0;
			var choice = '';
			for(var i=0; i<n; i++)
			{
				if(oSelect[i].selected)
				{
					c++;
					if(!choice)
						choice = oSelect[i].text;
				}
			}
			if(c == 1)
			{
				var name = prompt('<?echo GetMessage("admin_lib_sett_tab_rename")?>', choice);
				if(name && name.length > 0)
				{
					for(var i=0; i<n; i++)
						if(oSelect[i].selected)
						{
							oSelect[i].text = name;
							break;
						}
				}
			}
		}
	}
	if(id == 'subfields_rename')
	{
		var oSelect = GetSubFieldsActiveSelect();
		if(oSelect)
		{
			var n = oSelect.length;
			var c = 0;
			var choice = '';
			for(var i=0; i<n; i++)
			{
				if(oSelect[i].selected)
				{
					c++;
					if(!choice)
						choice = oSelect[i].innerHTML;
				}
			}
			if(c == 1)
			{
				var prefix = '';
				if(choice.substring(0, 2) == '--')
				{
					choice = choice.substring(2);
					prefix = '--';
				}
				else
				{
					if(choice.substring(0, 1) == '*')
					{
						choice = choice.substring(1);
						prefix = '*';
					}
					else
					{
						if(choice.substring(0, 12) == '&nbsp;&nbsp;')
						{
							choice = choice.substring(12);
							prefix = '&nbsp;&nbsp;';
						}
						else
						{
							if(choice.substring(0, 2) == '\xA0\xA0')
							{
								choice = choice.substring(2);
								prefix = '&nbsp;&nbsp;';
							}
						}
					}
				}
				var name = prompt('<?echo GetMessage("admin_lib_sett_sec_rename")?>', choice);
				if(name && name.length > 0)
				{
					for(var i=0; i<n; i++)
						if(oSelect[i].selected)
						{
							if(prefix == '&nbsp;&nbsp;')
							{
								oSelect[i].text = name;
								oSelect[i].innerHTML = '&nbsp;&nbsp;' + oSelect[i].innerHTML;
							}
							else
							{
								oSelect[i].text = prefix + name;
							}
							break;
						}
				}
			}
		}
	}
}
function SubFieldsUpAndDown(direction)
{
	var oSelect = GetSubFieldsActiveSelect();
	if(oSelect)
	{
		if(direction == 'up')
			jsSelectUtils.moveOptionsUp(oSelect);
		else
			jsSelectUtils.moveOptionsDown(oSelect);
	}
}
SubSync();
</script>

<div class="title">
<table cellspacing="0" width="100%">
	<tr>
		<td width="100%" class="title-text" onmousedown="jsFloatDiv.StartDrag(arguments[0], document.getElementById('settings_float_div'));"><?echo GetMessage("admin_lib_sett_tab_title")?></td>
		<td width="0%"><a class="close" href="javascript:<?echo $this->name?>.CloseSettings();" title="<?echo GetMessage("admin_lib_sett_close")?>"></a></td>
	</tr>
</table>
</div>
<div class="content">
<form ENCTYPE="multipart/form-data" name="form_settings" action="<?echo $APPLICATION->GetCurPageParam()?>" method="POST">
<h2><?echo GetMessage("admin_lib_sett_tab_fields")?></h2>
<table cellspacing="0">
	<tr valign="center">
		<td><?echo GetMessage("admin_lib_sett_tab_available_tabs")?>:</td>
		<td></td>
		<td><?echo GetMessage("admin_lib_sett_tab_selected_tabs")?>:</td>
		<td></td>
	</tr>
	<tr valign="center">
		<td>
			<select class="select" name="available_tabs" id="available_tabs" size="8" onchange="SubSync();">
		<?
		foreach($arSubSystemTabs as $id => $label)
			echo '<option value="'.htmlspecialchars($id).'">'.htmlspecialchars($label).'</option>';
		?>
			</select>
		</td>
		<td>
			<input type="button" name="subtabs_copy" id="subtabs_copy" value="&nbsp; &gt; &nbsp;" title="<?echo GetMessage("admin_lib_sett_tab_copy")?>" disabled onclick="OnSubAdd(this.id);">
		</td>
		<td>
			<select class="select" name="selected_tabs" id="selected_tabs" size="8" onchange="SubSync();">
		<?
		foreach($arCustomFields as $tab_id => $arTab)
			echo '<option value="'.htmlspecialchars($tab_id).'">'.htmlspecialchars($arTab["TAB"]).'</option>';
		?>
			</select>
		</td>
		<td>
			<input type="button" name="subtabs_up" id="subtabs_up" class="button" value="<?echo GetMessage("admin_lib_sett_up")?>" title="<?echo GetMessage("admin_lib_sett_up_title")?>" disabled onclick="jsSelectUtils.moveOptionsUp(document.form_settings.selected_tabs);"><br>
			<input type="button" name="subtabs_down" id="subtabs_down" class="button" value="<?echo GetMessage("admin_lib_sett_down")?>" title="<?echo GetMessage("admin_lib_sett_down_title")?>" disabled onclick="jsSelectUtils.moveOptionsDown(document.form_settings.selected_tabs);"><br>
			<input type="button" name="subtabs_rename" id="subtabs_rename" class="button" value="<?echo GetMessage("admin_lib_sett_tab_rename")?>" title="<?echo GetMessage("admin_lib_sett_tab_rename_title")?>" disabled onclick="OnSubRename(this.id);"><br>
			<input type="button" name="subtabs_add" id="subtabs_add" class="button" value="<?echo GetMessage("admin_lib_sett_tab_add")?>" title="<?echo GetMessage("admin_lib_sett_tab_add_title")?>" onclick="OnSubAdd(this.id);"><br>
			<input type="button" name="subtabs_delete" id="subtabs_delete" class="button" value="<?echo GetMessage("admin_lib_sett_del")?>" title="<?echo GetMessage("admin_lib_sett_del_title")?>" disabled onclick="OnSubDelete(this.id);"><br>
		</td>
	</tr>
	<tr valign="center">
		<td><?echo GetMessage("admin_lib_sett_tab_available_fields")?>:</td>
		<td></td>
		<td><?echo GetMessage("admin_lib_sett_tab_selected_fields")?>:</td>
		<td></td>
	</tr>
	<tr valign="center">
		<td>
			<select class="select" name="available_fields" id="available_fields" size="12" multiple onchange="SubSync();">
		<?
		foreach($arAvailableFields as $id => $label)
			echo '<option value="'.$id.'">'.$label.'</option>';
		?>
			</select>
		</td>
		<td>
			<input type="button" name="subfields_copy" id="subfields_copy" value="&nbsp; &gt; &nbsp;" title="<?echo GetMessage("admin_lib_sett_fields_copy")?>" disabled onclick="OnSubAdd(this.id);"><br><br>
		</td>
		<td id="selected_fields">
		<select style="display:block" disabled class="select" name="selected_fields[undef]" id="selected_fields[undef]" size="12" multiple></select>
		<?
		foreach($arCustomFields as $tab_id => $arTab)
		{
			if(is_array($arTab["FIELDS"]))
			{
				echo '<select style="display:none" class="select" name="selected_fields['.$tab_id.']" id="selected_fields['.$tab_id.']" size="12" multiple onchange="SubSync();">';
				foreach($arTab["FIELDS"] as $field_id => $label)
					echo '<option value="'.$field_id.'">'.$label.'</option>';
				echo '</select>';
			}
		}
		?>
		</td>
		<td>
			<input type="button" name="subfields_up" id="subfields_up" class="button" value="<?echo GetMessage("admin_lib_sett_up")?>" title="<?echo GetMessage("admin_lib_sett_up_title")?>" disabled onclick="SubFieldsUpAndDown('up');"><br>
			<input type="button" name="subfields_down" id="subfields_down" class="button" value="<?echo GetMessage("admin_lib_sett_down")?>" title="<?echo GetMessage("admin_lib_sett_down_title")?>" disabled onclick="SubFieldsUpAndDown('down');"><br>
			<input type="button" name="subfields_rename" id="subfields_rename" class="button" value="<?echo GetMessage("admin_lib_sett_field_rename")?>" title="<?echo GetMessage("admin_lib_sett_field_rename_title")?>" disabled onclick="OnSubRename(this.id);"><br>
			<input type="button" name="subfields_add" id="subfields_add" class="button" value="<?echo GetMessage("admin_lib_sett_field_add")?>" title="<?echo GetMessage("admin_lib_sett_field_add_title")?>" onclick="OnSubAdd(this.id);"><br>
			<input type="button" name="subfields_delete" id="subfields_delete" class="button" value="<?echo GetMessage("admin_lib_sett_del")?>" title="<?echo GetMessage("admin_lib_sett_fields_delete")?>" disabled onclick="OnSubDelete(this.id);">
		</td>
	</tr>
</table>
		<?if($GLOBALS["USER"]->CanDoOperation('edit_other_settings')):?>
<h2><?echo GetMessage("admin_lib_sett_common")?></h2>
<table cellspacing="0">
	<tr>
		<td><input type="checkbox" name="subset_default" id="subset_default" value="Y"></td>
		<td><label for="subset_default"><?echo GetMessage("admin_lib_sett_common_set")?></label></td>
		<td>|</td>
		<td><a class="delete-icon" title="<?echo GetMessage("admin_lib_sett_common_del")?>" href="javascript:if(confirm('<?echo GetMessage("admin_lib_sett_common_del_conf")?>'))<?echo $this->name?>.DeleteSettings(true)"></a></td>
	</tr>
</table>
		<?endif?>
</div>
<div class="buttons">
	<input type="button" id="save_settings" value="<?echo GetMessage("admin_lib_sett_save")?>" onclick="<?echo $this->name?>.SaveSettings()" title="<?echo GetMessage("admin_lib_sett_save_title")?>">
	<input type="button" value="<?echo GetMessage("admin_lib_sett_cancel")?>" onclick="<?echo $this->name?>.CloseSettings()" title="<?echo GetMessage("admin_lib_sett_cancel_title")?>">
	<input type="button" value="<?echo GetMessage("admin_lib_sett_reset")?>" onclick="if(confirm('<?echo GetMessage("admin_lib_sett_reset_ask")?>'))<?echo $this->name?>.DeleteSettings()" title="<?echo GetMessage("admin_lib_sett_reset_title")?>">
</div>
</form>
<?
		require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin_after.php");
		die();
	}

	function End()
	{
		if(!$this->bButtons)
		{
			while ($this->tabIndex < count($this->tabs))
				$this->BeginNextTab();

			//end previous tab
			$this->EndTab();

			if (!$this->bPublicMode)
			{
				echo '
					</td>
				</tr>
			</table>
';
			}
		}
		elseif (!$this->bPublicMode)
		{
			echo '
</div>
';
		}

		if (!$this->bPublicMode)
		{
			echo '
		</td>
		<td class="right"><div class="empty"></div></td>
	</tr>
	<tr class="bottom">
		<td class="left"><div class="empty"></div></td>
		<td><div class="empty"></div></td>
		<td class="right"><div class="empty"></div></td>
	</tr>
</table>';
		}

		echo '
</div>

<input type="hidden" id="'.$this->name.'_active_tab" name="'.$this->name.'_active_tab" value="'.htmlspecialchars($this->selectedTab).'">

<script>';
		$s = "";
		foreach($this->tabs as $tab)
		{
			$s .= ($s <> ""? ", ":"").
			"{".
			"'DIV': '".$tab["DIV"]."' ".
			($tab["ONSELECT"] <> ""? ", 'ONSELECT': '".CUtil::JSEscape($tab["ONSELECT"])."'":"").
			"}";
		}
		echo '
var '.$this->name.' = new SubTabControl("'.$this->name.'", "'.$this->unique_name.'", ['.$s.'],"'.$this->GetListUrl(true).'",'.$this->GetListPostParams(true,true).');';
		if($this->bCanExpand && count($this->tabs) > 1)
		{
			$aEditOpt = CUserOptions::GetOption("edit", $this->unique_name, array());
			if($aEditOpt["expand"] == "on")
				echo '
'.$this->name.'.ToggleTabs();';
		}
		echo '
'.$this->name.'.InitEditTables('.($this->bPublicMode ? 'true' : '').');
jsUtils.addEvent(window, "unload", function(){'.$this->name.'.Destroy();});
</script>
';
		if ($this->bPublicModeBuffer)
		{
			echo '</div>';
			echo '<script type="text/javascript">BX.ready(function() {'.$this->publicObject.'.SwapContent(\''.$this->publicModeBuffer_id.'\');});</script>';
		}
		$this->ShowSettingsButton();
	}

	function ShowSettingsButton()
	{
		if ($this->boolShowSettings)
		{
			$this->__AddListUrlParams('mode', 'settings');
			$strLink = $this->GetListUrl(true);
			$this->__DeleteListUrlParams('mode');
			?><script>
			<? echo $this->name ?>.btnSettingsShow(<? echo $this->publicObject; ?>, '<? echo $strLink ?>', '<? echo CUtil::JSEscape(htmlspecialchars(GetMessage('SUB_FORM_SETTINGS'))); ?>',<? echo ($this->AUTOSAVE ? 'true' : 'false'); ?>);
			<?
			if ($this->bCustomFields)
			{
				if (is_array($_SESSION["ADMIN_CUSTOM_FIELDS"]) && array_key_exists($this->name, $_SESSION["ADMIN_CUSTOM_FIELDS"]))
				{
					echo $this->name ?>.btnSettingsOnOff(<? echo $this->publicObject; ?>,'<? echo CUtil::JSEscape(htmlspecialchars(GetMessage('admin_lib_sett_sett_enable'))); ?>',true,<? echo ($this->AUTOSAVE ? 'true' : 'false'); ?>);<?
				}
				else
				{
					echo $this->name ?>.btnSettingsOnOff(<? echo $this->publicObject; ?>,'<? echo CUtil::JSEscape(htmlspecialchars(GetMessage('admin_lib_sett_sett_disable'))); ?>',false,<? echo ($this->AUTOSAVE ? 'true' : 'false'); ?>);<?
				}
			}
			?>
			</script><?
		}
	}
}
?>