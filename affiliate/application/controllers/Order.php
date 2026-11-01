<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;
require APPPATH . 'libraries/Format.php';

class Order extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('email');
        $this->load->library('form_validation');
        $this->load->model('Common_model');
        $this->load->model('Product_model');
        $this->load->model('Total_model');
        $this->load->library('user_agent');
        $this->load->model("Form_model");
        $this->load->model('IntegrationModel');
    }

    public function my_orders_list_post()
    {
        $headers = $this->input->request_headers();
        $verify_data = verify_request();
        if(isset($verify_data['status']) && $verify_data['status'] == 401) {
            $response = array(
                'status' => 401,
                'message' => 'Unauthorized Access!',
            );

            $this->response($response, 401); 
        }
        else
        {
            $this->form_validation->set_rules('page_id', 'page_id', 'required|trim',
            array('required'      => 'Oops ! page id is required.'
            ));

            $this->form_validation->set_rules('per_page', 'per_page', 'required|trim',
                array('required'      => 'Oops ! per page is required.'
            ));

            $this->form_validation->set_error_delimiters('', '');
            if($this->form_validation->run()== false)
            {
                if(!empty(form_error('page_id')))$errors['page_id'] =form_error('page_id');
                if(!empty(form_error('per_page')))$errors['per_page'] =form_error('per_page');

                $response['message'] = "Please required field";
                $response['errors'] = $errors;

                $this->response($response, 422);
            }
            else
            {
                $id = $verify_data['userdata']['id'];
                $filter_status = $this->input->post('filter_status');
                $page_id = $this->input->post('page_id');
                $per_page = $this->input->post('per_page');

                $page = max((int)$page_id,1);
                $post = $this->input->post(null,true);
                $filter = array(
                    'limit' => $per_page,
                    'page' => $page,
                    'user_id' => $id,
                    'o_status_gt' => 1,
                );

                if(isset($filter_status) && $filter_status != '')
                {
                    $filter['o_status'] = $filter_status;
                }

                list($data['orders'],$total) = $this->Order_model->getAllOrders($filter);
                
                $data['start_from'] = (($page-1) * $filter['limit'])+1;
                $data['wallet_status'] = $this->Wallet_model->request_status;

                $response = array(
                    'status' => TRUE,
                    'message' => 'order list get successfully',
                    'data' => $data
                );
                $this->response($response, REST_Controller::HTTP_OK);
            }
        }
    }

    public function my_orders_status_list_get()
    {
        $headers = $this->input->request_headers();
        $verify_data = verify_request();
        if(isset($verify_data['status']) && $verify_data['status'] == 401) {
            $response = array(
                'status' => 401,
                'message' => 'Unauthorized Access!',
            );

            $this->response($response, 401); 
        }
        else
        {
            $data = $this->Order_model->status();
            $response = array(
                'status' => TRUE,
                'message' => 'my orders status list get successfully',
                'data' => $data
            );
            $this->response($response, REST_Controller::HTTP_OK);
        }
    }
}
?>