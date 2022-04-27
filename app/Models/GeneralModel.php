<?php

namespace App\Models;

use CodeIgniter\Model;

class GeneralModel extends Model {

    public function update_data_table($table = '', $where = array(), $upadte_data = array()) {
        $db = $this->db;
        $builder = $db->table($table);
        $builder->where($where);
        $result = $builder->update($upadte_data);
        $return = array();
        if ($result) {
            $return = array('st' => 'success', 'txt' => 'success');
        } else {
            $return = array('st' => 'fail', 'txt' => 'update fail');
        }
        return $return;
    }

    public function get_data_table($table = '', $where = array(), $select = '') {
        $db = $this->db;
        $db->setDatabase("jen2022f1uo");

        $builder = $db->table($table);
        if ($select == '')
            $select = '*';
        $query = $builder->select($select)->where($where)->get();

        $getdata = $query->getResultArray();
        //echo $db->getLastQuery();exit;
        if (!empty($getdata)) {
            $result = $getdata[0];
        } else {
            $result = array();
        }
        return $result;
    }

    public function get_array_table($table = '', $where = array(), $select = '') {
         
        $db = $this->db;
        $db->setDatabase("jen2022f1uo");
        $builder = $db->table($table);
        if ($select == '')
            $select = '*';
        $query = $builder->select($select)->where($where)->get();
        $getdata = $query->getResultArray();
        // echo $db->getLastQuery();exit;

        $result = array();
        if (!empty($getdata)) {
            $result = $getdata;
        } 
        return $result;
    }

    public function get_saleInv_id($table = '')
    {
        $db = $this->db;
        $db->setDatabase("jen2022f1uo");
        $builder = $db->table($table);
        $select = 'MAX(invoice_no) as max_id';
        $builder->select($select);
        if($table == 'sales_ACinvoice'){
            $builder->where('v_type','general');
        }
        $builder->where('is_delete','0');
        $query = $builder->get();
        $getdata = $query->getRow();
        if (!empty($getdata)) {
            $result = $getdata->max_id;
        } else {
            $result = '';
        }
        
        return $result;
    }

    public function get_result_table($table = '', $where = array(), $select = '') {
        $db = $this->db;
        $builder = $db->table($table);
        if ($select == '')
            $select = '*';
        $query = $builder->select($select)->where($where)->get();

        $getdata = $query->getResultArray();

        if (!empty($getdata)) {
            $result = $getdata;
        } else {
            $result = array();
        }
        return $result;
    }

    public function remove_data_table($table = '', $where = array()) {
        $db = $this->db;
        $builder = $db->table($table);
        $result = $builder->where($where)->delete();

        $return = array();
        if ($result) {
            $return = array('st' => 'success', 'txt' => 'success');
        } else {
            $return = array('st' => 'fail', 'txt' => 'update fail');
        }
        return $return;
    }



}

?>