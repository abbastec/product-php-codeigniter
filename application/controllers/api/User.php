<?php

require APPPATH.'libraries/REST_Controller.php';
require APPPATH.'libraries/JWT.php';
use \Firebase\JWT\JWT;

class User extends REST_Controller{

  public function __construct(){

    parent::__construct();
    //header('Access-Control-Allow-Origin: *');
    //header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
    //header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    //load database
    $this->load->database();
    $this->load->model(array("api/user_model"));
  }

  // GET: <project_url>/index.php/api/user/register
  public function register_post(){
    $data = json_decode(file_get_contents("php://input"));

    $mobile = isset($data->mobile) ? html_escape($data->mobile) : "";
    $password = isset($data->password) ? html_escape($data->password) : "";

      if(empty($mobile) || empty($password)){
        $this->response(array(
          "status" => 0,
          "message" => "All fields are needed"
        ), REST_Controller::HTTP_NOT_FOUND);
        return;
      }
      
      // all values are available
      $user = array(
        "mobile" => $mobile,
        "password_hash" => password_hash($password, PASSWORD_BCRYPT)
      );

      // Checking if mobile number already registered
      $user_by_mobno = $this->user_model->get_user($mobile);
      if(count($user_by_mobno) > 0){
        $this->response(array(
          "status" => 0,
          "message" => "Mobile number already exist"
        ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        return;
      }

      //Creating(Inserting) new user
      if($this->user_model->register_user($user)){
        $this->response(array(
          "status" => 1,
          "message" => "User has been register"
        ), REST_Controller::HTTP_OK);
      }else{
        //Error while creating
        $this->response(array(
          "status" => 0,
          "message" => "Failed to register user"
        ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
      }
  }

  // POST: <project_url>/index.php/api/user/register
  public function login_post(){
    $data = json_decode(file_get_contents("php://input"));

    $mobile = isset($data->mobile) ? html_escape($data->mobile) : "";
    $password = isset($data->password) ? html_escape($data->password) : "";

      if(empty($mobile) || empty($password)){
        $this->response(array(
          "status" => 0,
          "message" => "All fields are needed"
        ), REST_Controller::HTTP_NOT_FOUND);
        return;
      }

      // all values are available, get user based on login mobile number
      $user = $this->user_model->get_user($mobile);
      if(count($user) == 0){
        //No User found based on Mobile Number
        $this->response(array(
          "status" => 0,
          "message" => "Invalid login"
        ), REST_Controller::HTTP_NOT_FOUND);
        return;
      }

      //User Found. Checking Password is valid
      if(password_verify($password, $user[0]->password_hash))
      {
        //Valid Password. Placing mobile number in jwt token
        $token['mobile'] = $user[0]->mobile;
        $date = new DateTime();
        $token['iat'] = $date->getTimestamp();
        $token['exp'] = $date->getTimestamp() + 60*60*5;

        $this->response(array(
            "status" => 1,
            "token" => JWT::encode($token, "my secret key 4 5 3 2 1"),
            "message" => "Login successful"
        ), REST_Controller::HTTP_OK);
      } 
      else 
      {
        //Invalid Password
        $this->response(array(
            "status" => 0,
            "message" => "Invalid login"
        ), REST_Controller::HTTP_NOT_FOUND);
      }
  }

  // POST: <project_url>/index.php/api/user/placeorder
  public function placeorder_post() 
  {
        $token = $this->input->get_request_header('Authorization');
        $data = json_decode(file_get_contents("php://input"));

        if(!$token) {
          //Authorization header is missing
          $this->response(array(
            "status" => 0,
            "message" => "All fields are needed"
          ), REST_Controller::HTTP_NOT_FOUND);
        }
        
        //Decoding JWT Token and extracting mobile number
        $decode = JWT::decode($token, "my secret key 4 5 3 2 1", array('HS256'));
        $mobile = $decode->mobile;

        //$addr = isset($data->addr) ? htmlspecialchars(strip_tags($data->addr)) : "";
        $addr = isset($data->addr) ? html_escape($data->addr) : "";
          
        $order_list = $data->list; //Contains list of ordered items

        $order_master = array(
          "mobile" => $mobile,
          "addr" => $addr
        );
          
        try {
          //Transaction is needed, since we are updating two or more tables
          //If any issue we can rollback
          $this->user_model->BeginTransaction();

          //Creating(Inserting) data in order master. It will return order_id to process order_list
          $order_id = $this->user_model->order_master($order_master); 
          
          //Iterating order list 
          for ($x = 0; $x < count($order_list); $x++) {
            $list = array(
              "order_id" => $order_id,
              "product_id" => html_escape($order_list[$x]->id),
              "price" => html_escape($order_list[$x]->price),
              "qty" => html_escape($order_list[$x]->qty),
            );

            $this->user_model->order_list($list);
          }
        } catch(Exception $e) {
          $this->user_model->RollbackTransaction();
          $this->response(array(
            "status" => 0,
            "message" => "Failed to place order"
          ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
          return;
        }
            
        $this->user_model->CommitTransaction();
        $this->response(array(
            "status" => 1,
            "message" => "Order placed successful"
        ), REST_Controller::HTTP_OK);
  }
}

 ?>
