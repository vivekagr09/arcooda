<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Api_Document extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model('user_model', 'user');
		$this->load->model('Product_model');
		___construct(1);
	}
	public function index()
	{
		print_r("expression");
	}
}