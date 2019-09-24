<?php
use Restserver\Libraries\REST_Controller;

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

/**
 * Description of RestPostController
 *
 * @author https://roytuts.com
 */
class RestPostController extends CI_Controller {

	use REST_Controller {
		REST_Controller::__construct as private __resTraitConstruct;
	}
	
	function __construct() {
        parent::__construct();
		$this->__resTraitConstruct();

		//model for accounts
		$this->load->model('ContactModel', 'cm');

		//model for users
		$this->load->model('user');
    }

    function add_contact_post() {
        $contact_name = $this->post('contact_name');
        $contact_address = $this->post('contact_address');
        $contact_phone = $this->post('contact_phone');
        
        $result = $this->cm->add_contact($contact_name, $contact_address, $contact_phone);

        if ($result === FALSE) {
            $this->response(array('status' => 'failed'));
        } else {
            $this->response(array('status' => 'success'));
        }
    }

    function contacts_get() {
        $contacts = $this->cm->get_contact_list();

        if ($contacts) {
            $this->response($contacts, 200);
        } else {
            $this->response(NULL, 404);
        }
    }

    function contact_get() {
        if (!$this->get('id')) {
            $this->response(NULL, 400);
        }

        $contact = $this->cm->get_contact($this->get('id'));

        if ($contact) {
            $this->response($contact, 200); // 200 being the HTTP response code
        } else {
            $this->response(NULL, 404);
        }
    }

//delete

   function delete_contact_delete($contact_id) {

        $result = $this->cm->delete_contact($contact_id);

        if ($result === FALSE) {
            $this->response(array('status' => 'failed'));
        } else {
            $this->response(array('status' => 'success'));
        }
    }















    //user speceific controls
public function login_post() {
        // Get the post data
        $uniqueId = $this->post('uniqueId');
        
        // Validate the post data
        if(!empty($uniqueId)){
            
            // Check if any user exists with the given credentials
            $con['returnType'] = 'single';
            $con['conditions'] = array(
                'uniqueId' => $uniqueId,
                'isDeleted' => 0
            );
            $user = $this->user->getRows($con);
            
            if($user){

                $this->response(array(
                    'status' => "success",
                    'message' => 'User login successful.'
                ));

            }else{
                $this->response(array(
                    'status' => "failed",
                    'message' => 'Wrong email or password.'
                ));
            }
        }else{
              $this->response(array(
                    'status' => "failed",
                    'message' => 'Provide email and password.'
                ));

            //}else{
        }
    }


     public function login_post_all() {
        // Get the post data
        $email = $this->post('email');
        $password = $this->post('password');
        
        // Validate the post data
        if(!empty($email) && !empty($password)){
            
            // Check if any user exists with the given credentials
            $con['returnType'] = 'single';
            $con['conditions'] = array(
                'email' => $email,
                'password' => verifyHashedPassword($password),
                'isDeleted' => 0
            );
            $user = $this->user->getRows($con);
            
            if($user){
                // Set the response and exit
                /*$this->response([
                    'status' => TRUE,
                    'message' => 'User login successful.',
                    'data' => $user
                ], REST_Controller::HTTP_OK);*/

                $this->response(array(
                    'status' => "success",
                    'message' => 'User login successful.'
                ));

            }else{
                // Set the response and exit
                //BAD_REQUEST (400) being the HTTP response code
                //$this->response("Wrong email or password.", REST_Controller::HTTP_BAD_REQUEST);
                $this->response(array(
                    'status' => "failed",
                    'message' => 'Wrong email or password.'
                ));
            }
        }else{
            // Set the response and exit
            //$this->response("Provide email and password.", REST_Controller::HTTP_BAD_REQUEST);
              $this->response(array(
                    'status' => "failed",
                    'message' => 'Provide email and password.'
                ));

            //}else{
        }
    }
    
    public function registration_post_all() {
        // Get the post data
        $name = strip_tags($this->post('name'));
        $mobile = strip_tags($this->post('mobile'));
        $email = strip_tags($this->post('email'));
        $password = $this->post('password');
        $roleId = "4";
        
        // Validate the post data
        if(!empty($name) && !empty($mobile) && !empty($email) && !empty($password)){
            
            // Check if the given email already exists
            $con['returnType'] = 'count';
            $con['conditions'] = array(
                'email' => $email,
            );
            $userCount = $this->user->getRows($con);
            
            if($userCount > 0){
                // Set the response and exit
                $this->response("The given email already exists.", REST_Controller::HTTP_BAD_REQUEST);
            }else{
                // Insert user data
                $userData = array(
                    'name' => $name,
                    'mobile' => $mobile,
                    'email' => $email,
                    'password' => getHashedPassword($password),
                    'roleId'  => $roleId
                    
                );
                $insert = $this->user->insert($userData);
                
                // Check if the user data is inserted
                if($insert){
                    // Set the response and exit
                 /*   $this->response([
                        'status' => TRUE,
                        'message' => 'The user has been added successfully.',
                        'data' => $insert
                    ], REST_Controller::HTTP_OK);*/

                    $this->response(array('status' => 'success'));
                }else{
                    // Set the response and exit
                    /*$this->response("Some problems occurred, please try again.", REST_Controller::HTTP_BAD_REQUEST);*/
                    $this->response(array('status' => 'failed'));
                }
            }
        }else{
            // Set the response and exit
            //$this->response("Provide complete user info to add.", REST_Controller::HTTP_BAD_REQUEST);
            $this->response(array('status' => 'Provide complete user info to add', 'message' => 'failed'));
        }
    }

    public function registration_post() {
        // Get the post data
        $uniqueId = strip_tags($this->post('uniqueId'));
        $roleId = "4";
        
        // Validate the post data
        if(!empty($uniqueId)){
            
            // Check if the given email already exists
            $con['returnType'] = 'count';
            $con['conditions'] = array(
                'uniqueId' => $uniqueId,
            );
            $userCount = $this->user->getRows($con);
            
            if($userCount > 0){
                // Set the response and exit
                $this->response("The given Unique Id already exists. Please choose another", REST_Controller::HTTP_BAD_REQUEST);
            }else{
                // Insert user data
                $userData = array(
                    'uniqueId' => $uniqueId,
                    'roleId'  => $roleId
                    
                );
                $insert = $this->user->insert($userData);
                
                // Check if the user data is inserted
                if($insert){

                    $this->response(array('status' => 'success'));
                }else{
                    $this->response(array('status' => 'failed'));
                }
            }
        }else{
            // Set the response and exit
            $this->response(array('status' => 'Oops please try again', 'message' => 'failed'));
        }
    }
    
    public function user_get($id = 0) {
        // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
        $con = $id?array('id' => $id):'';
        $users = $this->user->getRows($con);
        
        // Check if the user data exists
        if(!empty($users)){
            // Set the response and exit
            //OK (200) being the HTTP response code
            //$this->response($users, REST_Controller::HTTP_OK);
            $this->response(array('status' => $users, 'message' => 'success'));

        }else{
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
          /*  $this->response([
                'status' => FALSE,
                'message' => 'No user was found.'
            ], REST_Controller::HTTP_NOT_FOUND);*/

            $this->response(array('status' => 'failed', 'message' => 'No user was found.'));
        }
    }
    
    public function user_put() {
        $id = $this->put('userId');
        
        // Get the post data
        $name = strip_tags($this->put('name'));
        $password = $this->put('password');
        $mobile = strip_tags($this->put('mobile'));
        
        // Validate the post data
        if(!empty($id) && (!empty($name) || !empty($email) || !empty($password) || !empty($mobile))){
            // Update user's account data
            $userData = array();
            if(!empty($name)){
                $userData['name'] = $name;
            }
            if(!empty($email)){
                $userData['email'] = $email;
            }
            if(!empty($password)){
                $userData['password'] = getHashedPassword($password);
            }
            if(!empty($mobile)){
                $userData['mobile'] = $mobile;
            }
            $update = $this->user->update($userData, $id);
            
            // Check if the user data is updated
            if($update){
                // Set the response and exit
                $this->response(array(
                    'status' => "success",
                    'message' => 'The user info has been updated successfully.'
                ));
            }else{
                // Set the response and exit
                $this->response(array('status' => "failed", 'message' => "Some problems occurred, please try again."));
            }
        }else{
            // Set the response and exit
            $this->response(array('status' => "failed", 'message' => "Provide at least one user info to update."));
        }
    }

}