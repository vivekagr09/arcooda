<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;
require APPPATH . 'libraries/Format.php';

use App\MembershipPlan;
use App\Slug;

class Subscription_Plan extends REST_Controller {

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

    public function get_membership_plan_get()
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
            $is_vendor = $verify_data['userdata']['is_vendor'];
            $plan_id = $verify_data['userdata']['plan_id'];

            $membership = $this->Product_model->getSettings('membership');
            if(($membership['status'] == 1) || (($membership['status'] == 2) && ($is_vendor == 1)) || (($membership['status'] == 3) && ($is_vendor == 0))){
                $data = ['notcheckmember'=>1];
               
                $data['MembershipSetting'] =$this->Product_model->getSettings('membership');
                if($plan_id == 0){
                  
                }
                else if($plan_id == -1){
                    $data['is_lifetime_plan'] = 1;
                } else if ($user) {
                  $plan = $verify_data['userdata']->plan();
                  if($plan){
                    $data['plan']  = $plan;
                  }
                }

                
                $data['plans'] = MembershipPlan::where('status',1)->orderBy('sort_order', 'ASC')->get();
                $data['methods'] = MembershipPlan::getPaymentMethods();

            $response = array(
                'status' => TRUE,
                'message' => 'get membership plan successfully',
                'data' => $data
            );
            $this->response($response, REST_Controller::HTTP_OK);
        }
        }
    }

    public function purchase_history_post()
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
            array('required'      => 'Oops ! per_page is required.'
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
                $is_vendor = $verify_data['userdata']['is_vendor'];
                $plan_id = $verify_data['userdata']['plan_id'];
                $page_id = $this->input->post('page_id');
                $per_page = $this->input->post('per_page');

                $membership = $this->Product_model->getSettings('membership');
                    if(($membership['status'] == 1) || (($membership['status'] == 2) && ($is_vendor == 1)) || (($membership['status'] == 3) && ($is_vendor == 0))){
                        $data = ['notcheckmember'=>1];
                        $membership = $this->Product_model->getSettings('membership');
                        
                        $page = max((int)$page_id,1);

                        \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($page) {
                            return $page;
                        });

                        $limit = $per_page;
                        $query = App\MembershipUser::with("plan")->where("user_id",$id)->orderBy("id","DESC")->paginate($limit);
                        $data['plans'] = $query;

                    $response = array(
                        'status' => TRUE,
                        'message' => 'get purchase plan successfully',
                        'data' => $data
                    );
                    $this->response($response, REST_Controller::HTTP_OK);
                }
            }
        }
    }
}
?>