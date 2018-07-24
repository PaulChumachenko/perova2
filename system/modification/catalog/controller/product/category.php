<?php

require_once(DIR_AUTOPARTS . 'tdmcore/PcOpenCartCategoryListProcessor.php');

class ControllerProductCategory extends Controller
{
	public function index()
	{
		$this->load->language('product/category');
		$this->load->language('coloring/coloring');

		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		$this->load->model('setting/setting');

		$xds_coloring_theme = $this->model_setting_setting->getSetting('xds_coloring_theme');

		$data['subcategory_left'] = isset($xds_coloring_theme['xds_coloring_theme_left_subcategory']) ? $xds_coloring_theme['xds_coloring_theme_left_subcategory'] : null;
		$data['disable_cart_button'] = isset($xds_coloring_theme['xds_coloring_theme_disable_cart_button']) ? $xds_coloring_theme['xds_coloring_theme_disable_cart_button'] : null;
		$data['disable_cart_button_text'] = isset($xds_coloring_theme['xds_coloring_theme_disable_cart_button_text']) ? $xds_coloring_theme['xds_coloring_theme_disable_cart_button_text'][$this->config->get('config_language_id')] : null;
		$data['description_position'] = isset($xds_coloring_theme['xds_coloring_theme_category_description_position']) ? $xds_coloring_theme['xds_coloring_theme_category_description_position'] : null;

		$filter = isset($this->request->get['filter']) ? $this->request->get['filter'] : null;
		$sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'p.isbn';
		$order = isset($this->request->get['order']) ? $this->request->get['order'] : 'ASC';
		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
		$limit = isset($this->request->get['limit']) ? $this->request->get['limit'] : $this->config->get('config_product_limit');

		$data['breadcrumbs'] = $this->getBreadcrumbs();

		if (isset($this->request->get['path'])) {
			$url = '';
			if (isset($this->request->get['sort'])) $url .= '&sort=' . $this->request->get['sort'];
			if (isset($this->request->get['order'])) $url .= '&order=' . $this->request->get['order'];
			if (isset($this->request->get['limit'])) $url .= '&limit=' . $this->request->get['limit'];

			$path = '';
			$parts = explode('_', (string)$this->request->get['path']);
			$category_id = (int)array_pop($parts);

			foreach ($parts as $path_id) {
				if (!$path) {
					$path = (int)$path_id;
				} else {
					$path .= '_' . (int)$path_id;
				}

				if ($category_info = $this->model_catalog_category->getCategory($path_id)) {
					$data['breadcrumbs'][] = [
						'text' => $category_info['name'],
						'href' => $this->url->link('product/category', 'path=' . $path . $url)
					];
				}
			}
		} else {
			$category_id = 0;
		}

		if ($category_info = $this->model_catalog_category->getCategory($category_id)) {
			$this->document->setTitle($category_info['meta_title']);
			$this->document->setDescription($category_info['meta_description']);
			$this->document->setKeywords($category_info['meta_keyword']);
			$this->document->addLink($this->url->link('product/category', 'path=' . $this->request->get['path']), 'canonical');

			$data['heading_title'] = $category_info['name'];
			$this->getTextLabels($data);

			// Set the last category breadcrumb
			$data['breadcrumbs'][] = array(
				'text' => $category_info['name'],
				'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'])
			);

			$data['thumb'] = $category_info['image'] ? $this->model_tool_image->resize($category_info['image'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height')) : null;
			$data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
			$data['compare'] = $this->url->link('product/compare');

			$url = '';
			if (isset($this->request->get['filter'])) $url .= '&filter=' . $this->request->get['filter'];
			if (isset($this->request->get['sort'])) $url .= '&sort=' . $this->request->get['sort'];
			if (isset($this->request->get['order'])) $url .= '&order=' . $this->request->get['order'];
			if (isset($this->request->get['limit'])) $url .= '&limit=' . $this->request->get['limit'];

			$data['categories'] = [];
			$results = $this->model_catalog_category->getCategories($category_id);

			foreach ($results as $result) {
				$filter_data = [
					'filter_category_id'  => $result['category_id'],
					'filter_sub_category' => true
				];

				$data['categories'][] = [
					'image' => $this->model_tool_image->resize($result['image'], 120, 120),
					'name'  => $result['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '_' . $result['category_id'] . $url)
				];
			}

			$data['products'] = [];
			$filter_data = [
				'filter_category_id' => $category_id,
				'filter_filter'      => $filter,
				'sort'               => $sort,
				'order'              => $order,
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit
			];
			$product_total = $this->model_catalog_product->getTotalProducts($filter_data);
			$results = $this->model_catalog_product->getProducts($filter_data);

			foreach ($results as $result) {
				$image = $result['image']
					? $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'))
					: $this->model_tool_image->resize('placeholder.png', $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));

				$price = ($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')
					? $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')))
					: false;

				$special = $result['special']
					? $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')))
					: false;

				$tax = $this->config->get('config_tax')
					? $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'])
					: false;

				$data['products'][] = [
					'quantity'     => $result['quantity'],
					'product_id'   => $result['product_id'],
					'thumb'        => $image,
					'name'         => $result['name'],
					'description'  => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('config_product_description_length')) . '..',
					'price'        => $price,
					'sku'          => $result['sku'],
					'model'        => $result['model'],
					'ean'          => $result['ean'],
					'mpn'          => $result['mpn'],
					'isbn'         => (empty($result['isbn'])) ? '' : $this->language->get('text_isbn') . ' ' . $result['isbn'],
					'jan'          => (empty($result['jan'])) ? '' : $this->language->get('text_jan') . ' ' . $result['jan'],
					'manufacturer' => $result['manufacturer'],
					'special'      => $special,
					'tax'          => $tax,
					'minimum'      => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'       => $result['rating'],
					'reviews'      => $result['reviews'],
					'href'         => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'])
				];
			}

			$clp = new PcOpenCartCategoryListProcessor(array_column($data['products'], 'product_id'));
			$tdParts = $clp->getParts();
			array_walk($data['products'], function(&$product) use ($tdParts) { $product['pc_tecdoc'] = $tdParts[$product['product_id']]; });

			$data['sorts'] = $this->getSortList();
			$data['limits'] = $this->getLimitList();
			$data['pagination'] = $this->getPagination($product_total, $page, $limit);

			$data['results'] = sprintf($this->language->get('text_pagination'),
				($product_total) ? (($page - 1) * $limit) + 1 : 0,
				((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit),
				$product_total, ceil($product_total / $limit));

			$data['sort'] = $sort;
			$data['order'] = $order;
			$data['limit'] = $limit;
			$data['continue'] = $this->url->link('common/home');
			$this->loadCommonStuff($data);

			$tpl = file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/category.tpl')
				? $this->config->get('config_template') . '/template/product/category.tpl'
				: 'default/template/product/category.tpl';

			$this->response->setOutput($this->load->view($tpl, $data));

		} else {
			$this->processNotFoundResponse($data);
		}
	}

	/**
	 * @return array
	 */
	protected function getBreadcrumbs()
	{
		return [
			[
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home'),
			]
		];
	}

	/**
	 * @param $data
	 */
	protected function processNotFoundResponse(&$data)
	{
		$url = '';
		if (isset($this->request->get['path'])) $url .= '&path=' . $this->request->get['path'];
		if (isset($this->request->get['filter'])) $url .= '&filter=' . $this->request->get['filter'];
		if (isset($this->request->get['sort'])) $url .= '&sort=' . $this->request->get['sort'];
		if (isset($this->request->get['order'])) $url .= '&order=' . $this->request->get['order'];
		if (isset($this->request->get['page'])) $url .= '&page=' . $this->request->get['page'];
		if (isset($this->request->get['limit'])) $url .= '&limit=' . $this->request->get['limit'];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_error'),
			'href' => $this->url->link('product/category', $url)
		];

		$this->document->setTitle($this->language->get('text_error'));
		$data['heading_title'] = $this->language->get('text_error');
		$data['text_error'] = $this->language->get('text_error');
		$data['button_continue'] = $this->language->get('button_continue');
		$data['continue'] = $this->url->link('common/home');
		$this->loadCommonStuff($data);

		$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

		$tpl = file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/error/not_found.tpl')
			? $this->config->get('config_template') . '/template/error/not_found.tpl'
			: 'default/template/error/not_found.tpl';

		$this->response->setOutput($this->load->view($tpl, $data));
	}

	protected function getTextLabels(&$data)
	{
		$data['text_refine'] = $this->language->get('text_refine');
		$data['text_empty'] = $this->language->get('text_empty');
		$data['text_quantity'] = $this->language->get('text_quantity');
		$data['text_manufacturer'] = $this->language->get('text_manufacturer');
		$data['text_model'] = $this->language->get('text_model');
		$data['text_price'] = $this->language->get('text_price');
		$data['text_tax'] = $this->language->get('text_tax');
		$data['text_points'] = $this->language->get('text_points');
		$data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));
		$data['text_sort'] = $this->language->get('text_sort');
		$data['text_limit'] = $this->language->get('text_limit');

		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');
		$data['button_continue'] = $this->language->get('button_continue');
		$data['button_list'] = $this->language->get('button_list');
		$data['button_grid'] = $this->language->get('button_grid');
		$data['disable_cart_button_text'] = 'Нет в наличии';
	}

	protected function loadCommonStuff(&$data)
	{
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
	}

	/**
	 * @return array
	 */
	protected function getSortList()
	{
		$url = '';
		if (isset($this->request->get['filter'])) $url .= '&filter=' . $this->request->get['filter'];
		if (isset($this->request->get['limit'])) $url .= '&limit=' . $this->request->get['limit'];

		return [
			[
				'text'  => $this->language->get('text_isbn_asc'),
				'value' => 'p.isbn-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.isbn&order=ASC' . $url)
			],
			[
				'text'  => $this->language->get('text_isbn_desc'),
				'value' => 'p.isbn-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.isbn&order=DESC' . $url)
			],
			[
				'text'  => 'Производитель (А - Я)',
				'value' => 'm.name-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=m.name&order=ASC' . $url)
			],
			[
				'text'  => 'Производитель (Я - А)',
				'value' => 'm.name-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=m.name&order=DESC' . $url)
			],
			[
				'text'  => $this->language->get('text_price_asc'),
				'value' => 'p.price-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=ASC' . $url)
			],
			[
				'text'  => $this->language->get('text_price_desc'),
				'value' => 'p.price-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=DESC' . $url)
			],
			[
				'text'  => 'Марка (А - Я)',
				'value' => 'p.jan-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.jan&order=ASC' . $url)
			],
			[
				'text'  => 'Марка (Я - А)',
				'value' => 'p.jan-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.jan&order=DESC' . $url)
			],
		];
	}

	/**
	 * @return array
	 */
	protected function getLimitList()
	{
		$url = '';
		if (isset($this->request->get['filter'])) $url .= '&filter=' . $this->request->get['filter'];
		if (isset($this->request->get['sort'])) $url .= '&sort=' . $this->request->get['sort'];
		if (isset($this->request->get['order'])) $url .= '&order=' . $this->request->get['order'];

		$list = [];
		$limits = array_unique([$this->config->get('config_product_limit'), 25, 50, 75, 100]);
		sort($limits);
		foreach ($limits as $value) {
			$list[] = [
				'text'  => $value,
				'value' => $value,
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&limit=' . $value)
			];
		}

		return $list;
	}

	/**
	 * @param $total
	 * @param $page
	 * @param $limit
	 * @return string
	 */
	protected function getPagination($total, $page, $limit)
	{
		$url = '';
		if (isset($this->request->get['filter'])) $url .= '&filter=' . $this->request->get['filter'];
		if (isset($this->request->get['sort'])) $url .= '&sort=' . $this->request->get['sort'];
		if (isset($this->request->get['order'])) $url .= '&order=' . $this->request->get['order'];
		if (isset($this->request->get['limit'])) $url .= '&limit=' . $this->request->get['limit'];

		$pagination = new Pagination();
		$pagination->total = $total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&page={page}');

		return $pagination->render();
	}

}