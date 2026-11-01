<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Installversion extends MY_Controller {
	function __construct() {
		parent::__construct();

		$this->loginID = $this->session->userdata('administrator');
		if(!isset($this->loginID['id'])){ redirect($this->admin_domain_url, 'refresh'); }
		___construct(1);
	}

	public function uninstall_script($password, $licence){

		// Demo Mode
		if (ENVIRONMENT === 'demo') {
			echo json_encode([
				'status' => 'error',
				'message' => 'Disabled on demo mode'
			]);
			return;
		}
		// Demo Mode

		$password = base64_decode($password);
		$licence = base64_decode($licence);

		$user = $this->db->query("SELECT * FROM users WHERE id=". (int)$this->loginID['id'])->row();
		if(strtolower($licence) != strtolower(CODECANYON_LICENCE)){
			$json['errors']['licence'] = "Wrong licence number";
		}
		if(sha1($password) != $user->password){
			$json['errors']['password'] = "Wrong Admin Password..!";
		}

		if( !isset($json['errors']) ){

			list($c,$data) = api('codecanyon/uninstall_script',[
				'codecanyon'   => CODECANYON_LICENCE,
				"is_localhost" => isLocalHost(),
				"base_url"     => base_url()
			]);

			if(isset($data['success'])){
				$json['success'] = true;
				clear_session();
			} else {
				$json['warning'] = $data['error'];
			}
		}

		echo json_encode($json);
	}

	public function downloadDatabase(){
		$this->dbexport('download');
	}

	public function downloadDatabaseNewVersion(){
		$this->dbexport('download', true);
	}

	public function check_confirm_password(){
		$password = $this->input->post('password',true);
		$codecanyon = $this->input->post('codecanyon',true);
		$create_license_version = $this->input->post('create_license_version',true);

		$user = $this->db->query("SELECT * FROM users WHERE id=". (int)$this->loginID['id'])->row();
		if(sha1($password) == $user->password){

			list($c,$data) = api('codecanyon/checklicense-update',['codecanyon' => $codecanyon]);
			if(isset($data['item']['code'])){
				$json['success'] = true;

				if($create_license_version == 1) {
					$version = "<?php \n";
					$version .= "define('SCRIPT_VERSION', '".$this->config->item('app_version')."'); \n";
					$version .= "define('CODECANYON_LICENCE', '". $codecanyon ."'); \n";
					file_put_contents(FCPATH."/install/version.php", $version);
				}

				$this->session->set_userdata('tmp_login' , $user->id);
			} else {
				$json['warning'] = $data['error'];
			}

		} else {
			$json['warning'] = "Wrong Password..!";
		}

		echo json_encode($json);
	}

	public function confirm_password(){
		$json = [];

		// Demo Mode
		if (ENVIRONMENT === 'demo') {
			echo json_encode([
				'status' => 'error',
				'message' => 'Disabled on demo mode'
			]);
			return;
		}
		// Demo Mode

		$lastLogin = $this->session->userdata('tmp_login');
		if(!$lastLogin){
			$data['for'] = $this->input->post('for', true);
			$json['html'] = $this->load->view("admincontrol/setting/steps/password",$data,true);
		} else {
			$json['callback'] = true;
		}

		echo json_encode($json);
	}

	public function getStep(){
		$number = (int)$this->input->post("number");
		$json = [];
		
		if($number == 0){
			$this->session->unset_userdata('tmp_login');
			$json['html'] = $this->load->view("admincontrol/setting/steps/files",$data,true);
		}

		if($number == 1){
			$json['html'] = $this->load->view("admincontrol/setting/steps/finish",$data,true);
			$json['version']= SCRIPT_VERSION;
		}

		echo json_encode($json);
	}
            
	public function migrateFiles(){

		ini_set('max_execution_time', 0);

		$files = $_FILES['update'];
		$json = [];

		if(!isset($files['name']) || $files['name'] == ''){
			$json['errors']['update'] = 'Please select update.zip file';
		} else {
			$ext = pathinfo($files['name'], PATHINFO_EXTENSION);
			if($ext != 'zip'){
				$json['errors']['update'] = 'Please select .zip file';
			}
		}

		if(!isset($json['errors'])){
			$newVersion = str_replace(['update-','.zip'], ['',''], $files['name']);
			if(version_compare($newVersion,SCRIPT_VERSION) < 0){
				$json['errors']['update'] = 'Script version must be greater than '. SCRIPT_VERSION;
			}
		}

		if(!isset($json['errors'])){

			$destination = "updates.zip";
			unlink($destination);
			file_put_contents($destination, file_get_contents($files['tmp_name'])); 
			$json['success'] = true;
			$json['filepath'] = $destination;
			$json['new_version'] = $newVersion;
		}

		echo json_encode($json);
	}

	public function extractFiles() {
		ini_set('max_execution_time', 0);
		$destination = $this->input->post("filepath");
		$newVersion = $this->input->post("version");
		$zip = new ZipArchive;
		$res = $zip->open($destination);
		if ($res === TRUE) {
			if(!$zip->extractTo('.')) { 
				$json['errors']['update'] = "Error during extracting"; 
			} else {
				$zip->close();
				unlink($destination);

				$version = "<?php \n";
				$version .= "define('SCRIPT_VERSION', '". $newVersion ."'); \n";
				$version .= "define('CODECANYON_LICENCE', '". CODECANYON_LICENCE ."'); \n";
				file_put_contents(FCPATH."/install/version.php", $version); 

				$data['success_message'] = 'Files Migrated Successfully';
				$data['getStep'] = 2;
				$json['success'] = $this->load->view("admincontrol/setting/steps/success",$data,true);

				updateVersiontoserver($newVersion,CODECANYON_LICENCE);
			}
			
		}
		echo json_encode($json);
	}

	public function migrateDatabase(){
		$files = $_FILES['database'];
		$json= array();

		if(!isset($files['name']) || $files['name'] == ''){
			$json['errors']['database'] = 'Please select a database file';
		} else {
			$ext = pathinfo($files['name'], PATHINFO_EXTENSION);
			if($ext != 'sql'){
				$json['errors']['database'] = 'Please select .sql file';
			}
		}

		if(!isset($json['errors'])){
			$this->dbexport('backup');
			$json['errors'] = [];
			$templine = '';
			$destination = "updates_database.sql";
			unlink($destination);
			file_put_contents($destination, file_get_contents($files['tmp_name'])); 
			$file = fopen($destination,"r");
			while(! feof($file))
			{
				$line = fgets($file);
				if (substr($line, 0, 2) == '--' || $line == '')
				continue;
				$templine .= $line;
				if (substr(trim($line), -1, 1) == ';') {
					try {
						$this->db->db_debug = false;
						if(!@$this->db->query($templine)) {
							$json['errors'][] = $this->db->error();
						}
					} catch (\Throwable $th) {
						$json['errors'][] = $th->getMessage();
					}
					$templine = '';
				}
			}
			fclose($file);

			$json['all_lines'] = $lines;
			foreach ($lines as $line) {
			}
			unlink($destination);
			$data['btn'] = 'Next Migrate Files';
			$data['success_message'] = 'Databse Migrated Successfully';
			$data['getStep'] = 1;
			$json['success'] = $this->load->view("admincontrol/setting/steps/success",$data,true);
		}

		echo json_encode($json);
	}

	public function dbexport($action = "download", $withData = false) {
		$this->load->dbutil();

		if($action == "backup") {
			$prefs = array(
				'format'        => 'txt',
				'filename'      => $this->db->database,
				'add_drop'      => true,
				'add_insert'    => true,
				'newline'       => "\n"
			);
		} else {
			$prefs = array(
				'format'        => 'txt',
				'filename'      => $this->db->database,
				'add_drop'      => false,
				'add_insert'    => false,
				'newline'       => "\n"
			);
		}

		$backup =& $this->dbutil->backup($prefs);

		$db_name = 'database_backup_'.SCRIPT_VERSION.'_'.time().'.sql';
		$bk_path = 'application/backup/'.$db_name;

		if($action == 'download') {
			if($withData) {
				$backup .= $this->get_default_data();
			}
			$this->load->helper('download');
			force_download($db_name, $backup);
		} else {
			$this->load->helper('file');
			write_file($bk_path, $backup);
		}
	}

	public function get_default_data(){
		$default_data_queries = "";

		$tables = ['countries','mail_templates','setting','cities','states','theme_pages'];
		$customs = ['currency','language','users'];

		foreach ($customs as $key => $table) {
			$data = [];
			if($table == 'users'){
				$data = $this->db->query("SELECT * FROM {$table} WHERE id = 1")->result_array();
			}
			else if($table == 'language'){
				$data = $this->db->query("SELECT * FROM {$table} WHERE id = 1")->result_array();
			}
			else if($table == 'currency'){
				$data = $this->db->query("SELECT * FROM {$table} WHERE currency_id = 1")->result_array();
			}

			foreach ($data as $row) {
				$default_data_queries .= "INSERT INTO {$table} SET ";
				$datas = [];
				foreach ($row as $column => $d) {
					$datas[] ="`{$column}`= ". $this->db->escape($d);
				}
				
				$default_data_queries .=  implode(",", $datas) .";\n";
			}
		}

		foreach ($tables as $key => $table) {
			$data = $this->db->query("SELECT * FROM {$table}")->result_array();
			foreach ($data as $row) {
				$default_data_queries .= "INSERT INTO {$table} SET ";
				$datas = [];
				foreach ($row as $column => $d) {
					$datas[] ="`{$column}`= ". $this->db->escape($d);
				}
				
				$default_data_queries .=  implode(",", $datas) .";\n";
			}
		}

		$default_data_queries = str_replace("DEFAULT 'NULL'", "DEFAULT NULL", $default_data_queries);

		return $default_data_queries;
	}
}