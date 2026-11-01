<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;
require APPPATH . 'libraries/Format.php';
require APPPATH . 'libraries/Affiliate-Script-SDK/AffiliateScript.php';

class My_Wallet extends REST_Controller {

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


    public function my_wallet_post()
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
                $id = $verify_data['userdata']['id'];
                $type = $this->input->post('type');
                $paid_status = $this->input->post('paid_status');
                $page_id = $this->input->post('page_id');
                $per_page = $this->input->post('per_page');
                $userdetails= $this->db->query("SELECT * FROM users WHERE id=". $id)->row_array();
                

                $filter = array(
                    'user_id' => $id,
                    'status_gt' => 0,
                    'parent_id' => 0,
                );

                if ( isset($type) && !empty($type) ) {
                    $filter['types'] = $type;
                }

                if (isset($paid_status) && !empty($paid_status)) {
                    $filter['paid_status'] = $paid_status;
                }

                $data['user_totals'] = $this->Total_model->getUserTotals((int)$id);

                $data['wallet_unpaid_amount'] = (float)$this->db->query("SELECT SUM(amount) as total FROM wallet WHERE status=1 AND user_id=". (int)$id)->row()->total;


                $filter['page_num'] = $page_id;        
                $filter['per_page'] = $per_page;        
                $filter['offset'] = ($filter['page_num'] - 1) * $per_page;

                $total_rows = $this->Wallet_model->getTransaction($filter, false, 'ONLY_PARENTS');

                $data['transaction'] = $this->Wallet_model->getTransaction($filter, false, 'ONLY_PARENTS');

                if($userdetails['is_vendor'] == 1)
                unset($filter['user_id']);
                $transactionSorted = [];
                $child_transaction=array();
                $filter['not_negative_balence']=true;
                for ($i=0; $i < sizeof($data['transaction']); $i++) {
                   
                    $vendor=array();
                    $filter['group_id'] = $data['transaction'][$i]['group_id'];

                    $filter['not_tran_id'] = $data['transaction'][$i]['id'];
                    if($userdetails['is_vendor'] != 1) {
                        
                        $child_transaction = $this->Wallet_model->getTransaction($filter);

                        if($userdetails['is_vendor'] != 1)
                        $child_transaction[]  = $data['transaction'][$i];

                        $child_transaction = array_reverse($child_transaction);
                    }else{
                    

                        if (!empty($data['transaction'][$i]['from_user_id'])) {
                            $child_transaction = $this->Wallet_model->getTransaction($filter);

                            if($userdetails['is_vendor'] != 1)
                            $child_transaction[]  = $data['transaction'][$i];

                            $child_transaction = array_reverse($child_transaction);
                        }else if($data['transaction'][$i]['is_vendor'] == 1 && $data['transaction'][$i]['comm_from'] =='store'){
                            $filter['amount']=1;
                            $product_detail=$this->Wallet_model->getProductDetail($userdetails['id'],$data['transaction'][$i]['reference_id']);
                            
                            if(!empty($product_detail)){
                                $child_transaction = $this->Wallet_model->getTransaction($filter);
                            
                                if($userdetails['is_vendor'] == 1 && $data['transaction'][$i]['amount'] > 0)
                                $child_transaction[]  = $data['transaction'][$i];

                                $child_transaction = array_reverse($child_transaction);
                            }else{
                                $vendor=$data['transaction'][$i];
                            
                                array_push($child_transaction,$vendor);

                                $child_transaction = array_reverse($child_transaction);

                            }
                            


                        }else if($data['transaction'][$i]['is_vendor'] == 0 && $data['transaction'][$i]['comm_from'] =='store'){
                            $filter['amount']=1;
                            $product_detail=$this->Wallet_model->getProductDetail($userdetails['id'],$data['transaction'][$i]['reference_id']);
                            if(!empty($product_detail)){
                                $child_transaction = $this->Wallet_model->getTransaction($filter);
                            
                                if($userdetails['is_vendor'] != 1)
                                $child_transaction[]  = $data['transaction'][$i];

                                $child_transaction = array_reverse($child_transaction);
                            }else{
                                $vendor=$data['transaction'][$i];
                            
                                array_push($child_transaction,$vendor);

                                $child_transaction = array_reverse($child_transaction);

                            }
                        }
                        else{
                            
                            $vendor=$data['transaction'][$i];
                            
                            array_push($child_transaction,$vendor);

                            $child_transaction = array_reverse($child_transaction);
                            
                        }
                        
                    }
                    
                    $child_transaction_sorted = $child_transaction;
                   
                    $transactionSorted = array_merge($transactionSorted, $child_transaction_sorted);
                }
                
                $data['transaction'] = $transactionSorted;
                $response = array(
                    'status' => TRUE,
                    'message' => 'my wallet list get successfully',
                    'data' => $data
                );
                $this->response($response, REST_Controller::HTTP_OK);
            }
        }
    }

    public function getRecurringTransaction_post(){

        $headers = $this->input->request_headers();
        $verify_data = verify_request();
         
        if(isset($verify_data['status']) && $verify_data['status'] == 401) {
            $response = array(
                'status' => 401,
                'message' => 'Unauthorized Access!',
            );

            $this->response($response, 401); 
        }else{

            $this->form_validation->set_rules('transaction_id', 'transaction_id', 'required|trim',
            array('required'      => 'Oops ! Transaction id is required.'
            ));

            $this->form_validation->set_error_delimiters('', '');
            if($this->form_validation->run()== false)
            {
                if(!empty(form_error('transaction_id')))$errors['transaction_id'] =form_error('transaction_id');

                $response['message'] = "Please required field";
                $response['errors'] = $errors;

                $this->response($response, 422);
            }
            else
            { 
                $id = (int)$this->input->post('transaction_id');
                $filter = array(
                    'parent_id' => $id,
                );

                $data['transaction'] = $this->Wallet_model->getTransaction($filter);

                $response = array(
                    'status' => TRUE,
                    'message' => 'transaction recursion list get successfully',
                    'data' => $data
                );
                $this->response($response, REST_Controller::HTTP_OK);
            }
        }
    }

 
}
?>