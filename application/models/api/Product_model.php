<?php

class Product_model extends CI_Model{

  public function __construct(){
    parent::__construct();
    $this->load->database();
  }

  public function get_products(){

    $this->db->select("*");
    $this->db->from("products");
    $query = $this->db->get();

    return $query->result();
  }

  public function insert_product($data = array()) {
    return $this->db->insert("products", $data);
  }
}

 ?>
