<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Api extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function get_table_by($table,$where,$field=false)
    {
        $this->db->where($where);
        if($field)
            return  $this->db->get($table)->row($field);
        else
            return  $this->db->get($table)->row_array();
    }


    function get_column_by($table,$column,$where = array('1'=>'1'))
    {
        $data = $this->db->select($column)->where($where)->get($table)->result_array();
        global $tmp_column;
        $tmp_column = $column;
        return array_map (function($value){
            global $tmp_column;
            return $value[$tmp_column];
        },$data);
    }

    function get_all($table,$where=array('1'=>'1'),$order_by=array(),$limit=false)
    {
        $this->db->select('*');
        $this->db->where($where);
        if(isset($order_by) && count($order_by)==2){
            $this->db->order_by($order_by[0],$order_by[1]);
        }
        if($limit){
            $this->db->limit($limit);
        }
        return  $this->db->get($table)->result_array();
    }

    function get_all_data($table,$where=array('1'=>'1'),$order_by=array(),$limit=false,$offset=false)
    {
        $this->db->select('*');
        $this->db->where($where);
        if(isset($order_by) && count($order_by)==2){
            $this->db->order_by($order_by[0],$order_by[1]);
        }
        if($limit){
            $this->db->limit($offset,$limit);
        }
        return  $this->db->get($table)->result_array();
    }

    public function get_data($table,$column,$where = array('1'=>'1'))
    {
        return $this->db->select($column)
                        ->from($table)
                        ->where($where)
                        ->get()
                        ->row_array();
    }

    function insert($table='',$data=[])
    {
        if(!empty($table) && is_array($data) && !empty($data)){
            $is_insert = $this->db->insert($table,$data);
            if($is_insert)
            {
                return $this->db->insert_id();
            }
            return false;
        }
        return false;
    }

    function update($table='',$data=[],$where=[])
    {
        if(!empty($table) && is_array($data) && !empty($data) && is_array($where) && !empty($where)){
            $this->db->where($where);
            return $this->db->update($table,$data);
        }
        return false;
    }

    function delete($table='',$where=[])
    {
        if(!empty($table) && is_array($where) && !empty($where))
        {
            return $this->db->delete($table,$where);
        }
        return false;
    }

    function get_count($table,$where=array('1'=>'1')) {
        return $this->db->select('*')->where($where)->get($table)->num_rows();
    }

     function singleImageUpload($upload_name,$folder,$extension,$bnr,$filename)
    {
        if($folder == '')
        {
            $config['upload_path'] = './upload/';
        }
        else
        {
            $config['upload_path'] = './upload/'.$folder."/";
        }
        $config['allowed_types'] = '*';
        if($bnr == 2)
        {
            $config['max_width'] = '3000';
            $config['max_height'] = '3000';
        }
        elseif ($bnr == 1)
        {}
        // $config['file_name'] = $user_name.date('YmdHis').".".$extension;
        $config['file_name'] = $filename.date('YmdHis').'.png';
       
        $this->upload->initialize($config);
        $this->load->library('upload', $config);
        if ( ! $this->upload->do_upload($upload_name))
        {
            $arrayRetutn['upload'] = 'False';
            $arrayRetutn['error'] = $this->upload->display_errors();
        }
        else
        {
            $arrayRetutn['upload'] = 'True';
            $arrayRetutn['data'] = $this->upload->data();
        }
         //echo '<pre>';print_r($arrayRetutn);echo '</pre>'; die;
        return $arrayRetutn;
    }
}
?>