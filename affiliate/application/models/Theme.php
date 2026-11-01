<?php
class Theme extends MY_Model{

	public function getNewPosition($table) {

		$query = $this->db->query('SELECT * FROM '.$table);

		return ($query->num_rows() + 1);

	}

	//This function is not active
	public function getAllSliders($theme_id){

		if($theme_id > 0){

			$this->db->select("*")->from('theme_sliders')->where(array('theme_id'=>$theme_id));

		} else {

			$this->db->select("*")->from("theme_sliders")->order_by('slider_id','ASC');
		}

		$query = $this->db->get();

		$fetchRows = $query->result();

		return $fetchRows;

	}

	public function getSliders($slider_id = 0,$language_id=null){


		if(isset($slider_id) && $slider_id > 0)
		{

			$this->db->query("SELECT * FROM theme_sliders")->where(array('slider_id'=>$slider_id));
 
		}
		else if(isset($language_id) && $language_id > 0)
		{
			$this->db->select("theme_sliders.*,language.name")->from("theme_sliders")->join('language', 'language.id=theme_sliders.language_id')->where(array('language_id'=>$language_id))->order_by('slider_id','ASC');
		}
		else
		{
			$this->db->select("theme_sliders.*,language.name")->from("theme_sliders")->join('language', 'language.id=theme_sliders.language_id')->order_by('slider_id','ASC');
		}


		$query = $this->db->get();

		$fetchRows = $query->result();

		return $fetchRows;
	}

	

	public function getSections($section_id = 0,$language_id=null){

		if(isset($section_id) && $section_id > 0){

			$this->db->query("SELECT * FROM theme_sections")->where(array('section_id'=>$section_id));

    	} 
    	else if(isset($language_id) && $language_id > 0)
    	{
    		
    		$this->db->select("theme_sections.*,language.name")->from("theme_sections")->join('language', 'language.id=theme_sections.language_id')->where(array('language_id'=>$language_id))->order_by('position','ASC'); 
    	}
    	else 
    	{
			$this->db->select("theme_sections.*,language.name")->from("theme_sections")->join('language', 'language.id=theme_sections.language_id')->order_by('position','ASC'); 
		}

		$query = $this->db->get();

		$fetchRows = $query->result();

		return $fetchRows;

	}



	public function getThemeHomeSectionsSettings($theme_id = 0) {

		$this->db->select("*");

		$this->db->from("theme_home_sections_setting");

		$this->db->where(array('theme_id'=>$theme_id));

		$this->db->order_by('sec_position', 'ASC');

		if($this->db->get()->num_rows() > 0) {

				$this->db->select("*");

				$this->db->from("theme_home_sections_setting");

				$this->db->where(array('theme_id'=>$theme_id));

				$this->db->order_by('sec_position', 'ASC');

				return $this->db->get()->result();

		} else {

			if($theme_id == 0) {

				$defaultSections = ['Membership Section','Home Content','Home Section','Video Section','Recommendation Section'];

				for ($i=0; $i < sizeof($defaultSections); $i++) { 

					$data = [

						'theme_id' => $theme_id,

						'sec_title' => $defaultSections[$i],

						'sec_is_enable' => 1,

						'sec_position'=> ($i + 1)

					];

					$this->db->insert('theme_home_sections_setting', $data);

				}

				$this->db->select("*");

				$this->db->from("theme_home_sections_setting");

				$this->db->where(array('theme_id'=>$theme_id));

				$this->db->order_by('sec_position', 'ASC');

				return $this->db->get()->result();

			}

		}

		return false;

	}



	public function updateThemeHomeSectionsSettings($secIds, $secStatus){

		try {

			for ($i=0; $i < sizeOf($secIds); $i++) { 

				$data = [

					'sec_is_enable' => $secStatus[$i],

					'sec_position'=> ($i + 1)

				];

				$this->db->where('sec_id', $secIds[$i]);

				$this->db->update('theme_home_sections_setting', $data);

			}

			return 'success';

		} catch (\Throwable $th) {

			return $th->getMessage();

		}

	}


	public function updateThemeHomePageTopMenuPositionSettings($pageIds){

		try {

			for ($i=0; $i < sizeOf($pageIds); $i++) { 

				$data = [

					'position'=> ($i + 1)

				];

				$this->db->where('page_id', $pageIds[$i]);

				$this->db->update('theme_pages', $data);

			}

			return 'success';

		} catch (\Throwable $th) {

			return $th->getMessage();

		}

	}



	public function getRecommendation($recommendation_id = 0,$language_id=null){

		if(isset($recommendation_id) && $recommendation_id > 0)
		{
		
		$this->db->query("SELECT * FROM theme_recommendation")->where(array('recommendation_id'=>$recommendation_id));

		}
		else if(isset($language_id) && $language_id > 0)
		{
			$this->db->select("theme_recommendation.*,language.name")->from("theme_recommendation")->join('language', 'language.id=theme_recommendation.language_id')->where(array('language_id'=>$language_id))->order_by('position','ASC');
		}
		else{
			$this->db->select("theme_recommendation.*,language.name")->from("theme_recommendation")->join('language', 'language.id=theme_recommendation.language_id')->order_by('position','ASC');
		}
 
		$query = $this->db->get();

		$fetchRows = $query->result();

		return $fetchRows;

	}



	public function getHomeContent($homecontent_id = 0,$language_id=null)
	{

		if(isset($homecontent_id) && $homecontent_id > 0)
		{
			$this->db->query("SELECT * FROM theme_homecontent")->where(array('homecontent_id'=>$homecontent_id));
		}
		else if(isset($language_id) && $language_id > 0)
		{
			$this->db->select("theme_homecontent.*,language.name")->from("theme_homecontent")->join('language', 'language.id=theme_homecontent.language_id')->where(array('language_id'=>$language_id))->order_by('position','ASC');
		}
		else
		{
			$this->db->select("theme_homecontent.*,language.name")->from("theme_homecontent")->join('language', 'language.id=theme_homecontent.language_id')->order_by('position','ASC');
		}
 
		$query = $this->db->get();
		$fetchRows = $query->result();
		return $fetchRows;

	}


	public function getVideos($video_id = 0,$language_id=null){

		if(isset($video_id) && $video_id > 0)
		{
			$this->db->query("SELECT * FROM theme_videos")->where(array('video_id'=>$video_id));
		}
		else if(isset($language_id) && $language_id > 0)
		{
			$this->db->select("theme_videos.*,language.name")->from("theme_videos")->join('language', 'language.id=theme_videos.language_id')->where(array('language_id'=>$language_id))->order_by('position','ASC');
		}
		else {
			$this->db->select("theme_videos.*,language.name")->from("theme_videos")->join('language', 'language.id=theme_videos.language_id')->order_by('position','ASC');
		}

		$query = $this->db->get();

		$fetchRows = $query->result();

		return $fetchRows;

	}



    public function getPages($page_id = 0,$language_id=null){

		if(isset($page_id) && $page_id > 0){

			$this->db->query("SELECT * FROM theme_pages")->where(array('page_id'=>$page_id));
			$fetchRows = $query->result();
		}
		else{

			if(isset($language_id) && $language_id > 0)
			{
				//$this->db->select("*")->from("theme_pages")->order_by('position','ASC');

				$this->db->select("theme_pages.*,language.name")->from("theme_pages")->join('language', 'language.id=theme_pages.language_id')->where(array('language_id'=>$language_id,'page_type'=>'editable')); 

				$menu_pages = $this->security->xss_clean($this->input->get('menu_pages', true));

				
				if (!empty($menu_pages) && $menu_pages=='header') {

					$this->db->where('is_header_menu', 1);

				}else if (!empty($menu_pages) && $menu_pages=='header_dropdown') {

					$this->db->where('is_header_dropdown', 1);

				}else if (!empty($menu_pages) && $menu_pages=='footer') {

					$this->db->where("link_footer_section != ''");

				}else if (!empty($menu_pages) && $menu_pages=='both') {

					$this->db->where('is_header_menu', 1);
					$this->db->or_where("link_footer_section  != ''");

				}

				$query1 = $this->db->get_compiled_select();

				$sql='SELECT DISTINCT * FROM ('.$query1.' UNION ALL  SELECT theme_pages.*, language.name FROM theme_pages JOIN language ON language.id=theme_pages.language_id WHERE language_id = 1 and page_type="fixed") as A ORDER BY position ASC';
				 
				$query=$this->db->query($sql); 
				$fetchRows = $query->result(); 

			}
			else
			{

				$this->db->select("theme_pages.*,language.name")->from("theme_pages")->join('language', 'language.id=theme_pages.language_id')->order_by('position','ASC');


				$menu_pages = $this->security->xss_clean($this->input->get('menu_pages', true));

				if (!empty($menu_pages) && $menu_pages=='header') {

					$this->db->where('is_header_menu', 1);

				}else if (!empty($menu_pages) && $menu_pages=='header_dropdown') {

					$this->db->where('is_header_dropdown', 1);

				}else if (!empty($menu_pages) && $menu_pages=='footer') {

					$this->db->where("link_footer_section != ''");

				}else if (!empty($menu_pages) && $menu_pages=='both') {

					$this->db->where('is_header_menu', 1);
					$this->db->or_where("link_footer_section  != ''");

				}

				$query = $this->db->get();

				$fetchRows = $query->result();
			}
 

		}
 

		return $fetchRows;

	}

	public function getHeaderMenus($language_id=null)
	{
		if(isset($language_id) && $language_id > 0)
		{

			$this->db->select("*")->from("theme_pages")->where(array('language_id'=>$language_id,'page_type'=>'editable','is_header_menu'=>1,'status'=>1)); 

			
			$query1 = $this->db->get_compiled_select();

				$sql='SELECT DISTINCT * FROM ('.$query1.' UNION ALL  SELECT * FROM theme_pages   WHERE language_id = 1 and page_type="fixed" and status=1 and is_header_menu=1 ) as A ORDER BY position ASC';
				$query=$this->db->query($sql); 
				$result = $query->result_array();
		}	
		else
		{
			$query = $this->db->where('is_header_menu', 1)->where('status', 1)->where('language_id', 1)->order_by('position', 'ASC')->get('theme_pages');
			$result = $query->result_array();
		}	
		 

		return $result;;
	}
	

	public function get_links($status = null,$language_id=null){
		if($status == true) {
			if(isset($language_id) && $language_id > 0)
			{
				$this->db->select("theme_links.*,language.name")->from("theme_links")->join('language', 'language.id=theme_links.language_id')->where('tlink_status', 1)->where('language_id', $language_id);
			}
			else
			{
				$this->db->select("theme_links.*,language.name")->from("theme_links")->join('language', 'language.id=theme_links.language_id')->where('tlink_status', 1); 
			}
		}
		else
		{
			if(isset($language_id) && $language_id > 0)
			{
				$this->db->select("theme_links.*,language.name")->from("theme_links")->join('language', 'language.id=theme_links.language_id')->where('language_id',$language_id);
			}
			else
			{
				$this->db->select("theme_links.*,language.name")->from("theme_links")->join('language', 'language.id=theme_links.language_id'); 
			}

		}
		$query = $this->db->get();
		$fetchRows = $query->result();
		return $fetchRows;
	}
 

	public function create_tlink($data) {
		return $this->db->insert('theme_links',$data);
	}

	public function update_tlink($id, $data) {
		$this->db->where('tlink_id', $id);
		return $this->db->update('theme_links', $data);
	}

	public function delete_tlink($id) {
		return $this->db->delete('theme_links', array('tlink_id'=> $id));
	}

	

	public function save_slider_into_db($form_data,$image_details){

	     $post_data = [

	        'title' => $form_data['title'],

	        'description' => $form_data['description'],

	        'image' => $image_details,

	        'link' => $form_data['link'],

	        'button_text' => $form_data['button_text'],
	        'status'=>$form_data['status'],
	        'language_id'=>$form_data['language_id'],

	         ];

	         $this->db->insert('theme_sliders',$post_data);

	}



	public function save_section_into_db($form_data,$image_details){

	     $post_data = [

	         'title' => $form_data['title'],

	         'description' => $form_data['description'],

	         'image' => $image_details,

	         'link' => $form_data['link'],

	         'button_text' => $form_data['button_text'],

	         'position' => $form_data['position'],

	         'status' => $form_data['status'],

	         'language_id' => $form_data['language_id'],

	         ];

	         $this->db->insert('theme_sections',$post_data);

	}



	public function save_recommendation_into_db($form_data,$image_details){

	     $post_data = [

	         'title' => $form_data['title'],

	         'description' => $form_data['description'],

	         'occupation'=>$form_data['occupation'],

	         'image' => $image_details,

	         'status' => $form_data['status'],

	         'language_id' => $form_data['language_id'], 

	         ];

	         $this->db->insert('theme_recommendation',$post_data);

	}



	public function save_video_into_db($form_data){



	     $post_data = [

	         'video_title' => $form_data['video_title'],

	         'video_sub_title' => $form_data['video_sub_title'],

	         'video_link'=>$form_data['video_link'],

	         'status' => $form_data['status'],

	         'language_id' => $form_data['language_id'],

	         ];

	         $this->db->insert('theme_videos',$post_data);

	}



	public function save_homecontent_into_db($form_data,$image_details){



		 $title = $this->security->xss_clean($this->input->post('title'));

		 $description = $this->input->post('description');

		 $image = $this->security->xss_clean($this->input->post('image'));

		 $status = $this->security->xss_clean($this->input->post('status'));



	     $post_data = [

	         'title' => $form_data['title'],

	         'description' => $form_data['description'],

	         'image' => $image_details,

	         'status' => $form_data['status'],

	         'language_id' => $form_data['language_id'] 

	        ];

	        $this->db->insert('theme_homecontent',$post_data);

	}





	

	public function delete_faq($faq_id){

		$condtion = ['faq_id' =>$faq_id];

		$this->db->delete('theme_faq',$condtion);

   	}



	public function delete_slider($slider_id){

	     $condtion = ['slider_id' =>$slider_id];

	     $this->db->delete('theme_sliders',$condtion);

	}



	public function delete_section($section_id){

	     $condtion = ['section_id' =>$section_id];

	     $this->db->delete('theme_sections',$condtion);

	}



	public function delete_recommendation($recommendation_id){

	     $condtion = ['recommendation_id' =>$recommendation_id];

	     $this->db->delete('theme_recommendation',$condtion);

	}



	public function delete_homecontent($homecontent_id){

	     $condtion = ['homecontent_id' =>$homecontent_id];

	     $this->db->delete('theme_homecontent',$condtion);

	}



	public function delete_page($page_id){

	     $condtion = ['page_id' =>$page_id];

	     $this->db->delete('theme_pages',$condtion);

	}



	public function delete_video($video_id){

	     $condtion = ['video_id' =>$video_id];

	     $this->db->delete('theme_videos',$condtion);

	}



	public function get_slider_data_byid($slider_id){
	     return $this->db->where('slider_id',$slider_id)->get('theme_sliders')->row();
	}



	public function get_section_data_byid($section_id){



	     return $this->db->where('section_id',$section_id)->get('theme_sections')->row();

	}



	public function get_recommendation_data_byid($recommendation_id){



	     return $this->db->where('recommendation_id',$recommendation_id)->get('theme_recommendation')->row();

	}



	public function get_homecontent_data_byid($homecontent_id){



	     return $this->db->where('homecontent_id',$homecontent_id)->get('theme_homecontent')->row();

	}



	public function get_page_data_byid($page_id){



	     return $this->db->where('page_id',$page_id)->get('theme_pages')->row();

	}

	public function get_dropdown_parent_menus()
	{


		return $this->db->where('is_header_dropdown', 1)->get('theme_pages')->result();

	}



	public function get_page_data_by_slug($slug){



	     return $this->db->where('slug',$slug)->get('theme_pages')->row();

	}

	public function get_all_routes()

    {

        return $this->db->get_where('theme_pages',array('status'=>1))->result_array();

    }

	public function get_video_data_byid($video_id){

	     return $this->db->where('video_id',$video_id)->get('theme_videos')->row();

	}


	public function create_slider_data($data){

		return $this->db->insert('theme_sliders',$data);

   	}



	public function update_slider_data($slider_id,$data){

		$this->db->where('slider_id', $slider_id);

		return $this->db->update('theme_sliders', $data);

	}



	public function get_faq($faq_id = null,$language_id=null) { 
		if($faq_id != null && $faq_id>0) {


			return $this->db->where('faq_id', $faq_id)->get('theme_faq')->row();

		}
		else if(isset($language_id) && $language_id > 0)
		{
			 
			return $this->db->select("theme_faq.*,language.name")->from("theme_faq")->join('language', 'language.id=theme_faq.language_id')->where(array('language_id'=>$language_id))->order_by('position','ASC')->get()->result();

		}
		 else {

		 	return $this->db->select("theme_faq.*,language.name")->from("theme_faq")->join('language', 'language.id=theme_faq.language_id')->order_by('position','ASC')->get()->result(); 
		} 

	}



	public function create_faq_data($data){

		return $this->db->insert('theme_faq',$data);

   	}



	public function update_faq_data($faq_id,$form__updated_data){

	    $this->db->where('faq_id', $faq_id);

		return $this->db->update('theme_faq', $form__updated_data);

	}

	 

	public function update_section_data($section_id,$form__updated_data){

	     $this->db->where('section_id',$section_id);

	     $this->db->update('theme_sections',$form__updated_data);

	}



	public function update_recommendation_data($recommendation_id,$form__updated_data){

	     $this->db->where('recommendation_id',$recommendation_id);

	     $this->db->update('theme_recommendation',$form__updated_data);

	}



	public function update_homecontent_data($homecontent_id,$form__updated_data){

	     $this->db->where('homecontent_id',$homecontent_id);

	     $this->db->update('theme_homecontent',$form__updated_data);

	}



	public function update_data($table,$where,$data){

		$this->db->where($where);

		$this->db->update($table,$data);

   }



	//This is the save function in module

	public function save_page_into_db($form_data){

	    $page_name 				= $this->security->xss_clean($this->input->post('page_name'));

        $slug 					= str_replace(' ','_', $page_name);

		$top_banner_title 		= $this->security->xss_clean($this->input->post('top_banner_title'));

		$top_banner_sub_title 	= $this->security->xss_clean($this->input->post('top_banner_sub_title'));

		$page_content_title 	= $this->security->xss_clean($this->input->post('page_content_title'));

        $page_content 			= $this->input->post('page_content');

		$is_header_menu    		= $this->input->post('is_header_menu',true);

		$is_header_dropdown   	= $this->input->post('is_header_dropdown',true);

		$parent_id    			= $this->input->post('parent_id',true);

	    $link_footer_section 	= $this->security->xss_clean($this->input->post('link_footer_section'));

		$page_banner_image 		= $form_data['page_banner_image'];

        $status 				= $this->security->xss_clean($this->input->post('status'));

        $language_id 			= $this->input->post('language_id',true);
 

	     $post_data = [

			'page_name' =>$page_name,  	

	        'slug'=>$slug, 

	        'top_banner_title' => $top_banner_title,

	        'top_banner_sub_title' => $top_banner_sub_title,

	        'page_content_title' => $page_content_title,

	        'page_content' => $page_content,

	        'is_header_menu' => $is_header_menu,

	        'is_header_dropdown' => $is_header_dropdown,

	        'parent_id' => $parent_id,

			'link_footer_section' =>$link_footer_section,  	

			'page_banner_image' => $page_banner_image,

	        'status' => $status,

	        'language_id' => $language_id,

		];

		
		$this->db->insert('theme_pages',$post_data);

	}
 

	public function update_page_data($page_id, $form__updated_data){

		$this->db->where('page_id',$page_id);

		$page_type 				= $this->security->xss_clean($this->input->post('page_type'));

		$link_footer_section 	= $this->security->xss_clean($this->input->post('link_footer_section'));

	    $page_name 				= $this->security->xss_clean($this->input->post('page_name'));

		$top_banner_title 		= $this->security->xss_clean($this->input->post('top_banner_title'));

		$top_banner_sub_title 	= $this->security->xss_clean($this->input->post('top_banner_sub_title'));

		$page_content_title 	= $this->security->xss_clean($this->input->post('page_content_title'));

        $page_content 			= $this->input->post('page_content');

		$is_header_menu    		= $this->input->post('is_header_menu',true);

		$is_header_dropdown   	= $this->input->post('is_header_dropdown',true);

		$parent_id    			= $this->input->post('parent_id',true);
         
        $slug 					= str_replace(' ','_', $page_name);

        $page_banner_image 		= $form__updated_data['page_banner_image'];

        $status 				= $this->security->xss_clean($this->input->post('status'));

        $language_id  			= $this->input->post('language_id',true); 


        $post_data = [

			'page_name' =>$page_name,  	

	        'top_banner_title' => $top_banner_title,

	        'top_banner_sub_title' => $top_banner_sub_title,

	        'page_content_title' => $page_content_title,

	        'page_content' => $page_content,

	        'is_header_menu' => $is_header_menu,

	        'is_header_dropdown' => $is_header_dropdown,

	        'parent_id' => $parent_id,

			'link_footer_section' =>$link_footer_section,  	

			'page_banner_image' => $page_banner_image,

	        'status' => $status, 

	        ];

	        if ($page_type=='editable') {
	        	$post_data['slug'] = $slug;
	        	$post_data['language_id'] = $language_id;
	        }

	        //debug($post_data);exit;

	    $this->db->update('theme_pages',$post_data);

	}

	public function update_page_status($page_id,$status){

		 $this->db->where('page_id',$page_id);

		 $post_data = [

	        'status' => $status 

	        ];

	     $this->db->update('theme_pages',$post_data);

	}

	public function update_video_data($video_id,$form__updated_data){

	     $this->db->where('video_id',$video_id);

	     $this->db->update('theme_videos',$form__updated_data);

	}



    public function getSliderDetailsObject($slider_id){ 

         return $this->db->get_where('theme_sliders', array('slider_id' => $slider_id))->row_object(); 

    }

     

    public function update_settings_data($settings_id,$form__updated_data,$language_id){

         if($settings_id != 0){

             $this->db->where('settings_id',$settings_id);

             $this->db->where('language_id',$language_id);

	         $this->db->update('theme_settings',$form__updated_data); 

         }else{
         	$form__updated_data["language_id"]=$language_id;
	         $this->db->insert('theme_settings',$form__updated_data);

         }

	        return true;   

	}



    public function getSettingsDetailsObject($settings_id){ 
 
        return $this->db->get_where('theme_settings', array('settings_id' => $settings_id))->row_object(); 

    }
 

     public function getSettings($language_id=null){

     	$query="";
         if(isset($settings_id) && $settings_id > 0){

         	if(isset($language_id) && $language_id>0)
            	$query=$this->db->query("SELECT * FROM theme_settings where language_id=".$language_id);
            else
            	$query=$this->db->query("SELECT * FROM theme_settings limit 1");  
         }
         else
         {
         	if(isset($language_id) && $language_id>0)
            	$query=$this->db->query("SELECT * FROM theme_settings where language_id=".$language_id);
            else
            	$query=$this->db->query("SELECT * FROM theme_settings limit 1");  
         }
 
        $fetchRows = $query->result();
		 return $fetchRows;

     }

 

}