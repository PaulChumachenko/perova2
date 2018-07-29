<?php

class PcOpenCartPartItemProcessor
{
	/** @var TDMCore */
	private $core;
	private $partId;
	private $part = [];

	/**
	 * @param $partId
	 */
	public function __construct($partId)
	{
		$this->partId = $partId;

		define("TDM_PROLOG_INCLUDED", true);
		require_once('defines.php');
		require_once('init.php');
		$this->core = $TDMCore;
	}

	/**
	 * @return array
	 */
	public function getData()
	{
		$this->loadFromPrices()
			 ->loadImages()
			 ->loadAlternativeArticles();

		return $this->part;
	}

	/**
	 * @return $this
	 */
	protected function loadFromPrices()
	{
		if (empty($this->partId)) return $this;
		
		$this->core->DBSelect("MODULE");
		$prices = new TDMQuery();
		$prices->SimpleSelect("
			SELECT AKEY
				,BKEY
				,ARTICLE 			AS article
				,BRAND 				AS brand
				,PC_SKU
				,PC_MANUFACTURER
				,AVAILABLE 			AS available
				,DAY
				,PRICE 				AS price
			FROM TDM_PRICES 
			WHERE PC_OC_CROSS_ID = '{$this->partId}'
			ORDER BY PC_OC_CROSS_ID ASC
			LIMIT 1");
		
		while ($record = $prices->Fetch()){
			$this->part = $record;
			$this->part['code'] = $record['PC_SKU'];
			$this->part['manufacturer'] = $record['PC_MANUFACTURER'] ?: $record['BRAND'];
			$this->part['en_route'] = $record['DAY'];
			$this->part['analogs_href'] = '/' . TDM_ROOT_DIR . '/search/' . $record['AKEY'] . '/' . BrandNameEncode($record['brand']);
			$this->part['images'] = [];
			$this->part['alts'] = [];
		}
		
		return $this;
	}

	/**
	 * @return $this
	 */
	protected function loadImages()
	{
		if(empty($this->part)) return $this;

		$this->core->DBSelect("TECDOC");
		$artId = $this->getArtId();
		if (!$artId) return $this;

		$imagesQuery = TDSQL::GetImagesUnion([$artId]);
		while($image = $imagesQuery->Fetch()){
			if ($artId != $image['AID'] || mb_strpos($image['PATH'], '0/0.jpg') !== false) continue;
			$this->part['images'][] = 'http://' . TECDOC_FILES_PREFIX . $image['PATH'];
		}

		return $this;
	}

	/**
	 * @return string|null
	 */
	protected function getArtId()
	{
		$brand = $this->part['PC_MANUFACTURER'];
		$article = TDMSingleKey($this->part['PC_SKU']);
		$tdPart = TDSQL::GetPartByPKEY($brand, $article);
		if (!empty($tdPart['AID'])) return $tdPart['AID'];

		$brand = $this->part['BKEY'];
		$article = TDMSingleKey($this->part['AKEY']);
		$tdPart = TDSQL::GetPartByPKEY($brand, $article);
		if (!empty($tdPart['AID'])) return $tdPart['AID'];
		
		return null;
	}

	/**
	 * @return $this
	 */
	protected function loadAlternativeArticles()
	{
		if(empty($this->part)) return $this;

		$this->core->DBSelect("MODULE");
		$alts = new TDMQuery();
		$alts->SimpleSelect("
			SELECT ALTERNATIVE
			FROM PC_ALT_ARTICLES
			WHERE ORIGINAL = '{$this->part['AKEY']}'
			ORDER BY ALTERNATIVE ASC");

		while ($item = $alts->Fetch()) $this->part['alts'][] = reset($item);

		return $this;
	}
}