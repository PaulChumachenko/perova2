<?if(!defined("TDM_PROLOG_INCLUDED") || TDM_PROLOG_INCLUDED!==true)die();
?>
<table class="tdlist">
	<tr class="head">
		<td>Производитель</td>
		<td></td>
		<td><?= Lng('Name',1,0)?></td>
		<td></td>
	</tr>
	<?
	foreach($arResult['PARTS'] as $NumKey => $arPart){
		if ($arPart['PKEY']=='') continue;
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
		} else {
			$Zoom='https://www.google.com/search?q='.$arPart['BRAND'].'+'.$arPart['ARTICLE'].'&tbm=isch'; $ZClass='';
			$PicText=Lng('Search_photo_in_google',1,0); $Target='target="_blank"';
		}
		if(TDM_ISADMIN AND $arPart['LINK_CODE']!=''){
			$BrandClass='linked';
			$BrLink = '<a href="/'.TDM_ROOT_DIR.'/admin/dbedit.php?selecttable=Y&table=TDM_LINKS&LINK='.$arPart['LINK_LEFT_AKEY'].'" target="_blank" class="ttip link" title="'.$arPart['LINK_INFO'].'<br>'.$arPart['LINK_CODE'].'"></a>';
		}else{$BrandClass=''; $BrLink='';}
		?>
		<tr class="cols">
			<td class="tdbrand">
				<a href="/<?=TDM_ROOT_DIR?>/search/<?=$arPart['AKEY']?>/<?=BrandNameEncode($arPart['BRAND'])?>" class="<?=$BrandClass?>"><?= !empty($arPart['PC_MANUFACTURER']) ? $arPart['PC_MANUFACTURER'] : $arPart['BRAND'] ?></a>
				<?=$BrLink?><br>
				<div class="ttip" <?if(TDM_ISADMIN){?>ttip" title="BKEY: <?=$arPart['BKEY']?><br>AKEY: <?=$arPart['AKEY']?><br>AID: <?=$arPart['AID']?><?}?>"><?= !empty($arPart['PC_SKU']) ? $arPart['PC_SKU'] : $arPart['ARTICLE'] ?></div>
				<?if($arPart['KIND']>0){?><span style="font-size:11px;"><?=TDMPrintArtKinde($arPart['KIND']);?></span><?}?>
			</td>
			<td width = "15%">
				<div class="tditem" id="item<?=$arPart['PKEY']?>">
				<?// Preview images: ?>
				<a href="<?=$Zoom?>" class="image <?=$ZClass?>" rel="img<?=$arPart['PKEY']?>" <?=$Target?> title="<?=$arPart['BRAND']?> <?=$arPart['ARTICLE']?>">
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
					foreach($arPart["IMG_ADDITIONAL"] as $AddImgSrc){ $AddF++;?><a href="<?=$AddImgSrc?>" class="cbx_imgs" rel="img<?=$arPart['PKEY']?>" title="<?=$arPart['BRAND']?> <?=$arPart['ARTICLE']?>"></a><?}
				}?>
			</td>
			<td>
				<b class="name" title="TecDoc name: <?=$arPart['TD_NAME']?>"><?=$arPart['NAME']?></b><br>
				<div class="criteria"><?=$arPart['CRITERIA']?></div>
				<div class="itemprops" id="props<?=$arPart['PKEY']?>">
					<?if($arPart["PROPS_COUNT"]>0){
						foreach($arPart['PROPS'] as $PName=>$PValue){?>
							<span class="criteria"><?=$PName?><?if($PValue!=''){?>: <?=$PValue?><?}else{?>.<?}?></span><br>
						<?}
					}?>
				</div>
				<?php if ($arPart["PROPS_COUNT"] > 3): ?>
					<a class="moreprops" href="javascript:void(0)" onClick="ShowMoreProps(this,'props<?=$arPart['PKEY']?>')">&#9660; <?=Lng('Show_more_properties',1,false)?> (<?=($arPart["PROPS_COUNT"]-3)?>)</a>
				<?php endif; ?>
			</td>
			<td style="width:40px; white-space:nowrap;" class="rigbord">
				<?if($arPart["AID"]>0){?><table class="propstb"><tr><td>
					<a href="/<?=TDM_ROOT_DIR?>/props.php?of=<?=$arPart["AID"]?>" class="dopinfo popup" title="<?=Lng('Additional_Information',1,0)?>"></a></td><td>
					<a href="javascript:void(0)" OnClick="AppWin('<?=TDM_ROOT_DIR?>',<?=$arPart["AID"]?>,980)" class="carsapp" title="<?=Lng('Applicability_to_model_cars',1,0)?>"></a></table>
				<?}?>
			</td>
		</tr>
	<?}?>
</table>