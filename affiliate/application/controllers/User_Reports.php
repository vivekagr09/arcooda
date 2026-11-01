<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;
require APPPATH . 'libraries/Format.php';

class User_Reports extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('email');
        $this->load->library('form_validation');
        $this->load->model('Common_model');
        $this->load->model('user_model', 'user');
        $this->load->model('Product_model');
        $this->load->model('Report_model');
    }

    public function get_user_reports_post()
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
            array('required'      => 'Oops ! per page count is required.'
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
                $page_id = $this->input->post('page_id');
                $per_page = $this->input->post('per_page');

                $id = $verify_data['userdata']['id'];

                $referlevelSettings = $this->Product_model->getSettings('referlevel');
                $disabled_for = json_decode( (isset($referlevelSettings['disabled_for']) ? $referlevelSettings['disabled_for'] : '[]'),1);
                $refer_status = true;
                if((int)$referlevelSettings['status'] == 0){ $refer_status = false; }
                else if((int)$referlevelSettings['status'] == 2 && in_array($id, $disabled_for)){ $refer_status = false; }

                $data['refer_status'] = $refer_status;

                $data['statistics'] = $this->Report_model->getStatistics(['user_id' => $id]);

                /*Transaction*/
                $page = max(isset($page_id) ? $page_id : 1,1);
                $filter = array();
                $filter['user_id'] = $id;
                $filter['status_gt'] = 1;
                $data['totals'] = $this->Wallet_model->getTotals(['user_id' => $id],true);

                $filter['page'] = $page;
                $filter['limit'] = $per_page;

                $transaction = $this->Report_model->getAllTransaction($filter);
                $data['transaction'] = $transaction['data'];
                

                $response = array(
                    'status' => TRUE,
                    'message' => 'get user reports details successfully',
                    'data' => $data
                );
                $this->response($response, REST_Controller::HTTP_OK);   
            }
        }
    }

 
}
?>