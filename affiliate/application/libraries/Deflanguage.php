<?php


class Deflanguage extends CI_Lang {

    var $existing_langs = array();
    var $lang_path = APPPATH.'language';
    var $CI;

    function __construct(){
        parent::__construct();
		
        $this->existing_langs = $this->find();
    }
    
    function find($alt_path = '') {
		
		
	
        $results = scandir(($alt_path != '') ? $alt_path : $this->lang_path);
	
        foreach ($results as $result) {
            if ($result === '.' or $result === '..') continue;
	
            if (is_dir(APPPATH.'language' . '/' . $result)) {
                $dirs[] = $result;
            }
        }
	
        return $dirs;
    }

    public function remove_line($line, $file, $lang = ''){

        $CI =& get_instance();
        $CI->load->helper('file');

        if($lang == ''){ //Apply to all languages

            foreach($this->existing_langs as $lang){

                $file_path = $this->lang_path.'/'.$lang.'/'.$file.'_lang.php';
                if(!file_exists($file_path)){

                    $file_path = BASEPATH.'language/'.$lang.'/'.$file.'_lang.php';
                    if(!file_exists($file_path)){

                        show_error("Could not find the requested language file.");

                    }

                }

                $lang_contents = read_file($file_path);

                $new_contents = preg_replace("^\n\\$"."lang\['$line'\] = '(.*?)';^", '', $lang_contents);

                write_file($file_path, $new_contents, 'w+');

            }

        } else { //Apply only to specified language

            $file_path = $this->lang_path.'/'.$lang.'/'.$file.'_lang.php';
            if(!file_exists($file_path)){

                $file_path = BASEPATH.'language/'.$lang.'/'.$file.'_lang.php';
                if(!file_exists($file_path)){

                    show_error("Could not find the requested language file.");

                }

            }

            $lang_contents = read_file($file_path);

            $new_contents = preg_replace("^\n\\$"."lang\['$line'\] = '(.*?)';^", '', $lang_contents);

            write_file($file_path, $new_contents, 'w+');

        }

    }

    public function add_line($line, $value, $file, $lang = ''){

        $CI =& get_instance();
        $CI->load->helper('file');

        if($lang == ''){ //Apply to all languages

            foreach($this->existing_langs as $lang){

                $file_path = $this->lang_path.'/'.$lang.'/'.$file.'_lang.php';
                if(!file_exists($file_path)){

                    $file_path = BASEPATH.'language/'.$lang.'/'.$file.'_lang.php';
                    if(!file_exists($file_path)){

                        show_error("Could not find the requested language file.");

                    }

                }

                $lang_contents = read_file($file_path);

                $new_contents = $lang_contents."\n".'$lang'."['".$line."'] = '".$value."';";

                write_file($file_path, $new_contents, 'w+');

            }

        } else { //Apply only to specified languages


            $file_path = $this->lang_path.'/'.$lang.'/'.$file.'_lang.php';
            if(!file_exists($file_path)){

                $file_path = BASEPATH.'language/'.$lang.'/'.$file.'_lang.php';
                if(!file_exists($file_path)){

                    show_error($file_path);

                }

            }

            $lang_contents = read_file($file_path);

            $new_contents = $lang_contents."\n".'$lang'."['".$line."'] = '".$value."';";

            write_file($file_path, $new_contents, 'w+');

        }

    }

    public function change_line($line, $value, $file, $lang = ''){
		
		

        $CI =& get_instance();
        $CI->load->helper('file');

        if($lang == ''){ //Apply to all languages
		
            foreach($this->existing_langs as $lang){

                $file_path = $this->lang_path.'/'.$lang.'/'.$file.'.php';
				echo '<pre>';
		print_r($file_path);exit;
                if(!file_exists($file_path)){

                    $file_path = APPPATH.'language/'.$lang.'/'.$file.'.php';
                    if(!file_exists($file_path)){

                        show_error("Could not find the requested language file.");

                    }

                }
                $lang_contents = read_file($file_path);
                $new_contents = preg_replace("^\\$"."lang\['$line'\] = '(.*?)';^", '$lang'."['".$line."'] = '$value';", $lang_contents);

                write_file($file_path, $new_contents, 'w+');
            }

        } else {

            $file_path = $this->lang_path.'/'.$lang.'/'.$file.'.php';
			
            if(!file_exists($file_path)){

                $file_path = APPPATH.'language/'.$lang.'/'.$file.'.php';
                if(!file_exists($file_path)){

                    show_error("Could not find the requested language file.");

                }

            }
			
            $lang_contents = read_file($file_path);
			
            $new_contents = preg_replace("^\\$"."lang\['$line'\] = '(.*?)';^", '$lang'."['".$line."'] = '$value';", $lang_contents);
			
            write_file($file_path, $new_contents, 'w+');

        }

    }
}