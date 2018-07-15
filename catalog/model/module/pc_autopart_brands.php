<?php

class ModelModulePcAutopartBrands extends Model
{
	/**
	 * @param string $brands
	 * @return array
	 */
	public function getBrandsByTitle($brands)
	{
		$brands = is_array($brands) ? $brands : [$brands];

		$query = $this->db->query("
			SELECT id, brand, website, logo, description
			FROM " . DB_PREFIX . "pc_autopart_brands
			WHERE brand IN ('" . implode("', '", $brands) . "')");

		return $query->rows;
	}
}
