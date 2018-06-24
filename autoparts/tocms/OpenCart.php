<?if(!defined("TDM_PROLOG_INCLUDED") || TDM_PROLOG_INCLUDED!==true)die();

//User group price type - from 1 to 5
global $TDMCore;
if(!TDM_ISADMIN){
	$arPGID = $TDMCore->arPriceGID;
	foreach($arPGID as $TDM_GID=>$CMS_GID){
		if($_SESSION['TDM_CMS_USER_GROUP']==$CMS_GID){
			if($_SESSION['TDM_USER_GROUP']!=$TDM_GID){$_SESSION['TDM_USER_GROUP']=$TDM_GID; Header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);}
			break;
		} 
	}
}
 
//Lang & Curr
if($_SESSION['default']['language']!='' AND TDM_LANG!=$_SESSION['default']['language'] AND in_array($_SESSION['default']['language'],$TDMCore->arLangs)){
	$_SESSION['TDM_LANG']=$_SESSION['default']['language'];
	TDMRedirect($_SERVER['REQUEST_URI']);
}
if($_SESSION['default']['currency']!='' AND TDM_CUR!=$_SESSION['default']['currency']){
	$_SESSION['TDM_CUR']=$_SESSION['default']['currency'];
	TDMRedirect($_SERVER['REQUEST_URI']);
}


//Add to cart
if(defined('TDM_ADD_TO_CART') AND TDM_ADD_TO_CART){
	global $arCartPrice;
	if(is_array($arCartPrice)){
		if($_REQUEST['QTY']>1){$QUANTITY=intval($_REQUEST['QTY']);}else{$QUANTITY=1;}
		if(isset($_SESSION['cart']) AND isset($_SESSION['cart'][$arCartPrice['CPID']])){
			$_SESSION['cart'][$arCartPrice['CPID']]['quantity'] = $_SESSION['cart'][$arCartPrice['CPID']]['quantity']+$QUANTITY;
		}else{
			$OC_CURRENCY = $_SESSION['TDM_CMS_DEFAULT_CUR'];
			if($OC_CURRENCY==''){$OC_CURRENCY=TDM_CUR;}
			
			if($arCartPrice['OPTIONS']['MINIMUM']>1 AND $QUANTITY<$arCartPrice['OPTIONS']['MINIMUM']){$QUANTITY=$arCartPrice['OPTIONS']['MINIMUM'];}
			$arOCBasket = array();
			$arOCBasket['tecdoc'] = "Y";
			//Defaults
			$arOCBasket['product_id'] = $arCartPrice['CPID'];
			$arOCBasket['cart_id'] = $arOCBasket['product_id'];
			$arOCBasket['tax_class_id'] = 1;
			$arOCBasket['recurring'] = false;
			$arOCBasket['shipping'] = 1;
			$arOCBasket['reward'] = 0;
			$arOCBasket['stock'] = true;
			$arOCBasket['download'] = Array();
			$arOCBasket['subtract'] = false;
			$arOCBasket['model'] = $arCartPrice['BRAND'];
			//Fields
			$arOCBasket['price'] = TDMConvertPrice($arCartPrice['CURRENCY_CONVERTED'],$OC_CURRENCY,$arCartPrice['PRICE_CONVERTED']); 
			$arOCBasket['quantity'] = $QUANTITY;
			//$arOCBasket['stock'] = $arCartPrice['AVAILABLE'];
			$arOCBasket['name'] = $arCartPrice['NAME'];
			$arOCBasket['image'] = $arCartPrice['IMG_SRC'];
			// PC: Replace `BRAND` with `PC_MANUFACTURER` value
			$arOCBasket['brand'] = $arCartPrice['PC_MANUFACTURER'];
			$arOCBasket['product_url'] = $arCartPrice['ADD_URL'];
			$arOCBasket['day'] = $arCartPrice['DAY'];
			$arOCBasket['article'] = $arCartPrice['ARTICLE'];
			//Minimum
			$arOCBasket['minimum'] = 1;
			if($arCartPrice['OPTIONS']['MINIMUM']>0){ $arOCBasket['minimum']=$arCartPrice['OPTIONS']['MINIMUM']; }
			//Weight
			$arOCBasket['weight'] = '';
			$arOCBasket['weight_prefix'] = '';
			$arOCBasket['weight_class_id'] = 2; //1-Kg. 2-Gr
			if($arCartPrice['OPTIONS']['WEIGHT']>0){ $arOCBasket['weight']=$arCartPrice['OPTIONS']['WEIGHT']; }
			//Points
			$arOCBasket['points'] = '';
			$arOCBasket['points_prefix'] = Lng('Pcs',1,false).'.';
			if($arCartPrice['OPTIONS']['SET']>0){ $arOCBasket['points']=$arCartPrice['OPTIONS']['SET']; }
			//Options
			$arOCBasket['pre_option'][] = Array('name'=>Lng('Article',1,false),'value'=>$arCartPrice['ARTICLE'],'type'=>'text');
			//$arOCBasket['pre_option'][] = Array('name'=>Lng('Supplier',1,false),'value'=>$arCartPrice['SUPPLIER_STOCK'],'type'=>'text');
			$arOCBasket['pre_option'][] = Array('name'=>Lng('Dtime_delivery',1,false),'value'=>$arCartPrice['DAY'],'type'=>'text');
			$arOCBasket['pre_option'][] = Array('name'=>Lng('Availability',1,false),'value'=>$arCartPrice['AVAILABLE'],'type'=>'text');
			//$arOCBasket['pre_option'][] = Array('name'=>'Price','value'=>$arCartPrice['PRICE'].' '.$arCartPrice['CURRENCY'],'type'=>'text');
			//$arOCBasket['pre_option'][] = Array('name'=>'Date','value'=>$arCartPrice['DATE_FORMATED'],'type'=>'text');
			//$arOCBasket['pre_option'][] = Array('name'=>'Code','value'=>$arCartPrice['CODE'],'type'=>'text');
			if(is_array($arCartPrice['OPTIONS']) AND count($arCartPrice['OPTIONS'])>0){
				foreach($arCartPrice['OPTIONS'] as $OpCode=>$OpValue){
					$OpName = $arCartPrice['OPTIONS_NAMES'][$OpCode];
					if($OpName==''){$OpName=$OpCode;}
					$arOCBasket['pre_option'][] = Array('name'=>$OpName,'value'=>$OpValue,'type'=>'text');
				}
			}
			foreach($arOCBasket['pre_option'] as $arPreOp){
				$arPreOp['product_option_id']=0;
				$arPreOp['product_option_value_id']=0;
				$arPreOp['option_id']=0;
				$arPreOp['option_value_id']=0;
				$arOCBasket['option'][] = $arPreOp;
			}
			$_SESSION['cart'][$arCartPrice['CPID']] = $arOCBasket;
		}
	}
}
//unset($_SESSION['cart']);

//Login in module if OC admin
if($_SESSION['TDM_ISADMIN']!="Y"){
	if(isset($_SESSION['user_id']) AND $_SESSION['user_id']>0 AND strlen($_SESSION['token'])==32){
		//$_SESSION['TDM_ISADMIN']="Y";
	}
}



//Header & footer OC	
$arMPath = explode('/',TDM_ROOT_DIR);
if(count($arMPath)>1){$PrePath=$arMPath[0].'/';}else{$PrePath='';}
$_GET['route']='common/tecdoc_module';
chdir($_SERVER["DOCUMENT_ROOT"].'/'.$PrePath);
include($_SERVER["DOCUMENT_ROOT"].'/'.$PrePath.'index.php');

?>