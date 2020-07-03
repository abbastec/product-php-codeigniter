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

  public function get_orderListAll() {
    $this->db->select("id, mobile, addr");
    $this->db->from("order_master");
    $query = $this->db->get();

    $result = $query->result();

    for ($x = 0; $x < count($result); $x++) {
      $order_id = $result[$x]->id;
      $this->db->select("id, order_id, product_id, price, qty");
      $this->db->from("order_list");
      $this->db->where("order_id", $order_id);
      
      $query1 = $this->db->get();
      $result[$x]->list = $query1->result();
    }

    return $result;
  }
}

 ?>
