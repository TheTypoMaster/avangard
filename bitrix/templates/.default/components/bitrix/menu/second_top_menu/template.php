<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<div class="top_menu">
<?//var_dump($arResult);?>
<?if(!empty($arResult)): ?>
	<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>
			<?foreach($arResult as $arItem):?>
				<?if($arItem["PERMISSION"]>"D" && $arItem['PARAMS']['PARENT']==''):?>
					<td class="menuitem menuitem_<?=$arItem['PARAMS']['ID']?>">
						<a href="<?=$arItem["LINK"]?>">
							<?=$arItem["TEXT"]?>
						</a>
					<?if($arItem['PARAMS']['IS_PARENT']){?>
						<ul class="submenu_block">
						<?foreach($arResult as $arSubItem){?>
							<?if($arSubItem["PERMISSION"]>"D" && $arSubItem['PARAMS']['PARENT']==$arItem['PARAMS']['ID']){?>
								<li class="submenuitem submenuitem_<?=$arSubItem['PARAMS']['ID']?>">
									<a href="<?=$arSubItem["LINK"]?>">
										<?=$arSubItem["TEXT"]?>
									</a>
								</li>
							<?}?>
						<?}?>
						</ul>
					<?}?>
					</td>
				<?endif?>
			<?endforeach?>
		</tr>
	</table>
<?endif?>
</div>