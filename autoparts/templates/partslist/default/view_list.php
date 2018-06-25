<?if(!defined("TDM_PROLOG_INCLUDED") || TDM_PROLOG_INCLUDED!==true) die(); ?>

<table class="tdlist">
	<tr class="head">
		<td></td>
		<td></td>
		<td><?=Lng('Name',1,0);?></td>
		<td></td>
		<?php if (TDM_ISADMIN || $_SESSION['TDM_CMS_USER_GROUP'] === 7): ?>
			<td>Цена магазин</td>
		<?php endif; ?>
		<td style="padding:0px; text-align:right;">
			<table class="listprice"><tr class="thead">
				<td class="avail"><?=Lng('Avail.',1,1)?></td>
				<td class="cost ttip" title="<?=TDM_CUR?>"><?=Lng('Price',1,1)?></td>
				<td class="tocart"></td></tr>
			</table>
		</td>
	</tr>

	<?
	foreach($arResult['PARTS'] as $NumKey=>$arPart){
		if($arPart['PKEY']==''){continue;} //ERROR key
		$Cnt++; $PCnt=0; $OpCnt=0; $cm=''; $AddF=0;
		//Criteria display method
		if($arPart['CRITERIAS_COUNT']>0){
			foreach($arPart['CRITERIAS'] as $Criteria=>$Value){
				if($Criteria!=''){$arPart['CRITERIA'].=$cm.$Criteria.' - '.$Value;}else{$arPart['CRITERIA'].=$cm.UWord($Value);} $cm='; ';
			}
		}
		//Pictures display method
		if($arPart['IMG_ZOOM']=='Y'){
			$Zoom=$arPart['IMG_SRC']; $ZClass='cbx_imgs';
			$PicText=''; $Target='';
		}else{
			$Zoom='https://www.google.com/search?q='.$arPart['BRAND'].'+'.$arPart['ARTICLE'].'&tbm=isch'; $ZClass='';
			$PicText=Lng('Search_photo_in_google',1,0); $Target='target="_blank"';
		}
		if(TDM_ISADMIN AND $arPart['LINK_CODE']!=''){$BrandClass='linked';
			$BrLink = '<a href="/'.TDM_ROOT_DIR.'/admin/dbedit.php?selecttable=Y&table=TDM_LINKS&LINK='.$arPart['LINK_LEFT_AKEY'].'" target="_blank" class="ttip link" title="'.$arPart['LINK_INFO'].'<br>'.$arPart['LINK_CODE'].'"></a>';
		}else{$BrandClass=''; $BrLink='';}
		?>
		<tr class="cols">
		<td class="tdbrand">
			<a class = "<?= $BrandClass ?>"><?= !empty($arPart['PC_MANUFACTURER']) ? $arPart['PC_MANUFACTURER'] : $arPart['BRAND'] ?></a>
			<?= $BrLink ?><br>
			<div class="ttip" <?if(TDM_ISADMIN){?>ttip" title="BKEY: <?=$arPart['BKEY']?><br>AKEY: <?=$arPart['AKEY']?><br>AID:<?=$arPart['AID']?><?}?>"><?= !empty($arPart['PC_SKU']) ? $arPart['PC_SKU'] : $arPart['ARTICLE'] ?></div>
			<?php if($arPart['KIND'] > 0): ?>
				<span style="font-size:11px;"><?= TDMPrintArtKinde($arPart['KIND']) ?></span>
			<?php endif; ?>
		</td>
		<td>

			<div class="tditem" id="item<?=$arPart['PKEY']?>">
			<?// Preview images: ?>
			<a href="<?=$Zoom?>" class="image <?=$ZClass?>" rel="img<?=$arPart['PKEY']?>" <?=$Target?>>
				<?if($arResult['ART_LOGOS'][$arPart['AID']]!=''){?>
					<div style="background-image:url('<?=$arResult['ART_LOGOS'][$arPart['AID']]?>');" class="logobox"></div>
				<?}?>
				<?if($PicText!=''){?>
					<div class="gosrch"><?=$PicText?></div>
				<?}else{?>
					<div style="background-image:url('<?=$arPart['IMG_SRC']?>');" class="photobox"></div>
				<?}?>
				
			</a>
				<?if($AddF>0){?><div class="addphoto" title="<?=Lng('Photo_count',1,0);?>">x<?=($AddF+1)?></div><?}?>
			<?if(is_array($arPart["IMG_ADDITIONAL"])){
				foreach($arPart["IMG_ADDITIONAL"] as $AddImgSrc){ $AddF++;?><a href="<?=$AddImgSrc?>" class="cbx_imgs" rel="img<?=$arPart['PKEY']?>"></a><?}
			}?>

		</td>
		<td width="40%">
			<b class="name" title="TecDoc name: <?=$arPart['TD_NAME']?>"><?=$arPart['NAME']?></b><br>
			<div class="criteria"><?=$arPart['CRITERIA']?></div>
			<div class="itemprops" id="props<?=$arPart['PKEY']?>">
				<?if($arPart["PROPS_COUNT"]>0){
					foreach($arPart['PROPS'] as $PName=>$PValue){?>
						<span class="criteria"><?=$PName?><?if($PValue!=''){?>: <?=$PValue?><?}else{?>.<?}?></span><br>
					<?}
				}?>
			</div>
			<?php if($arPart["PROPS_COUNT"] > 3): ?>
				<a class="moreprops" href="javascript:void(0)" onClick="ShowMoreProps(this,'props<?=$arPart['PKEY']?>')">&#9660; <?=Lng('Show_more_properties',1,false)?> (<?=($arPart["PROPS_COUNT"]-3)?>)</a>
			<?php endif; ?>
		</td>
		<td style="width:40px; white-space:nowrap;" class="rigbord">
			<?if($arPart["AID"]>0){?><table class="propstb"><tr><td>
				<a href="/<?=TDM_ROOT_DIR?>/props.php?of=<?=$arPart["AID"]?>" class="dopinfo popup" title="<?=Lng('Additional_Information',1,0)?>"></a></td><td>
				<a href="javascript:void(0)" OnClick="AppWin('<?=TDM_ROOT_DIR?>',<?=$arPart["AID"]?>,980)" class="carsapp" target="_blank" title="<?=Lng('Applicability_to_model_cars',1,0)?>"></a></table>
			<?}?>
		</td>

		<?php if (TDM_ISADMIN || $_SESSION['TDM_CMS_USER_GROUP'] === 7): ?>
			<td class="options">
				<?php if($arPart["PRICES_COUNT"] > 0): ?>
					<table class="optionstab">
					<?foreach($arResult['PRICES'][$arPart['PKEY']] as $arPrice){ $OpCnt++;
						if($OpCnt>$arResult['LIST_PRICES_LIMIT']){$OpClass='op'.$arPart['PKEY']; $OpStyle='style="display:none;"'; }else{$OpClass=''; $OpStyle='';}?>
						<tr class="<?=$OpClass?>" <?=$OpStyle?> ><td><?=$arPrice['OPTIONS']['VIEW_INTAB']?></td></tr>
					<?}?>
					</table>
				<?php endif; ?>
			</td>
		<?php endif; ?>

		<td style="padding:0px;">
			<?if($arPart["PRICES_COUNT"]>0){?>
				<table class="listprice">
				<?foreach($arResult['PRICES'][$arPart['PKEY']] as $arPrice){
					$PCnt++;
					if($PCnt>1){$TopBord='topbord';}else{$TopBord='';}
					if($PCnt>$arResult['LIST_PRICES_LIMIT']){$HClass='pr'.$arPart['PKEY']; $HStyle='style="display:none;"'; }else{$HStyle=''; $HClass='';}?>
					<tr class="trow <?=$HClass?> <?=$TopBord?>" <?=$HStyle?> >
						<td class="avail"><?=$arPrice['AVAILABLE']?> шт.</td>
						<td class="cost ttip">
							<?if($arPrice['EDIT_LINK']!=''){?><a href="<?=$arPrice['EDIT_LINK']?>" class="popup editprice" title="<?=Lng('Price',1,0)?>: <?=Lng('Edit',2,0)?>"><?}?>
							<?=$arPrice['PRICE_FORMATED']?> грн.</a>
						</td>
						<td class="tocart">
							<?if($arResult['ADDED_PHID']==$arPrice['PHID']){?>
								<div class="tdcartadded" title="<?=Lng('Added_to_cart',1,0)?>"><div class="text_cart_added">В корзине</div></div>
							<? }else{ ?>
							<a href="javascript:void(0)" class="tdcartadd" OnClick="TDMAddToCart('<?=$arPrice['PHID']?>')" title="<?=Lng('Add_to_cart',1,0)?>"><div class="text_cart_add">Купить</div></a>
							<? } ?>
						</td>
					</tr>
				<?}?>
				</table>
				<?
				if($arPart["PRICES_COUNT"]>$arResult['LIST_PRICES_LIMIT']){?>
			<a href="javascript:void(0)" OnClick="ShowMoreListPrices('<?=$arPart['PKEY']?>')" class="sbut sb<?=$arPart['PKEY']?>">&#9660; <b><u><?=Lng('Show_more_prices',1,0)?></u></b> (<?=($arPart["PRICES_COUNT"]-$arResult['LIST_PRICES_LIMIT'])?>)</a><?
				}
			}elseif($arResult['ALLOW_ORDER']==1){?>
				<a href="javascript:void(0)" class="tdorder" OnClick="TDMOrder('<?=$arPart['PKEY']?>')"><?=Lng('Order',1,0)?></a>
			<?}?>

		</td>
		</tr>
	<?}?>
</table>