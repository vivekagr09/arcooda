<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;
require APPPATH . 'libraries/Format.php';

class Notification extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('email');
        $this->load->library('form_validation');
        $this->load->model('Common_model');
        $this->load->model('Product_model');
        $this->load->model('Total_model');
        $this->load->model("Form_model");
        $this->load->model('user_model', 'user');
    }

    public function notification_list_post()
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
                $page_id = $this->input->post('page_id');
                $per_page = $this->input->post('per_page');

                $notification = $this->user->getAllNotificationPaging('user',$id,$per_page,$page_id);
                $response = array(
                    'status' => TRUE,
                    'message' => 'notification list get successfully',
                    'data' => $notification['notifications']
                );
                $this->response($response, REST_Controller::HTTP_OK);

            }
        }
    }

    public function delete_notifications_post()
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

            $delete_ids = $this->input->post('delete_ids');
            $delete_ids = implode(",", $delete_ids);
            $delete_data = $this->db->query("DELETE FROM notification WHERE notification_id IN ({$delete_ids})");
            if($delete_data)
            {
                $response = array(
                    'status' => TRUE,
                    'message' => 'notification delete successfully',
                );
                $this->response($response, REST_Controller::HTTP_OK);
            }
            else
            {
                $response = array(
                    'status' => FALSE,
                    'message' => 'notification delete failed',
                );
                $this->response($response, REST_Controller::HTTP_OK);
            }            
        }
    }
}
?>