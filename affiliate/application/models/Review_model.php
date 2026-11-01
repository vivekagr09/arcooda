<?php
class Review_model extends MY_Model{
     
    
    
    public function exportReviewXMLData($userdetails)
    {
        $json=array(); 

        $json['structure_only'] = $structure_only = $this->input->post('structure_only');
        
        $datalist = [];
        if($structure_only == 1) {
            
        } else {
            $filter=array("product_created_by"=>$userdetails['id']);
            $data = $this->Product_model->getAllReviewFilter($product_id,$limit,$page,$filter);
            if(isset($data['reviews']))
            $datalist =$data['reviews'];
        }
 
        $header = array(
            'rating_id' => 'Review ID [need to be empty for new]',
            'products_id' => 'Product ID [available in products export]',
            'firstname' => 'First Name',
            'lastname' => 'Last Name',
            'rating_number' => 'Rating Number [1 to 5]',
            'rating_comments' => 'Review Description',
            'rating_created' => 'Review Date and Time [ex. 2022-11-25 22:40:40]',
        ); 

        $dom = new DOMDocument();
        $dom->encoding = 'utf-8';
        $dom->xmlVersion = '1.0';
        $dom->formatOutput = true;
        $root = $dom->createElement('reviews');
        
        if($structure_only == 1) 
        {
            $product_node = $dom->createElement('review');
            foreach ($header as $name_key => $_value) 
            {
                if($name_key!='product_short_description' && $name_key!='product_description' )
                {
                    $child_node_title = $dom->createElement($name_key, $_value);
                    $product_node->appendChild($child_node_title);
                }
                else
                {
                    $child_node_title = $dom->createElement($name_key);
                    $cdataname     = $dom->createCDATASection($_value);
                    $child_node_title->appendChild($cdataname);
                    $product_node->appendChild($child_node_title);
                     
                }

            }
            $root->appendChild($product_node);
            $dom->appendChild($root);

            $dom->save(FCPATH.'assets/xml/export_admin_product_reviews_structure.xml');
            $json['download'] = base_url('assets/xml/export_admin_product_reviews_structure.xml');
            
        }
        else
        {

            $index = 0;
            $_exportData = array();
            $_exportData[$index] = array_values($header);
            foreach ($datalist as $key => $value) 
            {
                $xml_node = $dom->createElement('review');
                $index++;
                foreach ($header as $name_key => $_value) 
                {
                    $val = '';

                    if(isset($value[$name_key])){

                        switch ($name_key) {
                            case 'product_tags':
                            $t = ( is_array(json_decode($value[$name_key], true)) ? json_decode($value[$name_key], true): [] );
                            $val = implode(",", $t);
                            break;
                            default:
                            $val = $value[$name_key];
                            break;
                        }
                    } 

                    if($name_key!='product_short_description' && $name_key!='product_description' )
                    {
                         $child_node_title = $dom->createElement($name_key, $val);
                         $xml_node->appendChild($child_node_title);
                    }
                    else
                    {
                        
                        $child_node_title = $dom->createElement($name_key);
                        $cdataname     = $dom->createCDATASection($val);
                        $child_node_title->appendChild($cdataname);
                        $xml_node->appendChild($child_node_title);
                         
                    }
                }

                $root->appendChild($xml_node);
            }

            $dom->appendChild($root);
            $dom->save(FCPATH.'assets/xml/export_admin_product_reviews.xml');
            $json['download'] = base_url('assets/xml/export_admin_product_reviews.xml');    
        }

        return $json;
    }

     public function bulkReviewsImportData($userdetails)
     {
        $data=array();
        $extension="";
        if(!isset($_FILES['file']['error']) || $_FILES['file']['error'] != 0)
        {
            $json['warning'] = __('admin.please_select_xml_file');

        } else {

            $extension = pathinfo($_FILES['file']["name"], PATHINFO_EXTENSION);
            if($extension == 'xml')
            {}
            else
            {
                $json['warning'] = __('admin.only_xml_file_are_allowed');
            }
        }
     
        $f_result = [
            'products_available' => 0,
            'products_managed' => 0,
            'status' => 'danger',
            'message' => __('admin.something_went_wrong_please_try_again!'),
            'data'  => [],
            'dataPreview' => ""
        ];
        
        $bulkResult = [];

        if(!isset($json['warning'])){

            $inputFileName = $_FILES['file']['tmp_name'];

            if($extension == 'xml')
            {
                $xml = simplexml_load_file($inputFileName);
                if ($xml === false) 
                {
                    $xmlerrrostring="";
                      $json['warning'] = __('admin.failed_loading_xml');
                  foreach(libxml_get_errors() as $error) 
                  {
                    $xmlerrrostring.= "<br>". $error->message;
                  }

                  $json['warning'] =$xmlerrrostring;
                } 
                else 
                {
                    $reviews=$xml;

                    foreach($reviews as $review) 
                    {
                        $reviewArray = [];
                        foreach($review as $key => $value) 
                        {
                            $xmlobjvalue= (string)$value[0];
                            if(isset($xmlobjvalue)) 
                            {
                                $reviewArray[$key] = $xmlobjvalue != null ? $xmlobjvalue : '';
                            } else {
                                $reviewArray[$key] = '';
                            } 
                        } 

                        if(!empty($reviewArray)) 
                        {
                            $cdata = $this->initialReviewImportCheck($userdetails,$reviewArray);
                            if(isset($cdata) && is_array($cdata))
                            $reviewArray['rating_status']=$cdata['data']['rating_status'];

                            $cdata['row'] = $reviewArray;
                            $bulkResult[] = $cdata;
                        } 
                    }
                }
            }

        } 
     
        
        $data['action'] = 'confirm';
        $data['reviews'] = $bulkResult;

        return $data;
     }

     public function initialReviewImportCheck($userdetails,$post){
    try {
            if(!empty($post))
            {

                unset($this->validation);
                $rating_id = (int) $post['rating_id'];
                $product_id = (int) $post['products_id'];
                $rating_status=0;       
                if($rating_id > 0) 
                {
                    $rating_exist = $this->db->query('select rating_created_by,products_id from rating where rating_id='.$rating_id)->row_array();
                    if(empty($rating_exist))
                     {
                        return [
                            "status" => "error",
                            "message" => __('admin.review_not_available_having_rating_id_you_provided')
                        ];
                                
                    }
                    else
                    { 
                        if($rating_exist["rating_created_by"]==$userdetails['id'])
                            $rating_status=1;
                        else
                        return [
                            "status" => "error",
                            "message" => __('admin.review_reated_by_other_can_not_be_change')
                        ];
                    }
                }
                else
                {
                    $product_owner = $this->db->query('SELECT product_created_by FROM `product` where `product_id`='.$product_id)->row_array(); 

                    if(empty($product_owner)) 
                    {
                        return [
                            "status" => "error",
                            "message" => __('admin.product_not_available_having_product_id_you_provided')
                        ];

                    }
                    else if ($product_owner["product_created_by"]!=$userdetails['id'])
                        return [
                            "status" => "error",
                            "message" => __('admin.review_can_not_be_add_on_product_created_by_other') 
                        ];
                    else  
                        $rating_status=1;
                }
         
                $this->load->helper(array('form', 'url'));
                $this->load->library('form_validation');
                $this->form_validation->reset_validation();

                $this->form_validation->set_rules('products_id', __('admin.product_id'), 'required');
                $this->form_validation->set_rules('firstname', __('admin.firstname'), 'required' );
                $this->form_validation->set_rules('lastname', __('admin.lastname'), 'required' );
                $this->form_validation->set_rules(
                    'rating_comments', __('admin.review_description'),
                    'required|min_length[5]|max_length[150]',
                    array(
                        'required'      => 'Enter %s',
                        'is_unique'     => 'This %s already exists.',
                        'min_length'    => '%s: the minimum of characters is %s',
                        'max_length'    => '%s: the maximum of characters is %s',
                    )
                );
                
                $this->form_validation->set_rules('rating_number',__('admin.rating'), "required"); 
                
                $this->form_validation->set_rules('rating_created',__('admin.review_date_-_time'),  'required|callback_checkDateTime'); 
            
                $this->form_validation->set_data($post);
                 if ($this->form_validation->run() == FALSE) {

                    return [
                            "status" => "error",
                            "errors" => $this->form_validation->error_array()
                        ];
                    
                }
                else 
                {
                        
                        $errors = array();
                        
                            $review=array();
                            $review['rating_id'] = $post['rating_id']; 
                            $review['products_id'] = $post['products_id'];  
                            $review['rating_comments'] = $post['rating_comments'];
                            $review['rating_number'] = $post['rating_number']; 
                            $review['rating_status'] = 1; 
                            $review['rating_created_by'] = $userdetails['id'];
                            $review['rating_updated_by'] = $userdetails['id'];
                            $review['rating_created'] =  (isset($post['rating_created']) && $post['rating_created']) ? date("Y-m-d H:i:s",strtotime($post['rating_created'])) : null ;
                            $review['rating_updated'] =  date("Y-m-d H:i:s");
                            $review['rating_user_agent'] =  $this->agent->agent_string();
                            $review['rating_os'] =  $this->agent->platform();
                            $review['rating_browser'] =  $this->agent->browser();
                            $review['rating_isp'] =  gethostbyaddr($_SERVER['REMOTE_ADDR']);
                            $review['rating_ipaddress'] =  $_SERVER['REMOTE_ADDR'];

                            $review['firstname'] =  $post['firstname']; 
                            $review['lastname'] =  $post['lastname'];  
                     
                            
                            if(isset($post['rating_id']) && !empty($post['rating_id']) && $post['rating_id'] != 0){
                                
                                return [
                                    "status" => "Warning",
                                    "message" => "<span class='badge bg-warning'>update</span>",
                                    "data" => $review
                                ];

                            } else {
                                return [
                                    "status" => "Warning",
                                    "message" => "<span class='badge bg-success'>create</span>",
                                    "data" => $review
                                ];
                            }

                     
                } 
            }
            else
            {
                return [
                    "status" => "error",
                    "errors" => ["Something went wrong"]
                ];
            }

        } catch (Exception $e) {
            return [
                "status" => "error",
                "errors" => [$e->getMessage()]
            ];
        }
    }

    

    public function bulkReviewImportConfirmData($userdetails,$data){
        $result = [
            'total_products' => 0,
            'created_products' => 0,
            'updated_products' => 0,
            'failed_products' => 0,
            'skipped_products' => 0,
            'details' => []
        ];

        foreach($data as $d) {
            
            if($d['status'] !== 'error') {
                $r = $this->createUpdateImportedReview($userdetails,$d['data']);
                if(isset($r['created'])) {
                    $result['created_products']++;
                } else if(isset($r['updated'])) {
                    $result['updated_products']++;
                } else {
                    $result['failed_products']++;
                }
                
                $result['details'][] = [
                    'product' => $d['data'],
                    'result' => $r
                ];
            } else {
                $result['skipped_products']++;
            }
            $result['total_products']++;
        }
        return $result;
    }

    public function createUpdateImportedReview($userdetails,$post)
    {

        try {
        
            $json['status'] = false;

            $old_product_data =[];
            
            $details = $post;

            if(isset($post['rating_id']) && !empty($post['rating_id']) && $post['rating_id'] != 0){
                $rating_id = $post['rating_id'];

                unset($details['rating_id']);

                $user_id= $this->db->get_where('rating',array('rating_id'=>$rating_id))->row_array()['rating_user_id'];
                    $this->db->where('id',$user_id)->update('users',array(
                'firstname' => $post['firstname'],
                'lastname'  => $post['lastname'] 
                ));  

                unset($details['firstname']);
                unset($details['lastname']);    
                $this->Product_model->update_data('rating', $details, array('rating_id' => $rating_id));

                $details['product_created_date'] = date('Y-m-d H:i:s');
                
                $json['updated'] = true;
                $json['status'] = true;
                $json['success'] = 'Review has been updated successfully!';

            } else {
     
                $data=$this->user->insert(array(
                'firstname' => $post['firstname'],
                'lastname'  => $post['lastname'], 
                'status'  => 1,
                'refid'     => 0,
                'type'      => 'client',
                ));
                $insert_id = $this->db->insert_id(); 

                $details['rating_user_id']=$insert_id;

                unset($details['firstname']);
                unset($details['lastname']);    

                $rating_id = $this->Product_model->create_data('rating', $details);
                $json['created'] = true;
                $json['status'] = true;
                $json['success'] = 'Review has been added successfully!';

                  
            }
     
            
        } catch (Exception $e) {
            $json['status'] = false;
            $json['errors'] = $e->getMessage();
        }
        
        return $json;
        die;
    }

    public function bulkReviewImportFromUrlData($userdetails) 
    {
        $data=array();
        $f_result = [
            'reviews_available' => 0,
            'reviews_managed' => 0,
            'status' => 'danger',
            'message' => __('admin.something_went_wrong_please_try_again'),
            'data'  => [],
            'dataPreview' => ""
        ];
        
        $bulkResult = [];
        $json=array();
        $post = $this->input->post(null,true); 
        if(!isset($post['txt_review_xmlurl'])){
 
            $json['warning'] = __('admin.please_enter_xml_url'); 

        } 
        else {
 
            $xmlurl = $post['txt_review_xmlurl'];
            $featchurldata=file_get_contents($xmlurl);
            $xml=simplexml_load_string($featchurldata);

            if($xml)
            {
                $reviews=$xml;
                if(isset($reviews))
                {
                    foreach($reviews as $review) 
                    {
                        $reviewArray = [];
                        foreach($review as $key => $value) 
                        {
                            $xmlobjvalue= (string)$value[0];
                            if(isset($xmlobjvalue)) 
                            {
                                $reviewArray[$key] = $xmlobjvalue != null ? $xmlobjvalue : '';
                            } 
                            else {
                                $reviewArray[$key] = '';
                            } 
                        } 

                        if(!empty($reviewArray)) {
                            $cdata = $this->initialReviewImportCheck($userdetails,$reviewArray);
                            if(isset($cdata) && is_array($cdata))
                            $reviewArray['rating_status']=$cdata['data']['rating_status'];

                            $cdata['row'] = $reviewArray;
                            $bulkResult[] = $cdata;
                        } 
                    }
                }
                else
                    $json['warning'] = __('admin.not_valid_xm_format'); 
                        
            }
            else 
            {  
                $json['warning'] = __('admin.url_entered_not_valid_xml_content');
            }

        }


        $data['action'] = 'confirm';
        $data['reviews'] = $bulkResult;

        return $data;

    }
}
