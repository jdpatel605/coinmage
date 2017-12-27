<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
class v1 extends REST_Controller {

    private $web_url = 'http://localhost/qsekadmin/';

    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        // $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        // $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        // $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
        $this->load->model('Api','mdl');
        $this->load->helper('form');
        $this->load->helper('my');
        $this->load->library('form_validation');
        $this->load->library('upload');
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_data($_REQUEST);
    }

    public function count_post()
    {
        $config = array(
            array('field' => 'table', 'label' => 'Table', 'rules' => 'trim|required'),
        );
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() == FALSE) {
            $this->set_response(['status' => FALSE, 'message' =>validation_errors()], REST_Controller::HTTP_OK);
        }
        else{

            $table = $this->post('table');
            $count = $this->mdl->get_count($table );

            if($count) {
                $this->response([
                    'status' => TRUE,
                    'message' => 'Total '.$table.' record of count',
                    'count' => $count
                ], REST_Controller::HTTP_OK);
            }
            else {
                $this->response([
                    'status' => TRUE,
                    'message' => 'No data were found',
                    'count' => '0'
                ], REST_Controller::HTTP_OK);
            }
        }
    }

    public function getlist_post()
    {
        $config = array(
            array('field' => 'table', 'label' => 'Table', 'rules' => 'trim|required'),
            array('field' => 'page', 'label' => 'Page', 'rules' => 'trim|required'),
        );
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() == FALSE) {
            $this->set_response(['status' => FALSE, 'message' =>validation_errors()], REST_Controller::HTTP_OK);
        }
        else{
            
            $table = $this->post('table');
            $page = $this->post('page');
            if($page) {
                $page = $page;
            } else {
                $page = 0;
            }
            $list = $this->db->select('*')->limit('10',$page)->get($table)->result_array();
            if($list) {
                $this->response([
                    'status' => TRUE,
                    'message' => 'Project list successfully get',
                    'data' => $list
                ], REST_Controller::HTTP_OK);
            }
            else {
                $this->response([
                    'status' => FALSE,
                    'message' => 'No project were found'
                ], REST_Controller::HTTP_OK);
            }
        }
    }

    public function addproject_post()
    {
        $config = array(
            array('field' => 'title', 'label' => 'Title', 'rules' => 'trim|required'),
            array('field' => 'description', 'label' => 'Description', 'rules' => 'trim'),
            array('field' => 'link', 'label' => 'Link', 'rules' => 'trim|required'),
        );
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() == FALSE) {
            $this->set_response(['status' => FALSE, 'message' =>validation_errors()], REST_Controller::HTTP_OK);
        }
        else{
            $data['title'] = $this->post('title');
            $data['description'] = $this->post('description');
            $data['link'] = $this->post('link');
            if(isset($_FILES['logo']['name']) && $_FILES['logo']['name'] != '')
            {
                $image_name = explode(".",$_FILES['logo']['name']);
                $imgData = $this->mdl->singleImageUpload('logo','logo',$image_name[1],'2',$image_name[0]);
                if($imgData['upload']=='True')
                {
                    $data['logo'] = base_url().'upload/logo/'.$imgData['data']['file_name'];
                }
            }
            if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != '')
            {
                $image_name = explode(".",$_FILES['image']['name']);
                $imgData = $this->mdl->singleImageUpload('image','project',$image_name[1],'2',$image_name[0]);
                $source_path = './upload/project/'. $imgData['data']['file_name'];
                $target_path = './upload/project/thumb/';
                MakeThumb($source_path,$target_path,100,60);
                if($imgData['upload']=='True')
                {
                    $data['image'] = base_url().'upload/project/'.$imgData['data']['file_name'];
                    $data['thumb'] = base_url().'upload/project/thumb/'.$imgData['data']['file_name'];
                }
            }

            $is_slider = $this->mdl->insert('project',$data);

            if($is_slider) {
                $this->response([
                    'status' => TRUE,
                    'message' => 'Project successfully added...',
                    'url' => $this->web_url.'project',
                    'insert_data' => $is_slider
                ], REST_Controller::HTTP_OK);
            }
            else {
                $this->response([
                    'status' => TRUE,
                    'message' => 'Something went wrong please try after sometime !'
                ], REST_Controller::HTTP_OK);
            }
        }
    }

    public function getupdatedata_post()
    {
        $config = array(
            array('field' => 'id', 'label' => 'ID', 'rules' => 'trim|required'),
        );
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() == FALSE) {
            $this->set_response(['status' => FALSE, 'message' =>validation_errors()], REST_Controller::HTTP_OK);
        }
        else{

            $id = $this->post('id');
            $is_project = $this->mdl->get_data('project','*',array('id' => $id));

            if($is_project) {
                $this->response([
                    'status' => TRUE,
                    'message' => 'Project data successfully get...',
                    'data' => $is_project
                ], REST_Controller::HTTP_OK);
            }
            else {
                $this->response([
                    'status' => TRUE,
                    'message' => 'Something went wrong please try after sometime !'
                ], REST_Controller::HTTP_OK);
            }
        }
    }

    public function updateproject_post()
    {
        $config = array(
            array('field' => 'title', 'label' => 'Title', 'rules' => 'trim|required'),
            array('field' => 'description', 'label' => 'Description', 'rules' => 'trim'),
            array('field' => 'link', 'label' => 'Link', 'rules' => 'trim|required'),
        );
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() == FALSE) {
            $this->set_response(['status' => FALSE, 'message' =>validation_errors()], REST_Controller::HTTP_OK);
        }
        else{
            $id = $this->post('pid');
            $data['title'] = $this->post('title');
            $data['description'] = $this->post('description');
            $data['link'] = $this->post('link');
            $data['logo'] = $this->post('old_logo');
            $data['image'] = $this->post('old_image');
            $data['thumb'] = $this->post('old_thumb');
            if(isset($_FILES['logo']['name']) && $_FILES['logo']['name'] != '')
            {
                $image_name = explode(".",$_FILES['logo']['name']);
                $imgData = $this->mdl->singleImageUpload('logo','logo',$image_name[1],'2',$image_name[0]);
                if($imgData['upload']=='True')
                {
                    $data['logo'] = base_url().'upload/logo/'.$imgData['data']['file_name'];
                }
            }
            if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != '')
            {
                $image_name = explode(".",$_FILES['image']['name']);
                $imgData = $this->mdl->singleImageUpload('image','project',$image_name[1],'2',$image_name[0]);
                $source_path = './upload/project/'. $imgData['data']['file_name'];
                $target_path = './upload/project/thumb/';
                MakeThumb($source_path,$target_path,100,60);
                if($imgData['upload']=='True')
                {
                    $data['image'] = base_url().'upload/project/'.$imgData['data']['file_name'];
                    $data['thumb'] = base_url().'upload/project/thumb/'.$imgData['data']['file_name'];
                }
            }

            $is_slider = $this->mdl->update('project',$data,array('id' => $id));

            if($is_slider) {
                $this->response([
                    'status' => TRUE,
                    'message' => 'Project successfully updated...',
                    'url' => $this->web_url.'project',
                    'insert_data' => $is_slider
                ], REST_Controller::HTTP_OK);
            }
            else {
                $this->response([
                    'status' => TRUE,
                    'message' => 'Something went wrong please try after sometime !'
                ], REST_Controller::HTTP_OK);
            }
        }
    }

    public function removelist_post()
    {
        $config = array(
            array('field' => 'id', 'label' => 'ID', 'rules' => 'trim|required'),
        );
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() == FALSE) {
            $this->set_response(['status' => FALSE, 'message' =>validation_errors()], REST_Controller::HTTP_OK);
        }
        else{

            $id = $this->post('id');
            $is_project = $this->mdl->get_data('project','*',array('id' => $id));

            if(count($is_project) > 0) {

                $this->mdl->delete('project',array('id' => $id));

                $is_project = $this->mdl->get_all('project',$where=array('1'=>'1'),$order_by=array(),'10');

                $this->response([
                    'status' => TRUE,
                    'message' => 'Project data successfully deleted...',
                    'data' => $is_project
                ], REST_Controller::HTTP_OK);
            }
            else {
                $this->response([
                    'status' => TRUE,
                    'message' => 'Something went wrong please try after sometime !'
                ], REST_Controller::HTTP_OK);
            }
        }
    }
}
