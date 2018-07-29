<?php

require_once(DIR_AUTOPARTS . 'tdmcore/PcOpenCartPartItemProcessor.php');

class ControllerProductProduct extends Controller
{
	public function index()
	{
		$this->load->language('product/product');
		$this->load->language('coloring/coloring');
		$this->load->model('catalog/category');
		$this->load->model('catalog/manufacturer');
		$this->load->model('catalog/product');
		$this->load->model('catalog/review');
		$this->load->model('setting/setting');
		$this->load->model('tool/image');

		$data['breadcrumbs'] = $this->getProductBreadcrumbs();
		$data['currency_code'] = $this->currency->getCode();
		$this->getXdsColoringThemeSettings($data);

		if (isset($this->request->get['path'])) {
			$path = '';
			$parts = explode('_', (string)$this->request->get['path']);

			foreach ($parts as $path_id) {
				if (!$path) {
					$path = $path_id;
				} else {
					$path .= '_' . $path_id;
				}

				if ($category_info = $this->model_catalog_category->getCategory($path_id)) {
					$data['breadcrumbs'][] = [
						'text' => $category_info['name'],
						'href' => $this->url->link('product/category', 'path=' . $path)
					];
				}
			}

			// Set the last category breadcrumb
			$category_id = (int)array_pop($parts);
			if ($category_info = $this->model_catalog_category->getCategory($category_id)) {
				$data['breadcrumbs'][] = [
					'text' => $category_info['name'],
					'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $this->getCommonUrlParams())
				];
			}
		}
		
		if (isset($this->request->get['manufacturer_id'])) {
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('text_brand'),
				'href' => $this->url->link('product/manufacturer')
			];

			if ($manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->get['manufacturer_id'])) {
				$data['breadcrumbs'][] = [
					'text' => $manufacturer_info['name'],
					'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $this->getCommonUrlParams())
				];
			}
		}

		if (isset($this->request->get['search']) || isset($this->request->get['tag'])) {
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('text_search'),
				'href' => $this->url->link('product/search', $this->getExtendedUrlParams())
			];
		}

		$product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;

		if ($product_info = $this->model_catalog_product->getProduct($product_id)) {
			$url = '';
			if (isset($this->request->get['path'])) $url .= '&path=' . $this->request->get['path'];
			if (isset($this->request->get['filter'])) $url .= '&filter=' . $this->request->get['filter'];
			if (isset($this->request->get['manufacturer_id'])) $url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			$url .= $this->getExtendedUrlParams();

			$data['breadcrumbs'][] = [
				'text' => $product_info['name'],
				'href' => $this->url->link('product/product', $url . '&product_id=' . $this->request->get['product_id'])
			];

			$data['heading_title'] = $product_info['name'];

			$data['product_id'] = (int)$this->request->get['product_id'];
			$data['manufacturer'] = $product_info['manufacturer'];
			$data['manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);
			$data['model'] = $product_info['model'];
			$data['sku'] = $product_info['sku'];
			$data['jan'] = $product_info['jan'];
			$data['isbn'] = $product_info['isbn'];
			$data['reward'] = $product_info['reward'];
			$data['points'] = $product_info['points'];

			$pip = new PcOpenCartPartItemProcessor($product_id);
			$data['pc_tecdoc'] = $pip->getData();

			// PC: Use qty from TD table
			$product_info['quantity'] = $data['pc_tecdoc']['available'];
			if ($product_info['quantity'] <= 0) {
				$data['stock'] = $product_info['stock_status'];
			} elseif ($this->config->get('config_stock_display')) {
				$data['stock'] = $product_info['quantity'];
			} else {
				$data['stock'] = $this->language->get('text_instock');
			}
			
			$data['product_quantity'] = $product_info['quantity'];
			$data['config_stock_display'] = $this->config->get('config_stock_display');
			$data['product_stock'] = $product_info['quantity'] <= 0 ?  'Нет в наличии' : $this->language->get('text_instock');

			$data['popup'] = $product_info['image'] ? $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height')) : '';
			$data['thumb'] = $product_info['image'] ? $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height')) : '';
			$data['thumb_small'] = $product_info['image'] ? $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height')) : '';
			$data['img_small'] = $this->config->get('config_image_additional_width').'x'.$this->config->get('config_image_additional_height');
			$data['img_big'] = $this->config->get('config_image_thumb_width').'x'.$this->config->get('config_image_thumb_height');
      
			$data['images'] = [];
			$results = $this->model_catalog_product->getProductImages($this->request->get['product_id']);
			foreach ($results as $result) {
				$data['images'][] = [
					'helper_thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height')),
					'popup' => $this->model_tool_image->resize($result['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height')),
					'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'))
				];
			}

			// PC: Use price from TD table
			$data['price'] = ($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')
				? $this->currency->format($this->tax->calculate($data['pc_tecdoc']['price'], $product_info['tax_class_id'], $this->config->get('config_tax')))
				: false;
			$data['special'] = (float)$product_info['special']
				? $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')))
				: false;
			$data['tax'] = $this->config->get('config_tax')
				? ($this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price']))
				: false;

			$data['discounts'] = [];
			$discounts = $this->model_catalog_product->getProductDiscounts($this->request->get['product_id']);
			foreach ($discounts as $discount) {
				$data['discounts'][] = [
					'quantity' => $discount['quantity'],
					'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')))
				];
			}

			$data['options'] = [];
			$options = $this->model_catalog_product->getProductOptions($this->request->get['product_id']);
			foreach ($options as $option) {
				$product_option_value_data = [];
				foreach ($option['product_option_value'] as $option_value) {
					if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
						$price = (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']
							? $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false))
							: false;

						$product_option_value_data[] = [
							'product_option_value_id' => $option_value['product_option_value_id'],
							'option_value_id'         => $option_value['option_value_id'],
							'name'                    => $option_value['name'],
							'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
							'price'                   => $price,
							'price_prefix'            => $option_value['price_prefix']
						];
					}
				}

				$data['options'][] = [
					'product_option_id'    => $option['product_option_id'],
					'product_option_value' => $product_option_value_data,
					'option_id'            => $option['option_id'],
					'name'                 => $option['name'],
					'type'                 => $option['type'],
					'value'                => $option['value'],
					'required'             => $option['required']
				];
			}

			$data['minimum'] = $product_info['minimum'] ?: 1;
			$data['review_status'] = $this->config->get('config_review_status');
			$data['review_guest'] = $this->config->get('config_review_guest') || $this->customer->isLogged();
			$data['customer_name'] = $this->customer->isLogged() ? $this->customer->getFirstName() . '&nbsp;' . $this->customer->getLastName() : '';
			$data['reviews'] = sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']);
			$data['rating'] = (int)$product_info['rating'];
			$data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
			$data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($this->request->get['product_id']);

			$data['products'] = [];
			$results = $this->model_catalog_product->getProductRelated($this->request->get['product_id']);
			foreach ($results as $result) {
				$image = $result['image']
					? $this->model_tool_image->resize($result['image'], $this->config->get('config_image_related_width'), $this->config->get('config_image_related_height'))
					: $this->model_tool_image->resize('placeholder.png', $this->config->get('config_image_related_width'), $this->config->get('config_image_related_height'));

				$price = ($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')
					? $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')))
					: false;

				$special = (float)$result['special']
					? $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')))
					: false;

				$tax = $this->config->get('config_tax') ? ($this->currency->format((float)$result['special'] ? $result['special'] : $result['price'])) : false;
				$rating = $this->config->get('config_review_status') ? (int)$result['rating'] : false;

				$data['products'][] = [
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('config_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $rating,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				];
			}

			$data['tags'] = [];
			if ($product_info['tag']) {
				$tags = explode(',', $product_info['tag']);
				foreach ($tags as $tag) {
					$data['tags'][] = array(
						'tag'  => trim($tag),
						'href' => $this->url->link('product/search', 'tag=' . trim($tag))
					);
				}
			}

			$data['recurrings'] = $this->model_catalog_product->getProfiles($this->request->get['product_id']);
			$this->model_catalog_product->updateViewed($this->request->get['product_id']);

			if ($this->config->get('config_google_captcha_status')) {
				$this->document->addScript('https://www.google.com/recaptcha/api.js');
				$data['site_key'] = $this->config->get('config_google_captcha_public');
			} else {
				$data['site_key'] = '';
			}

			$this->addSeoStuff($product_info);
			$this->addTextLabels($data, $product_info);
			$this->addCommonViews($data);

			$tpl = file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/product.tpl')
				? $this->config->get('config_template') . '/template/product/product.tpl'
				: 'default/template/product/product.tpl';

			$this->response->setOutput($this->load->view($tpl, $data));
		} else {
			$url = '';
			if (isset($this->request->get['path'])) $url .= '&path=' . $this->request->get['path'];
			if (isset($this->request->get['filter'])) $url .= '&filter=' . $this->request->get['filter'];
			if (isset($this->request->get['manufacturer_id'])) $url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			$url .= $this->getExtendedUrlParams();

			$data['breadcrumbs'][] = [
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('product/product', $url . '&product_id=' . $product_id)
			];

			$this->document->setTitle($this->language->get('text_error'));
			$data['heading_title'] = $this->language->get('text_error');
			$data['text_error'] = $this->language->get('text_error');
			$data['button_continue'] = $this->language->get('button_continue');
			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');
			$this->addCommonViews($data);

			$tpl = file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/error/not_found.tpl')
				? $this->config->get('config_template') . '/template/error/not_found.tpl'
				: 'default/template/error/not_found.tpl';

			$this->response->setOutput($this->load->view($tpl, $data));
		}
	}

	/**
	 * @return array
	 */
	protected function getProductBreadcrumbs()
	{
		return [
			[
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home')
			],
		];
	}

	/**
	 * @param $data
	 */
	protected function getXdsColoringThemeSettings(&$data)
	{
		$xds_coloring_theme = $this->model_setting_setting->getSetting('xds_coloring_theme');
		$language_id = $this->config->get('config_language_id');

		$data['short_description_off'] = isset($xds_coloring_theme['xds_coloring_theme_product_short_description']) ? $xds_coloring_theme['xds_coloring_theme_product_short_description'] : '';
		$data['short_attribute_off'] = isset($xds_coloring_theme['xds_coloring_theme_product_short_attributes']) ? $xds_coloring_theme['xds_coloring_theme_product_short_attributes'] : '';
		$data['social_likes_off'] = isset($xds_coloring_theme['xds_coloring_theme_product_social_likes']) ? $xds_coloring_theme['xds_coloring_theme_product_social_likes'] : '';
		$data['disable_cart_button'] = isset($xds_coloring_theme['xds_coloring_theme_disable_cart_button']) ? $xds_coloring_theme['xds_coloring_theme_disable_cart_button'] : '';
		$data['related_product_position'] = isset($xds_coloring_theme['xds_coloring_theme_related_product_position']) ? $xds_coloring_theme['xds_coloring_theme_related_product_position'] : '';
		$data['disable_cart_button_text'] = isset($xds_coloring_theme['xds_coloring_theme_disable_cart_button_text']) ? $xds_coloring_theme['xds_coloring_theme_disable_cart_button_text'][$language_id] : '';
	}

	/**
	 * @return string
	 */
	protected function getCommonUrlParams()
	{
		$url = '';
		if (isset($this->request->get['sort'])) $url .= '&sort=' . $this->request->get['sort'];
		if (isset($this->request->get['order'])) $url .= '&order=' . $this->request->get['order'];
		if (isset($this->request->get['page'])) $url .= '&page=' . $this->request->get['page'];
		if (isset($this->request->get['limit'])) $url .= '&limit=' . $this->request->get['limit'];
		
		return $url;
	}

	/**
	 * @param $product_info
	 */
	protected function addSeoStuff($product_info)
	{
		$this->document->setTitle($product_info['meta_title']);
		$this->document->setDescription($product_info['meta_description']);
		$this->document->setKeywords($product_info['meta_keyword']);
		$this->document->addLink($this->url->link('product/product', 'product_id=' . $this->request->get['product_id']), 'canonical');
		$this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');
		$this->document->addStyle('catalog/view/theme/coloring/assets/owl-carousel/owl.carousel.css');
		$this->document->addStyle('catalog/view/theme/coloring/assets/owl-carousel/owl.theme.css');
		$this->document->addScript('catalog/view/theme/coloring/assets/owl-carousel/owl.carousel.min.js');
		$this->document->addScript('catalog/view/theme/coloring/assets/share/social-likes.min.js');
		$this->document->addStyle('catalog/view/theme/coloring/assets/share/social-likes_birman.css');
	}

	/**
	 * @param $data
	 */
	protected function addTextLabels(&$data, $product_info)
	{
		$data['product_short_description_text'] = $this->language->get('product_short_description_text');
		$data['product_read_more_text'] = $this->language->get('product_read_more_text');
		$data['product_all_specifications_text'] = $this->language->get('product_all_specifications_text');
		$data['product_required_text'] = $this->language->get('product_required_text');
		$data['product_share_text'] = $this->language->get('product_share_text');
		$data['product_quantity_text'] = sprintf($this->language->get('product_quantity_text'), $product_info['quantity']);
		$data['text_select'] = $this->language->get('text_select');
		$data['text_manufacturer'] = $this->language->get('text_manufacturer');
		$data['text_model'] = $this->language->get('text_model');
		$data['text_sku'] = $this->language->get('Артикул:');
		$data['text_isbn'] = $this->language->get('Применение:');
		$data['text_jan'] = $this->language->get('Марка:');
		$data['text_reward'] = $this->language->get('text_reward');
		$data['text_points'] = $this->language->get('text_points');
		$data['text_stock'] = $this->language->get('text_stock');
		$data['text_discount'] = $this->language->get('text_discount');
		$data['text_tax'] = $this->language->get('text_tax');
		$data['text_option'] = $this->language->get('text_option');
		$data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
		$data['text_write'] = $this->language->get('text_write');
		$data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '', 'SSL'));
		$data['text_note'] = $this->language->get('text_note');
		$data['text_tags'] = $this->language->get('text_tags');
		$data['text_related'] = $this->language->get('text_related');
		$data['text_loading'] = $this->language->get('text_loading');

		$data['entry_qty'] = $this->language->get('entry_qty');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_review'] = $this->language->get('entry_review');
		$data['entry_rating'] = $this->language->get('entry_rating');
		$data['entry_good'] = $this->language->get('entry_good');
		$data['entry_bad'] = $this->language->get('entry_bad');

		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');
		$data['button_upload'] = $this->language->get('button_upload');
		$data['button_continue'] = $this->language->get('button_continue');

		$data['tab_description'] = $this->language->get('tab_description');
		$data['tab_attribute'] = $this->language->get('tab_attribute');
		$data['tab_review'] = sprintf($this->language->get('tab_review'), $product_info['reviews']);

		$data['text_payment_recurring'] = $this->language->get('text_payment_recurring');
	}

	/**
	 * @return string
	 */
	protected function getExtendedUrlParams()
	{
		$url = '';
		if (isset($this->request->get['search'])) $url .= '&search=' . $this->request->get['search'];
		if (isset($this->request->get['tag'])) $url .= '&tag=' . $this->request->get['tag'];
		if (isset($this->request->get['description'])) $url .= '&description=' . $this->request->get['description'];
		if (isset($this->request->get['category_id'])) $url .= '&category_id=' . $this->request->get['category_id'];
		if (isset($this->request->get['sub_category'])) $url .= '&sub_category=' . $this->request->get['sub_category'];
		$url .= $this->getCommonUrlParams();

		return $url;
	}

	/**
	 * @param $data
	 */
	protected function addCommonViews(&$data)
	{
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
	}

	public function review()
	{
		$this->load->language('product/product');

		$this->load->model('catalog/review');

		$data['text_no_reviews'] = $this->language->get('text_no_reviews');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['reviews'] = array();

		$review_total = $this->model_catalog_review->getTotalReviewsByProductId($this->request->get['product_id']);

		$results = $this->model_catalog_review->getReviewsByProductId($this->request->get['product_id'], ($page - 1) * 5, 5);

		foreach ($results as $result) {
			$data['reviews'][] = array(
				'author'     => $result['author'],
				'text'       => nl2br($result['text']),
				'rating'     => (int)$result['rating'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$pagination = new Pagination();
		$pagination->total = $review_total;
		$pagination->page = $page;
		$pagination->limit = 5;
		$pagination->url = $this->url->link('product/product/review', 'product_id=' . $this->request->get['product_id'] . '&page={page}');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * 5) + 1 : 0, ((($page - 1) * 5) > ($review_total - 5)) ? $review_total : ((($page - 1) * 5) + 5), $review_total, ceil($review_total / 5));

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/review.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/product/review.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/product/review.tpl', $data));
		}
	}

	public function write() {
		$this->load->language('product/product');

		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 25)) {
				$json['error'] = $this->language->get('error_name');
			}

			if ((utf8_strlen($this->request->post['text']) < 25) || (utf8_strlen($this->request->post['text']) > 1000)) {
				$json['error'] = $this->language->get('error_text');
			}

			if (empty($this->request->post['rating']) || $this->request->post['rating'] < 0 || $this->request->post['rating'] > 5) {
				$json['error'] = $this->language->get('error_rating');
			}

			if ($this->config->get('config_google_captcha_status') && empty($json['error'])) {
				if (isset($this->request->post['g-recaptcha-response'])) {
					$recaptcha = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($this->config->get('config_google_captcha_secret')) . '&response=' . $this->request->post['g-recaptcha-response'] . '&remoteip=' . $this->request->server['REMOTE_ADDR']);

					$recaptcha = json_decode($recaptcha, true);

					if (!$recaptcha['success']) {
						$json['error'] = $this->language->get('error_captcha');
					}
				} else {
					$json['error'] = $this->language->get('error_captcha');
				}
			}

			if (!isset($json['error'])) {
				$this->load->model('catalog/review');

				$this->model_catalog_review->addReview($this->request->get['product_id'], $this->request->post);

				$json['success'] = $this->language->get('text_success');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getRecurringDescription() {
		$this->language->load('product/product');
		$this->load->model('catalog/product');

		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		} else {
			$product_id = 0;
		}

		if (isset($this->request->post['recurring_id'])) {
			$recurring_id = $this->request->post['recurring_id'];
		} else {
			$recurring_id = 0;
		}

		if (isset($this->request->post['quantity'])) {
			$quantity = $this->request->post['quantity'];
		} else {
			$quantity = 1;
		}

		$product_info = $this->model_catalog_product->getProduct($product_id);
		$recurring_info = $this->model_catalog_product->getProfile($product_id, $recurring_id);

		$json = array();

		if ($product_info && $recurring_info) {
			if (!$json) {
				$frequencies = array(
					'day'        => $this->language->get('text_day'),
					'week'       => $this->language->get('text_week'),
					'semi_month' => $this->language->get('text_semi_month'),
					'month'      => $this->language->get('text_month'),
					'year'       => $this->language->get('text_year'),
				);

				if ($recurring_info['trial_status'] == 1) {
					$price = $this->currency->format($this->tax->calculate($recurring_info['trial_price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')));
					$trial_text = sprintf($this->language->get('text_trial_description'), $price, $recurring_info['trial_cycle'], $frequencies[$recurring_info['trial_frequency']], $recurring_info['trial_duration']) . ' ';
				} else {
					$trial_text = '';
				}

				$price = $this->currency->format($this->tax->calculate($recurring_info['price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')));

				if ($recurring_info['duration']) {
					$text = $trial_text . sprintf($this->language->get('text_payment_description'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
				} else {
					$text = $trial_text . sprintf($this->language->get('text_payment_cancel'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
				}

				$json['success'] = $text;
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}