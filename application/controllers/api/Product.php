<?php

require APPPATH.'libraries/REST_Controller.php';

class Product extends REST_Controller{

  public function __construct(){

    parent::__construct();
    //load database
    $this->load->database();
    $this->load->model(array("api/product_model"));
  }

  // GET: <project_url>/index.php/api/product
  public function index_get(){
    //Get all product
    $products = $this->product_model->get_products();

    if(count($products) > 0){
      $this->response(array(
        "status" => 1,
        "message" => "Products found",
        "data" => $products
      ), REST_Controller::HTTP_OK);
    }else{
      //If products are not available
      $this->response(array(
        "status" => 0,
        "message" => "No Product found",
        "data" => $products
      ), REST_Controller::HTTP_NOT_FOUND);
    }
  }

  // POST: <project_url>/index.php/api/product
  public function index_post(){
    $data = json_decode(file_get_contents("php://input"));

    $name = isset($data->name) ? html_escape($data->name) : "";
    $price = isset($data->price) ? html_escape($data->price) : "";

    if(empty($name) || empty($price)){
      $this->response(array(
        "status" => 0,
        "message" => "All fields are needed"
      ), REST_Controller::HTTP_NOT_FOUND);
      return;
    }

    // all values are available
    $product = array(
      "name" => $name,
      "price" => $price
    );
    
    // Checking if product name already exist
    $product_by_name = $this->product_model->get_product_by_name($name);
    if(count($product_by_name) > 0){
      $this->response(array(
        "status" => 0,
        "message" => "Product name already exist"
      ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
      return;
    }

    //Creating(Inserting) new product
    if($this->product_model->insert_product($product)){
      $this->response(array(
        "status" => 1,
        "message" => "Product has been created"
      ), REST_Controller::HTTP_OK);
    }else{
      //Error while creating
      $this->response(array(
        "status" => 0,
        "message" => "Failed to create product"
      ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  //GET: <project_url>/index.php/api/product/orderlistall
  public function orderlistall_get(){
    //Get all order list
    $orders = $this->product_model->get_orderListAll();

    if(count($orders) > 0){
      $this->response(array(
        "status" => 1,
        "message" => "Orders found",
        "data" => $orders
      ), REST_Controller::HTTP_OK);
    }else{
      //If no orders available
      $this->response(array(
        "status" => 0,
        "message" => "No Order found",
        "data" => $orders
      ), REST_Controller::HTTP_NOT_FOUND);
    }
  }
}

 ?>
