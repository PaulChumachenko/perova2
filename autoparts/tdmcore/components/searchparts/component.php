<?if(!defined("TDM_PROLOG_INCLUDED") || TDM_PROLOG_INCLUDED!==true)die();?>
<?
$SEARCH = TDMSingleKey($_REQUEST['article']);
if(strlen($SEARCH)<3){TDMRedirect();}


$TDMCore->DBSelect("TECDOC");
TDMSetTime();

//VIEW
if($_POST['VIEW']=='LIST'){$_SESSION['TDM_SEACH_DEFAULT_VIEW']=2;}
if($_POST['VIEW']=='CARD'){$_SESSION['TDM_SEACH_DEFAULT_VIEW']=1;}
if($_SESSION['TDM_SEACH_DEFAULT_VIEW']==0){$_SESSION['TDM_SEACH_DEFAULT_VIEW']=$arComSets['DEFAULT_VIEW'];}
if($_SESSION['TDM_SEACH_DEFAULT_VIEW']==1){$arResult['VIEW']="CARD";}else{$arResult['VIEW']="LIST";}
$arResult['LIST_PRICES_LIMIT']=$arComSets['LIST_PRICES_LIMIT'];
$arResult['ALLOW_ORDER']=$arComSets['ALLOW_ORDER'];
if($arResult['LIST_PRICES_LIMIT']<3){$arResult['LIST_PRICES_LIMIT']=2;}

//Group
if($TDMCore->UserGroup>1){
	$arResult['GROUP_NAME'] = $TDMCore->arPriceType[$TDMCore->UserGroup];
	$arResult['GROUP_DISCOUNT'] = $TDMCore->arPriceDiscount[$TDMCore->UserGroup];
	$arResult['GROUP_VIEW'] = $TDMCore->arPriceView[$TDMCore->UserGroup];
}

$arPARTS_noP = Array();
$arResult['ALL_BRANDS'] = Array();
$arResult['ALL_BRANDS_LETTERS'] = Array();


$rsArts = TDSQL::LookupByNumber($SEARCH);
while($arArts = $rsArts->Fetch()){ //AID, KIND, ARTICLE, BRAND, TD_NAME
	$BKEY = TDMSingleKey($arArts['BRAND'],true);
	$AKEY = TDMSingleKey($arArts['ARTICLE']);
	$arPARTS_noP[$BKEY.$AKEY] = Array(
		"PKEY"=>$BKEY.$AKEY,
		"BKEY"=>$BKEY,
		"AKEY"=>$AKEY,
		"AID"=>$arArts['AID'],
		"ARTICLE"=>$arArts['ARTICLE'],
		"BRAND"=>$arArts['BRAND'],
		"TD_NAME"=>$arArts['TD_NAME'],
		"NAME"=>$arArts['TD_NAME'], //As default
		"IMG_SRC"=>'/'.TDM_ROOT_DIR.'/media/images/nopic.jpg'
	);
	$arPAIDs_noP[] = $arArts['AID']; //For criteria & images
}
TDMSetTime('LookupByNumber(SEARCH) ## Not sorted TecDoc result items count - <b>'.count($arPARTS_noP)).'</b>';

$TDMCore->DBSelect("MODULE");
$rsDBPrices = new TDMQuery;
$rsDBPrices->SimpleSelect('SELECT * FROM TDM_PRICES WHERE AKEY="'.$SEARCH.'" ' . PcAlternativeArticles::getAlternativeQuery($SEARCH));
while($arPArts = $rsDBPrices->Fetch()){
	$arPArts['PKEY'] = $arPArts['BKEY'].$arPArts['AKEY'];
	if(!is_array($arPARTS_noP[$arPArts['PKEY']])){
		$arPARTS_noP[$arPArts['PKEY']] = Array(
			"PKEY"=>$arPArts['PKEY'],
			"BKEY"=>$arPArts['BKEY'],
			"AKEY"=>$arPArts['AKEY'],
			"ARTICLE"=>$arPArts['ARTICLE'],
			"BRAND"=>$arPArts['BRAND'],
			"TD_NAME"=>$arPArts['ALT_NAME'],
			"NAME"=>$arPArts['ALT_NAME'],
			"IMG_SRC"=>'/'.TDM_ROOT_DIR.'/media/images/nopic.jpg'
		);
	}
}

if(count($arPARTS_noP)>0){
	$arPAIDs_noP_cnt = count($arPAIDs_noP); 
	
	
	//Webservers (CAHCE)
	/////////////////////////////
	$WS = new TDMWebservers;
	$WS->SearchPrices($arPARTS_noP,Array(),Array("CACHE_MODE"=>true,"PKEY"=>$SEARCH)); //Сработают только WS с включенным кэшированием
	
	//Sorting defines
	/////////////////////////////
	if($_SESSION['TDM_SEACH_SORTING']<=0){$_SESSION['TDM_SEACH_SORTING']=$arComSets['ITEMS_SORT'];}
	if($_POST['SORT']>0){ //Users switch
		$arAvailSortModes = Array(1,2,3,4,5);
		if(in_array($_POST['SORT'],$arAvailSortModes)){$_SESSION['TDM_SEACH_SORTING']=$_POST['SORT'];}
	}
	$arResult['SORT'] = $_SESSION['TDM_SEACH_SORTING'];
	
	//Prices
	/////////////////////////////
	$arResult['PRICES']=Array(); $arMinPrices=Array(); $arMinDays=Array();
	if(count($arPARTS_noP)>0){
		$rsDBPrices = new TDMQuery;
		if($arResult['GROUP_VIEW']==1){$GROUP_FILTER=' AND TYPE='.$TDMCore->UserGroup;}
		foreach($arPARTS_noP as $arTPart){
			$PrcsSQL.=$PUnion.'SELECT * FROM TDM_PRICES WHERE BKEY="'.$arTPart['BKEY'].'" AND AKEY="'.$arTPart['AKEY'].'" '.$GROUP_FILTER; $PUnion = ' UNION ';
		}
		switch($_SESSION['TDM_SEACH_SORTING']){
			case 1: $PrSort='PRICE ASC'; break; 	//По рейтингу бренда и наличию цены
			case 2: $PrSort='PRICE ASC'; break; 	//По наличию описания и цены
			case 3: $PrSort='PRICE ASC'; break; 	//По минимальной цене
			case 4: $PrSort='DAY ASC'; break;		//По минимальному сроку доставки
			case 5: $PrSort='PRICE ASC'; break;		//По наличию фото
		}
		$rsDBPrices->SimpleSelect($PrcsSQL.' ORDER BY '.$PrSort);
		$arNmC=Array(); $PrCnt=0;
		while($arPrice = $rsDBPrices->Fetch()){
			if($arComSets['HIDE_PRICES_NOAVAIL']==1 AND $arPrice['AVAILABLE']<1){continue;}
			$PrCnt++;
			$PrPKey = $arPrice['BKEY'].$arPrice['AKEY'];
			if(trim($arPrice['ALT_NAME'])!=''){
				//Clear TecDoc default NAME
				if(!in_array($PrPKey,$arNmC)){
					$arPARTS_noP[$PrPKey]["NAME"] = ''; 
					$arNmC[] = $PrPKey;
				}
				//Longest NAME of prices records
				if(strlen($arPARTS_noP[$PrPKey]["NAME"]) < strlen($arPrice['ALT_NAME'])){ $arPARTS_noP[$PrPKey]["NAME"]=$arPrice['ALT_NAME']; }
			}
			$arPrice = TDMFormatPrice($arPrice);
			$arResult['PRICES'][$PrPKey][] = $arPrice;
			$arPARTS_noP[$PrPKey]["PRICES_COUNT"]++;
			//Minimal prices for sorting
			if($arMinPrices[$PrPKey]==0 OR $arPrice['PRICE_CONVERTED']<$arMinPrices[$PrPKey]){$arMinPrices[$PrPKey]=$arPrice['PRICE_CONVERTED'];}
			//Minimal DAY for sorting
			if($arMinDays[$PrPKey]=='' OR $arPrice['DAY']<$arMinDays[$PrPKey]){ $arMinDays[$PrPKey] = ($arPrice['DAY']+1);} //Что бы товары без цен были всёравно в конце даже если дней доставки =0
			//Brands min price
			if($arResult['AB_MIN_PRICE'][$arPrice['BKEY']]>$arPrice['PRICE_CONVERTED'] OR $arResult['AB_MIN_PRICE'][$arPrice['BKEY']]==0){
				$arResult['AB_MIN_PRICE'][$arPrice['BKEY']] = $arPrice['PRICE_CONVERTED'];
				$arResult['AB_MIN_PRICE_F'][$arPrice['BKEY']] = $arPrice['PRICE_CONVERTED'];
			}
		}
		unset($arNmC);
		TDMSetTime('SelectPricesQuery(PARTS) ## For all selected '.count($arPARTS_noP).' items  - returned prices count <b>'.$PrCnt.'</b>');
	}
	
	//AppCriteria - Left/righr - filter
	/////////////////////////////
	$TDMCore->DBSelect("TECDOC");
	TDMSetTime('DBSelect(TECDOC)');
	
	//Img avail. for sorting
	$arPImgAvail=Array();
	if($arPAIDs_noP_cnt>0){
		$arPImgAvail = TDSQL::ImagesAvialable($arPAIDs_noP);
		TDMSetTime('ImagesAvialable(arPAIDs_noP) ## All selected '.$arPAIDs_noP_cnt.' items  - returned rows count <b>'.count($arPImgAvail).'</b>');
	}
	
	//Characteristics 1
	if($arPAIDs_noP_cnt>0 AND $arComSets['SHOW_ITEM_PROPS']==1 AND $arResult['VIEW']=="LIST"){
		$rsProps = TDSQL::GetPropertysUnion($arPAIDs_noP);
		TDMSetTime('GetPropertysUnion(PAIDs) ## For items count - '.$arPAIDs_noP_cnt);
		foreach($arPARTS_noP as $PKey=>$arTPart){
			$ar_AID[$PKey] = $arTPart['AID'];
			$ar_PKEY[$arTPart['AID']] = $PKey;
		}
		$arHiddenProps=Array(1073); //Спецификация (один ключ и масса моделей авто)
		while($arProp = $rsProps->Fetch()){
			if($arProp['VALUE']!=''){
				if($arProp['CRID']==836 OR $arProp['CRID']==596){ //Дополнительный артикул / Доп. информация
					$arProp['NAME']=$arProp["VALUE"]; $arProp["VALUE"]='';
				}
				if(in_array($arProp["AID"],$ar_AID) AND !isset($arPARTS_noP[$ar_PKEY[$arProp['AID']]]["PROPS"][$arProp['NAME']])){
					$arPARTS_noP[$ar_PKEY[$arProp['AID']]]["PROPS_COUNT"]++;
					$arPARTS_noP[$ar_PKEY[$arProp['AID']]]["PROPS"][$arProp['NAME']] = $arProp["VALUE"];
				}
			}
		}
		TDMSetTime('GetPropertysUnion(PAIDs) ## Processing result');
	}
	
	
	//Sorting
	/////////////////////////////
	foreach($arPARTS_noP as $PKEY=>$arTPart){
		$SortNum=999999999; //as default
		if($arResult['SORT']==1){ //По наличию цены и рейтингу бренда
			if($arTPart["PRICES_COUNT"]>0){$SortNum=999;}
		}elseif($arResult['SORT']==2){ //Наличию описания и цены
			if($arTPart["PRICES_COUNT"]>0){$SortNum=999;}
			if(in_array($arTPart["AID"],$arPImgAvail)){$SortNum=$SortNum-100;}
			if($arPARTS_noP[$PKEY]["PROPS_COUNT"]>0){$SortNum=$SortNum-$arPARTS_noP[$PKEY]["PROPS_COUNT"];}
		}elseif($arResult['SORT']==3){ //По минимальной цене
			if($arMinPrices[$PKEY]>0){$SortNum = $arMinPrices[$PKEY];}
		}elseif($arResult['SORT']==4){ //По минимальному сроку доставки
			if($arMinDays[$PKEY]>0){$SortNum = $arMinDays[$PKEY];}
		}elseif($arResult['SORT']==5){ //По наличию фото (Текдока) + добавленных своих фото сделать!
			if(in_array($arTPart["AID"],$arPImgAvail)){$SortNum=1;}
		}
		$arSortKeys[] = $SortNum;
	}
	array_multisort($arSortKeys,$arPARTS_noP);
	
	//No Pagination
	/////////////////////////////
	$arPARTS = $arPARTS_noP;
	$arPAIDs = $arPAIDs_noP;
	$arPAIDs_cnt = count($arPAIDs);		
	
	//Clear no page pices
	/////////////////////////////
							
						
	//Characteristics 2
	if($arComSets['SHOW_ITEM_PROPS']==1 AND $arResult['VIEW']=="LIST"){
		foreach($arPARTS as $PKey=>$arTPart){
			if(count($arTPart["PROPS"])>0){
				$arCProps = $arTPart["PROPS"];
				$arPARTS[$PKey]["PROPS"]=Array();
				foreach($arCProps as $PName=>$PValue){
					$PName = str_replace('/мм?','/мм²',$PName);
					$PName = str_replace('? ','Ø ',$PName);
					if(strpos($PName,'[')>0){
						$Dim = substr($PName,strpos($PName,'['));
						$PName = str_replace(' '.$Dim,'',$PName);
						$Dim = str_replace('[','',$Dim); $Dim = str_replace(']','',$Dim);
						$PValue = $PValue.' '.$Dim;
					}
					$arPARTS[$PKey]["PROPS"][UWord($PName)] = $PValue;
				}
			}
		}
	}
	
	
	//TecDoc Images
	/////////////////////////////
	$arResult['ART_LOGOS'] = Array();
	if($arPAIDs_cnt>0){
		$rsImages = TDSQL::GetImagesUnion($arPAIDs);
		TDMSetTime('GetImagesUnion(PAIDs) ## For items count - '.$arPAIDs_cnt);
		while($arImage = $rsImages->Fetch()){ //AID, PATH
			foreach($arPARTS as $PKey=>$arTPart){
				if($arTPart['AID']==$arImage["AID"] AND !strpos($arImage["PATH"],'0/0.jpg')){
					if($arPARTS[$PKey]["IMG_ZOOM"]==''){
						$arPARTS[$PKey]["IMG_SRC"]='http://'.TECDOC_FILES_PREFIX.$arImage["PATH"];
						$arPARTS[$PKey]["IMG_ZOOM"]='Y';
						$arPARTS[$PKey]["IMG_FROM"]='TecDoc';
					}else{//Additional images
						$arPARTS[$PKey]["IMG_ADDITIONAL"][]='http://'.TECDOC_FILES_PREFIX.$arImage["PATH"];
					}
					break;
				}
			}
		}
		// Brands LOGO
		$rsBLogos = TDSQL::GetArtsLogoUnion($arPAIDs);
		TDMSetTime('GetArtsLogoUnion(PAIDs) ## For items count - '.$arPAIDs_cnt);
		while($arBLogos = $rsBLogos->Fetch()){ //AID, PATH
			$arResult['ART_LOGOS'][$arBLogos['AID']] = 'http://'.TECDOC_FILES_PREFIX.$arBLogos['PATH'];
		}
	}
	
	
	foreach($arPARTS as $arPart){$SEO_PARTS_LIST.=$arPart['BRAND'].' '.$arPart['ARTICLE'].', ';}
	
	$arResult['PARTS'] = Array();
	$arResult['PARTS'] = $arPARTS;

	$arResult = (new PcPartsListProcessor($arResult, $TDMCore))->runPostProcessing()->flushPartsWithoutPrices()->getList();

	$arResult['ADDED_PHID'] = TDMPerocessAddToCart($arResult['PRICES'],$arResult['PARTS']);
}

//SEO-Meta
SetComMeta("SEARCHPARTS",Array("PARTS_LIST"=>$SEO_PARTS_LIST,"SEARCH_NUMBER"=>$SEARCH));


//echo '<pre>'; print_r($arPARTS_noP); echo '</pre>'; die();

?>