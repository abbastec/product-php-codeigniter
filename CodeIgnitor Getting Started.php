Edit example.php
-------------------
function __constructor()
{
    parent::__constructor();
    ...
    $this->post =$_REQUEST;
}

public function demo_get()
{
    $message = [
        'name' => 'PHP Studio',
        'city' => 'Chennai'
    ];
    $this->set_response($message, REST_Controller::HTTP_OK);
}

public functuion show_post() 
{
    $name = $this->post['name'];
    $city = $this->post['city'];
    $message = [
        'name' => $name,
        'city' => $city
    ];
    $this->set_response($message, REST_Controller::HTTP_OK);
}

Postman
GET [http://localhost/restapi/index.php/example/demo]
{
    "name" : "PHP Studio",
    "city": "Chennai"
}

https://github.com/owthub/codeigniter-rest-api

/application/config/rest.php

API configuration settings file
$config['force_https'] = FALSE;
$config['rest_default_format'] = 'json';
$config['rest_supported_formats'] = ['json', ...];
$config['rest_auth'] = FALSE;
$config['auth_source'] = ldap;
$config['allow_auth_and_keys'] = TRUE;

/application/libraries/Format.php
    Helpful to convert various formats such as XML, JSON, CSV, etc.,

/application/libraries/REST_Controller.php
    CodeIgnitor Rest Controller: A fully RESTful server implementation

/application/language/english/rest_controller_lang.php
    API Error messages according to selected rest language
    $lang['text_rest_invalid_api_key']
---------------------------------------------------------------------------

<%php
    require APPPATH.'libraries/REST_Controller.php';
    class Student extends REST_Controller {
        function __constructor()
        {
            parent::__constructor();
            ...
            $this->load->database();
            $this->load->model(array("api/student_model"));
        }

        //POST: <project-url>/index.php/student
        public function index_post()
        {

        }

        //PUT: <project-url>/index.php/student
        public function index_put()
        {
            
        }

        //DELETE: <project-url>/index.php/student
        public function index_delete()
        {
            
        }

        //GET: <project-url>/index.php/student
        public function index_get()
        {
            $students = $this->student_model->get_students();

            if(count($students) > 0) {
                $this->response(array(
                    "status"=>1,
                    "message"=>"Students found",
                    "data"=>$students
                ), REST_CONTROLLER::HTTP_OK);
            } else {
                $this->response(array(
                    "status"=>0,
                    "message"=>"No students found",
                    "data"=>$students
                ), REST_CONTROLLER::HTTP_NOT_FOUND);
            }
        }
    }
%>

------------------------------------------------------------------
student_model.php

<?php
class Student_model extends CI_Model {
    public function __constructor() {
        parent::__constructor();
        $this->load->database();
    }

    public function get_students() {
        this->db->select(*);
        this->db->from("tbl_students");
        $query = $this->db->get();

        return $query->result();
    }
}
?>

-----------------------------------------------------------------
POST API Method to save data

function __constructor() {
    parent::__constructor();
    ...
    $this->load->database();
    $this->load->model(array("api/student_model"));
}

public function index_post()
{
    $data = json_decode(file_get_contents("php://input"));

    $name = isset($data->name) ? $data->name : "";
    $email = isset($data->email) ? $data->email : "";
    $mobile = isset($data->mobile) ? $data->mobile : "";

    if(!empty($name) && !empty($email) && !empty(mobile)) {
        $student = array(
            "name" => $name,
            "email" => $email,
            "mobile" => $mobile
        );
        if($this->student_model->insert_student($student)) {
            $this->response(array(
                "status"=>1,
                "message"=>"Student has been created"
            ), REST_CONTROLLER::HTTP_OK);
        }
    } else {
        $this->response(array(
            "status"=>0,
            "message"=>"Failed to create student"
        ), REST_CONTROLLER::HTTP_INTERNAL_SERVER_ERROR);
    }
}

public function insert_student($data = array()) 
{
    return $this->db->insert("tbl_students", $data);
}

--------------------------------------------------------------------
ADMIN API 01:
GET: http://localhost/product/index.php/api/product

:Header:


:Request:


:Response:
{
    "data": [
        {"id": "1", "name": "Product1 500 ml", "price": "200" },
        {"id": "2", "name": "Product2 1 kg", "price": "150" },
        {"id": "3", "name": "Product3 250 gm", "price": "160" },
        {"id": "4", "name": "Product4 250 gm", "price": "120" },
        {"id": "5", "name": "Product5 250 gm", "price": "180" },
    ]
}
-------------------------------------------------------------------
ADMIN API 02:
POST: api/product

:Header:


:Request:
{
    "name": "Product1 500 ml", "price": "200"
}

:Response:
{
    "status": "1",
    "message": "Product created successful"
}
-------------------------------------------------------------------
ADMIN API 03:
GET: api/product/orderlistall

:Header:


:Request:


:Response:
{
    "data" :[
        {
            "mobno": "9791070918",
            "addr": "12, Kumaran Street, Salem - 636001",
            "list": [
                {"id": "1", "name": "Product1 500 ml", "price": "200", "qty": "1" },
                {"id": "3", "name": "Product3 250 gm", "price": "160", "qty": "2" },
                {"id": "4", "name": "Product4 250 gm", "price": "120", "qty": "1" }
            ]
        },
        {
            "mobno": "9791070919",
            "addr": "46, Laxmi Street, Salem - 636001",
            "list": [
                {"id": "2", "name": "Product2 1 kg", "price": "150", "qty": "1" },
                {"id": "3", "name": "Product3 250 gm", "price": "160", "qty": "1" },
                {"id": "5", "name": "Product5 250 gm", "price": "180", "qty": "1" }
            ]
        }
    ]
}

-------------------------------------------------------------------
USER API 01:
GET: api/register

:Header:


:Request:
{ "mobno": "9791070918", "password": "123456" }

:Response:
{
    "status": "1",
    "message": "Registered successful"
}

-------------------------------------------------------------------
USER API 02:
GET: api/login

:Header:


:Request:
{ "mobno": "9791070918", "password": "123456" }

:Response:
{
    "status": "1",
    "token": "..."
    "message": "Login successful"
}

:Token:
{
    "mobno": "9791070918"
}
-------------------------------------------------------------------
USER API 03:
GET: api/product/placeorder

:Header:
Authorization: [token]

:Request:
{
    "addr": "12, Kumaran Street, Salem - 636001",
    "list": [
        { "id": "1", "price": "200", "qty": "1"},
        { "id": "3", "price": "160", "qty": "2"},
        { "id": "4", "price": "120", "qty": "1"}
    ]
}

:Response:
{
    "status": "1",
    "message": "Order placed successful"
}
-------------------------------------------------------------------
