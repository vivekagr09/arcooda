<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;
require APPPATH . 'libraries/Format.php';

class My_Network extends REST_Controller {

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

    public function my_network_get()
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
            $refid = $verify_data['userdata']['refid'];

            $referlevelSettings = $this->Product_model->getSettings('referlevel');
            $disabled_for = json_decode( (isset($referlevelSettings['disabled_for']) ? $referlevelSettings['disabled_for'] : '[]'),1);
            $refer_status = true;
            if((int)$referlevelSettings['status'] == 0){ $refer_status = false; }
            else if((int)$referlevelSettings['status'] == 2 && in_array($id, $disabled_for)){ $refer_status = false; }

            $userlist = $this->Product_model->getAllUsersTreeV3(array(),$id);
            $site_setting = $this->Product_model->getSettings('site');
            $referlevel_setting = $this->Product_model->getSettings('referlevel');
             
            if($referlevel_setting['show_sponser'] == 'none'){
                $data['userslist'] = $userlist;
            }
            else if($referlevel_setting['show_sponser'] == 'real_sponser'){
                if($refid > 0){
                    $admin_result= $this->db->query("SELECT id,username as name,avatar,refid FROM users WHERE type='user' AND id= " . (int)$refid)->row_array();
                    if($admin_result){
                        $_children = [];
                        $_children[] = array(
                            'name'  => $admin_result['name'] ."<img class='user-avtar-tree' src='". $this->Product_model->getAvatar($admin_result['avatar']) ."'>",
                            'children' => $userlist,
                        );

                        $data['userslist'] = $_children;
                    }
                }

                if(!isset($data['userslist'])){
                    $data['userslist'] = $userlist;
                }
                
            } else{
                $admin_result= $this->db->query("SELECT id,username as name,avatar,refid FROM users WHERE type='admin'")->row_array();
                $_children = [];
                $_children[] = array(
                    'name'  => ($referlevel_setting['sponser_name'] ? $referlevel_setting['sponser_name'] : $admin_result['name']) ."<img class='user-avtar-tree' src='". $this->Product_model->getAvatar($admin_result['avatar']) ."'>",
                    'children' => $userlist,
                );
                
                $data['userslist'] = $_children;
            }
            $data['refer_total'] = $this->Product_model->getReferalTotals($id);
            $data['referred_users_tree'] = $this->Product_model->getMyUnder($id);

            $response = array(
                'status' => TRUE,
                'message' => 'my network get successfully',
                'data' => $data
            );
            $this->response($response, REST_Controller::HTTP_OK);
        }
    }

 
}
?>