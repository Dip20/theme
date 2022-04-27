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


    public function search_uom_data($post) 
    {
        $db = $this->db;
        $db->setDatabase("jen2022f1uo"); 
        $builder = $db->table('uom');
        $builder->select('id,code,name');
        $builder->where(array('is_delete' => '0'));
        if(isset($post['searchTerm']) && $post['searchTerm'] != ''){
            $builder->like('code',(@$post['searchTerm']) ? @$post['searchTerm'] : 'A');
        }
        $query = $builder->get();
        $getdata = $query->getResultArray();
       
        $result = array();
        
        foreach($getdata as $row){
            $result[] = array("text" => $row['code'] .'('.$row['name'].')' ,"id" => $row['id']);
        }
        
        return $result;
    }


    public function insert_edit_item($post)
    {
        $db = $this->db;
        $db->setDatabase("jen2022f1uo");
        $builder = $db->table('item');
        $uom = implode(',',$post['uom']);
        $gmodel = new GeneralModel();
        $msg = array();
        /* Prepare data */
        $pdata = array(
            'code' => $post['code'],
            'type' => $post['item_type'],
            'item_mode' =>$post['item_mode'],
            'item_grp' => @$post['item_grp'],
            'name' => ucwords($post['name']),
            'sku' => $post['sku'],
            'status' => 1,
            'default_cut' => $post['default_cut'],
            'uom' => $uom,
            'purchase_cost' => @$post['purchase_cost'] ? $post['purchase_cost']:'',
            'purchase_min_qty' => @$post['purchase_min_qty'] ? $post['purchase_min_qty']:'',
            'purchase_max_qty' => @$post['purchase_min_qty'] ? $post['purchase_min_qty']:'',
            'sales_price' => @$post['sales_price'] ? $post['sales_price']:'',
            'brokrage' => @$post['brokrage'] ? $post['brokrage']:'',
            'sale_min_qty' => @$post['sale_min_qty'] ? $post['sale_min_qty']:'',
            'sale_max_qty' => @$post['sale_max_qty'] ? $post['sale_max_qty']:'',
            'opening_stock' => @$post['opening_stock'] ? $post['opening_stock']:'',
            'opening_rate' => @$post['opening_rate'] ?$post['opening_rate']:'',
            'opening_total' => @$post['opening_total'] ? $post['opening_total']:'',
            'opening_uom' => @$post['opening_uom'] ? $post['opening_uom']:'',
            'hsn' => @$post['hsn'] ? $post['hsn']:'',
            'taxability' => @$post['taxability'] ? $post['taxability']:'',
            'rev_charge' => @$post['rev_charge'] ? $post['rev_charge']:'',
            'ineligible' => @$post['ineligible'] ? $post['ineligible']:'',
            'non_gst' => @$post['non_gst'] ? $post['non_gst']:'',
            'igst' => @$post['igst'] ? $post['igst']:'',
            'cgst' => @$post['cgst'] ? $post['cgst']:'',
            'sgst' => @$post['sgst'] ? $post['sgst']:'',
            );
        
            $result = $builder->Insert($pdata);
                    
            $id = $db->insertID();
            if ($result) {
                $msg = array('st' => 'success', 'msg' => "Your Details Added Successfully!!!");
            } else {
                $msg = array('st' => 'fail', 'msg' => "Your Details Updated fail");
            }

        return $msg;
    }


    public function get_uom_data($get)
    {
        $dt_search = $dt_col = array(
            "id",        
            "code",
            "name",
            "decimal_digit",
            "status",
            "is_static"
        );
    
        $filter = $get['filter_data'];
        $tablename = "uom";
        $where = '';
        
        $where .= " and is_delete=0";
    
        $rResult = getManagedData($tablename, $dt_col, $dt_search, $where);
        $sEcho = $rResult['draw'];
    
        $encode = array(); 
        $statusarray = array("1" => "Activate", "0" => "Deactivate");
        foreach ($rResult['table'] as $row) {
            $DataRow = array();
            
            $btnedit = '<a  data-toggle="modal" href="' . url('Home/Createuom/') . $row['id'] . '"   data-title="Edit OUM : ' . $row['name'] . '" class="btn btn-link pd-10" data-target="#fm_model" ><i class="far fa-edit"></i></a> ';
            $btndelete = '<a data-toggle="modal" target="_blank"   title="UOM : ' . $row['name'] . '"  onclick="editable_remove(this)"  data-val="' . $row['id'] . '"  data-pk="' . $row['id'] . '" tabindex="-1" class="btn btn-link pd-10"><i class="far fa-trash-alt"></i></a> ';
            $status= '<a  onclick="editable_os(this)"  data-val="'.$row['id'].'"  data-pk="'.$row['id'].'" tabindex="-1">'.$statusarray[$row['status']].'</a>';
    
            $btn = $btnedit;
            if($row['is_static'] != 0){
                $btn = $btnedit . $btndelete;
            }else{
                $btn = $btnedit;
            }
    
            $DataRow[] = $row['id'];
            $DataRow[] = $row['code'];
            $DataRow[] = $row['name'];
            $DataRow[] = $row['decimal_digit'];
            $DataRow[] = $status;
            $DataRow[] = $btn;
    
            $encode[] = $DataRow;
        }
    
        $json = json_encode($encode);
        echo '{ "draw": ' . intval($sEcho) . ',"recordsTotal": ' . $rResult['total'] . ',"recordsFiltered": ' . $rResult['total'] . ',"data":' . $json . '}';
        // exit;
    }  


    public function insert_edit_uom($post)
    {
        $db = $this->db;
        $db->setDatabase("jen2022f1uo");
        $builder = $db->table('uom');
        /* Prepare data */
        $pdata = array(
            'code' => $post['code'],
            'name' => $post['name'],
            'decimal_digit' => $post['decimal'],
            'status' => 1,
            'is_static' => 1,
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


    public function search_parent_glgrp_data($post) 
    {
        
        $db = $this->db;
        $db->setDatabase("jen2022f1uo");
        $builder = $db->table('gl_group');
        $builder->select('id,name,parent');
        $builder->where(array('is_delete' => '0'));
        if(isset($post['searchTerm'])){
            $builder->like('name',(@$post['searchTerm']) ? @$post['searchTerm'] : 'A');
        }
        $query = $builder->get();
        $getdata = $query->getResultArray();
        
        $gmodel = new GeneralModel();
        $income_id =$gmodel->get_data_table('gl_group',array('name'=> 'Incomes'),'id');
        $expence_id =$gmodel->get_data_table('gl_group',array('name'=> 'Expenses'),'id');
        $tradingincome_id =$gmodel->get_data_table('gl_group',array('name'=> 'Trading Expenses'),'id');
        $tradingexpence_id =$gmodel->get_data_table('gl_group',array('name'=> 'Trading Expenses'),'id');
        $result = array();
        
        foreach($getdata as $row){
            $parent = 0;
            $main_id = '';
            if($row['id'] == 16 || $row['id'] == 27 || $row['id'] == 29 || $row['id'] == 30 || $row['id'] == 31){
                $main_id = $row['id'];
            }else{
                if($row['parent'] != 0){
                    $x = 5;
                    $parent = $row['parent'];
                    for($i = 0;$i<$x;$i++){
                        $res = $gmodel->get_data_table('gl_group',array('id'=> $parent),'id,parent');
                        if($res['id'] == 16 || $res['id'] == 27 || $res['id'] == 29 || $res['id'] == 30 || $res['id'] == 31){
                            $x = 0;
                        }else{
                            $x = $res['parent'];
                        }
                        $parent = $res['parent'];
                    }
                    $main_id = $res['id'];
                    $i = 0;
                }
            }

            $tx_bn_hide = '';
            if($row['id'] == 21 || $row['id'] == 24 || $row['id'] == 28 || $row['id'] == 17){
                $tx_bn_hide = $row['id'];
            }else{
                if($row['parent'] != 0){
                    $x = 5;
                    $parent = $row['parent'];
                    for($i = 0;$i<$x;$i++){
                        $res = $gmodel->get_data_table('gl_group',array('id'=> $parent),'id,parent');
                        if($res['id'] == 21 || $res['id'] == 24 || $res['id'] == 28 || $res['id'] == 17){
                            $x = 0;
                        }else{
                            $x = $res['parent'];
                        }
                        $parent = $res['parent'];
                        $i = 0;
                    }
                    $tx_bn_hide = $res['id'];
                }
            }

            $new_hide = '';
            if($row['id'] == 21 || $row['id'] == 30 || $row['id'] == 29 || $row['id'] == 31){
                $new_hide = $row['id'];
            }else{
                if($row['parent'] != 0){
                    $x = 5;
                    $parent = $row['parent'];
                    for($i = 0;$i<$x;$i++){
                        $res = $gmodel->get_data_table('gl_group',array('id'=> $parent),'id,parent');
                        if($res['id'] == 21 || $res['id'] == 30 || $res['id'] == 29 || $res['id'] == 31){
                            $x = 0;
                        }else{
                            $x = $res['parent'];
                        }
                        $parent = $res['parent'];
                        $i = 0;
                    }
                    $new_hide = $res['id'];
                }
            }
           

            $bank = '';
            if($row['id'] == 22 ){
                $bank = $row['id'];
            }else{
                if($row['parent'] != 0){
                    $x = 5;
                    $parent = $row['parent'];
                    for($i = 0;$i<$x;$i++){
                        $res = $gmodel->get_data_table('gl_group',array('id'=> $parent),'id,parent');
                        if($res['id'] == 22){
                            $x = 0;
                        }else{
                            $x = $res['parent'];
                        }
                        $parent = $res['parent'];
                        $i = 0;
                    }
                    $bank = $res['id'];
                }
            }

            $cash = '';
            if($row['id'] == 21 ){
                $cash = $row['id'];
            }else{
                if($row['parent'] != 0){
                    $x = 5;
                    $parent = $row['parent'];
                    for($i = 0;$i<$x;$i++){
                        $res = $gmodel->get_data_table('gl_group',array('id'=> $parent),'id,parent');
                        if($res['id'] == 21){
                            $x = 0;
                        }else{
                            $x = $res['parent'];
                        }
                        $parent = $res['parent'];
                        $i = 0;
                    }
                    $cash = $res['id'];
                }
            }

            $opening_balCr = '';

            if($row['id'] == 4 || $row['id'] == 2){
                $opening_balCr = $row['id'];
            }else{
                if($row['parent'] != 0){
                    $x = 5;
                    $parent = $row['parent'];
                    for($i = 0;$i<$x;$i++){
                        $res = $gmodel->get_data_table('gl_group',array('id'=> $parent),'id,parent');
                        if($res['id'] == 4 || $res['id'] == 2){
                            $x = 0;
                        }else{
                            $x = $res['parent'];
                        }
                        $parent = $res['parent'];
                        $i = 0;
                    }
                    $opening_balCr = $res['id'];
                }
            }

            $opening_balDr = '';

            if($row['id'] == 1 || $row['id'] == 3){
                $opening_balDr = $row['id'];
            }else{
                if($row['parent'] != 0){
                    $x = 5;
                    $parent = $row['parent'];
                    for($i = 0;$i<$x;$i++){
                        $res = $gmodel->get_data_table('gl_group',array('id'=> $parent),'id,parent');
                        if($res['id'] == 1 || $res['id'] == 3){
                            $x = 0;
                        }else{
                            $x = $res['parent'];
                        }
                        $parent = $res['parent'];
                        $i = 0;
                    }
                    $opening_balDr = $res['id'];
                }
            }

            $creditor_debtor = '';

            if($row['id'] == 13 || $row['id'] == 19){
                $creditor_debtor = $row['id'];
            }else{
                if($row['parent'] != 0){
                    $x = 5;
                    $parent = $row['parent'];
                    for($i = 0;$i<$x;$i++){
                        $res = $gmodel->get_data_table('gl_group',array('id'=> $parent),'id,parent');
                        if($res['id'] == 13 || $res['id'] == 19){
                            $x = 0;
                        }else{
                            $x = $res['parent'];
                        }
                        $parent = $res['parent'];
                        $i = 0;
                    }
                    $creditor_debtor = $res['id'];
                }
            }

            $result[] = array("text" => $row['name'],"opening_balDr" => $opening_balDr, "opening_balCr" => $opening_balCr,"id" => $row['id'],"parent_id" =>$row['parent'],"income_id" =>$income_id['id'], "expense_id" => $expence_id['id'], "main_id"=>$main_id, "tx_bn_hide"=>$tx_bn_hide,'bank_id'=>$bank,'cash_id'=>$cash,'new_hide' => $new_hide,'creditor_debtor'=> $creditor_debtor);
        }
        //print_r($result);exit;
        return $result;
    }


    public function getCountry($post)
    {
            
        $db = $this->db;
        $db->setDatabase("jen2022f1uo");
        
        $builder=$db->table('countries');
        $builder->select('*');
        if(isset($post['searchTerm'])){
            $builder->like('name',(@$post['searchTerm']) ? @$post['searchTerm'] : 'A');
        }
        $result = $builder->get();
        $result_array = $result->getResultArray();

        $result = array();
        foreach($result_array as $row){
            $result[] = array("text" => $row['name'],"id" => $row['id']);
        }
        return $result;
    }


    public function getStates($post) 
    {
        $db = $this->db;
        $db->setDatabase("jen2022f1uo");
        $builder=$db->table('states');
        $builder->select('*');
        if(isset($post['searchTerm'])){
            $builder->like('name',(@$post['searchTerm']) ? @$post['searchTerm'] : 'A');
        }
        $builder->where('country_id', $post['country']);
        $result = $builder->get();
        $result_array = $result->getResultArray();
        $result = array();
        foreach($result_array as $row){
            $result[] = array("text" => $row['name'],"id" => $row['id']);
        }
        return $result;
    }


    public function getCities($post) 
    {
        $db = $this->db;
        $db->setDatabase("jen2022f1uo");
        
        $builder=$db->table('cities');
        $builder->select('*');
        if(isset($post['searchTerm'])){
            $builder->like('name', (@$post['searchTerm']) ? @$post['searchTerm'] : 'A');
        }
        if(isset($post['state'])){
            $builder->where('state_id', $post['state']);
        }
        $builder->limit(10);
        $result = $builder->get();
        $result_array = $result->getResultArray();
        $result = array();
        foreach($result_array as $row){
            $result[] = array("text" => $row['name'],"id" => $row['id']);
        }
        return $result;
    }
    

    public function insert_edit_account($post)
    {
        $db = $this->db;
        $db->setDatabase("jen2022f1uo");
        $builder = $db->table('account');
        $builder->select('*');
        $builder->where(array("id" => $post['id']));
        $builder->limit(1);
        $result = $builder->get();
        $result_array = $result->getRow();
        
        $msg = array();
        if(!empty($post['name'])){
            $name = $post['name'];
        }else{
            $name = $post['taxes_name'];
        }
        $gmodel = new GeneralModel();

        $res = $gmodel->get_data_table('account',array('name'=>$name),'*');
        
        if(empty($result_array)){
            if(!empty($res)){
                $msg = array('st' => 'fail', 'msg' => "Ledger With This Name Was Alredy Exist..!!");
                return $msg;
            }
        }
        
        $pdata = array(
            
            'code' => $post['code'],
            'name' => ucwords($name), 
            'owner' => $post['own_name'], 
            'gl_group' => $post['glgrp'],
            'party_group' => @$post['party'],
            'print_name' => !empty($post['Pname'])?$post['Pname']:'',
            'email' =>  !empty($post['email'])?$post['email']:'', 
            'opening_bal' => @$post['opening_bal'], 
            'opening_type' => @$post['opening_type'], 
            'brokrage' => !empty($post['brokrage'])?$post['brokrage']:'', 
            'address' => !empty($post['add'])?$post['add']:'',
            'gst_add' => @$post['gst_add'],
            'pin' => !empty($post['pin'])?$post['pin']:'',
            'city' => @$post['city'],
            'state' => @$post['state'], 
            'country' => @$post['country'],
            'mobile' => @$post['mob'] ? $post['mob'] : '',
            'whatspp' => @$post['whatspp'],
            'area' => @$post['area'] ? $post['area'] : '',
            'refrred' => @$post['refrred_id'],
            'transport' => @$post['transport_id'],
            'tax_pan' => strtoupper(@$post['taxpan']),
            'gst' => @$post['gst'] ? strtoupper($post['gst']) : '',
            'ineligible' => @$post['ineligible'] ? $post['ineligible'] : '',
            'rev_charge' => @$post['rev_charge'] ? $post['rev_charge'] : '',
            'igst' => @$post['igst'] ? $post['igst'] : '',
            'cgst' => @$post['cgst'] ? $post['cgst'] : '',
            'sgst' => @$post['sgst'] ? $post['sgst'] : '',
            'cess' => @$post['cess'] ? $post['cess'] : '',
            'intrest_rate' => @$post['intrate'],
            'bank' => @$post['bank_id'] ? $post['bank_id'] : '',
            'default_due_days' => @$post['due'] ? $post['due'] : '',
            'bank_branch' => @$post['bankbranch'] ? $post['bankbranch'] : '', 
            'bank_ac_no' => @$post['bankac'] ?$post['bankac']:'',
            'bank_holder' => @$post['bank_holder'] ? $post['bank_holder'] : '',
            'ac_type' => @$post['ac_type'] ? $post['ac_type'] : '' ,
            'bank_ifsc' => @$post['bankifsc'] ? $post['bankifsc'] : '',
            'taxability' => @$post['taxability'] ? $post['taxability'] : '',
            'hsn' => @$post['hsn'] ? $post['hsn'] : '',
            'alt_gst' => @$post['alt_gst'] ? $post['alt_gst']:'',
            'gst_type' => @$post['gst_type'] ? $post['gst_type'] : '',
        );
        if(isset($post['check_tds'])){
            $pdata += array(
                'tds_limit' => $post['tds_limit'],
                'tds_check' => 1,
                'tds_rate' => $post['tds_rate'],
                'tds_cess' => $post['tds_cess'],
                'tds_hcess' => $post['tds_hcess'], 
                'tds' => $post['tds'],
                'tds_surcharge' => $post['tds_surch'],
            );
        }else{
            $pdata['tds_check'] = 0;
        }
        // echo '<pre>';print_r($pdata);exit;
        if (!empty($result_array)) {
            
            $pdata['update_at'] = date('Y-m-d H:i:s');
            $pdata['update_by'] = session('uid');
            if (empty($msg)) {
                $builder->where(array("id" => $post['id']));
                $result = $builder->Update($pdata);
                
                $builder = $db->table('account');
    
                if ($result) {
                    $msg = array('st' => 'success', 'msg' => "Your Details updated Successfully!!!");
                    //return view('master/account_view');
                } else {
                    $msg = array('st' => 'fail', 'msg' => "Your Details Updated fail");
                }
            }
        }
        else {
            
            $pdata['created_at'] = date('Y-m-d H:i:s');
            // $pdata['created_by'] = session('uid');
            
            if (empty($msg)) {
                $result = $builder->Insert($pdata);
                // print_r($result);
                $id = $db->insertID();
                if ($result) {
                    $msg = array('st' => 'success', 'msg' => "Your Details Added Successfully!!!");
                    // return view('master/account_view');
                } else {
                    $msg = array('st' => 'fail', 'msg' => "Your Details Updated fail");
                }
            }
        }
        return $msg;
    }


    public function search_item_data($term) {
        $db = $this->db;
        $db->setDatabase("jen2022f1uo");
        $builder = $db->table('item');
        $builder->select('*');
        if($term != ''){
            $where = "(`code` LIKE '%".$term."%' OR  `name` LIKE '%".$term."%') AND `is_delete` = '0'";
        }else{
            $where = "`is_delete` = '0'";
        }
        $builder->where($where);           
        $builder->limit(10);
        $query = $builder->get();
        $getdata = $query->getResultArray();
        
        foreach($getdata as $row)
        {     

            $item_uom = explode(',',$row['uom']);
            $option = '';
            $gmodel = new GeneralModel();
            
            foreach($item_uom as $uom){
                $uom_name  = $gmodel->get_data_table('uom',array('id'=>$uom),'code');
                $option .= '<option value="'.$uom_name['code'].'">'.$uom_name['code'].'</option>';
            }

            $price_data = array(
                "id" => $row['id'],
                'sales_price' => $row['sales_price'],
                'purchase_price' => $row['purchase_cost'],
                'igst' => $row['igst'],
                'cgst' => $row['cgst'],
                'sgst' => $row['sgst'],
                'brokrage' => $row['brokrage'],
            );

            $result[] = array(
                "text" => $row['name'] .(!empty($row['hsn']) ? ' ('. $row['hsn'] .')' : ''),
                "id" => $row['id'],
                "price" => $price_data,
                "uom" => $option,
            );
        }
        
        return $result;
    }



    public function get_sales_invoice($id) {        
        
        $db = $this->db;
        $db->setDatabase("jen2022f1uo");
        $builder = $db->table('sales_invoice si'); 
        $builder->select('si.*,ac.name as account_name');
        $builder->join('account ac','ac.id = si.account');
        $builder->where(array('si.id'=>$id));
        $query = $builder->get();
        $invoice = $query->getResultArray();

        $getdata['salesinvoice'] = $invoice[0];
        $gmodel=new GeneralModel();
        foreach($invoice as $row){
            
            $getbroker = $gmodel->get_data_table('account',array('id'=>$row['broker']),'name');
            $getchallan = $gmodel->get_data_table('sales_challan',array('id'=>$row['challan_no']),'*');
            if(!empty($getchallan)){
                $getchallan_ac = $gmodel->get_data_table('account',array('id'=>@$getchallan['account']),'name');
                $challan_no = $getchallan['challan_no'].'('.$getchallan_ac['name'].')/ '.user_date($getchallan['challan_date']);
            }else{
                $getchallan_ac = '';
                $challan_no = '';
            }

            $getdelivery = $gmodel->get_data_table('account',array('id'=>$row['delivery_code']),'name');
            
            $gettransport = $gmodel->get_data_table('transport',array('id'=>$row['transport']),'name');
            $getvehicle = $gmodel->get_data_table('vehicle',array('id'=>$row['vhicle_no']),'name');
            $getvehicle = $gmodel->get_data_table('vehicle',array('id'=>$row['vhicle_no']),'name');
            $getvoucher = $gmodel->get_data_table('account',array('id'=>$row['voucher_type']),'name');
            $getround = $gmodel->get_data_table('account',array('id'=>$row['round']),'name');
            
            $getdata['salesinvoice']['voucher_name']=@$getvoucher['name'];
            $getdata['salesinvoice']['broker_name']=@$getbroker['name'];
            $getdata['salesinvoice']['delivery_name']=@$getdelivery['name'];
            $getdata['salesinvoice']['transport_name']=@$gettransport['name'];
            $getdata['salesinvoice']['vehicle_name']=@$getvehicle['name'];
            $getdata['salesinvoice']['broker_ledger_name']=@$getbroker_ledger['name'];
            $getdata['salesinvoice']['round_name']=@$getround['name'];
            $getdata['salesinvoice']['challan_name']=@$challan_no;

        }

        $item_builder =$db->table('sales_item st');
        $item_builder->select('st.*,i.id,i.type,i.item_mode,i.name,i.sku,i.purchase_cost,i.hsn,i.code,i.uom as item_uom ,st.uom as uom');
        $item_builder->join('item i','i.id = st.item_id' );
        $item_builder->where(array('st.parent_id' => $id,'st.type' => 'invoice','st.is_delete' => 0 ));
        $query= $item_builder->get();
        $getdata1['item'] = $query->getResultArray();
        foreach($getdata1['item'] as $row){
            $uom =  explode(',',$row['item_uom']);
            foreach($uom as $row1){
                $getuom = $gmodel->get_data_table('uom',array('id'=>$row1),'code');
                $uom_arr[] =$getuom['code']; 
            }
            
            $coma_uom = implode(',',$uom_arr);
            $row['item_uom'] =$coma_uom; 
            $getdata['item'][] = $row;
            $uom_arr = array();
        }
        // echo '<pre>';print_r($getdata);exit;
        
        return $getdata;
    }



    public function insert_edit_salesinvoice($post)
     { 
       
        if(!@$post['pid']){
            $msg = array('st' => 'fail', 'msg' => "Please Select any Item");
            return $msg;
        }
        $db = $this->db;
        $db->setDatabase("jen2022f1uo");
        $builder = $db->table('sales_invoice');
        $builder->select('*');
        $builder->where(array("id" => $post['id']));
        $builder->limit(1);
        $result = $builder->get();
        $result_array = $result->getRow();
        
        $msg = array();
       
        $pid=$post['pid'];
        $qty=$post['qty'];
        $price=$post['price'];
        $igst=$post['igst'];
        $cgst=$post['cgst'];
        $sgst=$post['sgst'];
        $item_brokrage = $post['item_brokrage'];
        $item_disc=$post['item_disc'];        
        $discount=$post['discount'];
        $amty=$post['amty'];
        $cess=$post['cess'];
        $total = 0.0;

        for($i=0;$i<count($pid);$i++)
        {
            $disc_amt=0;
            if($item_disc[$i] != 0 ){
                $sub = $post['qty'][$i] * $post['price'][$i];
                // $igst_amt = $sub * $igst[$i] / 100;
                $disc_amt = $sub * $item_disc[$i] / 100;
            }
            $total +=$post['qty'][$i] * $post['price'][$i] - $disc_amt;  
        }

        if($post['disc_type'] == '%'){
            if($post['discount'] == ''){
                $post['discount'] = 0;
            } 
            else{
                $post['discount'] = $total * $post['discount']/100;
                if($post['discount'] > 0){
                    $total = 0;
                    for($i=0;$i<count($pid);$i++){
                        $disc_amt=0;
                        $devide_disc = $post['discount'] /count($pid);

                        if($item_disc[$i] != 0 ){
                            $sub = $post['qty'][$i] * $post['price'][$i];
                            $disc_amt = $sub * $item_disc[$i] / 100;
                        }
                        $total +=$post['qty'][$i] * $post['price'][$i] - $disc_amt - $devide_disc;
                    }
                }
            }
        } else {
            if($post['discount'] == ''){
                $post['discount'] = 0; 
            }
            if($post['discount'] > 0){
                $total = 0;
                for($i=0;$i<count($pid);$i++){
                    $disc_amt=0;
                    $devide_disc = $post['discount'] /count($pid);
                    // echo 'devide_disc'. $devide_disc;exit;
                    if($item_disc[$i] != 0 ){
                        $sub = $post['qty'][$i] * $post['price'][$i];
                        $disc_amt = $sub * $item_disc[$i] / 100;
                    }
                        
                    $total +=$post['qty'][$i] * $post['price'][$i] - $disc_amt - $devide_disc;
                }
            }
        }
       

        if($post['amty_type'] == '%'){
            if($post['amty'] == '')
                $post['amty'] = 0;
            else
                $post['amty'] = $total *  $post['amty']/100;
        } else {
            if($post['amty'] == '')
                $post['amty'] = 0;
        }

        if($post['cess_type'] == '%'){
            if($post['cess'] == '')
                $post['cess'] = 0;
            else
                $post['cess'] = $total *  $post['cess']/100;
        } else {
            if($post['cess'] == '')
                $post['cess'] = 0;
        }

        if(!empty($post['tds_per'])){
            $tds_amt =$total *  $post['tds_per']/100;
        } else {
            $tds_amt = 0;
        }

        $netamount=$total + $post['cess'] + $post['amty'] +   $tds_amt + $post['tot_igst'];
        
        $pdata = array(
            'voucher_type'=> $post['voucher_type'],
            'invoice_no'=> $post['invoice_no'],
            'invoice_date'=> db_date($post['invoice_date']),
            'challan_no'=> @$post['challan'] ? $post['challan'] : '' ,
            'account' => $post['account'], 
            'tds_limit' => $post['tds_limit'],
            'acc_state' => $post['acc_state'],
            'gst' => $post['gst'],
            'broker'=> @$post['broker'],
            'other' => $post['other'],
            'lr_no' => $post['lrno'],
            'lr_date' => $post['lr_date'],
            'delivery_code'=>@$post['delivery_code'] ,
            'transport' => @$post['transport'], 
            'transport_mode'=> @$post['trasport_mode'], 
            'vhicle_no'=> @$post['vehicle'],
            'total_amount' => $total,
            'taxes' => json_encode(@$post['taxes']),
            'tot_igst' => $post['tot_igst'],
            'tot_cgst' => $post['tot_cgst'],
            'tot_sgst' => $post['tot_sgst'],
            'discount' => $discount,
            'disc_type' => $post['disc_type'],
            'amty' => $amty,
            'amty_type' => $post['amty_type'],
            'cess_type' => $post['cess_type'],        
            'cess' => $cess,        
            'tds_amt' => $post['tds_amt'],        
            'tds_per' => $post['tds_per'],
            'brokrage_type' =>@$post['brokerage_type'],
            'net_amount' => round($netamount),
            'due_days' => $post['due_day'],
            'due_date'=> $post['due_date'],
            'stat_adj' => isset($post['stat_adj']) ? $post['stat_adj'] : 0,
            'round' => @$post['round'],
            'round_diff' => @$post['round_diff'],
            'taxable' => @$post['taxable'],
        );
        
        if(isset($post['stat_adj']) && $post['stat_adj'] == 1){
            $pdata['ref_type'] = $post['ref_type'];
            $pdata['voucher_amt'] = $post['voucher_amt'];
            if($post['ref_type'] == 'Advance'){
                $pdata['voucher'] = $post['voucher'];
            }
        }

        if (!empty($result_array)) {
    
            $pdata['update_at'] = date('Y-m-d H:i:s');
            $pdata['update_by'] = session('cid');
            if (empty($msg)) {

                $builder->where(array("id" => $post['id']));
                $result = $builder->Update($pdata);
                
                $item_builder=$db->table('sales_item');
                $item_result = $item_builder->select('GROUP_CONCAT(item_id) as item_id')->where(array("parent_id" => $post['id'],"type" => 'invoice'))->get();
                $getItem = $item_result->getRow();

                $getpid = explode(',', $getItem->item_id);
                $delete_itemid = array_diff($getpid,$pid);
                //$itemdata=0;

                if(!empty($delete_itemid)){
                    foreach($delete_itemid as $key => $del_id){
                        $del_data = array('is_delete' => '1');
                        $item_builder->where(array('item_id' => $del_id , 'parent_id' => $post['id'] , 'type' => 'invoice'));
                        $item_builder->update($del_data);
                    }       
                }
                for($i=0;$i<count($pid);$i++)
                {   
                    $item_result = $item_builder->select('*')->where(array("item_id" => $pid[$i],"parent_id" => $post['id']))->get();
                    $getItem = $item_result->getRow();
                    
                    if(!empty($getItem)){
                        $qty = $post['qty'][$i] - $getItem->qty;
                        $item_data=array(
                            'uom'=> $post['uom'][$i],
                            'rate'=> $post['price'][$i],
                            'qty'=> $post['qty'][$i],
                            'brokrage'=> $post['item_brokrage'][$i],
                            'igst'=> $post['igst'][$i],
                            'cgst'=> $post['cgst'][$i],
                            'sgst'=> $post['sgst'][$i],
                            'item_disc' => $post['item_disc'][$i],
                            'remark'=> $post['remark'][$i],
                            'is_delete' => 0,
                            'update_at'=> date('Y-m-d H:i:s'),
                            'update_by'=> session('uid'),
                        );
                        $item_builder->where(array('item_id'=>$pid[$i],'parent_id'=>$post['id']));
                        $res = $item_builder->update($item_data);
                    }else{
                       // $id = $db->insertID();
                      // print_r($post['pid'][$i]);exit;
                        $item_data = array(
                            'parent_id'=>$post['id'],
                            'item_id'=> $post['pid'][$i],
                            'type'=> 'invoice',
                            'uom'=> $post['uom'][$i],
                            'rate'=> $post['price'][$i],
                            'qty'=> $post['qty'][$i],
                            'brokrage'=> $post['item_brokrage'][$i],
                            'igst'=> $post['igst'][$i],
                            'cgst'=> $post['cgst'][$i],
                            'sgst'=> $post['sgst'][$i],
                            'item_disc' => $post['item_disc'][$i],
                            'remark'=> $post['remark'][$i],
                            'created_at'=> date('Y-m-d H:i:s'),
                            'created_by'=> session('uid'),
                        );
                        $res = $item_builder->insert($item_data);
                    }
                    
                    $item_builder->where(array('parent_id' => $post['id'] , 'item_id'=> $post['pid'][$i], "type" => 'invoice'));
                    $result1=$item_builder->update($item_data);
                    
                }
                $builder = $db->table('sales_invoice');
    
                if ($result) {
                    $msg = array('st' => 'success', 'msg' => "Your Details updated Successfully!!!");
                    //return view('master/account_view');
                } else {
                    $msg = array('st' => 'fail', 'msg' => "Your Details Updated fail");
                }
            }
        }
        else {
            
            $pdata['created_at'] = date('Y-m-d H:i:s');
            // $pdata['created_by'] = session('uid');
            
            if (empty($msg)) {
                $result = $builder->Insert($pdata);
                // print_r($result);
                $id = $db->insertID();
                for($i=0;$i<count($pid);$i++)
                {
                    $item_data[]=array(
                        'parent_id'=> $id,
                        'item_id'=> $post['pid'][$i],
                        'type'=> 'invoice',
                        'uom'=> $post['uom'][$i],
                        'rate'=> $post['price'][$i],
                        'qty'=> $post['qty'][$i],
                        'brokrage'=> $post['item_brokrage'][$i],
                        'igst'=> $post['igst'][$i],
                        'cgst'=> $post['cgst'][$i],
                        'sgst'=> $post['sgst'][$i],
                        'item_disc' => $post['item_disc'][$i],
                        'remark'=> $post['remark'][$i],
                        'created_at'=> date('Y-m-d H:i:s'),
                        // 'created_by'=> session('uid'),
                    );
                }
                $item_builder=$db->table('sales_item');
                $result1=$item_builder->insertBatch($item_data);
               
                if ($result &&  $result1) {
                    $msg = array('st' => 'success', 'msg' => "Your Details Added Successfully!!!");
                    // return view('master/account_view');
                } else {
                    $msg = array('st' => 'fail', 'msg' => "Your Details Updated fail");
                }
            }
        }
        
        return $msg;
    }


    public function search_sun_debtor($post)
    {
        $gmodel = new GeneralModel();
        $sun_deb = $gmodel->get_data_table('gl_group',array('name'=>'Sundry Debtors'),'id');

 
        $sundry_debtor = gl_list([$sun_deb['id']]);
        $sundry_debtor[] = $sun_deb['id'];
        

        $db = $this->db;
        $db->setDatabase('jen2022f1uo');

        $builder = $db->table('account acc');
        $builder->select('acc.name,acc.id,acc.gst,acc.tds_rate,acc.tds_limit,acc.state,acc.default_due_days');
        $builder->join('gl_group gl','gl.id = acc.gl_group');
        $builder->where(array('acc.is_delete' => '0' ));
        $builder->whereIn('gl.id',$sundry_debtor);
        if(isset($post['searchTerm'])){
            $builder->like('acc.name',(@$post['searchTerm']) ? @$post['searchTerm'] : 'A');
        }
        $query = $builder->get();
        $getdata = $query->getResultArray();

        // print_r($getdata);exit;
        

        $result = array();
        foreach($getdata as $row){
            $result[] = array("text" => $row['name'],"id" => $row['id'],"gsttin"=>$row['gst'],"tds"=>$row['tds_rate'],"tds_limit"=>$row['tds_limit'],"state"=>$row['state'],"due_day"=>$row['default_due_days']);
        }
        return $result;
    }

}
