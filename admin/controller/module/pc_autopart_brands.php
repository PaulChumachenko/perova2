<?php

class ControllerModulePcAutopartBrands extends Controller
{
	private $error = [];

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
		$data['breadcrumbs'] = $this->getBreadCrumbs();
		$this->setLanguageParams($data);

		$data['action'] = $this->url->link('module/pc_autopart_brands', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		$data['pc_autopart_brands_status'] = isset($this->request->post['pc_autopart_brands_status'])
			? $this->request->post['pc_autopart_brands_status']
			: $this->config->get('pc_autopart_brands_status');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('module/pc_autopart_brands.tpl', $data));
	}

	protected function validate()
	{
		if (!$this->user->hasPermission('modify', 'module/pc_autopart_brands')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	/**
	 * @return array
	 */
	protected function getBreadCrumbs()
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

	protected function setLanguageParams(&$data)
	{
		$data['heading_title'] = $this->language->get('heading_title');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['text_status_enabled'] = $this->language->get('text_status_enabled');
		$data['text_status_disabled'] = $this->language->get('text_status_disabled');
	}
}