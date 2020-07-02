<?php

class User_model extends CI_Model{

  public function __construct(){
    parent::__construct();
    $this->load->database();
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
}

 ?>
