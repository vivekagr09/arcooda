<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;
require APPPATH . 'libraries/Format.php';

class Vendor_Market_Tools extends REST_Controller {

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

// start my marketing program
    public function manage_my_marketing_program_post()
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
            $data = $this->input->post(null,true);

            $this->form_validation->set_rules('name', 'name', 'required|trim',
            array('required'      => 'Oops ! name is required.'
            ));

            if($data['sale_status']){
                
                $this->form_validation->set_rules('commission_type', 'commission_type', 'required|trim',
                array('required'      => 'Oops ! commission type is required.'
                ));

                $this->form_validation->set_rules('commission_sale', 'commission_sale', 'required|trim|numeric|greater_than[0]',
                array('required'      => 'Oops ! commission sale is required.'
                ));
            }

            if($data['click_status']){

                $this->form_validation->set_rules('commission_number_of_click', 'commission_number_of_click', 'required|trim|numeric|greater_than[0]',
                array('required'      => 'Oops ! number of click is required.'
                ));

                $this->form_validation->set_rules('commission_click_commission', 'commission_click_commission', 'required|trim|numeric|greater_than[0]',
                array('required'      => 'Oops ! click commission is required.'
                ));

                }


            $this->form_validation->set_error_delimiters('', '');
            if($this->form_validation->run()== false)
            {
                if(!empty(form_error('name')))$errors['name'] =form_error('name');
                if($data['sale_status']){
                    if(!empty(form_error('commission_type')))$errors['commission_type'] =form_error('commission_type');
                    if(!empty(form_error('commission_sale')))$errors['commission_sale'] =form_error('commission_sale');
                }
                if($data['click_status']){
                    if(!empty(form_error('commission_number_of_click')))$errors['commission_number_of_click'] =form_error('commission_number_of_click');
                    if(!empty(form_error('commission_click_commission')))$errors['commission_click_commission'] =form_error('commission_click_commission');
                }

                $response['message'] = "Please required field";
                $response['errors'] = $errors;

                $this->response($response, 422);
            }
            else
            {   
                $id = $verify_data['userdata']['id'];
               
                $program_id = (int)$data['program_id'];

                $manage = $this->IntegrationModel->editProgram($data,$program_id,'user',$id);

                if($manage){
                    $response = array(
                        'status' => TRUE,
                        'message' => 'my marketing program manage successfully'
                    );
                } else{
                    $response = array(
                        'status' => FALSE,
                        'message' => 'my marketing program manage failed',
                        'data' => $dat
                    );
                }

                $this->response($response, REST_Controller::HTTP_OK);
            }
        }
    }

    public function get_my_marketing_program_list_get()
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

            $filter = [
                'vendor_id' => $id
            ];

            $data = $this->IntegrationModel->getPrograms($filter);
            $response = array(
                'status' => TRUE,
                'message' => 'my marketing program list get successfully',
                'data' => $data
            );
            $this->response($response, REST_Controller::HTTP_OK);
        }
    }

    public function delete_my_marketing_program_delete()
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
            
            $program_id = (int)$this->input->get("program_id",true);
            $delete = $this->db->query("DELETE FROM integration_programs WHERE id=". $program_id);
            if($delete)
            {
                $response = array(
                    'status' => TRUE,
                    'message' => 'my marketing program delete successfully'
                );
            }
            else
            {
                $response = array(
                    'status' => FALSE,
                    'message' => 'my marketing program delete failed'
                );
            }

            $this->response($response, REST_Controller::HTTP_OK);
        }
    }
// end my marketing program

//start marketing ads

public function get_integration_tools_post()
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

        $this->form_validation->set_rules('page_count', 'page_count', 'required|trim',
        array('required'      => 'Oops ! page count is required.'
        ));


        $this->form_validation->set_error_delimiters('', '');
        if($this->form_validation->run()== false)
        {
            if(!empty(form_error('page_id')))$errors['page_id'] =form_error('page_id');
            if(!empty(form_error('page_count')))$errors['page_count'] =form_error('page_count');

            $response['message'] = "Please required field";
            $response['errors'] = $errors;

            $this->response($response, 422);
        }
        else
        {
            $id = $verify_data['userdata']['id'];

            $page_id = $this->input->post('page_id');
            $page_count = $this->input->post('page_count');
            $category_id = $this->input->post('category_id');
            $ads_name = $this->input->post('ads_name');
            $filter = array(
                'page' => $page_id,
                'limitdata' => $page_count,
                'vendor_id' => $id
            );

            if (!empty($category_id)) {
                $filter['category_id'] = $category_id;
            }
            if (!empty($ads_name)) {
                $filter['ads_name'] = $ads_name;
            }

            $json = array();
            list($data,$total) = $this->IntegrationModel->getProgramTools($filter);

            $response = array(
                'status' => TRUE,
                'message' => 'integration tools get successfully',
                'data' => $data
            );
            $this->response($response, REST_Controller::HTTP_OK);
        }
    }
}

//end marketing ads
//start get vendor program

public function get_banner_vendor_program_list_get()
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

        $data = $this->IntegrationModel->getPrograms(['vendor_id' => $id,'status' => 1]);
        $response = array(
            'status' => TRUE,
            'message' => 'vendor program get successfully',
            'data' => $data
        );
        $this->response($response, REST_Controller::HTTP_OK);
    }
}

//end get vendor program

//start get vendor program

public function get_affiliate_list_get()
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

        $data = $this->db->query("SELECT username as name,id FROM users WHERE type='user'")->result_array();
        $response = array(
            'status' => TRUE,
            'message' => 'affiliate list get successfully',
            'data' => $data
        );
        $this->response($response, REST_Controller::HTTP_OK);
    }
}

//end get vendor program

//start interigation tools
public function intrigation_tools_manage_post()
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
        $tool_type = $this->input->post('tool_type');
        $type = $this->input->post('type');
        $recursion = $this->input->post('recursion') ? $this->input->post('recursion') : '';
        $this->form_validation->set_rules('target_link', 'target_link', 'callback_valid_url_custom',
        array('required'      => 'Oops ! target link is required.'
        ));

        $this->form_validation->set_rules('name', 'name', 'required|trim',
        array('required'      => 'Oops ! name is required.'
        ));

        $this->form_validation->set_rules('type', 'type', 'required|trim',
        array('required'      => 'Oops ! type is required.'
        ));

        $this->form_validation->set_rules('tool_type', 'tool_type', 'required|trim',
        array('required'      => 'Oops ! tool type is required.'
        ));


        if($tool_type == 'action'){
            $this->form_validation->set_rules('action_click', 'action_click', 'required|trim',
            array('required'      => 'Oops ! action click is required.'
            ));
            $this->form_validation->set_rules('action_amount', 'action_amount', 'required|trim',
            array('required'      => 'Oops ! action amount is required.'
            ));
            $this->form_validation->set_rules('action_code', 'action_code', 'required|trim',
            array('required'      => 'Oops ! action code is required.'
            ));
            $program_id = 0;
        }else if($tool_type == 'general_click'){
            $this->form_validation->set_rules('general_click', 'general_click', 'required|trim',
            array('required'      => 'Oops ! general click is required.'
            ));
            $this->form_validation->set_rules('general_amount', 'general_amount', 'required|trim',
            array('required'      => 'Oops ! genera amount is required.'
            ));
            $this->form_validation->set_rules('general_code', 'general_code', 'required|trim',
            array('required'      => 'Oops ! general code is required.'
            ));
            $program_id = 0;
        }
        else if($tool_type == 'program'){
            $this->form_validation->set_rules('program_id', 'program_id', 'required|trim',
            array('required'      => 'Oops ! program id is required.'
            ));
        }

        if($type == 'text_ads'){
            $this->form_validation->set_rules('text_ads_content', 'text_ads_content', 'required|trim',
            array('required'      => 'Oops ! text ads content is required.'
            ));
            $this->form_validation->set_rules('text_color', 'text_color', 'required|trim',
            array('required'      => 'Oops ! text color is required.'
            ));
            $this->form_validation->set_rules('text_bg_color', 'text_bg_color', 'required|trim',
            array('required'      => 'Oops ! text bg color is required.'
            ));
            $this->form_validation->set_rules('text_border_color', 'text_border_color', 'required|trim',
            array('required'      => 'Oops ! text border color is required.'
            ));
            $this->form_validation->set_rules('text_size', 'text_size', 'required|trim',
            array('required'      => 'Oops ! text size is required.'
            ));
        }else  if($type == 'link_ads'){
            $this->form_validation->set_rules('link_title', 'link_title', 'required|trim',
            array('required'      => 'Oops ! link title is required.'
            ));
        }else  if($type == 'video_ads'){
            $this->form_validation->set_rules('video_link', 'video_link', 'required|trim',
            array('required'      => 'Oops ! video link is required.'
            ));
            $this->form_validation->set_rules('button_text', 'button_text', 'required|trim',
            array('required'      => 'Oops ! button text is required.'
            ));
            $this->form_validation->set_rules('video_height', 'video_height', 'required|trim',
            array('required'      => 'Oops ! video height is required.'
            ));
            $this->form_validation->set_rules('video_width', 'video_width', 'required|trim',
            array('required'      => 'Oops ! video width is required.'
            ));
        }

        if($recursion == 'custom_time'){
            $this->form_validation->set_rules('recursion_custom_time', 'recursion_custom_time', 'required|greater_than[0]',
            array('required'     => 'Oops ! recursion custom time is required.'
            ));
        }
        

        $this->form_validation->set_error_delimiters('', '');
        if($this->form_validation->run()== false)
        {
            if(!empty(form_error('target_link')))$errors['target_link'] =form_error('target_link');
            if(!empty(form_error('name')))$errors['name'] =form_error('name');
            if(!empty(form_error('type')))$errors['type'] =form_error('type');
            if(!empty(form_error('tool_type')))$errors['tool_type'] =form_error('tool_type');
            if($tool_type == 'action'){
                if(!empty(form_error('action_click')))$errors['action_click'] =form_error('action_click');
                if(!empty(form_error('action_amount')))$errors['action_amount'] =form_error('action_amount');
                if(!empty(form_error('action_code')))$errors['action_code'] =form_error('action_code');
            }else if($tool_type == 'general_click'){
                if(!empty(form_error('general_click')))$errors['general_click'] =form_error('general_click');
                if(!empty(form_error('general_amount')))$errors['general_amount'] =form_error('general_amount');
                if(!empty(form_error('general_code')))$errors['general_code'] =form_error('general_code');
            }else if($tool_type == 'program'){
                if(!empty(form_error('program_id')))$errors['program_id'] =form_error('program_id');
            }

            if($type == 'text_ads'){
                if(!empty(form_error('text_ads_content')))$errors['text_ads_content'] =form_error('text_ads_content');
                if(!empty(form_error('text_color')))$errors['text_color'] =form_error('text_color');
                if(!empty(form_error('text_bg_color')))$errors['text_bg_color'] =form_error('text_bg_color');
                if(!empty(form_error('text_border_color')))$errors['text_border_color'] =form_error('text_border_color');
                if(!empty(form_error('text_size')))$errors['text_size'] =form_error('text_size');
            }else if($type == 'link_ads'){
                if(!empty(form_error('link_title')))$errors['link_title'] =form_error('link_title');
            }else if($type == 'video_ads'){
                if(!empty(form_error('video_link')))$errors['video_link'] =form_error('video_link');
                if(!empty(form_error('button_text')))$errors['button_text'] =form_error('button_text');
                if(!empty(form_error('video_height')))$errors['video_height'] =form_error('video_height');
                if(!empty(form_error('video_width')))$errors['video_width'] =form_error('video_width');
            }

                $this->form_validation->set_message('valid_url_custom','Enter a valid URL.');
                if($recursion == 'custom_time'){
                    if(!empty(form_error('recursion_custom_time')))$errors['recursion_custom_time'] =form_error('recursion_custom_time');
                }

            $response['message'] = "Please required field";
            $response['errors'] = $errors;

            $this->response($response, 422);
        }
        else
        {
            $id = $verify_data['userdata']['id'];
            $data = $this->input->post(null,true);
            $program_tool_id = isset($data['program_tool_id']) ? (int)$data['program_tool_id'] : 0;

            $checkActionCode = 0;

            if($data['tool_type'] == 'action'){
                $checkActionCode = $this->db->query("SELECT * FROM integration_tools WHERE action_code like ". $this->db->escape($data['action_code']) ." AND id != ". $program_tool_id)->num_rows();
                if($checkActionCode > 0)
                {
                    $errors['action_code'] = "Action code to be unique";
                    $response['message'] = "Please required field";
                    $response['errors'] = $errors;

                    $this->response($response, 422);
                }
            }
            else if($data['tool_type'] == 'general_click'){
                $checkActionCode = $this->db->query("SELECT * FROM integration_tools WHERE general_code like ". $this->db->escape($data['general_code']) ." AND id != ". $program_tool_id)->num_rows();
                if($checkActionCode > 0)
                {
                    $errors['general_code'] = "General code to be unique";
                    $response['message'] = "Please required field";
                    $response['errors'] = $errors;
                    $this->response($response, 422);
                }
            }

            if($_FILES['featured_image']['error'] != 0 && $program_tool_id == 0 ){
                $errors['featured_image'] = 'Select Featured Image File!';
                $response['message'] = "Please required field";
                $response['errors'] = $errors;

                $this->response($response, 422);
            }

            $market_data = $this->input->post('marketpostback');
            $market_data = json_decode($market_data,true);
            $market_data = $market_data[0];


            if (isset($market_data['status']) && $market_data['status'] == 'custom') {
                if (filter_var($market_data['url'], FILTER_VALIDATE_URL) === FALSE) {
                    $errors['marketpostback-url'] = 'Enter a valid postback URL';

                    $response['message'] = "Please required field";
                    $response['errors'] = $errors;

                    $this->response($response, 422);
                }
            }

            if(count($response['errors']) == 0){

                if($program_tool_id)
                {
                    $integration_tools_data = $this->Common_model->get_data_row('integration_tools', array('id'=>$program_tool_id), '*', 'id');
                    $data['featured_image'] = $integration_tools_data['featured_image'];
                }

                
                if(!empty($_FILES['featured_image']['name'])){
                    $upload_response = $this->Product_model->upload_photo('featured_image','assets/images/product/upload/thumb');
                    if($upload_response['success']){
                        $data['featured_image'] = $upload_response['upload_data']['file_name'];
                    }
                }

                $data['marketpostback'] = json_decode($data['marketpostback'],true);

                $program_tool_id = $this->IntegrationModel->editProgramTools($data,$_FILES['custom_banner'],'vendor', $id);

                if($program_tool_id){

                    $response = array(
                        'status' => TRUE,
                        'message' => 'intrigation tools manage successfully'
                    );
                    
                }
                else
                {

                    $response = array(
                        'status' => FALSE,
                        'message' => 'intrigation tools manage failed'
                    );
                }
            }

            $this->response($response, REST_Controller::HTTP_OK);
        }
    }
}

public function get_dynamic_param_get()
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
        $dynamicparam = [
            'city' => 'City',
            'regionCode' => 'Region Code',
            'regionName' => 'Region Name',
            'countryCode' => 'Country Code',
            'countryName' => 'Country Name',
            'continentName' => 'Continent Name',
            'timezone' => 'Timezone',
            'currencyCode' => 'Currency Code',
            'currencySymbol' => 'Currency Symbol',
            'ip' => 'IP',
            'type' => 'Type action,general_click,product_click,sale',
            'id' => 'ID (Sale ID OR Click ID)',
        ];

        $response = array(
            'status' => TRUE,
            'message' => 'dynamic param get successfully',
            'data' => $dynamicparam
        );
        $this->response($response, REST_Controller::HTTP_OK);
    }
}


function valid_url_custom($url) {
    if(filter_var($url, FILTER_VALIDATE_URL)){
        return TRUE;
    }
    else{
        return FALSE;
    }
}
//end interigation tools

//start duplicate tools ads
public function duplicate_intrigation_tools_post()
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

        $this->form_validation->set_rules('tools_id', 'tools_id', 'required|trim',
        array('required'      => 'Oops ! tools id is required.'
        ));

        $this->form_validation->set_error_delimiters('', '');
        if($this->form_validation->run()== false)
        {
            if(!empty(form_error('tools_id')))$errors['tools_id'] =form_error('tools_id');

            $response['message'] = "Please required field";
            $response['errors'] = $errors;

            $this->response($response, 422);
        }
        else
        {   
            $id = $verify_data['userdata']['id'];
            $type = $this->input->post('type');
            $tools_id = $this->input->post('tools_id');

            $data = $this->IntegrationModel->duplicate_tools($tools_id);
            $response = array(
                'status' => TRUE,
                'message' => 'duplicate ads create successfully',
            );
            $this->response($response, REST_Controller::HTTP_OK);
        }
    }
}
//end dupliacte tools ads

//start delete intrigation tools
public function delete_intrigation_tools_delete()
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

        $tools_id = $this->input->get('tools_id');

        $this->IntegrationModel->deleteTools($tools_id);

        $response = array(
            'status' => TRUE,
            'message' => 'intrigation tools ads delete successfully',
        );
        $this->response($response, REST_Controller::HTTP_OK);
    }
}
// end delete intrigatoin tools

}
?>