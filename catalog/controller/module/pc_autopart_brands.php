<?php

class ControllerModulePcAutopartBrands extends Controller
{
	public function index()
	{
		$this->load->language('module/pc_autopart_brands');
		$this->load->model('module/pc_autopart_brands');
		$this->load->model('tool/image');

		$result = [];
		if (isset($this->request->get['brands'])) {
			$models = $this->model_module_pc_autopart_brands->getBrandsByTitle($this->request->get['brands']);
			if ($models) {
				foreach($models as &$model){
					if ($model['logo'] && is_file(DIR_IMAGE . $model['logo'])) {
						$model['thumb'] = $this->model_tool_image->resize($model['logo'], 150, 150);
					}

					$html = file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/pc_autopart_brands.tpl')
						? $this->load->view($this->config->get('config_template') . '/template/module/pc_autopart_brands.tpl', ['model' => $model])
						: $this->load->view('default/template/module/pc_autopart_brands.tpl', ['model' => $model]);

					$result[$model['brand']] = $html;
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($result));
	}
}