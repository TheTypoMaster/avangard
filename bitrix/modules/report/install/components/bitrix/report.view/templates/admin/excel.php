<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

__IncludeLang(dirname(__FILE__).'/lang/'.LANGUAGE_ID.'/template.php');

foreach ($arResult['data'] as &$row)
{
	foreach($arResult['viewColumns'] as $col)
	{
		if (is_array($row[$col['resultName']]))
		{
			$row[$col['resultName']] = join(' / ', $row[$col['resultName']]);
		}
	}
}
unset($row);

?>
<meta http-equiv="Content-type" content="text/html;charset=<?echo LANG_CHARSET?>" />
<table border="1">
	<thead>
		<tr>
			<? foreach($arResult['viewColumns'] as $colId => $col): ?>
				<th><?=htmlspecialcharsbx($col['humanTitle'])?></th>
			<? endforeach; ?>
		</tr>
	</thead>
	<tbody>
		<? foreach ($arResult['data'] as $row): ?>
			<tr>
				<? foreach($arResult['viewColumns'] as $col): ?>
					<td><?=$row[$col['resultName']]?></td>
				<? endforeach; ?>
			</tr>
		<? endforeach; ?>
	</tbody>
</table>