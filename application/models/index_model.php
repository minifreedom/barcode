<?php
class index_model extends CI_Model {

	public function __construct() {        
		parent::__construct();
	}

  function school()
	{
		$query = $this->db->from('config_gpabook')->get();
		return $query->result();
	}

	function student($limit,$offset)
	{
		$this->db->from('tb_students');
		$this->db->limit($limit,$offset);
		$query=$this->db->get();
		if($query->num_rows()>0)
		{
			return $query->result();
		} else {
			return false;
		}
	}

	function count_student()
	{
		$query=$this->db->get('tb_students');
		return $query->num_rows();		
	}
	
	
	function student_id($id)
	{
		mysql_query("SET NAMES 'tis620'");
		$query = $this->db->from('tb_students')->where('id_student',$id)->get();
		return $query->result();
	}
	
	function student_id_pdf($id)
	{
		mysql_query("SET NAMES 'tis620'");
		$query = $this->db->from('tb_students')->where('id_student',$id)->get();
		return $query->result();
	}

}
