<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Common extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model('user_model', 'user');
		$this->load->model('Product_model');
		___construct(1);
	}
	public function term_condition()
	{
		
		$language_id=1;
		
		$row = $this->db->query("SELECT * FROM language where 	is_default=1")->row_array();
		if(count($row)>0)
			$language_id=$row['id'];

		if (isset($this->session) && $this->session->userdata('userLang') !== FALSE)
			$language_id=$this->session->userdata('userLang');

		$data['store'] = $this->Product_model->getSettings('store');
		$data['page'] 	= $this->Product_model->getSettingsWithLanaguage('tnc',$language_id);
		$data['LanguageHtml'] = $this->Product_model->getLanguageHtml('AuthController');
 
		
		$this->load->view('term-condition', $data);
	}

	public function api_document()
	{

		$this->load->model('PagebuilderModel');
		$register_form = $this->PagebuilderModel->getSettings('registration_builder');

		$data['registration_fields'] = json_decode($register_form['registration_builder'],1);

		$this->load->view('document/document_header');
		$this->load->view('document/document_sidebar');
		$this->load->view('document/api_document', $data);
		$this->load->view('document/document_footer');
	}

}