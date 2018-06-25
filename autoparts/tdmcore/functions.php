<?php

function TDMOptionsImplode($arOps, $arPrice = array()) {
	$SET = intval($arOps["SET"]);
	$WEIGHT = intval($arOps["WEIGHT"]);
	$USED = intval($arOps["USED"]);
	$RESTORED = intval($arOps["RESTORED"]);
	$DAMAGED = intval($arOps["DAMAGED"]);
	$NORETURN = intval($arOps["NORETURN"]);
	$COPY = intval($arOps["COPY"]);
	$HOT = intval($arOps["HOT"]);
	$OptionDay = TDMOptionNumbers($arPrice["DAY"]);
	$OptionAvail = TDMOptionNumbers($arPrice["AVAILABLE"]);
	$PRICE_ID = intval($arOps["PRICE_ID"]);
	$PERCENTGIVE = intval($arOps["PERCENTGIVE"]);
	$MINIMUM = intval($arOps["MINIMUM"]);
	$LITERS = floatval(str_replace(",", ".", $arOps["LITERS"]));
	$Res = $SET . ";" . $WEIGHT . ";" . $USED . ";" . $RESTORED . ";" . $DAMAGED . ";" . $NORETURN . ";" . $COPY . ";" . $HOT . ";" . $OptionDay . ";" . $OptionAvail . ";" . $PRICE_ID . ";" . $PERCENTGIVE . ";" . $MINIMUM . ";" . $LITERS;
	return $Res;
}

function TDMGetPriceOptions($OPTIONS) {
	$arOps = explode(";", $OPTIONS);
	$arRes["SET"] = $arOps[0];
	list(, $arRes["WEIGHT"]) = $arOps;
	list(, , $arRes["USED"]) = $arOps;
	list(, , , $arRes["RESTORED"]) = $arOps;
	list(, , , , $arRes["DAMAGED"]) = $arOps;
	list(, , , , , $arRes["NORETURN"]) = $arOps;
	list(, , , , , , $arRes["COPY"]) = $arOps;
	list(, , , , , , , $arRes["HOT"]) = $arOps;
	list(, , , , , , , , $arRes["DAY_TEMPLATE"]) = $arOps;
	list(, , , , , , , , , $arRes["AVAILABLE_TEMPLATE"]) = $arOps;
	list(, , , , , , , , , , $arRes["PRICE_ID"]) = $arOps;
	list(, , , , , , , , , , , $arRes["PERCENTGIVE"]) = $arOps;
	list(, , , , , , , , , , , , $arRes["MINIMUM"]) = $arOps;
	list(, , , , , , , , , , , , , $arRes["LITERS"]) = $arOps;
	return $arRes;
}

function TDMFormatOptions($OPTIONS) {
	global $TDMCore;
	$arOpts = tdmgetpriceoptions($OPTIONS);
	$arOpts["VIEW_INTAB"] = "<table class=\"opstab\"><tr>";
	$arVOps = array("SET", "WEIGHT", "USED", "RESTORED", "NORETURN", "DAMAGED", "COPY", "HOT", "PRICE_ID", "PERCENTGIVE", "MINIMUM", "LITERS");
	foreach ($arVOps as $VOp) {
		if (!($TDMCore->arSettings["OPTION_" . $VOp] || defined("TDM_ADMIN_SIDE"))) {
			continue;
		}
		if (!(0 < $arOpts[$VOp])) {
			continue;
		}
		$arOpts[$VOp . "_VIEW"] = TDMOptionView($VOp, $arOpts[$VOp]);
		$arOpts["VIEW"] .= $arOpts[$VOp . "_VIEW"];
		$arOpts["VIEW_INTAB"] .= "<td>" . $arOpts[$VOp . "_VIEW"] . "</td>";
	}
	$arOpts["VIEW_INTAB"] .= "</tr></table>";
	return $arOpts;
}

function TDMOptionView($CODE, $VALUE, $NAME = "") {
	$CODE = str_replace("*", "", $CODE);
	$arCodeOps = array("SET", "WEIGHT", "USED", "RESTORED", "NORETURN", "DAMAGED", "COPY", "HOT", "PRICE_ID", "PERCENTGIVE", "MINIMUM", "LITERS");
	if (in_array($CODE, $arCodeOps)) {
		if ($CODE == "USED") {
			if ($NAME != "") {
				$Text = $NAME;
			}
			else {
				$Text = Lng("PRICE_OPTION_" . $CODE, 1, false);
			}
		}
		else {
			if ($CODE == "SET") {
				if ($NAME != "") {
					$arSt = explode("(", $NAME);
					$Text = $VALUE . " (" . $arSt[1];
				}
				else {
					$Text = $VALUE . " " . Lng("Pcs", 0, false);
				}
			}
			else {
				if ($CODE == "WEIGHT") {
					$Text = round($VALUE / 1000, 1) . Lng("Kg", 1, false);
				}
				else {
					if ($CODE == "PERCENTGIVE") {
						$Text = $VALUE . "%";
					}
					else {
						if ($CODE == "MINIMUM") {
							$Text = "min." . $VALUE;
						}
						else {
							if ($CODE == "LITERS") {
								$Text = $VALUE . Lng("L.", 0, false);
							}
						}
					}
				}
			}
		}
		if ($NAME != "") {
			$Title = $NAME;
		}
		else {
			$Title = Lng("PRICE_OPTION_" . $CODE, 1, 0);
		}
		return "<div class=\"option_" . $CODE . " ttip\" title=\"" . $Title . "\">" . $Text . "</div>";
	}
	return false;
}

function TDMPerocessAddToCart($arALLPRICES, $arPARTS) {
	if (isset($_POST["PHID"]) && strlen($_POST["PHID"]) == 32) {
		foreach ($arALLPRICES as $PKEY => $arPrcs) {
			foreach ($arPrcs as $arPrice) {
				if (!($arPrice["PHID"] == $_POST["PHID"])) {
					continue;
				}
				global $TDMCore;
				$arPart = $arPARTS[$arPrice["BKEY"] . $arPrice["AKEY"]];
				unset($arPrice["INFO"]);
				unset($arPrice["EDIT_LINK"]);
				if ($arPrice["ALT_NAME"] == "") {
					$arPrice["NAME"] = $arPart["NAME"];
				}
				else {
					$arPrice["NAME"] = $arPrice["ALT_NAME"];
				}
				unset($arPrice["ALT_NAME"]);
				$arPrice["IMG_SRC"] = $arPart["IMG_SRC"];
				$arPrice["ADD_URL"] = $_SERVER["REQUEST_URI"];
				$arPrice["DETAIL_URL"] = $arPrice["ADD_URL"];
				$arPrice["DATE_FORMATED"] = date("j.n.y", $arPrice["DATE"]);
				$arPrice["CPID"] = substr(filter_var($arPrice["PHID"], FILTER_SANITIZE_NUMBER_INT), 0, 9);
				$arPrice["TYPE_NAME"] = $TDMCore->arPriceType[$arPrice["TYPE"]];
				if ($arPrice["STOCK"] != "") {
					$arPrice["SUPPLIER_STOCK"] = $arPrice["SUPPLIER"] . " (" . $arPrice["STOCK"] . ")";
				}
				else {
					$arPrice["SUPPLIER_STOCK"] = $arPrice["SUPPLIER"];
				}
				foreach ($arPrice["OPTIONS"] as $OpName => $OpValue) {
					if ($OpName != "VIEW" && strpos($OpName, "_") <= 0) {
						if ($OpValue == "" || $OpValue == "0") {
							unset($arPrice["OPTIONS"][$OpName]);
							continue;
						}
						$arPrice["OPTIONS_NAMES"][$OpName] = Lng("PRICE_OPTION_" . $OpName, 1, 0);
						continue;
					}
					unset($arPrice["OPTIONS"][$OpName]);
				}
				define("TDM_ADD_TO_CART", true);
				global $arCartPrice;
				$arCartPrice = $arPrice;
				return $arPrice["PHID"];
			}
		}
	}
	return false;
}

function TDMFormatPrice($arPrice, $CURRENCY_TEMPLATE = 0) {
	$arPrice = TDMSetPriceInfo($arPrice);
	if (0 < $arPrice["PRICE"]) {
		global $TDMCore;
		if ($arPrice["CURRENCY"] == "") {
			$arPrice["CURRENCY"] = $TDMCore->arSettings["DEFAULT_CURRENCY"];
		}
		if ($arPrice["CURRENCY"] != TDM_CUR) {
			if (0 < $TDMCore->arCurs[TDM_CUR]["RATE"] && 0 < $TDMCore->arCurs[$arPrice["CURRENCY"]]["RATE"]) {
				$arPrice["PRICE_CONVERTED"] = TDMConvertPrice($arPrice["CURRENCY"], TDM_CUR, $arPrice["PRICE"]);
				$arPrice["CURRENCY_CONVERTED"] = TDM_CUR;
			}
			else {
				if (TDM_ISADMIN) {
					ErAdd("Error! Cant convert from " . $arPrice["CURRENCY"] . " to " . TDM_CUR . " - <a href=\"/" . TDM_ROOT_DIR . "/admin/curs.php\" target=\"_blank\">rates</a> not specified");
				}
				$arPrice["PRICE_CONVERTED"] = $arPrice["PRICE"];
				$arPrice["CURRENCY_CONVERTED"] = $arPrice["CURRENCY"];
				$CURRENCY_TEMPLATE = 1;
			}
		}
		else {
			$arPrice["PRICE_CONVERTED"] = $arPrice["PRICE"];
			$arPrice["CURRENCY_CONVERTED"] = $arPrice["CURRENCY"];
		}
		$TRUNCATE = $TDMCore->arCurs[$arPrice["CURRENCY_CONVERTED"]]["TRUNCATE"];
		if (1 < $TDMCore->UserGroup) {
			if ($TDMCore->arPriceView[$TDMCore->UserGroup] == 2) {
				$PrDisc = $TDMCore->arPriceDiscount[$TDMCore->UserGroup];
				$arPrice["DISCOUNT"] = $PrDisc;
				$arPrice["DISCOUNT_GROUP"] = $TDMCore->arPriceType[$TDMCore->UserGroup];
				if ($arPrice["DISCOUNT"] < 0) {
					$arPrice["DISCOUNT"] = $arPrice["DISCOUNT"] * -1;
				}
				$arPrice["DISCOUNT_PRICE"] = $arPrice["PRICE_CONVERTED"];
				if (TDM_ISADMIN) {
					$arPrice["INFO"] .= "<br><hr><b>" . $PrDisc . "</b>% " . $TDMCore->arPriceType[$TDMCore->UserGroup];
				}
				$arPrice["PRICE_CONVERTED"] = $arPrice["PRICE_CONVERTED"] + $arPrice["PRICE_CONVERTED"] / 100 * $PrDisc;
				$arPrice["DISCOUNT_PRICE"] = round($arPrice["DISCOUNT_PRICE"] - $arPrice["PRICE_CONVERTED"], 2);
				if (TDM_ISADMIN) {
					$arPrice["INFO"] .= "<br>" . $arPrice["DISCOUNT_PRICE"] * -1 . " " . $arPrice["CURRENCY_CONVERTED"];
				}
			}
		}
		if ($arPrice["CURRENCY_CONVERTED"] == "BYR") {
			$arPrice["PRICE_CONVERTED"] = ceil($arPrice["PRICE_CONVERTED"] / 50) * 50;
		}
		else {
			if ($TRUNCATE == 2) {
				$arPrice["PRICE_CONVERTED"] = floor($arPrice["PRICE_CONVERTED"]);
			}
			else {
				if ($TRUNCATE == 3) {
					$arPrice["PRICE_CONVERTED"] = round($arPrice["PRICE_CONVERTED"], 0);
				}
				else {
					if ($TRUNCATE == 4) {
						$arPrice["PRICE_CONVERTED"] = ceil($arPrice["PRICE_CONVERTED"]);
					}
				}
			}
		}
		$arPrice["PRICE_FORMATED"] = number_format($arPrice["PRICE_CONVERTED"], 2, ",", " ");
		$arPrice["PRICE_FORMATED"] = str_replace(",00", "", $arPrice["PRICE_FORMATED"]);
		if ($CURRENCY_TEMPLATE == 1) {
			$arPrice["PRICE_FORMATED"] = str_replace("#", $arPrice["PRICE_FORMATED"], $TDMCore->arCurs[$arPrice["CURRENCY_CONVERTED"]]["TEMPLATE"]);
		}
		if (TDM_ISADMIN && $arPrice["ONLINE"] != "Y") {
			$arPrice["EDIT_LINK"] = "/" . TDM_ROOT_DIR . "/admin/dbedit_price.php?BKEY=" . $arPrice["BKEY"] . "&AKEY=" . $arPrice["AKEY"] . "&TYPE=" . $arPrice["TYPE"] . "&DAY=" . urlencode($arPrice["DAY"]) . "&SUPPLIER=" . urlencode($arPrice["SUPPLIER"]) . "&STOCK=" . urlencode($arPrice["STOCK"]);
		}
		$arPrice["PHID"] = md5($arPrice["BKEY"] . $arPrice["AKEY"] . $arPrice["TYPE"] . $arPrice["PRICE"] . $arPrice["DAY"] . $arPrice["SUPPLIER"] . $arPrice["STOCK"] . $arPrice["OPTIONS"]);
		$arPrice["OPTIONS"] = tdmformatoptions($arPrice["OPTIONS"]);
		$arPrice["AVAILABLE_NUM"] = intval($arPrice["AVAILABLE"]);
		if (strpos($arPrice["OPTIONS"]["AVAILABLE_TEMPLATE"], "#") !== false) {
			$arPrice["AVAILABLE"] = str_replace("#", $arPrice["AVAILABLE"], $arPrice["OPTIONS"]["AVAILABLE_TEMPLATE"]);
		}
		else {
			if ($arPrice["OPTIONS"]["AVAILABLE_TEMPLATE"] != "" && $arPrice["AVAILABLE"] == 0) {
				$arPrice["AVAILABLE"] = $arPrice["OPTIONS"]["AVAILABLE_TEMPLATE"];
			}
		}
		if (strpos($arPrice["OPTIONS"]["DAY_TEMPLATE"], "#") !== false) {
			$arPrice["DAY"] = str_replace("#", $arPrice["DAY"], $arPrice["OPTIONS"]["DAY_TEMPLATE"]);
		}
		else {
			if ($arPrice["OPTIONS"]["DAY_TEMPLATE"] != "" && $arPrice["DAY"] == 0) {
				$arPrice["DAY"] = $arPrice["OPTIONS"]["DAY_TEMPLATE"];
			}
		}
	}
	if (!(isset($arPrice["OPTIONS"]["VIEW_INTAB"]))) {
		$arPrice["OPTIONS"]["VIEW_INTAB"] = "";
	}
	return $arPrice;
}

function TDMSetPriceInfo($arPrice) {
	global $TDMCore;
	$arPrice["INFO"] = "<b>" . $arPrice["BRAND"] . "</b> " . $arPrice["ARTICLE"];
	if (trim($arPrice["ALT_NAME"]) != "") {
		$arPrice["INFO"] .= "<hr>" . $arPrice["ALT_NAME"];
	}
	if (TDM_ISADMIN) {
		if (0 < $arPrice["DATE"]) {
			$IMPORT_DATE = date("j.n.y", $arPrice["DATE"]);
		}
		else {
			$IMPORT_DATE = "";
		}
		$arPrice["INFO"] .= "<br><br><strong>" . $arPrice["PRICE"] . "</strong> " . $arPrice["CURRENCY"] . " - " . $TDMCore->arPriceType[$arPrice["TYPE"]] . "<br>" . $arPrice["SUPPLIER"] . " (" . $arPrice["STOCK"] . ")<br><hr>" . Lng("Price_code", 0, 0) . ": " . $arPrice["CODE"] . "<br>" . Lng("Price_date", 0, 0) . ": " . $IMPORT_DATE;
	}
	$arPrice["INFO"] = str_replace("\"", "", $arPrice["INFO"]);
	return $arPrice;
}

function TDMDayNumbers($VAL) {
	if (0 < strpos($VAL, "-")) {
		$VAL = substr($VAL, 0, strpos($VAL, "-"));
	}
	$VAL = preg_replace("/[^0-9]/", "", $VAL);
	return intval($VAL);
}

function TDMOnlyNumbers($VAL) {
	$VAL = preg_replace("/[^0-9]/", "", $VAL);
	return intval($VAL);
}

function TDMOptionNumbers($VAL) {
	$VAL = str_replace("#", "", $VAL);
	$VAL = str_replace(";", "", $VAL);
	if (0 < strpos($VAL, "-")) {
		$toV = substr($VAL, strpos($VAL, "-"));
		$VAL = substr($VAL, 0, strpos($VAL, "-"));
	}
	$VAL = preg_replace("/[0-9]+/", "#", $VAL);
	if ($VAL == "#" && $toV == "") {
		$VAL = "";
	}
	return $VAL . $toV;
}

function TDMClearName($VAL) {
	$VAL = str_replace("\"", "", $VAL);
	$VAL = str_replace("'", "", $VAL);
	return $VAL;
}

function TDMConvertPrice($FROM, $TO, $Value) {
	if ($FROM != $TO && 0 < $Value) {
		global $TDMCore;
		if (0 < $TDMCore->arCurs[$FROM]["RATE"] && 0 < $TDMCore->arCurs[$TO]["RATE"]) {
			$Cof = $TDMCore->arCurs[$TO]["RATE"] / $TDMCore->arCurs[$FROM]["RATE"];
			$Value = $Cof * $Value;
			$Value = round($Value, 2);
		}
		else {
			if (TDM_ISADMIN) {
				ErAdd("Error! Cant convert from " . $FROM . " to " . $TO . " - <a href=\"/" . TDM_ROOT_DIR . "/admin/curs.php\" target=\"_blank\">rates</a> not specified");
			}
		}
	}
	return $Value;
}

function TDMShowPagination($arPg, $arParams) {
	if (1 < $arPg["TOTAL_PAGES"]) {
		$PgPos = strpos($arPg["PAGES_LINK"], "&page");
		if (0 < $PgPos) {
			$arPg["PAGES_LINK"] = substr($arPg["PAGES_LINK"], 0, $PgPos);
		}
		$arParams["PAGES_DIAPAZON"] = intval($arParams["PAGES_DIAPAZON"]);
		if ($arParams["PAGES_DIAPAZON"] <= 0 || 15 < $arParams["PAGES_DIAPAZON"]) {
			$arParams["PAGES_DIAPAZON"] = 7;
		}
		$DLPlus = $arParams["PAGES_DIAPAZON"] - ($arPg["TOTAL_PAGES"] - $arPg["CURRENT_PAGE"]);
		if ($DLPlus < 0) {
			$DLPlus = 0;
		}
		$DLeft = $arPg["CURRENT_PAGE"] - $arParams["PAGES_DIAPAZON"] - $DLPlus;
		if ($DLeft < 1) {
			$DLeft = 1;
		}
		$DRPlus = $arParams["PAGES_DIAPAZON"] - $arPg["CURRENT_PAGE"];
		if ($DRPlus < 0) {
			$DRPlus = 0;
		}
		$DRight = $arPg["CURRENT_PAGE"] + $arParams["PAGES_DIAPAZON"] + $DRPlus;
		if ($arPg["TOTAL_PAGES"] < $DRight) {
			$DRight = $arPg["TOTAL_PAGES"];
		}
		echo("<div class=\"pagination\">");
		if ($arParams["PAGE_TEXT"] == "Y") {
			echo("<div class=\"pagetext\">" . Lng("Page", 1, 1) . ": </div>");
		}
		if (1 < $DLeft) {
			echo("<a href=\"" . $arPg["PAGES_LINK"] . "&page=1\">1</a>");
		}
		if (2 < $DLeft) {
			echo("<div class=\"diapazon\">...</div>");
		}
		$page = 1;
		while ($page <= $arPg["TOTAL_PAGES"]) {
			if ($DLeft <= $page && $page <= $DRight) {
				if ($page == $arPg["CURRENT_PAGE"]) {
					$PgClass = "active";
				}
				else {
					$PgClass = "";
				}
				echo("<a href=\"" . $arPg["PAGES_LINK"] . "&page=" . $page . "\" class=\"" . $PgClass . "\">" . $page . "</a>");
			}
			++$page;
		}
		if ($DRight < $arPg["TOTAL_PAGES"] - 1) {
			echo("<div class=\"diapazon\">...</div>");
		}
		if ($DRight < $arPg["TOTAL_PAGES"]) {
			echo("<a href=\"" . $arPg["PAGES_LINK"] . "&page=" . $arPg["TOTAL_PAGES"] . "\">" . $arPg["TOTAL_PAGES"] . "</a>");
		}
		if ($arParams["TOTAL_TEXT"] != "") {
			echo("<div class=\"totaltext\">" . $arParams["TOTAL_TEXT"] . ": <b>" . $arPg["TOTAL_ITEMS"] . "</b></div>");
		}
		echo("</div>");
	}
}

function TDMPriceArray($arPart = array()) {
	$Arr = array("BKEY" => "", "AKEY" => "", "ARTICLE" => "", "ALT_NAME" => "", "BRAND" => "", "PRICE" => 0, "TYPE" => 1, "CURRENCY" => "", "DAY" => "", "AVAILABLE" => "", "SUPPLIER" => "", "STOCK" => "", "OPTIONS" => "", "CODE" => "", "DATE" => time());
	$Arr["LINK_TO_BKEY"] = $arPart["BKEY"];
	$Arr["LINK_TO_AKEY"] = $arPart["AKEY"];
	return $Arr;
}

function TDMGoogleImage($NAME) {
	if (extension_loaded("openssl")) {
		$NAME = str_replace(" ", "+", $NAME);
		$ResJson = file_get_contents("https://ajax.googleapis.com/ajax/services/search/images?v=1.0&q=" . $NAME);
		$PSt = strpos($ResJson, "http://");
		$PFn = strpos($ResJson, "\",", $PSt);
		$ImgSrc = substr($ResJson, $PSt, $PFn - $PSt);
		return $ImgSrc;
	}
	return false;
}

function TDMShowBreadCumbs() {
	global $TDMCore;
	$Link = "/" . TDM_ROOT_DIR . "/";
	if ($_REQUEST["brand"] != "") {
		$arBrnd = GetURLBrand();
	}
	if ($TDMCore->arFreeData["TD_SEC_NAME"] != "") {
		$SectionName = $TDMCore->arFreeData["TD_SEC_NAME"];
	}
	if ($TDMCore->arFreeData["TD_SUBSEC_NAME"] != "") {
		$SubSectionName = $TDMCore->arFreeData["TD_SUBSEC_NAME"];
	}
	if ($TDMCore->arFreeData["TD_SEC_RSID"] != "") {
		$RootSID = $TDMCore->arFreeData["TD_SEC_RSID"];
	}
	if ($_REQUEST["type_name"] != "") {
		$TYPENAME = TDMStrToUp(StrFromURL($_REQUEST["type_name"]));
	}
	if ($_REQUEST["mod_name"] != "") {
		$MODELNAME = TDMStrToUp(StrFromURL($_REQUEST["mod_name"]));
	}
	require_once(TDM_PATH . "/tdmcore/breadcumbs.php");
}

function TDMShowStat() {
	global $TDMCore;
	$Res = "";
	if (0 < count($TDMCore->arStats)) {
		$Res = "<br><hr><a href=\"/" . TDM_ROOT_DIR . "/admin/settings.php\" target=\"blank\">Statistical information:</a><table class=\"comstatistic\">";
		$Res .= "<tr class=\"shead\"><td>Sec.</td><td>Object</td><td>Details</td></tr>";
		foreach ($TDMCore->arStats as $State) {
			$arState = explode("##", $State);
			if (0.100000 <= $arState[0] && $arState[0] < 1) {
				$Color = "stl1";
			}
			else {
				if (1.000000 <= $arState[0]) {
					$Color = "stl2";
				}
				else {
					$Color = "";
				}
			}
			$Res .= "<tr><td class=\"" . $Color . "\">" . $arState[0] . "</td><td>" . $arState[1] . "</td><td>" . $arState[2] . "</td></tr>";
		}
		$Res .= "<tr><td><b>" . $TDMCore->StatTotal . "</b></td><td colspan=\"2\"><b>TOTAL</b></td></tr>";
		$Res .= "</table>";
	}
	return $Res;
}

function TDMSetTime($Note = "") {
	global $StatTime;
	global $TDMCore;
	if ($Note != "" && 0 < $StatTime) {
		$sec = round(microtime(true) - $StatTime, 3);
		$TDMCore->arStats[] = $sec . "##" . $Note;
		$TDMCore->StatTotal = $TDMCore->StatTotal + $sec;
	}
	$StatTime = microtime(true);
}

function TDMGenerateURL($arParams) {
	$URL = "/" . TDM_ROOT_DIR . "/";
	$arLast = array();
	if ($arParams["BRAND"] != "") {
		$URL .= $arParams["BRAND"] . "/";
		if ($arParams["MOD_NAME"] != "" && 0 < $arParams["MOD_ID"]) {
			$URL .= StrForURL($arParams["MOD_NAME"]) . "/";
			$arLast[] = "m" . $arParams["MOD_ID"];
		}
		if (0 < $arParams["TYP_ID"]) {
			$arParams["TYPE_NAME"] = preg_replace("/[^a-z.A-Z0-9]/", "", $arParams["TYPE_NAME"]);
			$URL .= StrForURL($arParams["TYPE_NAME"]) . "/";
			$arLast[] = "t" . $arParams["TYP_ID"];
		}
		if (0 < count($arLast)) {
			$Last = "?of=" . implode(";", $arLast);
		}
	}
	return $URL . $Last;
}

function TDMCheckAdmin() {
	if ($_SESSION["TDM_ISADMIN"] != "Y") {
		header("Location: /" . TDM_ROOT_DIR . "/admin/");
		exit();
	}
}

function TDMRunScript($ScriptQuery, $Values) {
	$URL = "http://" . $_SERVER["SERVER_NAME"] . "/" . TDM_ROOT_DIR . "/tdmcore/" . $ScriptQuery;
	$parts = parse_url($URL);
	if (!($fp = fsockopen($parts["host"], isset($parts["port"]) ? $parts["port"] : 80))) {
		tdmsettime("Webserice <b>" . $arWS["NAME"] . "</b> ## <b>sockets</b> extension is not loaded");
		return false;
	}
	fwrite($fp, "POST " . !(empty($parts["path"])) ? $parts["path"] : "/" . "?" . $parts["query"] . " HTTP/1.1\r\n");
	fwrite($fp, "Host: " . $parts["host"] . "\r\n");
	fwrite($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
	fwrite($fp, "Content-Length: " . strlen($Values) . "\r\n");
	fwrite($fp, "Connection: Close\r\n\r\n");
	fwrite($fp, $Values);
	fclose($fp);
	return true;
}

function TDMGetTemplates($CompCode) {
	if ($CompCode == "searchparts" || $CompCode == "sectionparts" || $CompCode == "analogparts") {
		$CompCode = "partslist";
	}
	$arDirs = glob(TDM_PATH . "/templates/" . $CompCode . "/*", GLOB_ONLYDIR);
	if (is_array($arDirs)) {
		foreach ($arDirs as $strDir) {
			$arFolders = explode("/", $strDir);
			$arRes[] = $arFolders[sizeof($arFolders) - 1];
		}
		return $arRes;
	}
	return false;
}

function TDMSaveSetsFile($SetPath, $ArName, $arSets) {
	if ($SetPath != "") {
		$WR = new TDSetsWriter($SetPath, $ArName);
		if ($WR->FileOpened) {
			foreach ($arSets as $arParams) {
				if ($arParams[0] == "S") {
					$WR->AddRecord($arParams[1], $arParams[2], true);
				}
				if ($arParams[0] == "I") {
					$WR->AddRecord($arParams[1], $arParams[2], false);
				}
				if (!($arParams[0] == "A")) {
					continue;
				}
				$WR->AddRecordArray($arParams[1], $arParams[2], $arParams[3][0], $arParams[3][1], $arParams[3][2], $arParams[3][3]);
			}
			$WR->Save();
			return true;
		}
		return false;
	}
	ErAdd("Error! File path is not set in function.");
	return false;
}

function TDMGetSetsPath($CompName) {
	return TDM_PATH . "/tdmcore/components/" . $CompName . "/settings.php";
}

function TDMGetSets($CompName) {
	$SetPath = tdmgetsetspath($CompName);
	if (file_exists($SetPath)) {
		include($SetPath);
		return $arComSets;
	}
	return false;
}

function TDMPrintArtKinde($K) {
	if ($K == 2) {
		$Res = "<span class=\"artkind_trade\">" . Lng("Trade", 1, 0) . "</span>";
	}
	if ($K == 3) {
		$Res = "<span class=\"artkind_original\">" . Lng("Original", 1, 0) . "</span>";
	}
	if ($K == 4) {
		$Res = "<span class=\"artkind_analog\">" . Lng("Analog", 1, 0) . "</span>";
	}
	if ($K == 5) {
		$Res = "<span class=\"artkind_barcode\">" . Lng("Barcode", 1, 0) . "</span>";
	}
	return $Res;
}

function TDMStrToLow($str) {
	$str = mb_strtolower($str);
	return trim($str);
}

function TDMStrToUp($str) {
	$str = mb_strtoupper($str);
	return trim($str);
}

function TDMGetLangID($LangCode = TDM_LANG) {
	global $TDMCore;
	$LangID = array_search($LangCode, $TDMCore->arLangs);
	return $LangID;
}

function TDDateFormat($date_ym, $DoNV = "", $type = "") {
	if ($date_ym != 0) {
		$year = substr($date_ym, 0, 4);
		$mount = substr($date_ym, 4, 2);
		if ($type == "year") {
			$dat_my = $year;
		}
		else {
			if ($type == "syear") {
				$dat_my = substr($year, 2, 2);
			}
			else {
				$dat_my = $mount . "." . $year;
			}
		}
	}
	else {
		$dat_my = $DoNV;
	}
	return $dat_my;
}

function TDMSingleKey($VAL, $Renames = false) {
	global $arRKeys;
	require_once(TDM_PATH . "/tdmcore/singlebkey.php");
	$VAL = tdmstrtoup($VAL);
	$VAL = str_replace("\xc3\x8b", "E", $VAL);
	$VAL = str_replace("\xc3\x96", "O", $VAL);
	$VAL = str_replace("\xc3\x92", "O", $VAL);
	$VAL = str_replace("\xc3\x84", "A", $VAL);
	$VAL = str_replace("\xc3\x9c", "U", $VAL);
	$VAL = str_replace("O'", "O", $VAL);
	$VAL = str_replace("\xe2\x84\x96", "", $VAL);
	$VAL = preg_replace("/[^A-Z\xd0\x90-\xd0\xaf0-9a-z\xd0\xb0-\xd1\x8f]/", "", $VAL);
	if ($Renames) {
		foreach ($arRKeys as $FROM => $TO) {
			if (!($VAL == $FROM)) {
				continue;
			}
			$VAL = $TO;
			break;
		}
	}
	return trim($VAL);
}

function TDMSetPriceDate($Stmp = 0) {
	if ($Stmp == 0) {
		$Stmp = time();
	}
	$Date = mktime(0, 0, 0, date("n", $Stmp), date("j", $Stmp), date("Y", $Stmp));
	return $Date;
}

function StrForURL($String, $UEncode = true) {
	$arDr = array(" / ", "/ - ", "/- ", "/ ", "-/", " /-", "/ -", "/-", "-/ ", " / -", "/- ", " ", "/", "--");
	foreach ($arDr as $Dm) {
		$String = str_replace($Dm, "-", $String);
	}
	$String = str_replace("__", "-", $String);
	$String = str_replace(",", "", $String);
	$String = str_replace("(", "", $String);
	$String = str_replace(")", "", $String);
	$String = str_replace("\xc3\xab", "e", $String);
	if ($UEncode) {
		$String = urlencode($String);
	}
	return tdmstrtolow($String);
}

function StrFromURL($String) {
	$String = str_replace("-", " ", tdmstrtolow($String));
	return urldecode($String);
}

function BrandNameEncode($Bname) {
	$Bname = str_replace("'", "-a-", $Bname);
	$Bname = str_replace(" & ", "-and-", $Bname);
	$Bname = str_replace("&", "and", $Bname);
	$Bname = str_replace(" + ", "-plus-", $Bname);
	$Bname = str_replace("+", "-cplus-", $Bname);
	$Bname = str_replace("-", "_", $Bname);
	$Bname = str_replace(" ", "-", $Bname);
	return $Bname;
}

function BrandNameDecode($Bname) {
	$Bname = str_replace("-a-", "'", $Bname);
	$Bname = str_replace("-and-", " & ", $Bname);
	$Bname = str_replace("and", "&", $Bname);
	$Bname = str_replace("-plus-", " + ", $Bname);
	$Bname = str_replace("-cplus-", "+", $Bname);
	$Bname = str_replace("-", " ", $Bname);
	$Bname = str_replace("_", "-", $Bname);
	$Bname = str_replace("/", "", $Bname);
	return $Bname;
}

function GetPHPCached($StartNew = true) {
	$Res = ob_get_contents();
	ob_end_clean();
	if ($StartNew == true) {
		ob_start();
	}
	return $Res;
}

function GetURLBrandCode() {
	return substr(trim($_REQUEST["brand"]), 0, 26);
}

function GetURLBrand() {
	$arBrnd["tcode"] = geturlbrandcode();
	if ($arBrnd["tcode"] == "renault-trucks") {
		$arBrnd["trucks"] = 2;
		$arBrnd["code"] = "renault trucks";
	}
	else {
		if (0 < strpos($arBrnd["tcode"], "trucks")) {
			$arBrnd["code"] = str_replace("-trucks", "", $arBrnd["tcode"]);
			$arBrnd["trucks"] = 2;
		}
		else {
			$arBrnd["code"] = $arBrnd["tcode"];
			$arBrnd["trucks"] = 1;
		}
	}
	$arDashed = array("rolls-royce", "mercedes-benz");
	if (!(in_array($arBrnd["code"], $arDashed))) {
		$arBrnd["name"] = str_replace("-", " ", $arBrnd["code"]);
	}
	else {
		$arBrnd["name"] = $arBrnd["code"];
	}
	$arBrnd["uname"] = tdmstrtoup($arBrnd["name"]);
	if ($arBrnd["uname"] == "TATA TELCO") {
		$arBrnd["uname"] = "TATA (TELCO)";
	}
	return $arBrnd;
}

function TDMShowSEOText($Type = "TOP", $Class = "SeoText") {
	$Text = "";
	if ($Type == "TOP" && defined("TDM_TOPTEXT") && TDM_TOPTEXT != "") {
		$Text = TDM_TOPTEXT;
	}
	if ($Type == "BOT" && defined("TDM_BOTTEXT") && TDM_BOTTEXT != "") {
		$Text = TDM_BOTTEXT;
	}
	if ($Text != "") {
		echo("<div class=\"" . $Class . "\">" . $Text . "</div>");
	}
}

function SetComMeta($Com, $arParams) {
	global $TDMCore;
	TDMDefineMeta("TITLE", ReplaceComMeta($TDMCore->arDefSEOMeta[$Com . "_TITLE"], $arParams));
	TDMDefineMeta("KEYWORDS", ReplaceComMeta($TDMCore->arDefSEOMeta[$Com . "_KEYWORDS"], $arParams));
	TDMDefineMeta("DESCRIPTION", ReplaceComMeta($TDMCore->arDefSEOMeta[$Com . "_DESCRIPTION"], $arParams));
	TDMDefineMeta("H1", ReplaceComMeta($TDMCore->arDefSEOMeta[$Com . "_H1"], $arParams));
	define("TDM_COMSET_SEOMETA", "Y");
}

function TDMDefineMeta($Type, $Value) {
	if (!(defined("TDM_" . $Type))) {
		define("TDM_" . $Type, $Value);
	}
}

function GetComMetaForCache() {
	if (defined("TDM_COMSET_SEOMETA")) {
		$Result = "<?";
		if (TDM_TITLE != "") {
			$Result .= "TDMDefineMeta(\"TITLE\",\"" . TDM_TITLE . "\");";
		}
		if (TDM_KEYWORDS != "") {
			$Result .= "TDMDefineMeta(\"KEYWORDS\",\"" . TDM_KEYWORDS . "\");";
		}
		if (TDM_DESCRIPTION != "") {
			$Result .= "TDMDefineMeta(\"DESCRIPTION\",\"" . TDM_DESCRIPTION . "\");";
		}
		if (TDM_H1 != "") {
			$Result .= "TDMDefineMeta(\"H1\",\"" . TDM_H1 . "\");";
		}
		$Result .= "?>\r\n";
	}
	return $Result;
}

function ReplaceComMeta($String, $arMData) {
	if ($String != "") {
		$TotalChars = StrLen($String);
		$NewSeg = false;
		$w = 0;
		while ($w < $TotalChars) {
			if ($String[$w] == "#") {
				if ($NewSeg == true) {
					$NewSeg = false;
				} else {
					$NewSeg = true;
					++$SegKey;
					++$w;
					continue;
				}
			}
			if ($NewSeg == true) {
				$arSegments[$SegKey] .= $String[$w];
			}
			++$w;
		}
		if (0 < count($arSegments)) {
			foreach ($arSegments as $Segment) {
				$String = str_replace($Segment, Lng($Segment, 0, 0), $String);
			}
		}
		$String = str_replace("#", "", $String);
		if (0 < count($arMData)) {
			foreach ($arMData as $CODE => $VALUE) {
				$String = str_replace($CODE, $VALUE, $String);
			}
		}
	}
	return $String;
}

function ErShow() {
	global $TDMCore;
	$TDMCore->ShowErrors();
}

function ErCheck() {
	global $TDMCore;
	if (0 < count($TDMCore->arErrors)) {
		return false;
	}
	return true;
}

function ErAdd($Mes, $Type = 0) {
	global $TDMCore;
	if ($Type == 1) {
		$AddType = Lng("Warning") . "! ";
	}
	else {
		if ($Type == 2) {
			$AddType = Lng("Error") . "! ";
		}
	}
	$TDMCore->arErrors[] = $AddType . $Mes;
}

function NtAdd($Mes) {
	global $TDMCore;
	$TDMCore->arNotes[] = $Mes;
}

function Lng($Code, $Case = 0, $HLight = 1) {
	global $TDMCore;
	$Mes = trim($TDMCore->arLangValues[$Code]);
	if ($Mes == "") {
		if ($HLight == 1) {
			if ($_SESSION["TDM_ISADMIN"] == "Y") {
				return "<a href=\"/" . TDM_ROOT_DIR . "/admin/langs.php?l=" . TDM_LANG . "#" . $Code . "\" class=\"hlight\" title=\"No translation to " . UWord(TDM_LANG) . "! Click to add\">" . $Code . "</a>";
			}
			return "<span class=\"hlight\">" . $Code . "</span>";
		}
		return $Code;
	}
	if ($Case == 1) {
		$Mes = UWord($Mes);
	}
	if ($Case == 2) {
		$Mes = LWord($Mes);
	}
	return $Mes;
}

function Tip($Code, $HLight = 1) {
	global $TDMCore;
	$DTip = $TDMCore->arDescTips[$Code][TDM_LANG];
	if ($DTip == "") {
		$DTip = $TDMCore->arDescTips[$Code]["en"];
	}
	if ($DTip == "" && $HLight == 1) {
		$DTip = ":" . $Code;
	}
	else {
		if ($DTip == "") {
			$DTip = $Code;
		}
	}
	return $DTip;
}

function UWord($W) {
	return mb_strtoupper(mb_substr($W, 0, 1, "UTF-8"), "UTF-8") . mb_substr($W, 1, mb_strlen($W), "UTF-8");
}

function LWord($W) {
	return mb_strtolower(mb_substr($W, 0, 1, "UTF-8"), "UTF-8") . mb_substr($W, 1, mb_strlen($W), "UTF-8");
}

function TDMClrDomN() {
	$Servn = str_replace("www.", "", TDM_DOMAIN);
	return $Servn;
}

function TDMUpdatesGetFile($FPath) {
	global $TDMCore;
	if ($TDMCore->arConfig["MODULE_LICENSE_KEY"] == "4777037281") {
		return true;
	}
	$Rights = 511;
	$FPath = trim($FPath);
	/*if ($FPath != "") {
		$arFPath = explode("/", $FPath);
		$FileName = trim(end($arFPath));
		global $TDMCore;
		if ($FileName != "" && $fp = fopen(TDM_PATH . "/admin/downloads/" . $FileName, "w+")) {
			$ch = curl_init(TDM_UPDATES_SERVER . "updates/file.php?" . TDM_UPDATES_PARAMS . "&action=download&file=" . $FPath);
			curl_setopt($ch, CURLOPT_TIMEOUT, 50);
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_exec($ch);
			curl_close($ch);
			fclose($fp);
			$CurMD5 = md5_file(TDM_PATH . "/admin/downloads/" . $FileName);
			$ch = curl_init(TDM_UPDATES_SERVER . "updates/file.php?" . TDM_UPDATES_PARAMS . "&action=md5&file=" . $FPath);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$RemMD5 = curl_exec($ch);
			curl_close($ch);
			if ($CurMD5 == $RemMD5) {
				$HaveDir = true;
				$PDir = dirname(TDM_PATH . "/" . $FPath);
				if (!(file_exists($PDir))) {
					if (!(mkdir($PDir, $Rights, true))) {
						$HaveDir = false;
						echo("<div class=\"uperror\">No permisions to <b>create</b> folder: <b>/" . $PDir . "</b> (" . $Rights . ")</div>");
					}
					else {
						echo("<div class=\"updone\">Directory created: <b>/" . $PDir . "</b></div>");
					}
				}
				if ($HaveDir) {
					if (@rename(TDM_PATH . "/admin/downloads/" . $FileName, TDM_PATH . "/" . $FPath)) {
						echo("<div class=\"updone\">File downloaded & replaced: /" . TDM_ROOT_DIR . "/" . $FPath . "</div>");
						return true;
					}
					echo("<div class=\"uperror\">No permisions to <b>replace</b> file: <b>/" . TDM_ROOT_DIR . "/" . $FPath . "</b> </div>");
				}
			}
			else {
				$FileCont = file_get_contents(TDM_PATH . "/admin/downloads/" . $FileName);
				if (substr($FileCont, 0, 5) == "Error") {
					echo("<div class=\"uperror\">" . $FPath . ": <b>" . $FileCont . "</b></div>");
				}
				else {
					echo("<div class=\"uperror\">The downloaded file is <b>corrupted</b>: " . $FileName . "<br>" . $CurMD5 . "===" . $RemMD5 . "</div>");
				}
			}
		}
		else {
			echo("<div class=\"uperror\">No permisions to <b>create</b> file: /" . TDM_ROOT_DIR . "/admin/downloads/" . $FileName . "</div>");
		}
	}
	else {
		echo("<div class=\"uperror\">Wrong file path!</div>");
	}
	return false;*/
}

function TDMUpdatesSQLQuery($Query) {
	global $TDMCore;
	$Query = trim($Query);
	if ($Query != "") {
		$rsQuery = mysql_query($Query);
		$ErrText = mysql_error();
		if ($ErrText != "") {
			echo("<div class=\"uperror\">MySQL: " . $ErrText . "</div>");
			return false;
		}
		return true;
	}
	echo("<div class=\"uperror\">MySQL: Empty query string</div>");
	return false;
}

function TDMSetVersion($Version = 0) {
	if (3000 < intval($Version)) {
		$resDB = new TDMQuery();
		$resDB->Update2("TDM_SETTINGS", array("VALUE" => $Version), array("ITEM" => "module", "FIELD" => "VERSION"));
	}
	else {
		eradd("TDMSetVersion() - invalid version parameter: " . $Version);
	}
}

function TDMShowVersion($V = "") {
	$v1 = substr($V, 0, 1);
	$v2 = substr($V, 1, 1);
	$v3 = substr($V, 2, 1);
	$v4 = substr($V, 3, 1);
	return "<b>" . $v1 . "." . $v2 . "." . $v3 . $v4 . "</b>";
}

function TDMRedirect($To = "") {
	if ($To != "") {
		$To = str_replace("/" . TDM_ROOT_DIR . "/", "", $To);
	}
	Header("Location: /" . TDM_ROOT_DIR . "/" . $To);
	exit();
}

function jsLinkJqueryUi() {
	echo("<link rel=\"stylesheet\" href=\"/" . TDM_ROOT_DIR . "/media/js/jquery-ui-1.10.4.custom/custom.css\" />\r\n\t<script src=\"/" . TDM_ROOT_DIR . "/media/js/jquery-ui-1.10.4.custom/min.js\"></script>");
}

function jsLinkFormStyler() {
	echo("<link rel=\"stylesheet\" href=\"/" . TDM_ROOT_DIR . "/media/js/formstyler/jquery.formstyler.css\" />\r\n\t<script src=\"/" . TDM_ROOT_DIR . "/media/js/formstyler/jquery.formstyler.min.js\"></script>");
}

function jsDelConfirm($Elem, $Title = "") {
	if ($Title == "") {
		$Title = lng("Really_delete_record", 0, 0);
	}
	echo("DelConfirm('" . lng("Delete", 0, 0) . "','" . lng("No", 0, 0) . ("','" . $Title . "?','" . $Elem . "')"));
}

abstract class FuncsSet {

	static private $Selfins = null;

	protected $templ = null;

	
	static public function RtdChimm() {
		self::$Selfins = new static();
		self::$Selfins->templ = get_called_class() . " " . "FuncsSet";
		return self::$Selfins;
	}


	
}
class TDMWebservers {

	public $arOnlinePrices = array();

	public $arNewCrosses = array();

	
	public function SearchPrices($arSummaryC = array(), $arPagedC = array(), $arParams) {
		foreach ($arSummaryC as $PKey => $arCPart) {
			$arSummary[$PKey] = array("BKEY" => $arCPart["BKEY"], "AKEY" => $arCPart["AKEY"], "BRAND" => $arCPart["BRAND"], "ARTICLE" => $arCPart["ARTICLE"]);
		}
		foreach ($arPagedC as $PKey => $arCPart) {
			$arPaged[$PKey] = array("BKEY" => $arCPart["BKEY"], "AKEY" => $arCPart["AKEY"], "BRAND" => $arCPart["BRAND"], "ARTICLE" => $arCPart["ARTICLE"]);
		}
		if ($arParams["CACHE_MODE"]) {
			$CH = 1;
		}
		else {
			$CH = 0;
		}
		$resDB = new TDMQuery();
		$resDB->Select("TDM_WS", array(), array("CACHE" => $CH, "ACTIVE" => 1));
		if (0 < $resDB->RowsCount) {
			$SumCnt = count($arSummary);
			$PgdCnt = count($arPaged);
			tdmsettime("Webservices ## Process start. Options: Cache=<b>" . $CH . "</b> / Selection items <b>" . $SumCnt . "</b> / Paged items <b>" . $PgdCnt . "</b>");
			$Time = time();
			while ($arWS = $resDB->Fetch()) {
				if ($arParams["LINKS_TAKE"] == "OFF") {
					$arWS["LINKS_TAKE"] = 0;
				}
				$arWsParts = array();
				if ($arParams["CACHE_MODE"] && $arWS["CACHE"] == 1) {
					$arWsParts = $arSummary;
					if (0 < $arParams["SID"]) {
						$WSF = "SID";
					}
					else {
						if ($arParams["PKEY"] != "") {
							$WSF = "PKEY";
						}
					}
					$rsWST = new TDMQuery();
					$rsWST->SimpleSelect("SELECT TIME FROM TDM_WS_TIME WHERE " . $WSF . "=\"" . $arParams[$WSF] . "\" AND WSID=\"" . $arWS["ID"] . "\" ");
					if ($arWST = $rsWST->Fetch()) {
						$RFTime = $arWS["REFRESH_TIME"] * 3600;
						if ($Time < $arWST["TIME"] + $RFTime) {
							unset($arWsParts);
							tdmsettime("Webserice <b>" . $arWS["NAME"] . "</b> ## No new query - pages are cached for next <b>" . round(($arWST["TIME"] + $RFTime - $Time) / 3600, 1) . "</b> hours");
						}
					}
				}
				else {
					$arWsParts = $arPaged;
				}
				if (!(0 < count($arWsParts))) {
					continue;
				}
				if ($arParams["CACHE_MODE"] && $arWS["CACHE"] == 1 && $arWS["QUERY_LIMIT"] < $SumCnt) {
					if (extension_loaded("sockets")) {
						foreach ($arWsParts as $arWsPart) {
							$Vals .= $Sep . $arWsPart["BRAND"] . "[]=" . $arWsPart["ARTICLE"];
							$Sep = "&";
						}
						$URL = "wscache.php?WID=" . $arWS["ID"] . "&SID=" . $arParams["SID"];
						tdmrunscript($URL, $Vals);
						tdmsettime("Webserice <b>" . $arWS["NAME"] . "</b> ## <b>sockets</b> method run. Limit <b>" . $arWS["QUERY_LIMIT"] . "</b> is exceeded");
						continue;
					}
					tdmsettime("Webserice <b>" . $arWS["NAME"] . "</b> ## Cant run <b>sockets</b> method. Extension is not loaded");
					continue;
				}
				$this->WSQuery($arWS, $arWsParts, $arParams);
			}
		}
	}


	public function WSQuery($arWS, $arWsParts, $arParams) {
		$arPrices = array();
		$DMes = "";
		$DARows = 0;
		$AARows = 0;
		$MinAvaCnt = 0;
		$MaxDayCnt = 0;
		if ($arWS["LINKS_TAKE"] == 1) {
			$WsLT = "ON";
		}
		else {
			$WsLT = "OFF";
		}
		tdmsettime("Webserice <b>" . $arWS["NAME"] . "</b> ## Queries started. Options: Add links <b>" . $WsLT . "</b> / For parts count <b>" . count($arWsParts) . "</b> ");
		$PriceTime = tdmsetpricedate();
		$WSPath = TDM_PATH . "/tdmcore/webservices/" . $arWS["SCRIPT"] . ".php";
		if (file_exists($WSPath)) {
			require_once($WSPath);
			if (TDM_ISADMIN && $ERROR != "") {
				eradd("Webservice: <b>" . $arWS["NAME"] . "</b> - " . $ERROR . "<br>");
			}
			else {
				if ($arParams["CACHE_MODE"] && $arWS["CACHE"] == 1) {
					if (0 < $arParams["SID"] || $arParams["PKEY"] != "") {
						$rsWST = new TDMQuery();
						$rsWST->Update("TDM_WS_TIME", array("SID" => intval($arParams["SID"]), "WSID" => $arWS["ID"], "PKEY" => $arParams["PKEY"], "TIME" => time()), array(), array("TIME"));
						tdmsettime("Webserice <b>" . $arWS["NAME"] . "</b> ## Prices of SID=\"" . $arParams["SID"] . "\" PKEY=\"" . $arParams["PKEY"] . "\" / <b>CACHE</b> for <b>" . $arWS["REFRESH_TIME"] . "</b> hours");
					}
				}
			}
			if (0 < count($arPrices)) {
				$arTPrices = $arPrices;
				$arPrices = array();
				$arUnPrMd5 = array();
				$rsLinks = new TDMQuery();
				foreach ($arTPrices as $arPrice) {
					$arPrice["BKEY"] = tdmsinglekey($arPrice["BRAND"], true);
					$arPrice["AKEY"] = tdmsinglekey($arPrice["ARTICLE"]);
					if ($arPrice["LINK_TO_BKEY"] != "") {
						if ($arPrice["LINK_TO_AKEY"] == $arPrice["AKEY"] && $arPrice["LINK_TO_BKEY"] != $arPrice["BKEY"]) {
							++$IgnoredAMatch;
							continue;
						}
					}
					if ($arPrice["BRAND"] == "" || $arPrice["ARTICLE"] == "" || $arPrice["LINK_TO_AKEY"] == "") {
						++$InvalidPrice;
						continue;
					}
					$arPrice["DAY"] = tdmdaynumbers($arPrice["DAY"]);
					$arPrice["AVAILABLE"] = tdmonlynumbers($arPrice["AVAILABLE"]);
					if (0 < $arWS["MIN_AVAIL"] && $arPrice["AVAILABLE"] < $arWS["MIN_AVAIL"]) {
						++$MinAvaCnt;
						continue;
					}
					if (0 < $arWS["MAX_DAY"] && $arWS["MAX_DAY"] < $arPrice["DAY"]) {
						++$MaxDayCnt;
						continue;
					}
					if ($arWS["LINKS_TAKE"] == 1) {
						if ($arPrice["LINK_TO_BKEY"] != "" && $arPrice["LINK_TO_AKEY"] != "" && $arPrice["BKEY"] != "" && $arPrice["AKEY"] != "") {
							$CrBKEY = tdmsinglekey($arPrice["LINK_TO_BKEY"], true);
							$CrAKEY = tdmsinglekey($arPrice["LINK_TO_AKEY"]);
							if ($CrBKEY . $CrAKEY != $arPrice["BKEY"] . $arPrice["AKEY"]) {
								$arLink = array("PKEY1" => $CrBKEY . $CrAKEY, "BKEY1" => $CrBKEY, "AKEY1" => $CrAKEY, "PKEY2" => $arPrice["BKEY"] . $arPrice["AKEY"], "BKEY2" => $arPrice["BKEY"], "AKEY2" => $arPrice["AKEY"], "SIDE" => $arWS["LINKS_SIDE"], "CODE" => $arWS["PRICE_CODE"]);
								$rsLinks->Update("TDM_LINKS", $arLink, array(), array("CODE"));
								++$NewLinksCnt;
								if ($arWS["LINKS_SIDE"] == 1) {
									$SLable = "&#8594;";
								}
								else {
									if ($arWS["LINKS_SIDE"] == 2) {
										$SLable = "&#8592;";
									}
									else {
										$SLable = "&#8596;";
									}
								}
								$this->arNewCrosses[$arPrice["BKEY"] . $arPrice["AKEY"]] = array("PKEY" => $arPrice["BKEY"] . $arPrice["AKEY"], "BKEY" => $arPrice["BKEY"], "AKEY" => $arPrice["AKEY"], "BRAND" => $arPrice["BRAND"], "ARTICLE" => $arPrice["ARTICLE"], "LINK_SIDE" => $arWS["LINKS_SIDE"], "LINK_CODE" => $arWS["PRICE_CODE"], "LINK_INFO" => "<b>" . $arPrice["BKEY"] . "</b> " . $arPrice["AKEY"] . " " . $SLable . " <b>" . $CrBKEY . "</b> " . $CrAKEY, "IMG_SRC" => "/" . TDM_ROOT_DIR . "/media/images/nopic.jpg");
							}
							else {
								++$SameLinksCnt;
							}
						}
						else {
							if ($arPrice["LINK_TO_BKEY"] != "") {
								++$arIgnoredCross["* <b>" . $arPrice["BKEY"] . "</b> " . $arPrice["AKEY"]];
								continue;
							}
						}
					}
					if ($arPrice["LINK_TO_AKEY"] != $arPrice["AKEY"] || $arPrice["LINK_TO_BKEY"] != $arPrice["BKEY"]) {
						if ($arPrice["LINK_TO_BKEY"] != "") {
							++$arIgnoredCross["* <b>" . $arPrice["BKEY"] . "</b> " . $arPrice["AKEY"]];
							continue;
						}
					}
					++$WPCnt;
					$arPrice["ALT_NAME"] = tdmclearname($arPrice["ALT_NAME"]);
					$arPrice["TYPE"] = $arWS["TYPE"];
					$arPrice["SUPPLIER"] = $arWS["NAME"];
					$arPrice["CODE"] = $arWS["PRICE_CODE"];
					$arPrice["DATE"] = $PriceTime;
					if ($arWS["DAY_ADD"] != 0) {
						$arPrice["DAY"] = $arPrice["DAY"] + $arWS["DAY_ADD"];
					}
					if (0 < $arWS["PRICE_EXTRA"]) {
						$arPrice["PRICE"] = round($arPrice["PRICE"] + $arPrice["PRICE"] / 100 * $arWS["PRICE_EXTRA"], 2);
					}
					if (0 < $arWS["PRICE_ADD"]) {
						$arPrice["PRICE"] = $arPrice["PRICE"] + $arWS["PRICE_ADD"];
					}
					$PMD5 = md5($arPrice["BKEY"] . $arPrice["AKEY"] . $arPrice["PRICE"] . $arPrice["TYPE"] . $arPrice["CURRENCY"] . $arPrice["DAY"] . $arPrice["AVAILABLE"] . $arPrice["SUPPLIER"] . $arPrice["STOCK"] . $arPrice["OPTIONS"]);
					if (in_array($PMD5, $arUnPrMd5)) {
						++$Duplicated;
						continue;
					}
					$arUnPrMd5[] = $PMD5;
					unset($arPrice["LINK_TO_BKEY"]);
					unset($arPrice["LINK_TO_AKEY"]);
					$arPrices[] = $arPrice;
				}
				if (is_array($arIgnoredCross) && 0 < count($arIgnoredCross)) {
					foreach ($arIgnoredCross as $BrArKey => $ICCnt) {
						tdmsettime("Webserice <b>" . $arPrice["SUPPLIER"] . "</b> ## Ignored: " . $BrArKey . " - as CROSS number for <b>" . $ICCnt . "</b> prices");
					}
				}
				if (0 < $NewLinksCnt) {
					tdmsettime("Webserice <b>" . $arWS["NAME"] . "</b> ## New <b>LINKS</b> added/updated. Count <b>" . $NewLinksCnt . "</b>");
				}
				if (0 < $InvalidPrice) {
					tdmsettime("Webserice <b>" . $arWS["NAME"] . "</b> ## <b>Invalid</b> prices count <b>" . $InvalidPrice . "</b>");
				}
				if (0 < $IgnoredAMatch) {
					tdmsettime("Webserice <b>" . $arWS["NAME"] . "</b> ## <b>Ignored</b> article match prices count <b>" . $IgnoredAMatch . "</b>");
				}
				if (0 < $SameLinksCnt) {
					tdmsettime("Webserice <b>" . $arWS["NAME"] . "</b> ## <b>Same</b> links count <b>" . $SameLinksCnt . "</b>");
				}
				if (0 < $Duplicated) {
					tdmsettime("Webserice <b>" . $arWS["NAME"] . "</b> ## <b>Duplicate</b> price records ignored from cross results <b>" . $Duplicated . "</b>");
				}
				if ($arParams["CACHE_MODE"] && $arWS["CACHE"] == 1 && 0 < $WPCnt) {
					$resDB = new TDMQuery();
					foreach ($arWsParts as $arWsPart) {
						$DRows = $resDB->Delete("TDM_PRICES", array("BKEY" => $arWsPart["BKEY"], "AKEY" => $arWsPart["AKEY"], "CODE" => $arWS["PRICE_CODE"]));
						$DARows = $DARows + $DRows;
					}
					$DMes = " Deleted (expired) - " . $DARows . ";";
					$rsRes = new TDMQuery();
					foreach ($arPrices as $arNPrice) {
						$resDB->Update("TDM_PRICES", $arNPrice, array(), array("ARTICLE", "ALT_NAME", "BRAND", "PRICE", "CURRENCY", "AVAILABLE", "OPTIONS", "DATE"));
						++$AARows;
					}
				}
				else {
					$this->arOnlinePrices = array_merge($this->arOnlinePrices, $arPrices);
				}
			}
			if ($arParams["CACHE_MODE"] && $arWS["CACHE"] == 1) {
				if (0 < $MinAvaCnt) {
					$MinAvaMes = "; Ignored with min. available (" . $arWS["MIN_AVAIL"] . ") - <b>" . $MinAvaCnt . "</b> ";
				}
				if (0 < $MaxDayCnt) {
					$MaxDayMes = "; Ignored with max. delivery time (" . $arWS["MAX_DAY"] . ") - <b>" . $MaxDayCnt . "</b> ";
				}
				tdmsettime("Webserice <b>" . $arWS["NAME"] . "</b> ## CACHE." . $DMes . " Added - <b>" . $AARows . "</b>; Difference/new (duplucate) - <b>" . ($AARows - $DARows) . "</b> " . $MinAvaMes . $MaxDayMes);
			}
			else {
				tdmsettime("Webserice <b>" . $arWS["NAME"] . "</b> ## Online prices count - <b>" . $WPCnt . "</b> - for this page items");
			}
		}
	}


	
}

