<?php
class ControllerModuleCategory extends Controller
{
	public function index()
	{
		$this->load->language('module/category');
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$data['heading_title'] = $this->language->get('heading_title');

		$parts = isset($this->request->get['path']) ? explode('_', (string)$this->request->get['path']) : [];
		$data['category_id'] = isset($parts[0]) ? $parts[0] : 0;
		$data['child_id'] = isset($parts[1]) ? $parts[1] : 0;
		$data['categories'] = [];
		$categories = $this->model_catalog_category->getCategories(0);

		foreach ($categories as $category) {
			$children_data = [];
			if ($category['category_id'] == $data['category_id']) {
				$children = $this->model_catalog_category->getCategories($category['category_id']);
				foreach($children as $child) {
					$filter_data = [
						'filter_category_id' => $child['category_id'],
						'filter_sub_category' => true
					];
					$children_data[] = [
						'category_id' => $child['category_id'],
						'name'        => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
						'href'        => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
					];
				}
			}

			$filter_data = [
				'filter_category_id'  => $category['category_id'],
				'filter_sub_category' => true
			];
			$data['categories'][] = [
				'category_id' => $category['category_id'],
				'name'        => $category['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
				'children'    => $children_data,
				'href'        => $this->url->link('product/category', 'path=' . $category['category_id'])
			];
		}

		$tpl = file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/category.tpl')
			? $this->config->get('config_template') . '/template/module/category.tpl'
			: 'default/template/module/category.tpl';

		return $this->load->view($tpl, $data);
	}
}