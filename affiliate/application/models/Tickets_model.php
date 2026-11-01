<?php
class Tickets_model extends CI_Model{

	function __construct()
	{
		parent::__construct();

	}

	public function status($find=null) {
		$status = [
			'1'  => __('user.ticket_status_open'),
			'2'  => __('user.ticket_status_pending'),
			'3'  => __('user.ticket_status_close')
		];
		if($find!=null) {
			return $status[$find];
		} else {
			return $status;
		}

	}


	public function subject($id) {
		return $this->db->query("SELECT subject FROM tickets_subject WHERE id=".$id)->row()->subject;
	}


	public function ticket_owner($id) {
		return $this->db->query("SELECT user_id FROM tickets WHERE ticket_id='{$id}'")->row()->user_id;
	}


	public function getTotalCountTickest($status=null,$table='tickets',$where=null) {

		$this->db->select('IFNULL(count(id),0) as total');
		$this->db->from($table);
		if($where!=null)
			$this->db->where($where);
		if($status!=null){
			$this->db->where('status',$status);
		}
		$query = $this->db->get();
		return $query->row_array();

	}
	public function getAllticketsDatatables(){
		$status = $this->input->post('status');
		$range = $this->input->post('range');
		$subject = $this->input->post('subject');

		$this->datatables->select('tickets.id as action,tickets.ticket_id,tickets.created_at,tickets.subject_id,tickets.status,tickets.updated_at,tickets.created_at,tickets.user_id,tSubject.subject as subjectName, tickets.status as status_id,tickets.status as status_ids,(SELECT CONCAT(IFNULL(users.firstname,"")," ",IFNULL(users.lastname," ")) FROM users WHERE users.id=tickets.user_id LIMIT 1) as username');
		$this->datatables->from('tickets');
		$this->datatables->join('tickets_subject as tSubject', 'tSubject.id = tickets.subject_id', 'left');
		if($status!=null){
			$this->datatables->where("tickets.status", "$status");
		}
		if($subject!=null){
			$this->datatables->where("tickets.subject_id", "$subject");
		}
		if ($range !== "") {
			$fulldate = explode("to", $range);
			$startDate = date("Y-m-d", strtotime($fulldate['0']));
			$endDate = date("Y-m-d", strtotime($fulldate['1']));
			$this->datatables->where("DATE(tickets.created_at) BETWEEN '" . $startDate . "' AND '" . $endDate . "'");
		}
		$this->datatables->edit_column('action', "$1", 'set_tickets_admin_action(ticket_id,status_id)');
		$this->datatables->edit_column('status_ids', "$1", "get_user_tickets_status(status_id)");
		return $this->datatables->generate("json");
	}

	public function getAllticketsubject() {
		$this->datatables->select('tickets_subject.id,tickets_subject.subject,tickets_subject.updated_at as action,tickets_subject.id as action_id,tickets_subject.subject as tSubject');
		$this->datatables->from('tickets_subject');
		$this->datatables->edit_column('action', "$1", 'set_tickets_subject_action(action_id,tSubject)');
		return $this->datatables->generate("json");
	}

	public function getUserTickets($user_id) {

		$status = $this->input->post('status');
		$range = $this->input->post('range');
		$subject = $this->input->post('subject');

		$this->datatables->select('tickets.id as action,tickets.ticket_id,tickets.created_at,tickets.subject_id,tickets.status,tickets.updated_at,tickets.created_at,tickets.user_id,tSubject.subject as subjectName, tickets.status as status_id,tickets.updated_at,tickets.status');
		$this->datatables->from('tickets');
		$this->datatables->join('tickets_subject as tSubject', 'tSubject.id = tickets.subject_id', 'left');
		$this->datatables->where('tickets.user_id',$user_id);
		if($status!=null){
			$this->datatables->where("tickets.status", "$status");
		}
		if($subject!=null){
			$this->datatables->where("tickets.subject_id", "$subject");
		}
		if ($range !== "") {
			$fulldate = explode("to", $range);
			$startDate = date("Y-m-d", strtotime($fulldate['0']));
			$endDate = date("Y-m-d", strtotime($fulldate['1']));
			$this->datatables->where("DATE(tickets.created_at) BETWEEN '" . $startDate . "' AND '" . $endDate . "'");
		}
		$this->datatables->edit_column('action', "$1", 'set_tickets_action(ticket_id,status)');
		$this->datatables->edit_column('status', "$1", "get_user_tickets_status(status)");
		return $this->datatables->generate("json");
		
	}

	public function getTicketDetails($ticket_id,$user_id=null) {
		$this->db->select('tickets.id as action,tickets.ticket_id,tickets.created_at,tickets.subject_id,tickets.status,tickets.updated_at,tickets.created_at,tickets.user_id,tSubject.subject as subjectName, tickets.status as status_id,tickets.updated_at,user.firstname,user.lastname,user.email');
		$this->db->from('tickets');
		if($user_id !=null){
			$this->db->where('tickets.user_id',$user_id);
		}
		$this->db->where('tickets.ticket_id',$ticket_id);
		$this->db->join('users as user', 'user.id = tickets.user_id', 'left');
		$this->db->join('tickets_subject as tSubject', 'tSubject.id = tickets.subject_id', 'left');
		$query = 	$this->db->get();
		return $query->row_array();
	}
	public function getTickestReply($ticket_id,$where=null) {
		$this->db->select('*');
		$this->db->from('tickets_reply');
		$this->db->where('tickets_reply.ticket_id',$ticket_id);
		if($where!=null){
			$this->db->where($where);
		}
		$this->db->order_by('tickets_reply.id','DESC');
		$query = 	$this->db->get();
		return $query->result_array();
	}
	public function deleteTicket($ticket_id) {
		$result = $this->getTickestReply($ticket_id,['message_type'=>2]);
		if(!empty($result)) {
			foreach($result as $key=>$value) {
				if(!empty($value['attachment'])) {
					$attachment = json_decode($value['attachment'],true);
					foreach($attachment as $value) {
						$filepath = 'assets'.DIRECTORY_SEPARATOR.'user_upload'.DIRECTORY_SEPARATOR.$value; 
						if(file_exists($filepath)) {
							@unlink($filepath);
						}
					}
				}
			}
		}
		$this->db->where('ticket_id',$ticket_id);
		$this->db->delete('tickets');
		$this->db->where('ticket_id',$ticket_id);
		return $this->db->delete('tickets_reply');
	}
	public function getsubjectlist() {
		return $this->db->query("SELECT * FROM tickets_subject")->result_array();
	}

}

function  set_tickets_subject_action($id,$subject) {
	return '<a href=""   data-id="'.$id.'" data-title="'.$subject.'" title="Click to Edit Subject" class="edit btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>
	<a href="" title="Click to Delete Subject" data-id="'.$id.'" class="removets btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>';
}
function set_tickets_action($id,$status){
	$url = base_url('usercontrol/ticketdetails/'.encryptString($id));
	$html = '<a href="'.$url.'" title="Click to View ticket" class="edit btn btn-primary btn-sm"><i class="fa fa-eye"></i></a>';
	if($status!=3)
		$html .= '<a href="#" title="Click to close ticket" data-id="'.$id.'" class="ml-2 closeTickets btn btn-danger btn-sm"><i class="fa fa-close"></i></a>';
	return $html;
}
function set_tickets_admin_action($id){
	$url = base_url('admincontrol/ticketdetails/'.$id);
	$html = '<a href="'.$url.'" title="Click to View ticket" class="edit btn btn-primary btn-sm"><i class="fa fa-eye"></i></a><a data-id="'.$id.'" title="Click to Delet ticket" class="btnremove btn btn-danger btn-sm text-white"><i class="fa fa-trash"></i></a>';
	return $html;
}
function get_user_tickets_status($id) {
	$status = [
		'1'  => __('user.ticket_status_open'),
		'2'  => __('user.ticket_status_pending'),
		'3'  => __('user.ticket_status_close')
	];
	return $status[$id];
}
