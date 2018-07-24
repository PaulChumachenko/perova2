<?php

class PcOpenCartCategoryListProcessor
{
	/** @var TDMCore */
	private $core;
	private $partIds;
	private $partList = [];

	/**
	 * @param array $partIds
	 */
	public function __construct($partIds = [])
	{
		$this->partIds = $partIds;

		define("TDM_PROLOG_INCLUDED", true);
		require_once('defines.php');
		require_once('init.php');
		$this->core = $TDMCore;
	}

	/**
	 * @return array
	 */
	public function getParts()
	{
		$this->loadPartsFromPrices()->loadImages();
		return $this->partList;
	}

	/**
	 * @return $this
	 */
	protected function loadPartsFromPrices()
	{
		if (empty($this->partIds)) return $this;
		
		$this->core->DBSelect("MODULE");
		$prices = new TDMQuery();
		$prices->SimpleSelect("SELECT AKEY, BKEY, BRAND, PC_SKU, PC_MANUFACTURER, AVAILABLE, DAY, PRICE, PC_OC_CROSS_ID FROM TDM_PRICES WHERE PC_OC_CROSS_ID IN ('" . implode("', '", $this->partIds). "')");
		while ($price = $prices->Fetch()){
			$this->partList[$price['PC_OC_CROSS_ID']] = $price;
			$this->partList[$price['PC_OC_CROSS_ID']]['code'] = $price['PC_SKU'];
			$this->partList[$price['PC_OC_CROSS_ID']]['manufacturer'] = $price['PC_MANUFACTURER'] ?: $price['BRAND'];
			$this->partList[$price['PC_OC_CROSS_ID']]['available'] = $price['AVAILABLE'];
			$this->partList[$price['PC_OC_CROSS_ID']]['en_route'] = $price['DAY'];
			$this->partList[$price['PC_OC_CROSS_ID']]['price'] = $price['PRICE'];
			$this->partList[$price['PC_OC_CROSS_ID']]['analogs_href'] = '/' . TDM_ROOT_DIR . '/search/' . $price['AKEY'] . '/' . BrandNameEncode($price['BRAND']);
			$this->partList[$price['PC_OC_CROSS_ID']]['images'] = [];
		}
		
		return $this;
	}

	/**
	 * @return $this
	 */
	protected function loadImages()
	{
		if(empty($this->partList)) return $this;

		$this->core->DBSelect("TECDOC");
		if ($artIds = $this->getArtIds()){
			$imagesQuery = TDSQL::GetImagesUnion($artIds);
			while($image = $imagesQuery->Fetch()){
				foreach($this->partList as $ocCrossId => $part){
					if ($part['AID'] != $image['AID'] || mb_strpos($image['PATH'], '0/0.jpg') !== false) continue;
					$this->partList[$ocCrossId]['images'][] = 'http://' . TECDOC_FILES_PREFIX . $image['PATH'];
				}
			}
		}

		return $this;
	}

	/**
	 * @return array
	 */
	protected function getArtIds()
	{
		$artIds = [];
		foreach($this->partList as $ocCrossId => $part){
			$brand = $part['PC_MANUFACTURER'];
			$article = TDMSingleKey($part['PC_SKU']);
			$tdPart = TDSQL::GetPartByPKEY($brand, $article);
			if (!empty($tdPart['AID'])) {
				$artIds[] = $tdPart['AID'];
				$this->partList[$ocCrossId]['AID'] = $tdPart['AID'];
				continue;
			}

			$brand = $part['BKEY'];
			$article = TDMSingleKey($part['AKEY']);
			$tdPart = TDSQL::GetPartByPKEY($brand, $article);
			if (!empty($tdPart['AID'])) {
				$artIds[] = $tdPart['AID'];
				$this->partList[$ocCrossId]['AID'] = $tdPart['AID'];
			}
		}
		
		return array_unique($artIds);
	}
}