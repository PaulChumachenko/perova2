<?php  
class ControllerModulePcAutopartBrands extends Controller
{
	public function index()
	{
		$this->load->language('module/pc_autopart_brands');
		$data['heading_title'] = $this->language->get('heading_title');
		$data['content'] = 'Ваш контент';        //можно задать данные, сразу в контроллере

		$this->load->model('catalog/product'); //подключаем любую модель из OpenCart
		$data['product_info']=$this->model_catalog_product->getProduct(42); //используем метод подключенной модели, например getProduct(42) Ц информаци¤ о продукте id  42

		return file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/pc_autopart_brands.tpl')
			? $this->load->view($this->config->get('config_template') . '/template/module/pc_autopart_brands.tpl', $data)
			: $this->load->view('default/template/module/pc_autopart_brands.tpl', $data);
	}
}