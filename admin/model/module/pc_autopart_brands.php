<?php

class ModelModulePcAutopartBrands extends Model
{
	/**
	 * @return int
	 */
	public function getBrandsTotal()
	{
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "pc_autopart_brands");
		return $query->row['total'];
	}

	/**
	 * @param array $filter
	 * @return array
	 */
	public function getBrands($filter = [])
	{
		$sql = "
			SELECT id, brand, website, logo, description
			FROM " . DB_PREFIX . "pc_autopart_brands
			ORDER BY {$filter['sort']} {$filter['order']}";

		if (isset($filter['start']) || isset($filter['limit'])) {
			if ($filter['start'] < 0) $filter['start'] = 0;
			if ($filter['limit'] < 1) $filter['limit'] = 20;
			$sql .= " LIMIT " . (int)$filter['start'] . "," . (int)$filter['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	/**
	 * @param $id
	 * @return array
	 */
	public function getBrand($id)
	{
		$query = $this->db->query("
			SELECT id, brand, website, logo, description
			FROM " . DB_PREFIX . "pc_autopart_brands
			WHERE id = {$id}");

		return $query->row;
	}

	/**
	 * @param $post
	 * @return int
	 */
	public function addBrand($post)
	{
		$this->event->trigger('pre.admin.pc_autopart_brands.add', $post);

		$this->db->query("
			INSERT INTO " . DB_PREFIX . "pc_autopart_brands SET
				`brand` = '" . $post['brand'] . "'
				, `website` = '" . $post['website'] . "'
				, `logo` = '" . $this->db->escape($post['logo']) . "'
				, `description` = '" . $post['description'] . "'");

		$new_id = $this->db->getLastId();
		$this->cache->delete('pc_autopart_brands');
		$this->event->trigger('post.admin.pc_autopart_brands.add', $new_id);

		return $new_id;
	}

	/**
	 * @param string $brand
	 * @param int $id
	 * @return bool
	 */
	public function isBrandExists($brand, $id = null)
	{
		if (empty($id)){
			$query = $this->db->query("SELECT COUNT(*) AS cnt FROM " . DB_PREFIX . "pc_autopart_brands WHERE brand = '{$brand}'");
			return (bool)$query->row['cnt'];
		} else {
			$query = $this->db->query("SELECT id FROM " . DB_PREFIX . "pc_autopart_brands WHERE brand = '{$brand}'");
			foreach($query->rows as $row){
				if ($row['id'] != $id) return true;
			}
			return false;
		}
	}

	/**
	 * @param $brand_id
	 * @param $post
	 */
	public function editBrand($brand_id, $post)
	{
		$this->event->trigger('pre.admin.pc_autopart_brands.edit', $post);

		$this->db->query("
			UPDATE " . DB_PREFIX . "pc_autopart_brands SET
				brand = '" . $post['brand'] . "'
				, website = '" . $post['website'] . "'
				, logo = '" . $this->db->escape($post['logo']) . "'
				, description = '" . $post['description'] . "'
			WHERE id = {$brand_id}");

		$this->cache->delete('pc_autopart_brands');
		$this->event->trigger('post.admin.pc_autopart_brands.edit', $brand_id);
	}

	/**
	 * @param array $brand_ids
	 */
	public function deleteBrands($brand_ids)
	{
		$this->event->trigger('pre.admin.pc_autopart_brands.delete', $brand_ids);
		$this->db->query("DELETE FROM " . DB_PREFIX . "pc_autopart_brands WHERE id IN (" . implode(', ', $brand_ids) . ")");
		$this->cache->delete('pc_autopart_brands');
		$this->event->trigger('post.admin.pc_autopart_brands.delete', $brand_ids);
	}
}
