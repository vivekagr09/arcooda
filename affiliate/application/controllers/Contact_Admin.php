<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;
require APPPATH . 'libraries/Format.php';

class Contact_Admin extends REST_Controller {

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

    public function contact_to_admin_post()
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

            $this->form_validation->set_rules('subject', 'subject', 'required|trim',
            array('required'      => 'Oops ! subject is required.'
            ));

            $this->form_validation->set_rules('email', 'email', 'required|valid_email|trim',
            array('required'      => 'Oops ! email is required.'
            ));

            $this->form_validation->set_rules('fname', 'fname', 'required|trim',
            array('required'      => 'Oops ! first name is required.'
            ));

            $this->form_validation->set_rules('lastname', 'lastname', 'required|trim',
            array('required'      => 'Oops ! last name is required.'
            ));

            $this->form_validation->set_rules('phone', 'phone', 'required|trim',
            array('required'      => 'Oops ! phone is required.'
            ));

            $this->form_validation->set_rules('domain', 'domain', 'required|trim',
            array('required'      => 'Oops ! domain is required.'
            ));

            $this->form_validation->set_rules('body', 'body', 'required|trim',
            array('required'      => 'Oops ! body is required.'
            ));

            $this->form_validation->set_error_delimiters('', '');
            if($this->form_validation->run()== false)
            {
                if(!empty(form_error('subject')))$errors['subject'] =form_error('subject');
                if(!empty(form_error('email')))$errors['email'] =form_error('email');
                if(!empty(form_error('fname')))$errors['fname'] =form_error('fname');
                if(!empty(form_error('lastname')))$errors['lastname'] =form_error('lastname');
                if(!empty(form_error('phone')))$errors['phone'] =form_error('phone');
                if(!empty(form_error('domain')))$errors['domain'] =form_error('domain');
                if(!empty(form_error('body')))$errors['body'] =form_error('body');

                $response['message'] = "Please required field";
                $response['errors'] = $errors;

                $this->response($response, 422);
            }
            else
            {   
                $id = $verify_data['userdata']['id'];

                $data = $this->input->post(null);
                $this->load->model('Mail_model');
                $this->Mail_model->send_store_contact_vendor($data);
                $response = array(
                    'status' => TRUE,
                    'message' => 'contact to admin successfully'
                );
                $this->response($response, REST_Controller::HTTP_OK);
            }
        }
    }

 
}
?>