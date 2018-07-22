<?php

class PcPartsListProcessor
{
	/** @var TDMCore */
	private $core;
	private $list;
	private $artIds;

	/**
	 * @param $list
	 * @param TDMCore $core
	 */
	public function __construct($list, $core)
	{
		$this->list = $list;
		$this->core = $core;
	}

	/**
	 * @return $this
	 */
	public function runPostProcessing()
	{
		if(empty($this->list['PARTS']) || empty($this->list['PRICES'])) return $this;

		$newParts = array();
		$newPrices = array();

		foreach ($this->list['PARTS'] as $part) {
			if(empty($this->list['PRICES'][$part['PKEY']])) continue;
			foreach($this->list['PRICES'][$part['PKEY']] as $price){
				$newPart = $part;
				$newPart['PC_SKU'] = !empty($price['PC_SKU']) ? $price['PC_SKU'] : '';
				$newPart['PC_MANUFACTURER'] = !empty($price['PC_MANUFACTURER']) ? $price['PC_MANUFACTURER'] : '';
				$newPart['PC_OC_CROSS_ID'] = !empty($price['PC_OC_CROSS_ID']) ? $price['PC_OC_CROSS_ID'] : '';

				$pk = $this->getNewPk($newPart);
				$newPart['PKEY'] = $pk;

				$newParts[] = $newPart;
				if(empty($newPrices[$pk])) $newPrices[$pk] = array();
				$newPrices[$pk][] = $price;
			}
		}

		foreach($newParts as $key => $part){
			$newParts[$key]['PRICES_COUNT'] = isset($newPrices[$part['PKEY']]) ? count($newPrices[$part['PKEY']]) : 0;
		}

		$this->list['PRICES'] = $newPrices;
		$this->list['PARTS'] = $newParts;

		return $this;
	}

	/**
	 * @return $this
	 */
	public function flushPartsWithoutPrices()
	{
		if(empty($this->list['PARTS'])) return $this;
		$partsToUnset = array();

		foreach($this->list['PARTS'] as $index => $part){
			$partsByBrand = array();
			$article = TDMSingleKey($part['AKEY']);
			$brand = $part['BRAND'];

			if (strlen($article) < 3 || strlen($brand) < 2){
				$partsToUnset[] = $index;
				continue;
			}

			$this->lookupByBrandNumberInTecDoc($brand, $article, $partsByBrand);
			$this->lookupByBrandNumberInPrices($brand, $article, $partsByBrand);

			if (empty($partsByBrand)){
				$partsToUnset[] = $index;
				continue;
			}

			$this->core->DBSelect("MODULE");
			$crossesResults = new TDMQuery();
			$partsByBrandTmp = $partsByBrand;
			foreach($partsByBrandTmp as $pkey => $info){
				$sql = 'SELECT * FROM TDM_LINKS WHERE PKEY1="' . $pkey . '" AND SIDE IN (0,1) ';
				$crossesResults->SimpleSelect($sql);
				while ($rightCross = $crossesResults->Fetch()) {
					$akey = $rightCross["AKEY2"];
					$bkey = $rightCross["BKEY2"];
					$partsByBrand[$rightCross["PKEY2"]] = array('akey' => $akey, 'bkey' => $bkey);
				}

				$sql = 'SELECT * FROM TDM_LINKS WHERE PKEY2="' . $pkey . '" AND SIDE IN (0,2) ';
				$crossesResults->SimpleSelect($sql);
				while ($leftCross = $crossesResults->Fetch()) {
					$akey = $leftCross["AKEY1"];
					$bkey = $leftCross["BKEY1"];
					$partsByBrand[$leftCross["PKEY1"]] = array('akey' => $akey, 'bkey' => $bkey);
				}
			}

			if(empty($this->countPricesByBrands($partsByBrand))) $partsToUnset[] = $index;
		}

		$newParts = array();
		foreach($this->list['PARTS'] as $index => $part){
			if(in_array($index, $partsToUnset)) continue;
			$newParts[] = $part;
		}
		$this->list['PARTS'] = $newParts;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getList()
	{
		return $this->list;
	}

	/**
	 * @return $this
	 */
	public function sortList()
	{
		if(empty($this->list['PARTS']) || empty($this->list['PRICES'])) return $this;

		$availablePKList = [];
		$notAvailablePKList = [];
		foreach ($this->list['PRICES'] as $pk => $price){
			$availablePriceList = array_column($price, 'AVAILABLE_NUM');
			$available = $availablePriceList ? max($availablePriceList) : 0;
			if ($available == 0) {
				$notAvailablePKList[] = $pk;
			} else {
				$availablePKList[] = $pk;
			}
		}

		$sortedAvailableList = $this->sortByPriceAsc($availablePKList);
		$sortedNotAvailableList = $this->sortByPriceAsc($notAvailablePKList);
		$this->list['PARTS'] = array_merge($sortedAvailableList, $sortedNotAvailableList);

		return $this;
	}

	/**
	 * @param $pkList
	 * @return array
	 */
	protected function sortByPriceAsc($pkList)
	{
		if(empty($pkList)) return [];

		$sort = [];
		foreach ($pkList as $pk){
			$price = $this->list['PRICES'][$pk];
			$partCost = max(array_column($price, 'PRICE_CONVERTED'));
			$sort[$pk] = $partCost;
		}

		asort($sort);

		$sortedPartsList = [];
		foreach (array_keys($sort) as $pk) {
			foreach ($this->list['PARTS'] as $part) {
				if ($part['PKEY'] != $pk) continue;
				$sortedPartsList[] = $part;
				break;
			}
		}

		return $sortedPartsList;
	}

	/**
	 * @return $this
	 */
	public function loadImages()
	{
		if(empty($this->list['PARTS'])) return $this;

		$this->core->DBSelect("TECDOC");
		if($artIds = $this->getArtIds()){
			$this->unsetPreviousImages();
			$imagesQuery = TDSQL::GetImagesUnion($artIds);
			while($image = $imagesQuery->Fetch()){
				foreach($this->list['PARTS'] as $index => $part){
					if (!($part['AID'] == $image['AID'] && !(strpos($image['PATH'], '0/0.jpg')))) continue;

					if (empty($this->list['PARTS'][$index]['IMG_ZOOM'])) {
						$this->list['PARTS'][$index]['IMG_SRC'] = 'http://' . TECDOC_FILES_PREFIX . $image['PATH'];
						$this->list['PARTS'][$index]['IMG_ZOOM'] = 'Y';
						$this->list['PARTS'][$index]['IMG_FROM'] = 'TecDoc';
						continue;
					}

					$this->list['PARTS'][$index]['IMG_ADDITIONAL'][] = 'http://' . TECDOC_FILES_PREFIX . $image['PATH'];
				}
			}
		}

		return $this;
	}

	protected function unsetPreviousImages()
	{
		foreach($this->list['PARTS'] as $index => $part){
			if (isset($this->list['PARTS'][$index]['IMG_SRC'])) unset($this->list['PARTS'][$index]['IMG_SRC']);
			if (isset($this->list['PARTS'][$index]['IMG_ZOOM'])) unset($this->list['PARTS'][$index]['IMG_ZOOM']);
			if (isset($this->list['PARTS'][$index]['IMG_FROM'])) unset($this->list['PARTS'][$index]['IMG_FROM']);
			if (isset($this->list['PARTS'][$index]['IMG_ADDITIONAL'])) unset($this->list['PARTS'][$index]['IMG_ADDITIONAL']);
		}
	}

	/**
	 * @return array
	 */
	protected function getArtIds()
	{
		if (!empty($this->artIds)) return $this->artIds;
		
		$artIds = [];
		foreach($this->list['PARTS'] as &$part){
			$brand = $part['PC_MANUFACTURER'];
			$article = TDMSingleKey($part['PC_SKU']);
			$tdPart = TDSQL::GetPartByPKEY($brand, $article);
			//$tdPart = $this->getTecDocPartByPk($brand, $article);
			if (!empty($tdPart['AID'])) {
				$artIds[] = $tdPart['AID'];
				$part['AID'] = $tdPart['AID'];
				continue;
			}

			$brand = $part['BKEY'];
			$article = TDMSingleKey($part['AKEY']);
			$tdPart = TDSQL::GetPartByPKEY($brand, $article);
			//$tdPart = $this->getTecDocPartByPk($brand, $article);
			if (!empty($tdPart['AID'])) {
				$artIds[] = $tdPart['AID'];
				$part['AID'] = $tdPart['AID'];
			}
		}
		$this->artIds = array_unique($artIds);
		
		return $this->artIds;
	}

	/**
	 * @param $part
	 * @return string
	 */
	protected function getNewPk($part)
	{
		return $part['PKEY'] . '_' . $part['PC_SKU'] . $part['PC_MANUFACTURER'];
	}

	/**
	 * @param $brand
	 * @param $article
	 * @param $results
	 */
	protected function lookupByBrandNumberInTecDoc($brand, $article, &$results)
	{
		$this->core->DBSelect("TECDOC");
		$tdResults = TDSQL::LookupByBrandNumber($brand, $article);
		while ($tdPart = $tdResults->Fetch()) {
			$akey = TDMSingleKey($tdPart["ARTICLE"]);
			$bkey = TDMSingleKey($tdPart["BRAND"], true);
			$pkey = $bkey . $akey;
			$results[$pkey] = array('akey' => $akey, 'bkey' => $bkey);
		}
	}

	/**
	 * @param $brand
	 * @param $article
	 * @param $results
	 */
	protected function lookupByBrandNumberInPrices($brand, $article, &$results)
	{
		$this->core->DBSelect("MODULE");
		$pricesResults = new TDMQuery();
		$pricesResults->SimpleSelect('SELECT * FROM TDM_PRICES WHERE AKEY="' . $article . '" AND BKEY="' . TDMSingleKey($brand, true) . '" ');
		while ($pricePart = $pricesResults->Fetch()) {
			$akey = $pricePart["AKEY"];
			$bkey = $pricePart["BKEY"];
			$pkey = $bkey . $akey;
			if (!empty($results[$pkey])) continue;
			$results[$pkey] = array('akey' => $akey, 'bkey' => $bkey);
		}
	}

	/**
	 * @param $parts
	 * @return string
	 */
	protected function getBigPricesQuery($parts)
	{
		$sql = $unionSql = "";
		foreach ($parts as $info) {
			$sql .= $unionSql . 'SELECT * FROM TDM_PRICES WHERE BKEY="' . $info["bkey"] . '" AND AKEY="' . $info["akey"] . '" ';
			$unionSql = " UNION ";
		}

		return $sql;
	}

	/**
	 * @param $parts
	 * @return null
	 */
	protected function countPricesByBrands($parts)
	{
		$this->core->DBSelect("MODULE");
		$pricesResults = new TDMQuery();
		$pricesResults->SimpleSelect($this->getBigPricesQuery($parts));

		return $pricesResults->RowsCount;
	}

	/**
	 * @param array $settings TecDoc Settings
	 * @return $this
	 */
	public function addPagination($settings = [])
	{
		if (empty($this->list['PARTS'])) return $this;

		$this->list['PAGINATION']['TOTAL_ITEMS'] = count($this->list['PARTS']);
		$pageSize = $settings['ITEMS_ON_PAGE_' . $this->list['VIEW']];
		$this->list['PAGINATION']['TOTAL_PAGES'] = ceil($this->list['PAGINATION']['TOTAL_ITEMS'] / $pageSize);
		$currentPage = intval($_REQUEST['page']) ?: 1;
		
		if ($this->list['PAGINATION']['TOTAL_PAGES'] < $currentPage) {
			$currentPage = $this->list['PAGINATION']['TOTAL_PAGES'];
		}

		$partsPaginationList = [];
		if ($this->list['PAGINATION']['TOTAL_ITEMS'] > $pageSize) {
			if ($pageSize < 6) $pageSize = 6;
			if ($pageSize > 100) $pageSize = 100;
			
			$startPosition = $pageSize * ($currentPage - 1);
			$endPosition = $pageSize * $currentPage;
			$index = 0;
			$itemsOnCurrentPage = 0;
			foreach ($this->list['PARTS'] as $part) {
				$index++;
				if ($index > $startPosition && $index <= $endPosition) {
					$itemsOnCurrentPage++;
					$partsPaginationList[] = $part;
				}

				if ($index >= $endPosition) break;
			}
			$arResult['PAGINATION']['ITEMS_ON_THIS_PAGE'] = $itemsOnCurrentPage;
			$arResult['PAGINATION']['PAGES_LINK'] = $_SERVER['REQUEST_URI'];
		}

		$arResult['PAGINATION']['ITEMS_ON_PAGE'] = $pageSize;
		$arResult['PAGINATION']['CURRENT_PAGE'] = $currentPage;

		if ($partsPaginationList) $this->list['PARTS'] = $partsPaginationList;

		return $this;
	}

	/**
	 * @param bool $isSearchListView
	 * @return $this
	 */
	public function loadProperties($isSearchListView = false)
	{
		$artIds = $this->getArtIds();
		if (empty($artIds)) return $this;
		
		$rsProps = TDSQL::GetPropertysUnion($artIds);
		$pks = [];
		foreach($this->list['PARTS'] as $index => $part){
			$pks[$part['AID']][] = $index;
		}
		
		while ($prop = $rsProps->Fetch()) {
			if (empty($prop['VALUE']) || !in_array($prop['AID'], $artIds)) continue;

			if ($prop['CRID'] == 836 || $prop['CRID'] == 596) {
				$prop['NAME'] = $prop['VALUE']; $prop['VALUE'] = '';
			}

			foreach($pks[$prop['AID']] as $index){
				if (isset($this->list['PARTS'][$index]['PROPS'][$prop['NAME']])) continue;

				$this->list['PARTS'][$index]['PROPS_COUNT']++;
				$this->list['PARTS'][$index]['PROPS'][$prop['NAME']] = $isSearchListView ? $prop['VALUE'] : ['CRID' => $prop['CRID'], 'VALUE' => $prop['VALUE']];
			}
		}

		return $this;
	}

	protected function testLookupByBrandNumber($brand, $article)
	{
		$tdPart = TDSQL::LookupByBrandNumber($brand, $article);
		PcHelper::dump('LookupByBrandNumber Result for ' . $brand . ' ' . $article);
		while ($fetch = $tdPart->Fetch()) PcHelper::dump($fetch, 1);
	}

	protected function testLookupByNumber($article)
	{
		$tdPart = TDSQL::LookupByNumber($article);
		PcHelper::dump('LookupByNumber Result for ' . $article);
		while ($fetch = $tdPart->Fetch()) PcHelper::dump($fetch, 1);
	}

	protected function testLookupByPk($brand, $article)
	{
		$tdPart = TDSQL::GetPartByPKEY($brand, $article);
		PcHelper::dump('GetPartByPKEY Result for ' . $brand . ' ' . $article);
		PcHelper::dump($tdPart, 1);
	}

	/**
	 * There are some problems in this method.
	 * I don't know what exactly but something wrong with it.
	 * For continuing I need TDSQL::GetPartByPKEY code or some explanations
	 * about TecDoc DB structure
	 * @param $brand
	 * @param $article
	 * @return array
	 */
	protected function getTecDocPartByPk($brand, $article)
	{
		$sql = "
			SELECT DISTINCT
			  IF (ART_LOOKUP.ARL_KIND IN (3, 4), BRANDS.BRA_BRAND, SUPPLIERS.SUP_BRAND) AS BRAND,
			  ART_LOOKUP.ARL_SEARCH_NUMBER AS ARTICLE,
			  ART_LOOKUP.ARL_KIND AS KIND,
			  ARTICLES.ART_ID AS AID,
			  DES_TEXTS.TEX_TEXT AS TD_NAME
			FROM ART_LOOKUP
			  LEFT JOIN BRANDS ON BRANDS.BRA_ID = ART_LOOKUP.ARL_BRA_ID
			  INNER JOIN ARTICLES ON ARTICLES.ART_ID = ART_LOOKUP.ARL_ART_ID
			  INNER JOIN SUPPLIERS ON SUPPLIERS.SUP_ID = ARTICLES.ART_SUP_ID
			  INNER JOIN DESIGNATIONS ON DESIGNATIONS.DES_ID = ARTICLES.ART_COMPLETE_DES_ID
			  INNER JOIN DES_TEXTS ON DES_TEXTS.TEX_ID = DESIGNATIONS.DES_TEX_ID
			WHERE ART_LOOKUP.ARL_SEARCH_NUMBER = '{$article}'
				AND ART_LOOKUP.ARL_KIND IN (1, 2, 3, 4, 5)
				AND DESIGNATIONS.DES_LNG_ID = " . TDM_LANG_ID . "
			ORDER BY ARL_KIND DESC";

		$result = new TDSQLQuery();
		$result->QuerySelect($sql);
		while ($row = $result->Fetch()) {
			$fetchedBrand = TDMSingleKey($row['BRAND'], true);
			if ($fetchedBrand == $brand) return $row;
		}

		return [];
	}
}