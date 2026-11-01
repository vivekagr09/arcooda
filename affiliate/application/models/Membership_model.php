<?php 
class Membership_model extends CI_Model {
	function __construct(){
		parent::__construct();
	}
	
	function is_plan_activated_before($buy_id){
		$this->db->select('id');
		$this->db->from('membership_buy_history');
		$this->db->where(array('buy_id'=>$buy_id, 'status_id'=> 1));
		$query = $this->db->get();
		$fetchRow = $query->row();
		if($query->num_rows() > 0){
			return $fetchRow;
        } else {
			return 0;
		}
    }
    
}		