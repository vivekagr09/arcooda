<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Todo extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model('user_model', 'user');
		$this->load->model('Product_model');
		$this->load->model('Common_model');
		___construct(1);
	}

	public function userdetails(){ return $this->session->userdata('administrator'); }

	public function getodolist() {
		$user_id 	= $this->userdetails()['id'];
		$isCalView 	= $this->input->get('isCalView');
		$start 		= $this->input->get('start');
		$end 		= $this->input->get('end');

		if($isCalView){
			$this->db->select('id,LEFT(notes,10) as title, todo_date as start, notes,is_done');
			$this->db->from('todo_list');
			if(!empty($start) && !empty($end)){
				$this->db->where('todo_date >=',$start);
				$this->db->where('todo_date <=',$end);
			}
			$this->db->where('user_id',$user_id);
			$query = $this->db->get();
			$res = $query->result_array();
		} else {
			$res = $this->Common_model->get_data_all_asc('todo_list',['user_id'=>$user_id],'*','id');
		}

		if (sizeof($res) > 0) {
			$response = $res;
		}else{
			$response = 'null';
		}
		
		echo json_encode($response);
		exit;

	}

	public function addtodolist() {
	  if ($this->input->server('REQUEST_METHOD') === 'POST') {
		    $this->form_validation->set_rules('note', 'notes', 'trim|required');
		    $this->form_validation->set_rules('todo_date', 'todo_date', 'trim|required');

	    if ($this->form_validation->run()) {
	      $note = $this->input->post('note');
	      $id = $this->input->post('id');
	      $todo_date = $this->input->post('todo_date');
	      $user_id = $this->userdetails()['id'];

	      if ($id != 0) {
	        $update_data = ['notes' => $note, 'todo_date' => $todo_date, 'updated_at' => date('Y-m-d H:i:s')];
	        $res = $this->Common_model->update('todo_list', ['user_id' => $user_id, 'id' => $id], $update_data);
	        if ($res) {
	          echo json_encode(['status' => 'success', 'message' => __('admin.todo_list_updated_successfully')]);
	        } else {
	          echo json_encode(['status' => 'failure', 'message' => __('admin.todo_list_update_failed')]);
	        }
	      } else {
	        $this->Common_model->insert('todo_list', [
	          'user_id' => $user_id,
	          'notes' => $note,
	          'todo_date' => $todo_date,
	          'is_done' => '0',
	          'created_at' => date('Y-m-d H:i:s'),
	        ]);
	        echo json_encode(['status' => 'success', 'message' => __('admin.todo_list_added_successfully')]);
	      }
	    } else {
	      	echo json_encode(['status' => 'failure', 'message' => __('admin.todo_list_missing_date')]);
	    }
	  } else {
	    echo json_encode(['status' => 405, 'message' => __('admin.methods_not_allowed')]);
	    exit;
	  }
	}


public function actiontodolist() {
  if ($this->input->server('REQUEST_METHOD') === 'POST') {
    $this->form_validation->set_rules('id', 'id', 'trim|required');
    if ($this->form_validation->run()) {

      $id = $this->input->post('id');
      $action = $this->input->post('action');
      $is_done = $this->input->post('is_completed');
      $user_id = $this->userdetails()['id'];
      $where = ['user_id' => $user_id, 'id' => $id];

      if ($action == 1) {
        $res = $this->Common_model->deletedata('todo_list', $where);
        if ($res) {
          echo json_encode(['status' => 'success', 'message' => __('admin.todo_list_deleted_successfully')]);
        } else {
          echo json_encode(['status' => 'failure', 'message' => __('admin.todo_list_delete_failed')]);
        }
        exit;
      } else {
        $update_data = ['is_done' => "$is_done", 'updated_at' => date('Y-m-d H:i:s')];
        $res = $this->Common_model->update('todo_list', $where, $update_data);
        if ($res) {
          echo json_encode(['status' => 'success', 'message' => __('admin.todo_list_updated_successfully')]);
        } else {
          echo json_encode(['status' => 'failure', 'message' => __('admin.todo_list_update_failed')]);
        }
      }
    } else {
      echo json_encode(['status' => 'failure', 'message' => str_replace('</p>', '', str_replace('<p>', '', validation_errors()))]);
    }
  } else {
    echo json_encode(['status' => 405, 'message' => __('admin.methods_not_allowed')]);
    exit;
  }
}


	public function getodolistonly() {
		 $user_id 	= $this->userdetails()['id'];
		$this->db->select('id,LEFT(notes,10) as title, todo_date as start, notes,is_done,todo_list.created_at,todo_list.updated_at');
		$this->db->from('todo_list'); 
		$this->db->where('user_id',$user_id);
		$this->db->order_by("todo_date", "desc");
		$this->db->limit(5,0);
		$query = $this->db->get();
		$res = $query->result_array(); 
		$data['todolist']=array();
		if (sizeof($res) > 0) {
 
			$data['todolist'] = $res;
		} 

		$json['html'] = $this->load->view("admincontrol/todo/todo-list-dashboard",$data,true);
		 
		echo json_encode($json);
		exit;
	}
}