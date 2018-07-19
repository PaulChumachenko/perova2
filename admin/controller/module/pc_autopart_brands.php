<?php

class ControllerModulePcAutopartBrands extends Controller
{
	private $error = [];

	/**
	 * Action used in config: Modules -> Brands
	 * Just on/off functionality
	 */
	public function index()
	{
		$this->load->language('module/pc_autopart_brands');
		$this->load->model('setting/setting');
		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('pc_autopart_brands', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : null;
		$data['breadcrumbs'] = $this->getConfigBreadcrumbs();
		$this->loadLanguageParams($data);
		$this->loadDefaultBlocks($data);

		$data['action'] = $this->url->link('module/pc_autopart_brands', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		$data['pc_autopart_brands_status'] = isset($this->request->post['pc_autopart_brands_status'])
			? $this->request->post['pc_autopart_brands_status']
			: $this->config->get('pc_autopart_brands_status');

		$this->response->setOutput($this->load->view('module/pc_autopart_brands/config.tpl', $data));
	}

	public function items()
	{
		$this->load->language('module/pc_autopart_brands');
		$this->load->model('module/pc_autopart_brands');
		$this->document->setTitle($this->language->get('heading_title'));

		$this->getList();
	}

	public function add()
	{
		$this->load->language('module/pc_autopart_brands');
		$this->load->model('module/pc_autopart_brands');
		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_module_pc_autopart_brands->addBrand($this->request->post['pc_autopart_brands']);
			$this->session->data['success'] = $this->language->get('text_create_success');

			$url = '';
			if (isset($this->request->get['sort'])) $url .= '&sort=' . $this->request->get['sort'];
			if (isset($this->request->get['order'])) $url .= '&order=' . $this->request->get['order'];
			if (isset($this->request->get['page'])) $url .= '&page=' . $this->request->get['page'];

			$this->response->redirect($this->url->link('module/pc_autopart_brands/items', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit()
	{
		$this->load->language('module/pc_autopart_brands');
		$this->load->model('module/pc_autopart_brands');
		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_module_pc_autopart_brands->editBrand($this->request->get['id'], $this->request->post['pc_autopart_brands']);
			$this->session->data['success'] = $this->language->get('text_update_success');

			$url = '';
			if (isset($this->request->get['sort'])) $url .= '&sort=' . $this->request->get['sort'];
			if (isset($this->request->get['order'])) $url .= '&order=' . $this->request->get['order'];
			if (isset($this->request->get['page'])) $url .= '&page=' . $this->request->get['page'];

			$this->response->redirect($this->url->link('module/pc_autopart_brands/items', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->load->language('module/pc_autopart_brands');
		$this->load->model('module/pc_autopart_brands');
		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->post['selected']) && $this->validate()) {
			$this->model_module_pc_autopart_brands->deleteBrands($this->request->post['selected']);
			$this->session->data['success'] = $this->language->get('text_delete_success');

			$url = '';
			if (isset($this->request->get['sort'])) $url .= '&sort=' . $this->request->get['sort'];
			if (isset($this->request->get['order'])) $url .= '&order=' . $this->request->get['order'];
			if (isset($this->request->get['page'])) $url .= '&page=' . $this->request->get['page'];

			$this->response->redirect($this->url->link('module/pc_autopart_brands/items', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	public function import()
	{
		set_time_limit(3600);
		ini_set('memory_limit', '512M');

		$this->load->language('module/pc_autopart_brands');
		$this->load->model('module/pc_autopart_brands');

		$error = null;
		$allowedMimes = ['application/vnd.ms-excel','text/plain','text/csv','text/tsv'];

		if (!in_array($this->request->files['file']['type'], $allowedMimes)){
			$error = $this->language->get('error_import_mime_type');
		} elseif(empty($this->request->files['file']['name'])){
			$error = $this->language->get('error_import_empty_filename');
		} elseif(!is_file($this->request->files['file']['tmp_name'])){
			$error = $this->language->get('error_import_is_not_file');
		} elseif(!($handle = fopen($this->request->files['file']['tmp_name'], 'r'))){
			$error = $this->language->get('error_import_cannot_open');
		}


		if (!$error) {
			foreach ($this->rowGenerator($handle, ['brand', 'website', 'description'], ';') as $row){
				$this->model_module_pc_autopart_brands->importBrand($row);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode(['error' => $error]));
	}

	/**
	 * @param $handle
	 * @param $fieldsMap
	 * @param $delimiter
	 * @return Generator
	 */
	protected function rowGenerator($handle, $fieldsMap, $delimiter)
	{
		while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
			if (empty($fieldsMap)) {
				yield $row;
			} else {
				$assocRow = [];
				foreach ($fieldsMap as $number => $field) {
					if (isset($row[$number])) $assocRow[$field] = $row[$number];
				}
				yield $assocRow;
			}
		}
	}

	protected function getList() 
	{
		$sort = 'brand';
		$order = isset($this->request->get['order']) ? $this->request->get['order'] : 'ASC';
		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
		$url = '';
		
		if (isset($this->request->get['order'])) $url .= '&order=' . $this->request->get['order'];
		if (isset($this->request->get['page'])) $url .= '&page=' . $this->request->get['page'];
		
		$data['action_add'] = $this->url->link('module/pc_autopart_brands/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['action_delete'] = $this->url->link('module/pc_autopart_brands/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['brands'] = [];

		$filter = [
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		];
		$results = $this->model_module_pc_autopart_brands->getBrands($filter);
		$count_total = $this->model_module_pc_autopart_brands->getBrandsTotal();

		$this->load->model('tool/image');
		foreach ($results as &$result) {
			$result['edit'] = $this->url->link('module/pc_autopart_brands/edit', 'token=' . $this->session->data['token'] . '&id=' . $result['id'] . $url, 'SSL');
			if (!empty($result['logo']) && is_file(DIR_IMAGE . $result['logo'])) {
				$result['thumb'] = $this->model_tool_image->resize($result['logo'], 150, 150);
			}
			$data['brands'][] = $result;
		}

		$data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : null;
		$data['success'] = isset($this->session->data['success']) ? $this->session->data['success'] : null;
		if (isset($this->session->data['success'])) unset($this->session->data['success']);
		$data['selected'] = isset($this->request->post['selected']) ? (array)$this->request->post['selected'] : [];

		$url = '';
		$url .= $order == 'ASC' ? '&order=DESC' : '&order=ASC';
		if (isset($this->request->get['page'])) $url .= '&page=' . $this->request->get['page'];
		$data['sort_brand'] = $this->url->link('module/pc_autopart_brands/items', 'token=' . $this->session->data['token'] . '&sort=brand' . $url, 'SSL');

		$pagination = new Pagination();
		$pagination->total = $count_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$url = '';
		if (isset($this->request->get['sort'])) $url .= '&sort=' . $this->request->get['sort'];
		if (isset($this->request->get['order'])) $url .= '&order=' . $this->request->get['order'];
		$pagination->url = $this->url->link('module/pc_autopart_brands/items', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($count_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($count_total - $this->config->get('config_limit_admin'))) ? $count_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $count_total, ceil($count_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['breadcrumbs'] = $this->getListItemsBreadcrumbs();
		$data['token'] = $this->session->data['token'];
		$this->loadLanguageParams($data);
		$this->loadDefaultBlocks($data);

		$this->response->setOutput($this->load->view('module/pc_autopart_brands/list.tpl', $data));
	}

	protected function getForm()
	{
		$data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : null;
		$data['error_brand'] = isset($this->error['brand']) ? $this->error['brand'] : null;
		$data['error_website'] = isset($this->error['website']) ? $this->error['website'] : null;
		$data['error_logo'] = isset($this->error['logo']) ? $this->error['logo'] : null;
		$data['error_description'] = isset($this->error['description']) ? $this->error['description'] : null;

		$url = '';
		if (isset($this->request->get['sort'])) $url .= '&sort=' . $this->request->get['sort'];
		if (isset($this->request->get['order'])) $url .= '&order=' . $this->request->get['order'];
		if (isset($this->request->get['page'])) $url .= '&page=' . $this->request->get['page'];

		if (!isset($this->request->get['id'])) {
			$data['action'] = $this->url->link('module/pc_autopart_brands/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
			$data['text_action_brand'] = $this->language->get('text_add_brand');
		} else {
			$data['action'] = $this->url->link('module/pc_autopart_brands/edit', 'token=' . $this->session->data['token'] . '&id=' . $this->request->get['id'] . $url, 'SSL');
			$data['text_action_brand'] = $this->language->get('text_edit_brand');
		}
		$data['cancel'] = $this->url->link('module/pc_autopart_brands/items', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$model = null;
		if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$model = $this->model_module_pc_autopart_brands->getBrand($this->request->get['id']);
		}

		$data['model']['brand'] = $this->getAttribute('brand', $model);
		$data['model']['website'] = $this->getAttribute('website', $model);
		$data['model']['logo'] = $this->getAttribute('logo', $model);
		$data['model']['description'] = $this->getAttribute('description', $model);

		$this->load->model('tool/image');
		if (isset($this->request->post['pc_autopart_brands']['logo']) && is_file(DIR_IMAGE . $this->request->post['pc_autopart_brands']['logo'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['pc_autopart_brands']['logo'], 100, 100);
		} elseif (!empty($model) && is_file(DIR_IMAGE . $model['logo'])) {
			$data['thumb'] = $this->model_tool_image->resize($model['logo'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$this->load->model('catalog/manufacturer');
		$data['brands_list'] = $this->model_catalog_manufacturer->getManufacturers(array('start' => 0, 'limit' => 1000));

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		$data['breadcrumbs'] = $this->getListItemsBreadcrumbs();
		$this->loadLanguageParams($data);
		$this->loadDefaultBlocks($data);

		$this->response->setOutput($this->load->view('module/pc_autopart_brands/form.tpl', $data));
	}

	/**
	 * @return bool
	 */
	protected function validateForm()
	{
		$post = $this->request->post['pc_autopart_brands'];

		if (!$this->user->hasPermission('modify', 'module/pc_autopart_brands')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		if (empty($post['brand'])) {
			$this->error['brand'] = $this->language->get('error_empty_brand');
		}
		if ($this->model_module_pc_autopart_brands->isBrandExists($post['brand'], isset($this->request->get['id']) ? $this->request->get['id'] : null)) {
			$this->error['brand'] = $this->language->get('error_brand_duplicate');
		}
		if (mb_strlen($post['website']) > 255) {
			$this->error['website'] = $this->language->get('error_max_length_website');
		}

		return !$this->error;
	}

	protected function validate()
	{
		if (!$this->user->hasPermission('modify', 'module/pc_autopart_brands')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	/**
	 * @param string $attr
	 * @param array $model
	 * @return null
	 */
	protected function getAttribute($attr, $model)
	{
		return isset($this->request->post['pc_autopart_brands'][$attr])
			? $this->request->post['pc_autopart_brands'][$attr]
			: (!empty($model) ? $model[$attr] : null);
	}

	/**
	 * @return array
	 */
	protected function getConfigBreadcrumbs()
	{
		return [
			[
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
			],
			[
				'text' => $this->language->get('text_module'),
				'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
			],
			[
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('module/pc_autopart_brands', 'token=' . $this->session->data['token'], 'SSL')
			]
		];
	}

	/**
	 * @return array
	 */
	protected function getListItemsBreadcrumbs()
	{
		return [
			[
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
			],
			[
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('module/pc_autopart_brands/items', 'token=' . $this->session->data['token'], 'SSL')
			]
		];
	}

	protected function loadLanguageParams(&$data)
	{
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_status_enabled'] = $this->language->get('text_status_enabled');
		$data['text_status_disabled'] = $this->language->get('text_status_disabled');
		$data['text_btn_add'] = $this->language->get('text_btn_add');
		$data['text_btn_edit'] = $this->language->get('text_btn_edit');
		$data['text_btn_delete'] = $this->language->get('text_btn_delete');
		$data['text_btn_import'] = $this->language->get('text_btn_import');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_column_brand'] = $this->language->get('text_column_brand');
		$data['text_column_website'] = $this->language->get('text_column_website');
		$data['text_column_logo'] = $this->language->get('text_column_logo');
		$data['text_column_description'] = $this->language->get('text_column_description');
		$data['text_no_results'] = $this->language->get('text_no_results');
		
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_brand'] = $this->language->get('entry_brand');
		$data['entry_website'] = $this->language->get('entry_website');
		$data['entry_logo'] = $this->language->get('entry_logo');
		$data['entry_description'] = $this->language->get('entry_description');
	}

	protected function loadDefaultBlocks(&$data)
	{
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
	}
}