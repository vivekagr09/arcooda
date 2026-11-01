<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Manualcontrol extends MY_Controller {

	public function index(){
		$data['update_version_outside'] = true;
		if(!$this->session->administrator){
			$this->load->model('Product_model');
			$data['setting'] = $this->Product_model->getSettings('site');
			$this->load->view('auth/admin/index', $data);
		} else {
			$this->view($data,'setting/install_new_version');
		}
	}

	public function debug($dtype="logs", $file = null){
		if(!$this->session->administrator) {
			echo "Unauthorized access..";
			die;
		}
		switch ($dtype) {
			case 'logs':
				$data = $this->generate_logs_data();
				break;
			case 'dbstructure':
				$data = $this->generate_dbstructure_data();
				break;
			case 'sysupdatereport':

				$logFiles = array_reverse(array_values(array_diff(scandir(APPPATH."logs/system_update_logs/"), array('.', '..'))));

				$fileName = $file != null ? $file.".json" : $logFiles[0];

				$data = [
					'selected_file' => $fileName,
					'log_files' => $logFiles,
					'result'=>json_decode(file_get_contents(APPPATH."logs/system_update_logs/".$fileName))
				];
				break;
			default:
				$data = array();
				break;
		}

		$data['dtype'] = $dtype;
		$this->load->view('admincontrol/debug', $data);
	}

	private function generate_logs_data(){
		$data = array();
		try {
			$data['title'] = 'Error Log';
			$data['clear'] = site_url('tool/error_log/clear');
			$data['log'] = '';
			
			$files = scandir('application/logs/');
			foreach($files as $file) {
				$file = FCPATH.'application/logs/'.$file;
				if (file_exists($file)) {
					$size = filesize($file);
					if ($size >= 5242880) {
						$suffix = array('B','KB','MB','GB','TB','PB','EB','ZB','YB');
						$i = 0;
						while (($size / 1024) > 1) {
							$size = $size / 1024;
							$i++;
						}
						$error_warning = 'Warning: Your error log file %s is %s!';
						$data['error'] = sprintf($error_warning, basename($file), round(substr($size, 0, strpos($size, '.') + 4), 2) . $suffix[$i]);
					} else {
						$log = file_get_contents($file, FILE_USE_INCLUDE_PATH, null); 
						$lines = explode("\n", $log); 
						$content = implode("\n", array_slice($lines, 1)); 
						$data['log'] .= $content;
					}
				}
			}
		} catch (\Throwable $th) {
			$data['error'] = $th->getMessage();
		}
		return $data;
	}

	private function generate_dbstructure_data(){
		$data = array();
		$data['dbstructure'] = array();
		try {
			$tables = $this->db->list_tables();
			foreach($tables as $table) {
				$data['dbstructure'][$table] = $this->db->field_data($table);
			}
		} catch (\Throwable $th) {
			$data['error'] = $th->getMessage();
		}
		return $data;
	}
}