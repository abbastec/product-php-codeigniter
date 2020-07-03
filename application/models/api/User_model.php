<?php

class User_model extends CI_Model{

  public function __construct(){
    parent::__construct();
    $this->load->database();
  }

  public function BeginTransaction() {
    $this->db->trans_begin();
  }

  public function CommitTransaction() {
    $this->db->trans_commit();
  }

  public function RollbackTransaction() {
    $this->db->trans_rollback();
  }

  public function get_user($mobile){

    $this->db->select("*");
    $this->db->from("user");
    $this->db->where("mobile", $mobile);
    $query = $this->db->get();

    return $query->result();
  }

  public function register_user($data = array()) {
    return $this->db->insert("user", $data);
  }

  public function order_master($data = array()) {
    $this->db->insert("order_master", $data);
    return $this->db->insert_id();
  }

  public function order_list($data = array()) {
    return $this->db->insert("order_list", $data);
  }
}

 ?>
