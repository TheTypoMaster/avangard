<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<ul class="footer_menu">
<?if(!empty($arResult)){?>
	<?foreach($arResult as $arItem){?>
		<?if($arItem["PERMISSION"] > "D" && $arItem['PARAMS']['PARENT']==''){ ?>
			<li class="menu_item menuitem_<?=$arItem['PARAMS']['ID']?>">
				<a href="<?=$arItem["LINK"] ?>"><?=$arItem["TEXT"] ?></a>
			<?if($arItem['PARAMS']['IS_PARENT']){?>
				<ul class="submenu_block">
				<?foreach($arResult as $arSubItem){?>
					<?if($arSubItem["PERMISSION"]>"D" && $arSubItem['PARAMS']['PARENT']==$arItem['PARAMS']['ID']){?>
						<li class="submenuitem">
							<a href="<?=$arSubItem["LINK"]?>">
								<?=$arSubItem["TEXT"]?>
							</a>
						</li>
					<?}?>
				<?}?>
				</ul>
			<?}?>
			</li>
		<?}?>
	<?}?>
<?}?>
	<li class="clearall"></li>
</ul>