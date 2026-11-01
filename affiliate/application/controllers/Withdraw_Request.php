<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;
require APPPATH . 'libraries/Format.php';
require APPPATH . 'libraries/Affiliate-Script-SDK/AffiliateScript.php';

class Withdraw_Request extends REST_Controller {

    public function __construct() {
        parent::__construct();
         $this->load->model('user_model', 'user');
        $this->load->model('Wallet_model', 'wallet');
        $this->load->library('email');
        $this->load->library('form_validation');
        $this->load->model('Common_model');
        $this->load->model('Product_model');
        $this->load->model('Total_model');
        $this->load->library('user_agent');
        $this->load->model("Form_model");
        $this->load->model('IntegrationModel');
        $this->load->model('PagebuilderModel');
        $this->load->helper('reusable');
    }

    public function send_withdraw_request_post()
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
            $this->form_validation->set_rules('ids', 'ids', 'required|trim',
            array('required'      => 'Oops ! ids is required.'
            ));

            $this->form_validation->set_error_delimiters('', '');
            if($this->form_validation->run()== false)
            {
                if(!empty(form_error('ids')))$errors['ids'] =form_error('ids');

                $response['message'] = "Please required field";
                $response['errors'] = $errors;

                $this->response($response, 422);
            }
            else
            { 
                $id = $verify_data['userdata']['id'];
                $device_type = $verify_data['userdata']['device_type'];
                $device_token = $verify_data['userdata']['device_token'];

                $json = array();
                $ids = (array)$this->input->post('ids');

                $exists = null;
                foreach($ids as $i) {
                    $exists = $this->db->query("select * from wallet_requests where find_in_set(".$i.", tran_ids)")->row();

                    if(!empty($exists)) {
                        break;
                    }
                }

                if(!empty($exists)) {
                    $response = array(
                        'status' => FALSE,
                        'message' => 'withdrwal request already exist !'
                    );
                    $this->response($response, REST_Controller::HTTP_OK);
                } else {
                    $transactions = $this->db->query("SELECT * FROM wallet WHERE id IN (". implode(",", $ids) .")")->result();

                    $request = [
                        'tran_ids' => implode(",", $ids),
                        'status' => 0,
                        'user_id' => (int)$id,
                        'total' => 0,
                        'created_at' => date("Y-m-d H:i:s"),
                    ];

                    foreach ($transactions as $key => $value) {
                        $request['total'] += (float)$value->amount;
                    }

                    if($request['total'] > 0){
                        $this->db->query("UPDATE wallet SET status=2 WHERE id IN (". implode(",", $ids) .") ");
                        $this->db->insert("wallet_requests", $request);
                        $this->load->model('Mail_model');
                        $this->Mail_model->send_wallet_withdrawal_req($request['total'], $verify_data['userdata']);

                        $title = 'You made a withdrawal request';
                        $message = 'You made a withdrawal request';

                        if($device_type == 1)
                        {
                            send_push_android($device_token,$title,$message);
                        }
                        else if($device_type == 2)
                        {
                            send_push_ios($device_token,$title,$message);
                        }                    

                        $json['status'] = TRUE;
                        $json['message'] = 'withdraw request send successfully';
                        $this->response($json, REST_Controller::HTTP_OK);

                    } else{
                        $response = array(
                            'status' => FALSE,
                            'message' => 'withdrwal total must be greater than zero..!'
                        );
                        $this->response($response, REST_Controller::HTTP_OK);
                    }
                }                
            }
        }
    }

    public function withdraw_request_list_get()
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
            $id = $verify_data['userdata']['id'];
            $where = array('user_id'=>$id);
            $data['list'] = $this->Common_model->get_data_all_desc('wallet_requests', $where, $field = '*', 'id');
            $filter = array(
                'user_id' => $id,
                'status_gt' => 2,
                'old_with' => 1,
            );

            $data['status'] = $this->Wallet_model->status();
            $data['status_icon'] = $this->Wallet_model->status_icon;
            $data['payout_transaction'] = $this->Wallet_model->getTransaction($filter);

            $response = array(
                'status' => TRUE,
                'message' => 'withdraw request list get successfully',
                'data' => $data
            );

            $this->response($response, REST_Controller::HTTP_OK);
        }
    }
    
    public function perticular_withdraw_request_details_post()
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
            $this->form_validation->set_rules('id', 'id', 'required|trim',
            array('required'      => 'Oops ! id is required.'
            ));

            $this->form_validation->set_error_delimiters('', '');
            if($this->form_validation->run()== false)
            {
                if(!empty(form_error('id')))$errors['id'] =form_error('id');

                $response['message'] = "Please required field";
                $response['errors'] = $errors;

                $this->response($response, 422);
            }
            else
            {
                $id = $verify_data['userdata']['id'];
                $withdraw_id = $this->input->post('id');
                

                $where = array('id'=>$withdraw_id,'user_id'=>$id);
                $data['request'] = $this->Common_model->select_where_result('wallet_requests', $where);

                if(sizeof($data['request']) > 0)
                {
                    $filter = array(
                        'user_id' => $id,
                        'id_in' => $data['request']['tran_ids'],
                    );

                    $data['transaction'] = $this->Wallet_model->getTransaction($filter);
                    $data['status'] = $this->Wallet_model->status();
                    $data['status_icon'] = $this->Wallet_model->status_icon;

                }

                $response = array(
                    'status' => TRUE,
                    'message' => 'perticular withdraw request details get successfully',
                    'data' => $data,
                );
                $this->response($response, REST_Controller::HTTP_OK);
            }
        }
    }
}
?>