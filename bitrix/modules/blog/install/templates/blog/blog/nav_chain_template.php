<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
echo  '<font class="'.$StyleText.'">'.$title.' ';
$makeweight = ($this->NavRecordCount % $this->NavPageSize);
$NavFirstRecordShow = 0;
if($this->NavPageNomer != $this->NavPageCount)
	$NavFirstRecordShow += $makeweight;

$NavFirstRecordShow += ($this->NavPageCount - $this->NavPageNomer) * $this->NavPageSize + 1;
$NavLastRecordShow = $makeweight + ($this->NavPageCount - $this->NavPageNomer + 1) * $this->NavPageSize;

echo  $NavFirstRecordShow;
echo  ' - '.$NavLastRecordShow;
echo  ' '.GetMessage("nav_of").' ';
echo  $this->NavRecordCount;
echo  "\n<br>\n</font>";

echo  '<font class="'.$StyleText.'">';

if($this->NavPageNomer < $this->NavPageCount)
{
	echo '<a href="'.$sUrlPath.'">'.$sBegin.'</a>&nbsp;|&nbsp;';
	if($this->NavPageNomer == ($this->NavPageCount-1))
		echo '<a href="'.$sUrlPath.'">'.$sPrev.'</a>';
	else
		echo '<a href="'.$sUrlPath.'?PAGEN_'.$this->NavNum.'='.($this->NavPageNomer+1).$strNavQueryString.$add_anchor.'">'.$sPrev.'</a>';
}
else
	echo  $sBegin.'&nbsp;|&nbsp;'.$sPrev;

echo  '&nbsp;|&nbsp;';

$NavRecordGroup = $nStartPage;
while($NavRecordGroup >= $nEndPage)
{
	$NavRecordGroupPrint = $this->NavPageCount - $NavRecordGroup + 1;
	if($NavRecordGroup == $this->NavPageNomer)
		echo  '<b>'.$NavRecordGroupPrint.'</b>&nbsp';
	else
	{
		if($NavRecordGroup == $nStartPage)
			echo  '<a href="'.$sUrlPath.'">'.$NavRecordGroupPrint.'</a>&nbsp;';
		else
			echo  '<a href="'.$sUrlPath.'?PAGEN_'.$this->NavNum.'='.$NavRecordGroup.$strNavQueryString.$add_anchor.'">'.$NavRecordGroupPrint.'</a>&nbsp;';
	}
	$NavRecordGroup--;
}
echo  '|&nbsp;';
if($this->NavPageNomer > 1)
	echo  '<a href="'.$sUrlPath.'?PAGEN_'.$this->NavNum.'='.($this->NavPageNomer-1).$strNavQueryString.$add_anchor.'">'.$sNext.'</a>&nbsp;|&nbsp;<a href="'.$sUrlPath.'?PAGEN_'.$this->NavNum.'=1'.$strNavQueryString.$add_anchor.'">'.$sEnd.'</a>&nbsp;';
else
	echo  $sNext.'&nbsp;|&nbsp;'.$sEnd.'&nbsp;';
echo '</font>';
?>