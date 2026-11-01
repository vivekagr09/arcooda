<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tickets extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model('user_model', 'user');
		$this->load->model('Common_model');
		$this->load->library('datatables');
		$this->load->model('Tickets_model');
		___construct(1);
	}

	public function userdetails(){ return $this->session->userdata('administrator'); }


	public function getAlltickets() {
		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$user_id = $this->userdetails()['id'];
			$this->load->model('Setting_model');
			$this->Setting_model->save('site', [
				'tickets_filter_status' => $this->input->post('status')
			]);
			echo $res = $this->Tickets_model->getAllticketsDatatables();
		} else {
			echo json_encode(['status'=>405,'message'=>'methos not allowed']);
			exit;	
		}
	}

	public function getticketssubject(){
		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$user_id = $this->userdetails()['id'];
			echo $this->Tickets_model->getAllticketsubject();
		} else {

			echo json_encode(['status'=>405,'message'=>'methos not allowed']);
			exit;	
		}
	}

	public function addticketssubject() {
		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$subject 	= $this->input->post('subject');
			$id 		= $this->input->post('id');
			if($id==0){
				$this->form_validation->set_rules('subject', 'subject', 'trim|required|is_unique[tickets_subject.subject]');
			} else {
				$this->form_validation->set_rules('subject', 'subject', 'trim|required|callback_subject_exists',['subject_exists'=>'the subject must be unique']);
			}
			
			if ($this->form_validation->run()) {
				$user_id = $this->userdetails()['id'];
				if($id!=0) {
					$update_data =['subject'=>$subject,'updated_at'=>date('Y-m-d H:i:s')]; 
					$res = $this->Common_model->update('tickets_subject',['user_id'=>$user_id,'id'=>$id],$update_data);
					if($res) {
						echo json_encode(['status'=>true]);
					} else {
						echo json_encode(['status'=>false]);
					}
				}else {
					$this->Common_model->insert('tickets_subject',[
						'user_id'=>$user_id,
						'subject'=>$subject,
						'status'=>'1',
						'created_at'=>date('Y-m-d H:i:s'),
					]);
					echo json_encode(['status'=>true]);
				}
			} else {	
				echo json_encode(array('status' => false, 'message' => str_replace('</p>', '', str_replace('<p>', '', validation_errors()))));
			}
		} else {
			echo json_encode(['status'=>405,'message'=>'methos not allowed']);
			exit;
		}
	}

	function subject_exists() {
		$subject  = $this->input->post('subject');
		$id  = $this->input->post('id');
		$this->db->select('*');
		$this->db->from('tickets_subject');
		$this->db->where('subject',$subject);
		$this->db->where('id !=',$id);
		$query = $this->db->get();
		$res = $query->row_array();
		return empty($res) ?true:false;
	}

	public function actiontasksubject() {
		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$this->form_validation->set_rules('id', 'id', 'trim|required');
			if ($this->form_validation->run()) {
				$id = $this->input->post('id');
				$user_id = $this->userdetails()['id'];
				$where = ['user_id'=>$user_id,'id'=>$id];
				$res = $this->Common_model->select_where_result('tickets',['subject_id'=>$id]);
				if(empty($res)) {
					$res = $this->Common_model->deletedata('tickets_subject',$where);
					if($res) {
						echo json_encode(['status'=>true]);
					} else {
						echo json_encode(['status'=>false,'message'=>'Unable to delete please try again']);
					}
				} else {
					echo json_encode(['status'=>false,'message'=>'Unable to delete this subject first delete related ticket(s)']);
				}
				exit;

			} else {	
				echo json_encode(array('status' => false, 'message' => str_replace('</p>', '', str_replace('<p>', '', validation_errors()))));
			}
		} else {
			echo json_encode(['status'=>405,'message'=>'methos not allowed']);
			exit;
		}
	}

	public function getStaticeData() {
		$user_id = $this->userdetails()['id'];
		$data['total'] = $this->Tickets_model->getTotalCountTickest()['total']??0;
		$data['totalopen'] = $this->Tickets_model->getTotalCountTickest(1)['total']??0;
		$data['totalclose'] = $this->Tickets_model->getTotalCountTickest(3)['total']??0;
		$data['totalsubject'] = $this->Tickets_model->getTotalCountTickest(null,'tickets_subject')['total']??0;
		echo json_encode($data);
	}

	public function getTickestReply() {
		if ($this->input->server('REQUEST_METHOD') == 'POST'){
			$user_id = $userdetails['id'];
			$ticket_id = $this->input->post('ticket_id');
			$res = $this->Tickets_model->getTicketDetails($ticket_id);
			if(!empty($res)) {	
				$res = $this->Tickets_model->getTickestReply($ticket_id);
				echo json_encode($res);	
			}
			exit;
		}
	}
	
	public function changeTicketStatus() {
		if ($this->input->server('REQUEST_METHOD') == 'POST'){
			$user_id = $userdetails['id'];
			$ticket_id = $this->input->post('ticket_id');
			$status = $this->input->post('status');
			$res = $this->Tickets_model->getTicketDetails($ticket_id);
			if(!empty($res)) {	
				$update_data =['updated_at'=>date('Y-m-d H:i:s'),'status'=>$status]; 
				$res = $this->Common_model->update('tickets',['ticket_id' => $ticket_id],$update_data);
				if($res) {

					$this->load->model('Mail_model');
					$this->Mail_model->send_ticket_mail($ticket_id, 'ticket_status_email');

					$this->load->model('Tickets_model');

					if($status == 3) {
						$title	= '#'.$ticket_id.' '.__('admin.ticket_has_closed');
						$desc	= __('admin.support_team').', '.__('admin.has_closed_ticket_on').' '.date('d M Y');
					} else {
						$title	= '#'.$ticket_id.' '.__('admin.ticket_status_has_changed');
						$desc	= __('admin.support_team').', '.__('admin.has_changed_ticket_status').' '.date('d M Y');
					}

					$this->Product_model->sendTicketNotification([
						'id'	=> $ticket_id,
						'type'	=> 'ticket_status_updated',
						'title'	=> $title,
						'desc'	=> $desc,
						'admin_notification' => 1,
						'user_notification'	 => $this->Tickets_model->ticket_owner($ticket_id)
					]);

					echo json_encode(['status'=>true]);
				} else {
					echo json_encode(['status'=>false]);
				}
			}
			exit;
		}
	}

	public function sendMessage() {
		if ($this->input->server('REQUEST_METHOD') == 'POST'){
			$user_id = $this->userdetails()['id'];
			extract($this->input->post(),true);
			$imageFile =[];
			if (isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) {
				$count_file = count($_FILES['attachment']['name']);
				$files = $_FILES['attachment'];	

				for($i=0; $i<$count_file; $i++){

					$extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
					if(!empty(trim($files['name'][$i]))){
						if(in_array($extension, ['png','gif','jpeg','jpg','PNG','GIF','JPEG','JPG','ICO','ico','zip','doc','docs','pdf','xls','xlsx','ppt','pptx','txt'])) {

							$Fname= md5(random_string('alnum', 10)).'.'.$extension;
							$destinationPath = 'assets'.DIRECTORY_SEPARATOR.'user_upload'.DIRECTORY_SEPARATOR.$Fname;
							if(empty($files['error'][$i])){
								if(@move_uploaded_file($files['tmp_name'][$i], $destinationPath)) {
									$imageFile[] = $Fname;
								} else {
									echo json_encode(array('status' => false, 'message' => 'Fail to upload please try again'));
									exit;
								}
							} else {
								echo json_encode(array('status' => false, 'message' => 'Fail to upload please try again'));
								exit;
							}
						}

					}
				}
			}
			$data_to_add = array(
				'ticket_id' => $ticket_id,
				'user_id' => $user_id,
				'message' =>  $sendMessage,
				'attachment' => !empty($imageFile) ? json_encode($imageFile):'[]',
				'message_type' => !empty($imageFile) ? 2 :1,
				'created_at' => date('Y-m-d H:i:s'),
				'user_type'=>1
			);
			$res = $this->Common_model->insert('tickets_reply', $data_to_add);
			$old_status = $this->Tickets_model->getTicketDetails($ticket_id);
			if($old_status['status'] == 3) {
				$update_data =['updated_at'=>date('Y-m-d H:i:s'),'status'=>1]; 
			} else{
				$update_data =['updated_at'=>date('Y-m-d H:i:s')]; 
			}
			$res = $this->Common_model->update('tickets',['ticket_id' => $ticket_id],$update_data);
			if ($res) {
				$this->load->model('Mail_model');
				$this->load->model('Tickets_model');
				$this->Mail_model->send_ticket_mail($ticket_id, 'ticket_reply_email');

				$this->Product_model->sendTicketNotification([
					'id'	=> $ticket_id,
					'type'	=> 'ticket_created',
					'title'	=> __('user.new_replay_on_ticket').' #'.$ticket_id,
					'desc'	=> __('admin.support_team').', '.__('user.has_replyed_on_ticket'),
					'user_notification'	=> $this->Tickets_model->ticket_owner($ticket_id)
				]);
				echo json_encode(array('status' => true, 'data' => [], 'message' => 'message replay successfully'));
			} else {
				echo json_encode(array('status' => false, 'message' => 'Please try again'));
			}
		}
	}

	public function create_ticket() {
		if ($this->input->server('REQUEST_METHOD') == 'POST'){
			$this->load->library('form_validation');
			$this->form_validation->set_rules('user_id', 'User', 'required');
			$this->form_validation->set_rules('subject_id', 'Subject', 'required');
			$this->form_validation->set_rules('message', 'Message', 'required' );

			if($this->form_validation->run()){
				$data = $this->input->post(null);
				$imageFile=[];

				if(isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) {


					$count_file = count($_FILES['attachment']['name']);
					$files = $_FILES['attachment'];	

					for($i=0; $i<$count_file; $i++){

						$extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
						if(!empty(trim($files['name'][$i]))){
							if(in_array($extension, ['png','gif','jpeg','jpg','PNG','GIF','JPEG','JPG','ICO','ico','zip','doc','docs','pdf','xls','xlsx','ppt','pptx','txt'])) {

								$Fname= md5(random_string('alnum', 10)).'.'.$extension;
								$destinationPath = 'assets'.DIRECTORY_SEPARATOR.'user_upload'.DIRECTORY_SEPARATOR.$Fname;
								if(empty($files['error'][$i])){
									if(@move_uploaded_file($files['tmp_name'][$i], $destinationPath)) {
										$imageFile[] = $Fname;
									} else {
										echo json_encode(array('status' => false, 'message' => 'Fail to upload please try again'));
										exit;
									}
								} else {
									echo json_encode(array('status' => false, 'message' => 'Fail to upload please try again'));
									exit;
								}
							}

						}
					}
				}


				if(!isset($errors) || empty($errors)) {

					$data_to_add= [
						'user_id'=> $data['user_id'],
						'subject_id'=> $data['subject_id'],
						'status'=> 1,
						'created_at'=>date('Y-m-d H:i:s'),
						'updated_at'=>date('Y-m-d H:i:s'),
						'ticket_id'=>'AF'.date(('Ymd'),time()).rand()
					];

					$is_generate = $this->Common_model->insert('tickets',$data_to_add);

					if($is_generate) {

						$data_to_replay= [
							'user_id'=> $data['user_id'],
							'message'=> $data['message'],
							'attachment' => !empty($imageFile) ? json_encode($imageFile):'[]',
							'message_type' => !empty($imageFile) ? 2 : 1,
							'user_type'=> 2,
							'created_at'=>date('Y-m-d H:i:s'),
							'updated_at'=>date('Y-m-d H:i:s'),
							'ticket_id'=>$data_to_add['ticket_id']
						];

						$this->Common_model->insert('tickets_reply',$data_to_replay);

						$this->load->model('Mail_model');

						$this->Mail_model->send_ticket_mail($data_to_add['ticket_id'], 'ticket_created_email');

						$this->load->model('Product_model');
						$this->load->model('Tickets_model');
						
						$this->Product_model->sendTicketNotification([
							'id'	=> $data_to_add['ticket_id'],
							'type'	=> 'ticket_created',
							'title'	=> '#'.$data_to_add['ticket_id'].' '.__('user.new_ticket_created'),
							'desc'	=> __('admin.support_team').', '.__('user.has_created_new_ticket').', '.$this->Tickets_model->subject($data['subject_id']),
							'admin_notification'	=> 1,
							'user_notification'	=> $data['user_id']
						]);

						$json['success'] = __('admin.ticket_created_successfully');

					} else {
						$json['errors'] = __('admin.failed_to_send_mail');
					}
				} else {
					$json['errors']['attachment'] = $errors;
				}
			}else{
				$json['errors'] = $this->form_validation->error_array();
			}
			echo json_encode($json);die;
		}
	}

	public function deleteTicketStatus() {
		if ($this->input->server('REQUEST_METHOD') == 'POST'){
			$ticket_id = $this->input->post('ticket_id');
			$res = $this->Tickets_model->getTicketDetails($ticket_id);
			if(!empty($res)) {	
				$res = $this->Tickets_model->deleteTicket($ticket_id);
				echo json_encode(array('status' => true));
			}
			exit;
		}
	}
}