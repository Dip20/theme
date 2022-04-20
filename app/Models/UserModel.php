<?php

namespace App\Models;

use CodeIgniter\Model;
use \Hermawan\DataTables\DataTable;

class UserModel extends Model
{
    
    public function insert_data($post, $table)
    {
        $db = $this->db;
        $builder = $db->table($table);

        /* Prepare data */
        $pdata['created_at'] = date('Y-m-d H:i:s');
        $pdata = array(
            'name'     => $post['name'],
            'email'    => $post['email'],
            'password' => password_hash($post['password'], PASSWORD_DEFAULT)
        );

        /* Insert Data */
        $result = $builder->Insert($pdata);
        $id = $db->insertID();

        if ($result) {
            $msg = array('st' => 'success', 'msg' => "Your Details Added Successfully!!!");
        } else {
            $msg = array('st' => 'fail', 'msg' => "Your Details Updated fail");
        }

        return $msg;
    }



















/**un unable code for this section */



    public function get_student_data()
    {
        //print_r("jdfvhjdkf");exit;
        $db = $this->db;
        $builder = $db->table("student");
        $builder->select('id,fname,lname,gender,dob,proimg');
        $builder->where('is_delete',0);
        $data_table =  DataTable::of($builder);
        $data_table->setSearchableColumns(['fname,lname']);
        $data_table->edit('proimg',function($row){
            // $img = explode(",",$row->proimg);
            $img_tag = '';
            // foreach($img as $img_url)
            // {
                $img_tag .= '<img src=" '.$row->proimg.' " width="50" height="50">';
            // }
            return $img_tag;
        });
        $data_table->add('action',function($row){
            $btnedit = '<a data-toggle="modal" data-target="#fm_model" href="' . url('Student/createStudent/') . $row->id . '" data-title="Edit Student : ' . $row->fname . '"  class="btn btn-link pd-10"><i class="far fa-edit"></i></a> ';

            $btndelete = '<a data-toggle="modal" target="_blank"   title="Student Name: ' . $row->fname . '"  onclick="editable_remove(this)"  data-val="' . $row->id . '"  data-pk="' . $row->id . '" tabindex="-1" class="btn btn-link"><i class="far fa-trash-alt"></i></a> ';
            
            return $btnedit.$btndelete;
        },'last');

        return $data_table->toJson();
    }

    
    public function get_master_data($method,$id)
    {
        $db = $this->db;
        $result = array();
        if($method == 'student')
        {
            $gmodel = new GeneralModel();
            $result['student'] = $gmodel->get_data_table('student', array('id' => $id));
        }
        
        return $result;
    }

    public function UpdateData($post)
    {
        $result = array();
        $db = $this->db;
        if($post['type'] == 'Remove')
        {
            if($post['method'] == 'student')
            {
                // $builder = $db->table('student');
                // $builder->where('id',$post['pk']);
                // $builder->delete();
                //$result = array('st' => 'success');
                $gnmodel = new GeneralModel();
                $result = $gnmodel->update_data_table('student', array('id' => $post['pk']), array('is_delete' => '1'));
            }
        }
        return $result;
    }
}
