<?php
class Version_changes_model extends MY_Model{

	// function for one-time setup of new changes after the update
	public function update_changes() {
	    $this->load->library('session');

	    // Get the session status
	    $update_status = $this->session->userdata('update_status');
	    log_message('debug', 'Session Status: ' . $update_status);

	    if ($update_status === 'in_progress') {
	        redirect(base_url('admincontrol/system_update_report'));
	        exit();
	    }

	    $this->session->set_userdata('update_status', 'in_progress');

	    $data_results = [["info" => "system update is started..."]];
	    $updates = [
	        'import_database_changes', 'update_version_details', 'clear_image_cache_and_log_folders',
	        'set_default_theme', 'unlink_deprecated_files', 'remove_deprecated_directory',
	        'drop_deprecated_table', 'update_mail_templates', 'set_default_theme_colors'
	    ];

	    try {
	        foreach ($updates as $update) {
	            if (method_exists($this, $update)) {
	                $data_results_sub = $this->$update();
	                if (!is_array($data_results_sub)) {
	                    throw new Exception("Function {$update} did not return an array.");
	                }
	                $data_results = array_merge($data_results, $data_results_sub);
	            } else {
	                throw new Exception("Function {$update} does not exist.");
	            }
	        }
	    } catch (Exception $e) {
	        $data_results[] = ["error" => $e->getMessage()];
	    } finally {
	        $this->session->set_userdata('update_status', 'completed');
	    }

	    $data_results[] = ["success" => "Process completed on " . date('d-m-Y H:i:s')];

	    // Save update results
	    if (!file_exists(APPPATH . "logs/system_update_logs")) {
	        mkdir(APPPATH . "logs/system_update_logs", 0644, true);
	    }

	    $filename = time() . "-" . str_replace('.', '_', $this->config->item('app_version')) . ".json";
	    file_put_contents(APPPATH . "logs/system_update_logs/" . $filename, json_encode($data_results));

	    redirect(base_url('admincontrol/system_update_report'));
	    exit();
	}

	private function import_database_changes(){
	    $resultArray = [["info"=>"database update is started..."]];

	    try {
	        // generate backup of existing database before migrate to new version
	        $this->load->dbutil();
	        $prefs = array(
	            'format'        => 'txt',
	            'filename'      => $this->db->database,
	            'add_drop'      => true,
	            'add_insert'    => true,
	            'newline'       => "\n"
	        );
	        $backup =& $this->dbutil->backup($prefs);
	        $db_name = 'dbbkp_before_ver_'.$this->config->item('app_version').'_at_'.time().'.sql';
	        $bk_path = APPPATH.'backup/'.$db_name;
	        $this->load->helper('file');
	        write_file($bk_path, $backup);
	        $resultArray[] = [
	            "success" => 'generated backup of existing database'
	        ];
	    } catch (Exception $e) {
	        $resultArray[] = [
	            "error" => $e->getMessage()
	        ];

	        $backup_failed = true;
	    }

	    if(isset($backup_failed)) {
	        return $resultArray;
	        die;
	    }

	    try {
	        // migrate database to new version
	        $resultArray[] = [
	            "info" => 'migrate database to new version started'
	        ];

	        $templine = '';
	        $mysql_updates = APPPATH.'updates/database_update_'.$this->config->item('app_version').'.sql';
	        $file = fopen($mysql_updates,"r");
	        while(! feof($file))
	        {
	            $line = fgets($file);
	            if (substr($line, 0, 2) == '--' || $line == '')
	                continue;
	            $templine .= $line;
	            if (str_contains($templine, 'SET @preparedStatement') && !str_contains($templine,'DEALLOCATE PREPARE alterIfNotExists'))
	                continue;

	            if (substr(trim($line), -1, 1) == ';') {
	                $templine = str_replace('@databaseName', "\"".$this->db->database."\"", $templine);
	                try {
	                    $this->db->db_debug = true;
	                    if(str_contains($templine, 'SET @preparedStatement')) {
	                        $qurisArray = $this->explodeSkipOne($templine);
	                        $this->db->trans_start();
	                        foreach ($qurisArray as $qerySQL) {

	                            if(strlen($qerySQL) > 5) {
	                                if(!$this->db->query($qerySQL)) {
	                                    log_message('error', json_encode($this->db->error()));
	                                    $resultArray[] = [
	                                        "error" => json_encode($this->db->error())
	                                    ];
	                                    $has_db_error  = true;
	                                } else {
	                                    $resultArray[] = [
	                                        "success" => $qerySQL
	                                    ];
	                                }
	                            }
	                        }
	                        $this->db->trans_complete();
	                    } else {
	                        if(!$this->db->query($templine)) {
	                            log_message('error', json_encode($this->db->error()));
	                            $resultArray[] = [
	                                "error" => json_encode($this->db->error())
	                            ];
	                            $has_db_error  = true;
	                        } else {
	                            $resultArray[] = [
	                                "success" => $templine
	                            ];
	                        }
	                    }
	                } catch (\Throwable $th) {
	                    log_message('error', $th->getMessage());
	                    $resultArray[] = [
	                        "error" => $th->getMessage()
	                    ];
	                    $has_db_error  = true;
	                }
	                $templine = '';
	            }
	        }
	        fclose($file);

	        copy($mysql_updates, APPPATH.'backup/'.basename($mysql_updates));
	        unlink($mysql_updates);

	        if(!isset($has_db_error)) {
	            $resultArray[] = [
	                "success" => 'Database updated successfully!'
	            ];
	        } else {
	            $data['warning'][] = [
	                "success" => 'Database may not be updated successfully!'
	            ];
	        }
	    } catch (Exception $e) {
	        $resultArray[] = [
	            "error" => $e->getMessage()
	        ];
	    }

	    return $resultArray;
	}

	public function update_version_details() {
	    // Initialize the results array
	    $data['results'] = [];
	    $data['results'][] = ["info" => "System version details are upgrading"];

	    try {
	        $oldVersion = defined('SCRIPT_VERSION') ? SCRIPT_VERSION : 'undefined';
	        $newVersion = $this->config->item('app_version');

	        $versionFilePath = FCPATH . "/install/version.php";

	        if ($oldVersion !== $newVersion) {
	            // Check if we can write to the file
	            if (is_writable($versionFilePath) || !file_exists($versionFilePath)) {
	                $versionData  = "<?php \n";
	                $versionData .= "define('CODECANYON_LICENCE', '" . CODECANYON_LICENCE . "'); \n";
	                $versionData .= "define('SCRIPT_VERSION', '" . $this->config->item('app_version') . "'); \n";

	                // Attempt to write to the file
	                if (file_put_contents($versionFilePath, $versionData) !== false) {
	                    $data['results'][] = ["success" => "System version is upgraded from {$oldVersion} to {$newVersion}"];
	                } else {
	                    $data['results'][] = ["error" => "Failed to write to version file"];
	                }
	            } else {
	                $data['results'][] = ["error" => "Version file is not writable"];
	            }
	        } else {
	            $data['results'][] = ["info" => "System version details may be not defined or already a latest version available"];
	        }

	    } catch (Exception $e) {
	        // Catch any exceptions and log them
	        $data['results'][] = ["error" => $e->getMessage()];
	    }

	    return $data['results'];
	}

	private function clear_image_cache_and_log_folders() {
		$data['results'] = [["info"=>"system cache clearing"]];
		try {
			$folder_path = [];

			$folder_path[] =  FCPATH."assets/image_cache/cache/assets/images/form/favi/";
			$folder_path[] =  FCPATH."assets/image_cache/cache/assets/images/payments/";
			$folder_path[] =  FCPATH."assets/image_cache/cache/assets/images/product/upload/thumb/";
			$folder_path[] =  FCPATH."assets/image_cache/cache/assets/images/site/";
			$folder_path[] =  FCPATH."assets/image_cache/cache/assets/images/themes/";
			$folder_path[] =  FCPATH."assets/image_cache/cache/assets/images/wallet-icon/";
			$folder_path[] =  FCPATH."assets/image_cache/cache/assets/vertical/assets/images/users/";
			$folder_path[] =  FCPATH."application/logs/";
			$folder_path[] =  FCPATH."application/core/excel/Classes/";

			foreach ($folder_path as $key => $value) {

				$files = glob($value.'/*');

				foreach($files as $file) { 

					if(is_file($file))  {
						unlink($file);
						$data['results'] = [["success"=>$file." cleared"]];
					};  

				}

			}

			$data['results'] = [["success"=>"system cache clearing completed"]];
		} catch (Exception $e) {
			$data['results'][] = [
				"error" => $e->getMessage()
			];
		}

		return $data['results'];
	}

	private function set_default_theme() {
		try {
			$this->db->query("UPDATE `setting` SET `setting_value`= 0 WHERE `setting_type`='store' AND `setting_key`='theme'");
			$data['results'] = [["success"=>"default theme setting updated"]];
		} catch (Exception $e) {
			$data['results'][] = [
				"error" => $e->getMessage()
			];
		}

		return $data['results'];
	}

	private function set_default_theme_colors() {
	    $settings = [
	        'admin_side_bar_color' => '#ffffff',
	        'admin_side_bar_scroll_color' => '#007bff',
	        'admin_side_bar_text_color' => '#686868',
	        'admin_side_bar_text_hover_color' => '#007bff',
	        'admin_top_bar_color' => '#ffffff',
	        'admin_footer_color' => '#f2f3f5',
	        'admin_logo_color' => '#007bff',
	        'admin_button_color' => '#3d5674',
	        'admin_button_hover_color' => '#007bff'
	    ];

	    $data['results'] = [];

	    try {
	        foreach ($settings as $key => $value) {
	            $this->db->set('setting_value', $value);
	            $this->db->where('setting_key', $key);
	            $this->db->where('setting_type', 'theme');
	            $this->db->update('setting');

	            $data['results'][] = ["success" => "$key updated"];
	        }
	    } catch (Exception $e) {
	        $data['results'][] = [
	            "error" => $e->getMessage()
	        ];
	    }

	    return $data['results'];
	}



	private function unlink_deprecated_files() {
		$data['results'] = [["success"=>"deleting deprecated files"]];
		try {
			$deprecated_files = [
				FCPATH."assets/login/login/js/analytics.js",
				FCPATH."assets/vertical/assets/images/flags/Indian_flag.jpg",
				FCPATH."assets/vertical/assets/images/flags/french_flag.jpg",
				FCPATH."assets/vertical/assets/images/flags/germany_flag.jpg",
				FCPATH."assets/vertical/assets/images/flags/italy_flag.jpg",
				FCPATH."assets/vertical/assets/images/flags/russia_flag.jpg",
				FCPATH."assets/vertical/assets/images/flags/spain_flag.jpg",
				FCPATH."assets/vertical/assets/images/flags/us_flag.jpg",
				FCPATH."application/controllers/User_BK.php",
				FCPATH."application/models/Version_changes_model_completed.php",
				FCPATH."application/views/usercontrol/login/index1.php",
				FCPATH."application/views/usercontrol/login/index2.php",
				FCPATH."application/views/usercontrol/login/index3.php",
				FCPATH."application/views/usercontrol/login/index4.php",
				FCPATH."application/views/usercontrol/login/index5.php",
				FCPATH."application/views/usercontrol/login/index6.php",
				FCPATH."application/views/usercontrol/login/index7.php",
				FCPATH."application/views/usercontrol/login/index8.php",
				FCPATH."application/views/usercontrol/login/index9.php",
				FCPATH."application/views/usercontrol/login/login.php",
				FCPATH."application/core/Razorpay/libs/Requests-1.7.0/.coveralls.yml",
				FCPATH."application/core/Razorpay/libs/Requests-1.7.0/.gitignore",
				FCPATH."application/core/Razorpay/libs/Requests-1.7.0/.travis.yml",
				FCPATH."assets/notify/notification.mp3",
				FCPATH."assets/notify/notify.mp3",
				FCPATH."assets/images/themes/default.png",
				FCPATH."application/hooks/Router_Hook.php",
				FCPATH."application/core/excel/Classes/PHPExcel.php",
				FCPATH."application/views/common/css.php",
				FCPATH."assets/vertical/assets/plugins/dropzone/dist/dropzone.js",
				FCPATH."assets/css/base.css",
				FCPATH."assets/css/chat.css",
				FCPATH."assets/css/copy.svg",
				FCPATH."assets/vertical/assets/plugins/tinymce/themes/inlite/src/test/.eslintrc",
				FCPATH."assets/vertical/index.html",
				FCPATH."assets/js/app.js",
				FCPATH."assets/js/dashborad.js",
				FCPATH."assets/js/lightbox.js",
				FCPATH."assets/login/index1/css/presentation.css",
				FCPATH."assets/login/index1/js/main.js",
				FCPATH."assets/login/index1/js/demo.js",
				FCPATH."assets/login/index1/js/bootstrap.min.js",
				FCPATH."assets/login/index1/js/jquery.min.js",
				FCPATH."assets/login/index1/js/popper.min.js",
				FCPATH."assets/login/index2/css/presentation.css",
				FCPATH."assets/login/index2/js/main.js",
				FCPATH."assets/login/index2/js/demo.js",
				FCPATH."assets/login/index2/js/bootstrap.min.js",
				FCPATH."assets/login/index2/js/jquery.min.js",
				FCPATH."assets/login/index2/js/popper.min.js",
				FCPATH."assets/login/index3/css/presentation.css",
				FCPATH."assets/login/index3/css/dd.css",
				FCPATH."assets/login/index3/css/flags.css",
				FCPATH."assets/login/index3/css/toastr.min.css",
				FCPATH."assets/login/index3/js/main.js",
				FCPATH."assets/login/index3/js/bootstrap.min.js",
				FCPATH."assets/login/index3/image/blank.gif",
				FCPATH."assets/login/index3/image/flagssprite_small.png",
				FCPATH."assets/login/index3/image/Logo 1.png",
				FCPATH."assets/login/index3/image/padlock.png",
				FCPATH."assets/login/index3/image/user.png",
				FCPATH."assets/login/index4/css/presentation.css",
				FCPATH."assets/login/index4/js/main.js",
				FCPATH."assets/login/index4/js/demo.js",
				FCPATH."assets/login/index4/js/bootstrap.min.js",
				FCPATH."assets/login/index4/js/jquery.min.js",
				FCPATH."assets/login/index5/css/presentation.css",
				FCPATH."assets/login/index5/js/main.js",
				FCPATH."assets/login/index5/js/demo.js",
				FCPATH."assets/login/index5/js/bootstrap.min.js",
				FCPATH."assets/login/index5/js/jquery.min.js",
				FCPATH."assets/login/index6/css/presentation.css",
				FCPATH."assets/login/index6/js/main.js",
				FCPATH."assets/login/index6/js/demo.js",
				FCPATH."assets/login/index6/js/bootstrap.min.js",
				FCPATH."assets/login/index6/js/jquery.min.js",
				FCPATH."assets/login/index7/css/presentation.css",
				FCPATH."assets/login/index7/js/main.js",
				FCPATH."assets/login/index7/js/demo.js",
				FCPATH."assets/login/index7/js/bootstrap.min.js",
				FCPATH."assets/login/index7/js/jquery.min.js",
				FCPATH."assets/login/index8/css/presentation.css",
				FCPATH."assets/login/index8/js/main.js",
				FCPATH."assets/login/index8/js/demo.js",
				FCPATH."assets/login/index8/js/bootstrap.min.js",
				FCPATH."assets/login/index8/js/jquery.min.js",
				FCPATH."assets/login/index9/css/presentation.css",
				FCPATH."assets/login/index10/css/presentation.css",
				FCPATH."assets/login/index10/js/main.js",
				FCPATH."assets/login/index1/css/bootstrap.min.css",
				FCPATH."assets/login/index2/css/bootstrap.min.css",
				FCPATH."assets/login/index3/css/bootstrap.min.css",
				FCPATH."assets/login/index4/css/bootstrap.min.css",
				FCPATH."assets/login/index5/css/bootstrap.min.css",
				FCPATH."assets/login/index6/css/bootstrap.min.css",
				FCPATH."assets/login/index7/css/bootstrap.min.css",
				FCPATH."assets/login/index8/css/bootstrap.min.css",
				FCPATH."assets/login/index9/css/bootstrap.min.css",
				FCPATH."assets/login/index10/css/bootstrap.min.css",
				FCPATH."assets/login/index10/css/util.css",
				FCPATH."assets/login/index1/css/common.css",
				FCPATH."assets/login/index1/css/theme-01.css",
				FCPATH."assets/login/index2/css/common.css",
				FCPATH."assets/login/index2/css/theme-07.css",
				FCPATH."assets/login/index4/css/common.css",
				FCPATH."assets/login/index4/css/theme-06.css",
				FCPATH."assets/login/index5/css/common.css",
				FCPATH."assets/login/index5/css/theme-06.css",
				FCPATH."assets/login/index6/css/common.css",
				FCPATH."assets/login/index6/css/theme-06.css",
				FCPATH."assets/login/index7/css/common.css",
				FCPATH."assets/login/index7/css/theme-06.css",
				FCPATH."assets/login/index8/css/common.css",
				FCPATH."assets/login/index8/css/theme-06.css",
				FCPATH."assets/login/style.css",
				FCPATH."assets/js/jquery-1.10.2.min.js",
				FCPATH."assets/js/jquery-3.2.1.min.js",
				FCPATH."assets/login/multiple_pages/style.css",
				FCPATH."assets/admin/css/style.css",
				FCPATH."assets/template/summernote/summernote.css",
				FCPATH."assets/template/summernote/summernote.js",
				FCPATH."assets/template/summernote/summernote.js.map",
				FCPATH."assets/template/summernote/summernote.min.css",
				FCPATH."assets/template/summernote/summernote.min.js",
				FCPATH."assets/template/summernote/summernote.min.js.LICENSE.txt",
				FCPATH."assets/template/summernote/summernote.min.js.map",
				FCPATH."assets/template/summernote/summernote-bs4.css",
				FCPATH."assets/template/summernote/summernote-bs4.js",
				FCPATH."assets/template/summernote/summernote-bs4.js.map",
				FCPATH."assets/template/summernote/summernote-bs4.min.css",
				FCPATH."assets/template/summernote/summernote-bs4.min.js",
				FCPATH."assets/template/summernote/summernote-bs4.min.js.LICENSE.txt",
				FCPATH."assets/template/summernote/summernote-bs4.min.js.map",
				FCPATH."assets/template/summernote/summernote-bs5.js",
				FCPATH."assets/template/summernote/summernote-bs5.min.css",
				FCPATH."assets/template/summernote/summernote-bs5.min.js",
				FCPATH."assets/template/summernote/summernote-lite.css",
				FCPATH."assets/template/summernote/summernote-lite.js",
				FCPATH."assets/template/summernote/summernote-lite.js.map",
				FCPATH."assets/css/usercontrol-common.css",
				FCPATH."application/views/usercontrol/includes/sidebar.php",
				FCPATH."application/views/usercontrol/includes/topnav.php",
				FCPATH."assets/store/classified/dependencies/owl.carousel/css/owl.video.play.html"
			];

			foreach($deprecated_files as $file) {
				if(is_file($file)) {
					unlink($file);
					$data['results'] = [["success"=> $file." deleted successfully"]];
				};
			}
			} catch (Exception $e) {
				$data['results'][] = [
					"error" => $e->getMessage()
				];
			}

		return $data['results'];
	}

	public function remove_deprecated_directory(){
	    $results = [];
	    try {
	        $deprecated_directories = [
	            FCPATH."application/core/paytm",
					FCPATH."application/core/Razorpay",
					FCPATH."application/core/stripe",
					FCPATH."application/core/xendit",
					FCPATH."application/core/yandex",
					FCPATH."application/libraries/paypal",
					FCPATH."application/deposit_payments",
					FCPATH."application/membership_payment",
					FCPATH."application/payments",
					FCPATH."application/third_party/src",
					FCPATH."assets/images/payments",
					FCPATH."assets/login/login",
					FCPATH."assets/login/login/css",
					FCPATH."assets/login/login/js",
					FCPATH."assets/login/login/fonts",
					FCPATH."assets/login/css",
					FCPATH."assets/login/js",
					FCPATH."assets/login/fonts",
					FCPATH."application/core/excel/Classes",
					FCPATH."assets/vertical/assets/plugins/alertify/",
					FCPATH."assets/vertical/assets/plugins/bootstrap-datepicker",
					FCPATH."assets/vertical/assets/plugins/bootstrap-colorpicker",
					FCPATH."assets/vertical/assets/plugins/bootstrap-inputmask",
					FCPATH."assets/vertical/assets/plugins/bootstrap-maxlength",
					FCPATH."assets/vertical/assets/plugins/bootstrap-rating",
					FCPATH."assets/vertical/assets/plugins/bootstrap-session-timeout",
					FCPATH."assets/vertical/assets/plugins/bootstrap-touchspin",
					FCPATH."assets/vertical/assets/plugins/c3",
					FCPATH."assets/vertical/assets/plugins/colorpicker",
					FCPATH."assets/vertical/assets/plugins/dropify",
					FCPATH."assets/vertical/assets/plugins/animate",
					FCPATH."assets/vertical/assets/plugins/d3",
					FCPATH."assets/vertical/assets/plugins/chart.js",
					FCPATH."assets/vertical/assets/plugins/datatables",
					FCPATH."assets/vertical/assets/plugins/flot-chart",
					FCPATH."assets/vertical/assets/plugins/fullcalendar",
					FCPATH."assets/vertical/assets/plugins/gmaps",
					FCPATH."assets/vertical/assets/plugins/ion-rangeslider",
					FCPATH."assets/vertical/assets/plugins/jquery-confirm",
					FCPATH."assets/vertical/assets/plugins/jquery-sparkline",
					FCPATH."assets/vertical/assets/plugins/jquery-ui",
					FCPATH."assets/vertical/assets/plugins/moment",
					FCPATH."assets/vertical/assets/plugins/nestable",
					FCPATH."assets/vertical/assets/plugins/parsleyjs",
					FCPATH."assets/vertical/assets/plugins/prism",
					FCPATH."assets/vertical/assets/plugins/select2",
					FCPATH."assets/vertical/assets/plugins/summernote",
					FCPATH."assets/vertical/assets/plugins/sweet-alert2",
					FCPATH."assets/vertical/assets/plugins/tabledit",
					FCPATH."assets/vertical/assets/plugins/timepicker",
					FCPATH."assets/vertical/assets/plugins/tiny-editable",
					FCPATH."assets/vertical/assets/plugins/videoslider",
					FCPATH."assets/vertical/assets/plugins/x-editable",
					FCPATH."assets/vertical/assets/images/widgets",
					FCPATH."assets/vertical/assets/images/small",
					FCPATH."assets/vertical/assets/images/colorpicker",
					FCPATH."assets/vertical/assets/plugins/chartist/",
					FCPATH."assets/plugins/gojs",
					FCPATH."assets/plugins/images",
					FCPATH."assets/plugins/productpage",
					FCPATH."assets/plugins/roboto",
					FCPATH."application/views/admincontrol/pagebuilder",
					FCPATH."application/views/admincontrol/template_editor",
					FCPATH."assets/vertical/assets/plugins/jquery-knob",
					FCPATH."assets/vertical/assets/plugins/jvectormap",
					FCPATH."assets/vertical/assets/plugins/magnific-popup",
					FCPATH."assets/vertical/assets/plugins/morris",
					FCPATH."assets/vertical/assets/plugins/raphael",
					FCPATH."assets/vertical/assets/plugins/RWD-Table-Patterns",
					FCPATH."assets/vertical/assets/plugins/skycons",
					FCPATH."assets/vertical/assets/plugins/clockpicker",
					FCPATH."assets/vertical/assets/plugins/dropzone",
					FCPATH."assets/vertical/assets/plugins",
					FCPATH."application/views/usercontrol/login/login",
					FCPATH."assets/document_css",
					FCPATH."assets/document_img",
					FCPATH."assets/document_scripts",
					FCPATH."assets/document_vendor",
					FCPATH."assets/login/index10/js",
					FCPATH."assets/js/summernote-0.8.12-dist",
					FCPATH."assets/login/index3/js",
					FCPATH."assets/plugins/color-picker",
					FCPATH."application/views/auth/user/assets/img"
	        ];

	        foreach($deprecated_directories as $deprecated_directory){
	            if(is_dir($deprecated_directory)) {
	                $result = self::remove_deprecated_directory_folder_and_files($deprecated_directory);
	                if(!is_dir($deprecated_directory))
	                    $results[] = $deprecated_directory . " removed successfully";
	                else
	                    $results[] = "Failed to remove " . $deprecated_directory;
	            } else {
	                $results[] = $deprecated_directory . " does not exist";
	            }
	        }
	    } catch (\RuntimeException $e) {
	        $results[] = "Error: " . $e->getMessage();
	    }
	    return $results;
	}

	private function remove_deprecated_directory_folder_and_files($deprecated_directory){
	    if (!is_dir($deprecated_directory)) {
	        return false;
	    }

	    if(substr($deprecated_directory, strlen($deprecated_directory) - 1, 1) != '/')
	        $deprecated_directory .= '/';

	    $files = glob($deprecated_directory . '*', GLOB_MARK);
	    foreach($files as $file){
	        if(is_dir($file)) {
	            self::remove_deprecated_directory_folder_and_files($file);
	        } else {
	            if (!@unlink($file)) {
	                return false;
	            }
	        }
	    }

	    if (!@rmdir($deprecated_directory)) {
	        return false;
	    }

	    return true;
	}

	public function drop_deprecated_table() {
	    $this->db->trans_start();  // Start transaction

	    try {
	        if($this->db->table_exists('bt_custom_field')) {
	            $this->db->query("DROP TABLE `bt_custom_field`");
	        }

	        if($this->db->table_exists('bt_custom_field_status')) {
	            $existingData = $this->db->select('response, response_validate')->get('bt_custom_field_status')->row();

	            $this->updateOrInsertSetting('withdrawalpayment_bank_transfer', 'bt_custom_field', $existingData->response);
	            $this->updateOrInsertSetting('withdrawalpayment_bank_transfer', 'response_validate', $existingData->response_validate);

	            $this->db->query("DROP TABLE `bt_custom_field_status`");
	        }

	        //To drop another table
	        if($this->db->table_exists('your_table_name')) {
			    $this->db->query("DROP TABLE `your_table_name`");
			}

	        $data['results'][] = ["success" => 'deprecated table dropped successfully'];
			    } catch (\CI_DB_exception $e) {
			        $data['results'][] = ["error" => $e->getMessage()];
			    }

			    $this->db->trans_complete();  // Complete the transaction

			    if ($this->db->trans_status() === FALSE)
			    {
			        $data['results'][] = ["error" => 'Transaction failed'];
			    }

			    return $data['results'];
		}

	
	private function updateOrInsertSetting($type, $key, $value) {
	    // Validate parameters
	    if (!is_string($type) || !is_string($key)) {
	        throw new InvalidArgumentException('Type and key must be strings.');
	    }

	    $fieldsData = [
	        'setting_type' => $type,
	        'setting_key' => $key,
	        'setting_value' => $value,
	        'setting_status' => 1,
	        'setting_ipaddress' => '::1',
	        'setting_is_default' => 0
	    ];

	    $this->db->where(['setting_type' => $type, 'setting_key' => $key]);

	    if ($this->db->count_all_results('setting') > 0) {
	        // Update
	        if ($this->db->update('setting', $fieldsData)) {
	            return true;
	        } else {
	            return false; // Update failed
	        }
	    } else {
	        // Insert
	        if ($this->db->insert('setting', $fieldsData)) {
	            return true;
	        } else {
	            return false; // Insert failed
	        }
	    }
	}

	public function update_mail_templates() {
		try {
			
	    	$newMailTemplates = [['subscription_status_change', 'Subscription Status Changed', 'Subscription Status Changed', '<p>Dear [[firstname]],</p>\r\n                <p>Your subscription status has been changed to [[status_text]]</p>\r\n                <p>Comment: [[comment]] </p>\r\n                [[website_name]]<br />\r\n                Support Team</p>', '', NULL, NULL, '', 'comment,planname,price,expire_at,started_at,status_text,firstname,lastname,email,username,website_url,website_name,website_logo,name'], ['subscription_buy', 'Subscription Buy', 'Subscription Buy', '<h2>Thanks for your order</h2>\r\n\r\n<p>Welcome to Prime. As a Prime member, enjoy these great benefits. If you have any questions, call us any time at or simply reply to this email.</p>\r\n', 'New Subscription Buy From [[firstname]] [[lastname]]', NULL, NULL, '<h2>Thanks for your order</h2>\r\n\r\n<p>Welcome to Prime. As a Prime member, enjoy these great benefits. If you have any questions, call us any time at or simply reply to this email.</p>\r\n', 'planname,price,expire_at,started_at,firstname,lastname,email,username,website_url,website_name,website_logo,name'], ['subscription_expire_notification', 'Subscription Expire Notification', 'Your Subscription Will Be Expired Soon.', '<p>customText</p>\r\n', NULL, NULL, NULL, NULL, 'planname,price,expire_at,started_at,firstname,lastname,email,username,website_url,website_name,website_logo,name'], ['wallet_noti_on_hold_wallet', 'Wallet Status Change To On Hold', '[[amount]] is put on hold in your wallet', '<p>Dear [[name]],</p>\n        <p>Transactions #[[id]] status changed to [[new_status]]. amount is [[amount]]</p>\n        <p><br />\n        [[website_name]]<br />\n        Support Team</p>\n', '', NULL, NULL, NULL, 'amount,id,name,new_status,user_email,website_name,website_logo,name'], ['new_user_request', 'New User Request', 'User Registration Successfull', '<p>Dear [[firstname]] [[lastname]],</p>\r\n\r\n<p>User account has been registered successfully on [[website_name]], please wait while system admin apporve&nbsp;your request.<br />\r\nWe will inform you once account has been approved, Thank You.</p>\r\n\r\n<p>Support Team<br />\r\n[[website_name]]</p>\r\n', 'New User Registration - Approval Pending', NULL, NULL, '<p>Dear Admin,</p>\r\n\r\n<p>New user has been registered on [[website_name]], apporval is pending yet!</p>\r\n\r\n<p>User Details</p>\r\n\r\n<p>Name : [[firstname]][[lastname]]<br />\r\nEmail :&nbsp;[[email]]<br />\r\nUsername : [[username]]<br />\r\nSupport Team<br />\r\n[[website_name]]</p>', 'firstname,lastname,email,username,website_name,website_logo'], ['new_user_approved', 'New User Request Approved', 'User Account Approved', '<p>Dear [[firstname]] [[lastname]],</p>\r\n\r\n<p>Your new user account registration request is accepted by admin, you can login and use services.</p>\r\n\r\n<p>[[website_name]]<br />\r\nSupport Team</p>\r\n', 'User Account Approved', NULL, NULL, '<p>Dear Admin,</p>\r\n\r\n<p>You have approced registration request of user having</p>\r\n\r\n<p>Name : [[firstname]]&nbsp;[[lastname]]</p>\r\n\r\n<p>Email : [[email]]</p>\r\n\r\n<p>Username : [[username]]</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Support Team</p>\r\n\r\n<p>[[website_name]]</p>\r\n', 'firstname,lastname,email,username,website_name,website_logo'], ['new_user_declined', 'New User Request Declined', 'User Account Declined', '<p>Dear [[firstname]] [[lastname]],</p>\r\n\r\n<p>Your new user account registration request is declined by admin, for more information please contact supprt team</p>\r\n\r\n<p>[[website_name]]<br />\r\nSupport Team</p>\r\n', 'User Account Declined', NULL, NULL, '<p>Dear Admin,</p>\r\n\r\n<p>You have declined registration request of user having</p>\r\n\r\n<p>Name : [[firstname]]&nbsp;[[lastname]]</p>\r\n\r\n<p>Email : [[email]]</p>\r\n\r\n<p>Username : [[username]]</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Support Team</p>\r\n\r\n<p>[[website_name]]</p>\r\n', 'firstname,lastname,email,username,website_name,website_logo'], ['new_vendor_deposit_request', 'New Vendor Deposit Request', 'New Deposit Request Added', '<p>Dear [[firstname]] [[lastname]],</p>\r\n\r\n<p>Your deposit request of amount [[amount]] is added, if your balance not updated please contact support team</p>\r\n\r\n<p>[[website_name]]<br /> \r\n Support Team</p>', 'New Deposit Request Added', NULL, NULL, '<p>Dear Admin,</p>\r\n\r\n<p>You have new deposit request of amount [[amount]] from vendor having</p>\r\n\r\n<p>Name : [[firstname]]&nbsp;[[lastname]]</p>\r\n\r\n<p>Email : [[email]]</p>\r\n\r\n<p>Username : [[username]]</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Support Team</p>\r\n\r\n<p>[[website_name]]</p>', 'status,amount,firstname,lastname,email,username,website_name,website_logo'], ['vendor_deposit_request_updated', 'Deposit Request Updated', 'Deposit Request Updated', '<p>Dear [[firstname]] [[lastname]],</p>\r\n\r\n<p>Your deposit request of amount [[amount]] is updated to [[status]], if have any queries please contact support team</p>\r\n\r\n<p>[[website_name]]<br /> \r\n Support Team</p>', 'Deposit Request Updated', NULL, NULL, '<p>Dear Admin,</p>\r\n\r\n<p>You have changed status of deposit request to [[status]] from vendor having</p>\r\n\r\n<p>Name : [[firstname]]&nbsp;[[lastname]]</p>\r\n\r\n<p>Email : [[email]]</p>\r\n\r\n<p>Username : [[username]]</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Support Team</p>\r\n\r\n<p>[[website_name]]</p>', 'status,amount,firstname,lastname,email,username,website_name,website_logo'],['user_level_changed', 'Change user level', 'Your user level changed', '<p>Dear,</p><p>Your level changed from [[from_level]] to [[to_level]]</p>                     <p><br>                 [[website_name]]<br>                 Support Team</p>             ', NULL, NULL, NULL, NULL, 'from_level,to_level,website_name']];


	    	// tickets notification templates
	    	$newMailTemplates[] = ['ticket_created_email', 'Ticket Created Email', 'New ticket #[[ticket_id]] has been created', '<p>Dear [[firstname]],&nbsp;</p><p><br></p><p>Your ticket has been created successfully on the system. Please note down below the ticket number for future reference.</p><p><br></p><p>Ticket ID:</p><p><span style="font-size: 1rem;">[[ticket_id]]</span><br></p><p><br></p><p><span style="font-size: 1rem;">Ticket Status:</span><br></p><p><span style="font-size: 1rem;">[[ticket_status]]</span><br></p><p><br></p><p><br></p><p>Subject :</p><p><span style="font-size: 1rem;">[[ticket_subject]]</span><br></p><p><br></p><p><br></p><p>Message:</p><p><span style="font-size: 1rem;">[[ticket_body]]</span><br></p><p><br></p><p><span style="font-size: 1rem;">We will contact you very soon.</span><br></p><p><br></p><p><span style="font-size: 1rem;">Thank You</span><br></p><p><span style="font-size: 1rem;">Support Team</span><br></p>', 'New user ticket #[[ticket_id]] has been created', NULL, NULL, '<p>Dear Admin, </p><p><br></p><p>The user has created a new ticket on your site [[website_name]]. <br></p><p><br></p><p>Username:</p><p><span style="font-size: 1rem;">[[username]]</span><br></p><p><br></p><p>Email:</p><p><span style="font-size: 1rem;">[[email]]</span><br></p><p><br></p><p>Name:</p><p><span style="font-size: 1rem;">[[firstname]] [[lastname]]</span><br></p><p><br></p><p>Ticket ID:</p><p><span style="font-size: 1rem;">[[ticket_id]]</span><br></p><p><br></p><p>Ticket Status:</p><p><span style="font-size: 1rem;">[[ticket_status]]</span><br></p><p><br></p><p><span style="font-size: 1rem;">Subject :</span><br></p><p><span style="font-size: 1rem;">[[ticket_subject]]</span><br></p><p><br></p><p><br></p><p>Message:</p><p><span style="font-size: 1rem;">[[ticket_body]]</span><br></p><p><br></p><p><br></p><p>Thank You</p><p><span style="font-size: 1rem;">[[website_name]]</span><br></p><p><br></p>',  'ticket_id,ticket_status,ticket_subject,ticket_body,ticket_datetime,firstname,lastname,email,username,website_name,website_logo'];

	    	$newMailTemplates[] = ['ticket_reply_email', 'Ticket Replied Email', 'You have a new reply on ticket #[[ticket_id]]', '<p>Dear [[firstname]], </p><p><br></p><p>You have a reply from the support team on your ticket #[[ticket_id]]</p><p><br></p><p>Ticket ID:</p><p><span style="font-size: 1rem;">[[ticket_id]]</span><br></p><p><br></p><p><span style="font-size: 1rem;">Ticket Status:</span><br></p><p><span style="font-size: 1rem;">[[ticket_status]]</span><br></p><p><br></p><p><span style="font-size: 1rem;">Subject :</span><br></p><p><span style="font-size: 1rem;">[[ticket_subject]]</span><br></p><p><br></p><p>Message from the support team<br></p><p><span style="font-size: 1rem;">[[ticket_reply_message]]</span></p><p><span style="font-size: 1rem;"><br></span></p><p><span style="font-size: 1rem;">Time</span></p><p><span style="font-size: 1rem;">[[reply_datetime]]</span></p><p><span style="font-size: 1rem;"><br></span></p><p><span style="font-size: 1rem;">Thank You</span><br></p>', 'User added a new reply on ticket #[[ticket_id]]', NULL, NULL, '<p>Dear Admin, </p><p><br></p><p>User added a new reply on ticket #[[ticket_id]]</p><p><br></p><p>Username:</p><p><span style="font-size: 1rem;">[[username]]</span><br></p><p><br></p><p>Email:</p><p><span style="font-size: 1rem;">[[email]]</span><br></p><p><br></p><p>Name:</p><p><span style="font-size: 1rem;">[[firstname]] [[lastname]]</span></p><p><br></p><p>Ticket ID:</p><p><span style="font-size: 1rem;">[[ticket_id]]</span><br></p><p><br></p><p><span style="font-size: 1rem;">Ticket Status:</span><br></p><p><span style="font-size: 1rem;">[[ticket_status]]</span><br></p><p><br></p><p><span style="font-size: 1rem;">Subject :</span><br></p><p><span style="font-size: 1rem;">[[ticket_subject]]</span><br></p><p><br></p><p>Message from user<br></p><p><span style="font-size: 1rem;">[[ticket_reply_message]]</span></p><p><span style="font-size: 1rem;"><br></span></p><p><span style="font-size: 1rem;">Time</span></p><p><span style="font-size: 1rem;">[[reply_datetime]]</span></p><p><span style="font-size: 1rem;"><br></span></p><p><span style="font-size: 1rem;">Thank You</span></p>',   'ticket_id,ticket_status,ticket_subject,ticket_body,ticket_reply_message,reply_datetime,firstname,lastname,email,username,website_name,website_logo'];

	    	$newMailTemplates[] = ['ticket_status_email', 'Ticket Status Change Email', 'Ticket #[[ticket_id]] status has been updated', '<p>Dear [[firstname]],&nbsp;</p><p><br></p><p>The status of a ticket having id [[ticket_id]] has been updated, please log in to [[website_name]] to see full details of the ticket.</p><p><br></p><p>Ticket ID:</p><p><span style="font-size: 1rem;">[[ticket_id]]</span><br></p><p><br></p><p><span style="font-size: 1rem;">Ticket Status:</span><br></p><p><span style="font-size: 1rem;">[[ticket_status]]</span><br></p><p><br></p><p><span style="font-size: 1rem;">Subject :</span><br></p><p><span style="font-size: 1rem;">[[ticket_subject]]</span><br></p><p><br></p><p><span style="font-size: 1rem;">Thank You</span></p><p><span style="font-size: 1rem;">Support Team<br></span><br></p>',  'Ticket #[[ticket_id]] status has been updated', NULL, NULL, '<p>Dear Admin,&nbsp;</p><p><br></p><p>The status of the ticket having id [[ticket_id]] has been updated.</p><p><br></p><p>Username:</p><p><span style="font-size: 1rem;">[[username]]</span><br></p><p><br></p><p>Email:</p><p><span style="font-size: 1rem;">[[email]]</span><br></p><p><br></p><p>Name:</p><p><span style="font-size: 1rem;">[[firstname]] [[lastname]]</span></p><p><span style="font-size: 1rem;"><br></span></p><p>Ticket ID:</p><p><span style="font-size: 1rem;">[[ticket_id]]</span><br></p><p><br></p><p><span style="font-size: 1rem;">Ticket Status:</span><br></p><p><span style="font-size: 1rem;">[[ticket_status]]</span><br></p><p><br></p><p><span style="font-size: 1rem;">Subject :</span><br></p><p><span style="font-size: 1rem;">[[ticket_subject]]</span><br></p><p><br></p><p><span style="font-size: 1rem;">Thank You</span></p><p><span style="font-size: 1rem;">Support Team<br></span></p>', 'ticket_id,ticket_status,ticket_subject,ticket_body,firstname,lastname,email,username,website_name,website_logo'];


	    	for ($i=0; $i < sizeof($newMailTemplates); $i++) { 
	    		$this->db->query("INSERT INTO `mail_templates` (`unique_id`, `name`, `subject`, `text`, `admin_subject`, `client_subject`, `client_text`, `admin_text`, `shortcode`) SELECT * FROM (SELECT '".$newMailTemplates[$i][0]."' AS `unique_id`, '".$newMailTemplates[$i][1]."' AS `name`, '".$newMailTemplates[$i][2]."' AS `subject`, '".$newMailTemplates[$i][3]."' AS `text`, '".$newMailTemplates[$i][4]."' AS `admin_subject`, '".$newMailTemplates[$i][5]."' AS `client_subject`, '".$newMailTemplates[$i][6]."' AS `client_text`, '".$newMailTemplates[$i][7]."' AS `admin_text`,'".$newMailTemplates[$i][8]."' AS `shortcode`) AS tmp WHERE NOT EXISTS ( SELECT `unique_id` FROM `mail_templates` WHERE `unique_id` = '".$newMailTemplates[$i][0]."' ) LIMIT 1;");
	    	}

	    	$data['results'][] = ["info"=>"mail templates updated..."];
    	} catch (Exception $e) {
			$data['results'][] = [
				"error" => $e->getMessage()
			];
		}

		return $data['results'];
    }

	/**
	 * Splits the provided string based on ';' character and returns an array.
	 * 
	 * The function skips the second element of the split array, and instead appends
	 * it to the first element.
	 *
	 * @param string $weapon The string to be split.
	 * @return array|null The array with the split elements, or null if the input string is empty.
	 * @throws InvalidArgumentException if the string is empty.
	 */
	public function explodeSkipOne(string $weapon) {
	    if (!$weapon) {
	        throw new InvalidArgumentException('Input string cannot be empty.');
	    }

	    $spiltthum = explode(';', $weapon);
	    $ThuImg = [];
	    $arraySize = sizeof($spiltthum);
	    
	    for ($i = 0; $i < $arraySize; $i++){
	        if($i == 1) {
	            $ThuImg[0] .= $spiltthum[$i];
	        } else {
	            $ThuImg[] = $spiltthum[$i];
	        }
	    }
	    
	    return $ThuImg;
	}


	/**
	 * Store version update details.
	 *
	 * This function inserts a new record into the 'version_update' table
	 * with the current application version and license code.
	 *
	 * @return void
	 */
	public function store_version_update_details()
	{
	    $data = [
	        'code' => CODECANYON_LICENCE,
	        'script_version' => $this->config->item('app_version')
	    ];

	    // Insert the data into the table
	    if (!$this->db->insert('version_update', $data)) {
	        // Handle error, e.g., log the error message, throw an exception, etc.
	        log_message('error', 'Failed to store version update details.');
	    }
	}

}