<?php

require APPPATH.'libraries/REST_Controller.php';
require APPPATH.'libraries/JWT.php';
use \Firebase\JWT\JWT;

class User extends REST_Controller{

  public function __construct(){

    parent::__construct();
    //load database
    $this->load->database();
    $this->load->model(array("api/user_model"));
  }

  // GET: <project_url>/index.php/api/user/register
  public function register_post(){
    $data = json_decode(file_get_contents("php://input"));

    $mobile = isset($data->mobile) ? html_escape($data->mobile) : "";
    $password = isset($data->password) ? html_escape($data->password) : "";

      if(!empty($mobile) && !empty($password)){
        // all values are available
        $user = array(
          "mobile" => $mobile,
          "password_hash" => password_hash($password, PASSWORD_BCRYPT)
        );

        if($this->user_model->register_user($user)){

          $this->response(array(
            "status" => 1,
            "message" => "User has been register"
          ), REST_Controller::HTTP_OK);
        }else{

          $this->response(array(
            "status" => 0,
            "message" => "Failed to register user"
          ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
      }else{
        $this->response(array(
          "status" => 0,
          "message" => "All fields are needed"
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    
  }

  // GET: <project_url>/index.php/api/user/register
  public function login_post(){
    $data = json_decode(file_get_contents("php://input"));

    $mobile = isset($data->mobile) ? html_escape($data->mobile) : "";
    $password = isset($data->password) ? html_escape($data->password) : "";

      if(!empty($mobile) && !empty($password)){
        $user = $this->user_model->get_user($mobile);
        if(count($user) == 1){
            //print_r($user[0]->password_hash);
            if(password_verify($password, $user[0]->password_hash))
            {
                $token['mobile'] = $user[0]->mobile;
                $date = new DateTime();
                $token['iat'] = $date->getTimestamp();
                $token['exp'] = $date->getTimestamp() + 60*60*5;

                $this->response(array(
                    "status" => 1,
                    "token" => JWT::encode($token, "my secret key"),
                    "message" => "Login successful"
                ), REST_Controller::HTTP_OK);
            } 
            else 
            {
                $this->response(array(
                    "status" => 0,
                    "message" => "Invalid login"
                ), REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            $this->response(array(
                "status" => 0,
                "message" => "Invalid login"
            ), REST_Controller::HTTP_NOT_FOUND);
        }
      }else{
        $this->response(array(
          "status" => 0,
          "message" => "All fields are needed"
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    
  }

  public function placeorder_post() 
  {
        $token = $this->input->get_request_header('Authorization');
        $data = json_decode(file_get_contents("php://input"));

        if($token) {
          $decode = JWT::decode($token, "my secret key", array('HS256'));
          $mobile = $decode->mobile;

          //$addr = isset($data->addr) ? htmlspecialchars(strip_tags($data->addr)) : "";
          $addr = isset($data->addr) ? html_escape($data->addr) : "";
          
          $order_list = $data->list;

          $order_master = array(
            "mobile" => $mobile,
            "addr" => $addr
          );
          
          try {
            $this->user_model->BeginTransaction();
            $order_id = $this->user_model->order_master($order_master); 
            for ($x = 0; $x < count($order_list); $x++) {
              $list = array(
                "order_id" => $order_id,
                "product_id" => html_escape($order_list[$x]->id),
                "price" => html_escape($order_list[$x]->price),
                "qty" => html_escape($order_list[$x]->qty),
              );

              if(!$this->user_model->order_list($list)) {
                $error = true;
              }
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
        } else {
            $this->response(array(
                "status" => 0,
                "message" => "All fields are needed"
            ), REST_Controller::HTTP_NOT_FOUND);
        }

  }
}

 ?>
