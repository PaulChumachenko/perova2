<?if(!defined("TDM_PROLOG_INCLUDED") || TDM_PROLOG_INCLUDED!==true)die();?>
<link rel="stylesheet" href="/<?=TDM_ROOT_DIR?>/media/js/colorbox/cmain.css" />
<script type="text/javascript" language="javascript" src="/<?=TDM_ROOT_DIR?>/media/js/colorbox/colorbox.js"></script>
<?jsLinkFormStyler()?>
<script>AddFSlyler('select');</script>
<?jsLinkJqueryUi()?>
<script> $(function() {  
	$(".popup").colorbox({rel:false, current:'', preloading:false, arrowKey:false, scrolling:false, overlayClose:false});
	$('.ttip').tooltip({ position:{my:"left+25 top+20"}, track:true, content:function(){return $(this).prop('title');}});   }); 
</script>

<div class="tclear"></div>
<h1><?=TDM_H1?></h1>
<?TDMShowBreadCumbs()?>
<?=TDMShowSEOText("TOP")?>

<?php if(count($arResult['PARTS']) > 0): ?>

	<?php if($arResult['PAGINATION']['TOTAL_PAGES'] > 1): ?>
		<?TDMShowPagination($arResult['PAGINATION'],Array(
			"PAGE_TEXT"=>"Y",
			"TOTAL_TEXT"=>Lng('Total_items',1,0),
			"PAGES_DIAPAZON"=>6,
		))?>
	<?php endif; ?>

	<div class="tclear"></div>

	<? // VIEWS
	if ($arResult['VIEW']=="CARD"){
		include('view_card.php');
	} elseif($arResult['VIEW']=="LIST"){
		include('view_list.php');
	} ?>

<?php else : ?>

	<br><br>
	<b><?=Lng('No_parts_for_model')?>...</b>
	<br><br><br><br>

<?php endif; ?>

<div class="tclear"></div>

<?php if($arResult['PAGINATION']['TOTAL_PAGES'] > 1 AND $arResult['PAGINATION']['ITEMS_ON_THIS_PAGE'] > 6): ?>
	<br>
	<?TDMShowPagination($arResult['PAGINATION'],Array(
		"PAGE_TEXT"=>"Y",
		"TOTAL_TEXT"=>Lng('Total_items',1,0),
		"PAGES_DIAPAZON"=>6,
	))?>
	<div class="tclear"></div>
	<hr>
<?php endif; ?>

<?=TDMShowSEOText("BOT")?><br><br>

<script>
	$(document).ready(function(){
		$(".cbx_imgs").colorbox({ current:'', innerWidth:900, innerHeight:600, onComplete:function(){$('.cboxPhoto').unbind().click($.colorbox.next);} });
		$(".cbx_chars").colorbox({rel:false, current:'', overlayClose:true, arrowKey:false, opacity:0.6});
	});
</script>