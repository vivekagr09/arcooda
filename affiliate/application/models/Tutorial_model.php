<?php
class Tutorial_model extends MY_Model{
    
    public function create_data($table, $details){
        $this->db->insert($table, $details);
        return $this->db->insert_id();
    }

    public function getNewPosition() {
        $query = $this->db->query('SELECT id FROM tutorial_pages');
        return ($query->num_rows() + 1);
    }

    public function getNewCategoryPosition() {
        $query = $this->db->query('SELECT id FROM tutorial_categories');
        return ($query->num_rows() + 1);
    }

    

     public function getAll($language_id=0,$limit=20, $offset=0,$filter=null){
        $this->db->select('tutorial_pages.id,title,content,category_id,tutorial_categories.name,tutorial_pages.language_id,tutorial_pages.status');
        $this->db->order_by('tutorial_pages.position', 'asc');
        $this->db->from('tutorial_pages'); 
        $this->db->join('tutorial_categories', 'tutorial_categories.id = tutorial_pages.category_id'); 

        if($language_id>0)
        $this->db->where('tutorial_pages.language_id', $language_id); 

        $query = $this->db->get(); 
        $res["total"]=$query->num_rows();

        $this->db->select('tutorial_pages.id,title,content,category_id,tutorial_categories.name,tutorial_pages.language_id,tutorial_pages.status');
        $this->db->order_by('tutorial_pages.position', 'asc');
        $this->db->from('tutorial_pages'); 
        $this->db->join('tutorial_categories', 'tutorial_categories.id = tutorial_pages.category_id'); 

        if($language_id>0)
        $this->db->where('tutorial_pages.language_id', $language_id); 

        $offset = $limit * ($offset-1);
        $this->db->limit($limit, $offset);
        $query = $this->db->get(); 
        $res["tutorials"]=$query->result_array();
        return $res;
    } 

    public function getSingle($id){
        $this->db->select('tutorial_pages.id,title,content,category_id,tutorial_categories.name,tutorial_pages.language_id,tutorial_pages.status');
        $this->db->order_by('id', 'asc');
        $this->db->from('tutorial_pages'); 
        $this->db->join('tutorial_categories', 'tutorial_categories.id = tutorial_pages.category_id'); 
        $this->db->where('tutorial_pages.id', $id); 
        $query = $this->db->get(); 
        return $query->row_array();
    } 

    public function getAllRecords($language_id=0){
        
        $this->db->select('tutorial_pages.id,title,content,category_id,tutorial_categories.name,tutorial_pages.language_id,tutorial_pages.status');
        $this->db->order_by('tutorial_categories.position', 'asc');
        $this->db->order_by('tutorial_pages.position', 'asc');
        $this->db->from('tutorial_pages'); 
        $this->db->join('tutorial_categories', 'tutorial_categories.id = tutorial_pages.category_id'); 

        if($language_id>0)
        $this->db->where('tutorial_pages.language_id', $language_id); 

        $this->db->where('tutorial_pages.status',1); 

        $query = $this->db->get();  
        return $query->result_array();

    } 
 

    public function delete($id)
    {
        $this->db->where('id', $id);
        $res= $this->db->delete('tutorial_pages');
         return $res;
    }

    public function getAllCategory($language_id){
        
        $this->db->select('id,name');
        $this->db->from('tutorial_categories'); 
        $this->db->order_by('name', 'asc');

       if(isset($language_id) && $language_id>0)
        $this->db->where('language_id', $language_id);

        $query = $this->db->get(); 
        return $query->result_array(); 

    }

     public function getSingleCategory($id){
        $this->db->select('id,name,language_id');
        $this->db->order_by('id', 'asc');
        $this->db->from('tutorial_categories');  
        $this->db->where('tutorial_categories.id', $id); 
        $query = $this->db->get(); 
        return $query->row_array();
    } 

    public function getCateogryDropdown(){
        
       $str='<select name="category_id" id="category_id" class="form-control"> 
                        <option value="" selected>'.__('admin.all_category').'</option>';
                    
       $post = $this->input->post(null,true);
    
       if(!empty($post) && isset($post['language_id'])){ 
        $language_id=$post['language_id'];
        $categories=$this->getAllCategory($language_id);
            foreach ($categories as $key => $value) 
            {
                $str.='<option  value="'.$value['id'].'">'.$value['name'].'</option>';
            }
        }

        $str.='</select>';

        return $str;
    }

    public function list($page = 1)
    {
         
        $get = $this->input->get(null,true);
        $post = $this->input->post(null,true);
        
        $page=isset($get['page']) ? $get['page'] : $page;
        $limit=50;
         
        $language_id=null;
        if(isset($post['language_id']) && $post['language_id']) 
            $language_id = (int)$this->input->post('language_id');
       
        
        $data = $this->getAll($language_id,$limit,$page);

        $json['view'] = $this->load->view("tutorial/tutorial_list", $data, true);

        
        $this->load->library('pagination');

        $this->pagination->cur_page = $page;

        $config['base_url'] = base_url('admincontrol/listTutorals_ajax');

        $config['per_page'] = $limit;

        $config['total_rows'] = $data['total'];

        $config['use_page_numbers'] = TRUE;

        $config['page_query_string'] = TRUE;

        $config['enable_query_strings'] = TRUE;

        $_GET['page'] = $page;

        $config['query_string_segment'] = 'page';

        $this->pagination->initialize($config);

        $json['pagination'] = $this->pagination->create_links();

        $json['total']=$data['total'];
 
        echo json_encode($json);
    }

    public function manage($userdetails,$id=null)
    {
        $post = $this->input->post(null,true);
        $post = $this->security->xss_clean($post);
        if(!empty($post) && isset($post['title'])){

            $this->load->helper(array('form', 'url'));
            $this->load->library('form_validation');

            $this->form_validation->set_rules('title', __('admin.page_title'), 'required');
            $this->form_validation->set_rules('content', __('admin.page_content'), 'required' );
            
             if ($this->form_validation->run() == FALSE) {
                $json['errors'] = $this->form_validation->error_array();
                if(!isset($post['category_id']) || $post['category_id']=="")
                {
                    $errors['category_id'] =__('admin.please_select_tutorial_category');
                    $json['errors']=array_merge($json['errors'],$errors);
                } 
            } 
            else 
            { 
                $errors=array();  
                if(!isset($post['category_id']) || $post['category_id']=="")
                {
                    $errors =array("category_id"=>__('admin.please_select_tutorial_category'));
                    $json['errors']= $errors;
                } 

                $language_id=$post['language_id'];
                $category_id=$post['category_id'];
                $content= $this->input->post('content',false);

                $id=$post['id'];

                $tutorial=array();

                if(count($errors)==0)    
                {
                    if(isset($id) && $id>0)
                    {
                        $tutorial['language_id'] = $language_id;
                        $tutorial['category_id'] = $category_id;
                        $tutorial['title'] = $post['title'];
                        $tutorial['content'] = $content;
                        $tutorial['status'] = $post['status'];
                        $tutorial['updated_at'] =date("Y-m-d H:i:s");
                        $this->db->where('id',$id)->update('tutorial_pages',$tutorial); 
                        $this->session->set_flashdata('success', __('admin.tutorial_updated_successfully'));
                        $json['location'] = base_url('admincontrol/tutorial'); 
                    }
                    else
                    {
                        $tutorial['language_id'] = $language_id;
                        $tutorial['category_id'] = $category_id;
                        $tutorial['title'] = $post['title']; 
                        $tutorial['content'] = $content;
                        $tutorial['status'] = $post['status'];
                        $tutorial['position'] =$this->getNewPosition();
                        $tutorial['created_at'] =date("Y-m-d H:i:s");
                        $tutorial['updated_at'] =date("Y-m-d H:i:s");

                        
                        $this->create_data('tutorial_pages', $tutorial);
                        $this->session->set_flashdata('success', __('admin.tutorial_inserted_successfully'));
                        $json['location'] = base_url('admincontrol/tutorial'); 
                    }
                }
            }
            echo json_encode($json);
            exit; 
        }   
        
        $data=array();
        $data['languages'] = $this->db->query("SELECT * FROM language where status=1")->result_array();
        $data['tutorial'] = $this->getSingle($id);
        return  $data;
    }

    /**/

    public function deleteCategory($id)
    {
        $this->db->select('id'); 
        $this->db->where('category_id', $id); 
        $this->db->from('tutorial_pages');   
        $query = $this->db->get(); 
        $total=$query->num_rows(); 

        if($total>0)
            $res=2;
        else
        {
            $this->db->where('id', $id);
            $res= $this->db->delete('tutorial_categories');
        }  
         
         return $res;
    }

    public function getAllCategoryForPagination($language_id=0,$limit=20, $offset=0,$filter=null){
        $this->db->select('id,name,language_id');
        $this->db->order_by('position', 'asc');
        $this->db->from('tutorial_categories');  

        if($language_id>0)
        $this->db->where('language_id', $language_id); 

        $query = $this->db->get(); 
        $res["total"]=$query->num_rows(); 

        $this->db->select('id,name,language_id');
        $this->db->order_by('position', 'asc');
        $this->db->from('tutorial_categories');  

        if($language_id>0)
        $this->db->where('language_id', $language_id); 

        $offset = $limit * ($offset-1);
        $this->db->limit($limit, $offset);
        $query = $this->db->get(); 
        $res["categories"]=$query->result_array();
        return $res;
    } 
    public function listCategory($page = 1)
    {
         
        $get = $this->input->get(null,true);
        $post = $this->input->post(null,true);
        
        $page=isset($get['page']) ? $get['page'] : $page;
        $limit=50;
         
        $language_id=null;
        if(isset($post['language_id2']) && $post['language_id2']) 
            $language_id = (int)$this->input->post('language_id2');
       
        
        $data = $this->getAllCategoryForPagination($language_id,$limit,$page);

        $json['view'] = $this->load->view("tutorial/category_list", $data, true);

        
        $this->load->library('pagination');

        $this->pagination->cur_page = $page;

        $config['base_url'] = base_url('admincontrol/listTutorialCategory_ajax');

        $config['per_page'] = $limit;

        $config['total_rows'] = $data['total'];

        $config['use_page_numbers'] = TRUE;

        $config['page_query_string'] = TRUE;

        $config['enable_query_strings'] = TRUE;

        $_GET['page'] = $page;

        $config['query_string_segment'] = 'page';

        $this->pagination->initialize($config);

        $json['pagination'] = $this->pagination->create_links();

        $json['total']=$data['total'];
 
        echo json_encode($json);
    }


     public function manageCategory($userdetails,$id=null)
    {
        $post = $this->input->post(null,true);
        //$post = $this->security->xss_clean($post);
        if(!empty($post) && isset($post['name'])){
            
            $this->load->helper(array('form', 'url'));
            $this->load->library('form_validation');
           
           $original_name= $this->input->post('original_name');
           $name= $this->input->post('name');
           
           
            $this->form_validation->set_rules('name', __('admin.category_name'), 'required'); 
            
             if ($this->form_validation->run() == FALSE) {
                $error=$this->form_validation->error_array();
                $json['errors'] = @$error['name'];                 
            } 
            else 
            {
                $error=array();
                if($post['id'] != ""){
                    $checkduplicate= $this->db->query("SELECT * FROM tutorial_categories where name='". $name."' AND id != ".$post['id']."")->result_array();
                   }else{
                    $checkduplicate= $this->db->query("SELECT * FROM tutorial_categories where name='". $name."' ")->result_array();
                   }
                if(empty($checkduplicate)){ 
               $errors=array();  

                $language_id=$post['language_id'];  
                $name=$post['name'];
                $id=$post['id'];

                $category=array();

                if(count($errors)==0)    
                {
                    if(isset($id) && $id>0)
                    {
                        $category['language_id'] = $language_id;
                        $category['name'] = $name; 
                        $category['updated_at'] =date("Y-m-d H:i:s");
                        $this->db->where('id',$id)->update('tutorial_categories',$category); 
                        $this->session->set_flashdata('success', __('admin.tutorial_category_updated_successfully'));
                        $json['location'] = base_url('admincontrol/tutorial'); 

                    }
                    else
                    {

                        $category['language_id'] = $language_id;
                        $category['name'] = $name; 
                        $category['position'] =$this->getNewCategoryPosition();
                        $category['created_at'] =date("Y-m-d H:i:s");
                        $category['updated_at'] =date("Y-m-d H:i:s");
                        $this->create_data('tutorial_categories', $category);
                        $this->session->set_flashdata('success', __('admin.tutorial_category_inserted_successfully'));
                        $json['location'] = base_url('admincontrol/tutorial'); 
                    }
                }
            }else{
                
                $json['errors']= __('admin.this_category_name_already_exists');
            }
            }
            echo json_encode($json);
            exit; 
        } 
         
        
        $data=array();
        $data['languages'] = $this->db->query("SELECT * FROM language where status=1")->result_array();
        $data['category'] = $this->getSingleCategory($id);
        return  $data;
    }

    public function viewTutorial($userdetails,$id)
    {
        if(isset($id) && $id>0)
        {
            $tutorial=$this->getSingle($id);
          $data["tutorial"]=$tutorial;
           $data["title"] = $tutorial["title"];
          
        }
        else
        {
            $data["title"] ="";
        }

         return $data ;
        
    } 
    
}
