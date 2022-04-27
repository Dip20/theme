<?php

namespace App\Models;
use CodeIgniter\Model;

class SalesModel extends Model
{

    public function get_ac_invoice($get){
        
        $dt_search= array(
            "si.id",
            "si.party_account",
            "si.v_type",
            "si.supp_inv",
            "si.status",
            "si.net_amount",
        );
        $dt_col = array(
            "si.invoice_no",
            "si.id",
            "si.v_type",
            "si.party_account",
            "(select name from account ac where si.party_account = ac.id) as party_account_name",
            "si.supp_inv",
            "si.net_amount",
            "si.status",
            "si.is_cancle",
            "si.is_delete",
        );
    
        $filter = $get['filter_data'];
        $tablename = "sales_ACinvoice si";
        $where = '';
        if ($filter != '' && $filter != 'undefined') {
            $where .= ' and v_type ="' . $filter . '"';
        }
        $where .= " and is_delete=0";
    
        $rResult = getManagedData($tablename, $dt_col, $dt_search, $where);
        $sEcho = $rResult['draw'];
    
        $encode = array();
        $statusarray = array("1" => "Activate", "0" => "Deactivate");
        $gmodel = new GeneralModel();
        foreach ($rResult['table'] as $row) {

            $DataRow = array();
            $statusarray = array("1" => "Cancled", "0" => "Cancle");

            $btn_cancle = '<a target="_blank" title=" ' . $row['party_account_name'] . '" onclick="editable_os(this)"  data-val="' . $row['is_cancle'] . '"  data-pk="' . $row['id'] . '" tabindex="-1" class="" title="'.$statusarray[$row['is_cancle']].'"><i class="far fa-times-circle"></a>';

            $btnedit = '<a href="' . url('sales/add_ACinvoice/') .$row['v_type'] .'/'.$row['id'] . '"  class="btn btn-link pd-6"><i class="far fa-edit"></i></a> ';
            $btnview = '<a href="' . url('sales/general_detail/') . $row['id'] . '"    class="btn btn-link pd-6"><i class="far fa-eye"></i></a> ';
            $btndelete = '<a data-toggle="modal" target="_blank"   title="Ac Invoice Id: ' . $row['id'] . '"  onclick="editable_remove(this)"  data-val="' . $row['id'] . '"  data-pk="' . $row['id'] . '" tabindex="-1" class="btn btn-link pd-6"><i class="far fa-trash-alt"></i></a> ';
            $status= '<a target="_blank"   title="Item Invoice Id: ' . $row['id'] . '" onclick="editable_os(this)"  data-val="' . $row['status'] . '"  data-pk="' . $row['id'] . '" tabindex="-1"  >' . $statusarray[$row['status']] . '</a>';
           
            $getMax = $gmodel->get_data_table('sales_ACinvoice',array('is_delete'=>0 , 'v_type'=> $filter),'MAX(invoice_no) as max_no');

            if($row['is_cancle'] ==1 || $row['is_delete'] == 1){
                $btn =  $btnview;
            }else{
                $btn =  $btnedit . $btnview;
            }
            
            if($getMax['max_no'] == $row['invoice_no']){
                if($row['is_cancle'] != 1){
                    $btn .= $btndelete;
                }
            }else{
                if($row['is_cancle'] == 0){
                    $btn .= $btn_cancle;
                }
            }

            $DataRow[] = $row['invoice_no'];
            $DataRow[] = $row['v_type'];
            $DataRow[] = $row['party_account_name'];
            $DataRow[] = $row['supp_inv'];
            $DataRow[] = $row['net_amount'];
            $DataRow[] = ($row['is_cancle'] == 1) ? '<p class="tx-danger">Cancled</p>' : '<p class="tx-success">Approved</p>';
            $DataRow[] = $btn;
    
            $encode[] = $DataRow;
        }
    
        $json = json_encode($encode);
        echo '{ "draw": ' . intval($sEcho) . ',"recordsTotal": ' . $rResult['total'] . ',"recordsFiltered": ' . $rResult['total'] . ',"data":' . $json . '}';
        exit;
    }

    public function get_ACinvoice_byid($id)
    {
        $db = $this->db;
        $db->setDatabase("jen2022f1uo"); 
        $builder = $db->table('sales_ACinvoice si'); 
        $builder->select('si.*,ac.name as party_name');
        $builder->join('account ac','ac.id = si.party_account');
        $builder->where(array('si.id'=>$id));
        $query = $builder->get();
        $invoice = $query->getResultArray();
                
        $getdata['invoice'] = $invoice[0];
        $gmodel = new GeneralModel();
        foreach($invoice as $row){    
            $total_return = $gmodel->get_data_table('sales_ACinvoice',array('return_sale'=>$row['return_sale'] ,'v_type'=>'return' ),'SUM(net_amount) as total');
            $getreturn = $gmodel->get_data_table('sales_ACinvoice',array('id'=>$row['return_sale'],'v_type'=>'general'),'id,net_amount,invoice_date');
            $getvoucher = $gmodel->get_data_table('account',array('id'=>$row['voucher_type']),'name');
            $getround = $gmodel->get_data_table('account',array('id'=>$row['round']),'name');
            
            $getdata['invoice']['return_sale_name'] = '('.@$getreturn['id'].') - '.@$getreturn['invoice_date'].'-'.$row['party_name'].'- ₹'.(@$getreturn['net_amount'] + @$row['net_amount'] - @$total_return['total']);
            $getdata['invoice']['voucher_name']=@$getvoucher['name'];
            $getdata['invoice']['round_name']=@$getround['name'];
        }
        
        $item_builder =$db->table('sales_ACparticu sp');
        $item_builder->select('sp.*,ac.name as account_name,ac.code as code');
        $item_builder->join('account ac','ac.id = sp.account');
        $item_builder->where(array('sp.parent_id' => $id, 'sp.is_delete' => 0 ));
        $query= $item_builder->get();
        $getdata['acc'] = $query->getResultArray();
        // echo '<pre>';print_r($getdata['acc']);exit;
        return $getdata;
    }


    public function insert_edit_challan($post)
    {
        if(!@$post['pid']){
            $msg = array('st' => 'fail', 'msg' => "Please Select any Product");
            return $msg;
        }

        $db = $this->db;
        $db->setDatabase("jen2022f1uo"); 
        $builder = $db->table('sales_challan');
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
        $item_disc=$post['item_disc'];
        $discount=$post['discount'];
        $amty=$post['amty'];
        $cess=$post['cess'];
        $total=0.0;
        
        for($i=0;$i<count($pid);$i++)
        {
            $disc_amt=0;
            if($item_disc[$i] != 0 ){
                $sub = $post['qty'][$i] * $post['price'][$i];
                $disc_amt = $sub * $item_disc[$i] / 100;
            }
            $total +=$post['qty'][$i] * $post['price'][$i] - @$disc_amt;  
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

        $dt = date_create($post['challan_date']);
        $date = date_format($dt,'Y-m-d');
        if(isset($post['lr_date'])){
            $lr_dt = date_create($post['lr_date']);
            $lr_date = date_format($lr_dt,'Y-m-d');
        }else{
            $lr_date = '';
        }
        
        $netamount=$total + $post['cess'] + $post['amty'] +   $tds_amt + $post['tot_igst'];
        
        $pdata = array(
        'voucher_type' => $post['voucher_type'],
        'challan_date' => $date,    
        'challan_no' => $post['challan_no'],    
        'account' => $post['account'],
        'tds_limit' => $post['tds_limit'],
        'acc_state' => $post['acc_state'],
        'gst' => $post['gst'],
        'broker' => @$post['broker'],
        'delivery_code' => @$post['delivery_code'],
        'other' => @$post['other'],
        'lr_no' => $post['lrno'],
        'lr_date'=> $lr_date,
        'weight' => $post['weight'],
        'freight' => $post['freight'],
        'transport' => @$post['transport'],
        'city' => @$post['city'],
        'taxes' => json_encode(@$post['taxes']),
        'tot_igst' => $post['tot_igst'],
        'tot_cgst' => $post['tot_cgst'],
        'tot_sgst' => $post['tot_sgst'],
        'total_amount' => $total,
        'discount' => $discount,
        'disc_type' => $post['disc_type'],
        'amty' => $amty,
        'amty_type'=> $post['amty_type'],
        'cess_type' => $post['cess_type'],        
        'cess' => $cess,        
        'tds_amt' => $post['tds_amt'],        
        'tds_per' => $post['tds_per'],        
        'net_amount' => round($netamount),
        'transport_mode' => $post['trasport_mode'],
        'vehicle_modeno' => @$post['vhicle_modeno'],
        'round' => @$post['round'],
        'round_diff' => @$post['round_diff'],
        'taxable' => @$post['taxable'],
        );
        
        if (!empty($result_array)) {
    
            $pdata['update_at'] = date('Y-m-d H:i:s');
            $pdata['update_by'] = session('cid');
            if (empty($msg)) {
                $builder->where(array("id" => $post['id']));
                $result = $builder->Update($pdata);
                
                $item_builder=$db->table('sales_item');
                $item_result = $item_builder->select('GROUP_CONCAT(item_id) as item_id')->where(array("parent_id" => $post['id'],"type" => 'challan'))->get();
                $getItem = $item_result->getRow();

                $getpid = explode(',', $getItem->item_id);
                $delete_itemid = array_diff($getpid,$pid);

                if(!empty($delete_itemid)){
                    foreach($delete_itemid as $key => $del_id){
                        $del_data = array('is_delete' => '1');
                        $item_builder->where(array('item_id' => $del_id , 'parent_id' => $post['id'] , 'type' => 'challan'));
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
                            'item_disc'=> $post['item_disc'][$i],
                            'igst'=> $post['igst'][$i],
                            'cgst'=> $post['cgst'][$i],
                            'sgst'=> $post['sgst'][$i],
                            'remark'=> $post['remark'][$i],
                            'is_delete' => 0,
                            'update_at'=> date('Y-m-d H:i:s'),
                            'update_by'=> session('uid'),
                        );
                        $item_builder->where(array('item_id'=>$pid[$i],'parent_id'=>$post['id']));
                        $res = $item_builder->update($item_data);
                    }else{
                        $item_data = array(
                            'parent_id'=> $post['id'],
                            'item_id'=> $post['pid'][$i],
                            'type'=> 'challan',
                            'uom'=> $post['uom'][$i],
                            'rate'=> $post['price'][$i],
                            'qty'=> $post['qty'][$i],
                            'igst'=> $post['igst'][$i],
                            'cgst'=> $post['cgst'][$i],
                            'sgst'=> $post['sgst'][$i],
                            'item_disc'=> $post['item_disc'][$i],
                            'remark'=> $post['remark'][$i],
                            'created_at'=> date('Y-m-d H:i:s'),
                            'created_by'=> session('uid'),
                        );
                        $res = $item_builder->insert($item_data);
                    }
                    $item_builder->where(array('parent_id' => $post['id'] , 'item_id'=> $post['pid'][$i], "type" => 'challan'));
                    $result1=$item_builder->update($item_data);
                    
                }
                $builder = $db->table('sales_challan');
    
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
            $pdata['created_by'] = session('uid');
            
            if (empty($msg)) {
                $result = $builder->Insert($pdata);
                // print_r($result);
                $id = $db->insertID();
                for($i=0;$i<count($pid);$i++)
                {
                    $itemdata[]=array(
                        'parent_id'=> $id,
                        'item_id'=> $post['pid'][$i],
                        'type'=> 'challan',
                        'uom'=> $post['uom'][$i],
                        'rate'=> $post['price'][$i],
                        'qty'=> $post['qty'][$i],
                        'igst'=> $post['igst'][$i],
                        'cgst'=> $post['cgst'][$i],
                        'sgst'=> $post['sgst'][$i],
                        'item_disc'=> $post['item_disc'][$i],
                        'remark'=> $post['remark'][$i],
                        'created_at'=> date('Y-m-d H:i:s'),
                        'created_by'=> session('uid'),
                    );
                }
                $item_builder=$db->table('sales_item');
                $result1=$item_builder->insertBatch($itemdata);
               
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

    public function insert_edit_ACinvoice($post){
        // echo '<pre>';print_r($post);exit;
        $db = $this->db;
        $db->setDatabase(session('DataSource'));
        $builder = $db->table('sales_ACinvoice');
        $builder->select('*');
        $builder->where(array('id' => $post['id']));
        $builder->limit(1);
        $result = $builder->get();
        $result_array = $result->getRow();
        $msg = array();
       
        $pid=$post['pid'];
        $price=$post['price'];
        $igst=$post['igst'];
        $cgst=$post['cgst'];
        $sgst=$post['sgst'];
        $discount=$post['discount'];
        $amty=$post['amty'];
        $total=0.0;

        for($i=0;$i<count($price);$i++)
        {
            $sub = $post['price'][$i];
            $total += $post['price'][$i];  
        }
        
        if($post['disc_type'] == '%'){
            if($post['discount'] == ''){
                $post['discount'] = 0;
            }else{
                $post['discount'] = $total * $post['discount']/100;
                if($post['discount'] > 0){
                    $total = 0;
                    for($i=0;$i<count($pid);$i++){
                        $disc_amt=0;
                        $devide_disc = $post['discount'] /count($pid);
    
                        $sub =  $post['price'][$i];
                        $total +=$post['price'][$i] - $devide_disc;
                    }
                }
            }
        }else{
            if($post['discount'] == '')
                $post['discount'] == 0;
            if($post['discount'] > 0){
                $total = 0;
                $devide_disc = $post['discount'] /count($pid);
                for($i=0;$i<count($pid);$i++){
                    $disc_amt=0;

                    $sub = $post['price'][$i];
                    $total +=$post['price'][$i] - $devide_disc;
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
    
        $netamount = $total + $post['amty']  +  $post['tot_igst'];
    
        if(in_array('tds',$post['taxes'])){
            $netamount +=   $tds_amt;
        }
        if(in_array('cess',$post['taxes'])){
            $netamount +=   $post['cess'];
        }

        $pdata = array(
            'voucher_type'=> $post['voucher_type'],
            'invoice_date'=> db_date($post['invoice_date']),
            'invoice_no'=> $post['invoice_no'],
            'party_account'=> @$post['party_account'],
            'v_type'=> @$post['v_type'],
            'method'=> @$post['method'] ? $post['method'] : '' ,
            'return_sale'=> @$post['invoice'],
            'other'=> @$post['other'],
            'tds_per'=> @$post['tds_per'],
            'tds_amt' => $post['tds_amt'],
            'tds_limit'=> @$post['tds_limit'],
            'acc_state'=> @$post['acc_state'],
            'taxes'=> json_encode($post['taxes']),
            'discount'=> $discount,
            'disc_type'=> $post['disc_type'],
            'amty'=> $amty,
            'amty_type'=> $post['amty_type'],
            'cess'=> @$post['cess'],
            'cess_type' => @$post['cess_type'], 
            'supp_inv' => $post['supp_inv'],
            'supp_inv_date' => $post['supp_inv_date'] ? db_date($post['supp_inv_date']) : '',
            'tot_igst' => $post['tot_igst'],
            'tot_cgst' => $post['tot_cgst'],
            'tot_sgst' => $post['tot_sgst'],
            'total_amount' => $total,
            'net_amount' => round($netamount),
            'round' => @$post['round'],
            'round_diff' => @$post['round_diff'],
            'taxable' => @$post['taxable']
        );

        if (!empty($result_array)) {

            $pdata['update_at'] = date('Y-m-d H:i:s');
            $pdata['update_by'] = session('cid');
            if (empty($msg)) {
                $builder->where(array("id" => $post['id']));
                $result = $builder->Update($pdata);
            
                $account_builder=$db->table('sales_ACparticu');
                $account_result = $account_builder->select('GROUP_CONCAT(account) as account')->where(array("parent_id" => $post['id'],"type" => $post['v_type']))->get();
                $getAccount = $account_result->getRow();
    
                $getpid = explode(',', $getAccount->account);
                $delete_accountid = array_diff($getpid,$pid);
                
                if(!empty($delete_accountid)){
                    foreach($delete_accountid as $key => $del_id){
                        $del_data = array('is_delete' => '1');
                        $account_builder->where(array('account' => $del_id , 'parent_id' => $post['id'] , 'type' => $post['v_type']));
                        $account_builder->update($del_data);
                        
                    }       
                }

                for($i=0;$i<count($pid);$i++)
                {   
                    $account_result = $account_builder->select('*')->where(array("account" => $pid[$i],"parent_id" => $post['id']))->get();
                    $getAccount = $account_result->getRow();
                    
                    if(!empty($getAccount)){
                        $account_data=array(
                            
                            'amount'=> $post['price'][$i],
                            'igst'=> $post['igst'][$i],
                            'cgst'=> $post['cgst'][$i],
                            'sgst'=> $post['sgst'][$i],
                            'remark'=> $post['remark'][$i],
                            'is_delete' => 0,
                            'update_at'=> date('Y-m-d H:i:s'),
                            'update_by'=> session('uid'),
                        );
                        $account_builder->where(array('account'=>$pid[$i],'parent_id'=>$post['id']));
                        $res = $account_builder->update($account_data);
                    }else{
                        $account_data = array(
                            'parent_id'=> $post['id'],
                            'account'=> $post['pid'][$i],
                            'type'=> @$post['v_type'],
                            'amount'=> $post['price'][$i],
                            'igst'=> $post['igst'][$i],
                            'cgst'=> $post['cgst'][$i],
                            'sgst'=> $post['sgst'][$i],
                            'remark'=> $post['remark'][$i],
                            'created_at'=> date('Y-m-d H:i:s'),
                            'created_by'=> session('uid'),
                        );
                        $res = $account_builder->insert($account_data);
                    }
                    $account_builder->where(array('parent_id' => $post['id'] , 'account'=> $post['pid'][$i], "type" => 'challan'));
                    $result1=$account_builder->update($account_data);
                }

                $builder = $db->table('sales_ACparticu');
    
                if ($result) {
                    $msg = array('st' => 'success', 'msg' => "Your Details updated Successfully!!!");
                    //return view('master/account_view');
                } else {
                    $msg = array('st' => 'fail', 'msg' => "Your Details Updated fail");
                }
            }
        } else {
            $pdata['created_at'] = date('Y-m-d H:i:s');
            $pdata['created_by'] = session('uid');
            
            if (empty($msg)) {
                $result = $builder->Insert($pdata);
                // print_r($result);
                $id = $db->insertID();
                
                for($i=0;$i<count($pid);$i++)
                {
                    $accountdata[]=array(
                        'parent_id'=> $id,
                        'account'=> $post['pid'][$i],
                        'type'=> @$post['v_type'],
                        'amount'=> $post['price'][$i],
                        'igst'=> $post['igst'][$i],
                        'cgst'=> $post['cgst'][$i],
                        'sgst'=> $post['sgst'][$i],
                        'remark'=> $post['remark'][$i],
                        'created_at'=> date('Y-m-d H:i:s'),
                        'created_by'=> session('uid'),
                    );
                }
                $account_builder=$db->table('sales_ACparticu');
                $result1=$account_builder->insertBatch($accountdata);
               
                if ($result &&  $result1) {
                    $msg = array('st' => 'success', 'msg' => "Your Details Added Successfully!!!");
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
        
        foreach($getdata as $row){     

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

    public function get_BankCashAdvance($post) {
        
        $db = $this->db;
        $db->setDatabase(session('DataSource'));
        $builder = $db->table('bank_tras');
        $builder->select('*');
        $builder->where(array("nature_pay" => 2 ));
        $builder->orWhere(array("nature_rec" => 2 ));
        $builder->where(array("particular" => $post['account']));
        $builder->limit(10);
        $query = $builder->get();
        $getdata = $query->getResultArray();
        $result =array();
        foreach($getdata as $row){     
            $gmodel = new GeneralModel();
            $ac_name = $gmodel->get_data_table('account',array('id'=>$row['particular']),'name');
            $result[] = array(
                "text" => $row['id'] .' ( '. $ac_name['name'] .' )' . ' - ₹ ' .$row['amount'],
                "id" => $row['id']
            );
        }
        return $result;
    }

    public function get_challan_detail($get){
       // print_r("bdchjd");exit;
        $dt_search = array(
            "sc.challan_date",
            "sc.challan_no",
            "(select name from account ac where ac.id = sc.account) as account_name",
            "(select name from account ac where ac.id = sc.broker) as broker_name",
        );

        $dt_col = array(
            "sc.id",
            "sc.challan_no",
            "sc.challan_date",
            "(select name from account ac where ac.id = sc.account) as account_name",
            "(select name from account ac where ac.id = sc.broker) as broker_name",
            "sc.account",	
            "sc.broker",	
            "sc.delivery_code",
            "sc.lr_no",
            "sc.lr_date",
            "sc.weight",	
            "sc.freight",
            "sc.transport",
            "sc.gst",
            "sc.is_cancle",
            "sc.is_delete",
        );
    
        $filter = $get['filter_data'];
        $tablename = "sales_challan sc";
        $where = '';
        $where .= " and is_delete=0";
        $rResult = getManagedData($tablename, $dt_col, $dt_search, $where);
        $sEcho = $rResult['draw'];
    
        $encode = array(); 
        $gmodel = new GeneralModel();
        foreach ($rResult['table'] as $row) {
            $DataRow = array();
            $statusarray = array("1" => "Cancled", "0" => "Cancle");

            $btn_cancle = '<a target="_blank" title="Cancle Challan" onclick="editable_os(this)"  data-val="' . $row['is_cancle'] . '"  data-pk="' . $row['id'] . '" tabindex="-1" class="btn btn-link pd-6" title="'.$statusarray[$row['is_cancle']].'"><i class="far fa-times-circle"></i></a>';

            $btnedit = '<a href="' . url('Sales/add_challan/') . $row['id'] . '" data-target="#fm_model"  data-title="Edit Group : " class="btn btn-link pd-6"><i class="far fa-edit"></i></a> ';
            $btnview = '<a href="' . url('Sales/challan_detail/') . $row['id'] . '"    class="btn btn-link pd-6"><i class="far fa-eye"></i></a> ';
            $btndelete = '<a data-toggle="modal" target="_blank"   title="Delete Voucher"  onclick="editable_remove(this)"  data-val="' . $row['id'] . '"  data-pk="' . $row['id'] . '" tabindex="-1" class="btn btn-link pd-6"><i class="far fa-trash-alt"></i></a> ';
            
            $getMax = $gmodel->get_data_table('sales_challan',array('is_delete'=>0),'MAX(challan_no) as max_no');


            if($row['is_cancle'] ==1 || $row['is_delete'] == 1){
                $btn =  $btnview;
            }else{
                $btn =  $btnedit . $btnview;
            }
            
            if($getMax['max_no'] == $row['challan_no']){
                $btn .= $btndelete;
            }else{
                if($row['is_cancle'] == 0){
                    $btn .= $btn_cancle;
                }
            }
            
            $date = user_date($row['challan_date']);
            if(!empty($row['gst']))
            {
                $gst = '<br>('.$row['gst'].')';
            }
            else
            {
                $gst = '';
            }
            $DataRow[] = $row['challan_no'];
            $DataRow[] = $date;
            $DataRow[] = $row['account_name'].$gst;
            $DataRow[] = $row['broker_name'];
            $DataRow[] = ($row['is_cancle'] == 1)  ? '<p class="tx-danger">Cancled</p>' : '<p class="tx-success">Approved</p>';
            $DataRow[] = $btn;
            
            $encode[] = $DataRow;
        }
    
        $json = json_encode($encode);
        echo '{ "draw": ' . intval($sEcho) . ',"recordsTotal": ' . $rResult['total'] . ',"recordsFiltered": ' . $rResult['total'] . ',"data":' . $json . '}';
        exit;
    }


    public function search_challan_data($post){
        
        $db = $this->db;
        $db->setDatabase("jen2022f1uo"); 
        $builder = $db->table('sales_challan sc'); 
        $builder->select('sc.*,ac.name as account_name,ac.default_due_days');
        $builder->join('account ac','ac.id = sc.account');
        if(!empty(@$post['searchTerm'])){
            $builder->like('sc.challan_no',@$post['searchTerm']);
        }
        $builder->where('sc.is_delete','0');
        $builder->where('sc.is_cancle','0');
        $query = $builder->get();
        $challan = $query->getResultArray();
        
        // $getdata['challan'] = $challan[0];
        
        $gmodel=new GeneralModel();
        
        foreach($challan as $row){
            
            $getbroker = $gmodel->get_data_table('account',array('id'=>$row['broker']),'name,brokrage');
            $getdelivery = $gmodel->get_data_table('account',array('id'=>$row['delivery_code']),'name');
            // $getclass = $gmodel->get_data_table('class',array('id'=>$row['class']),'code');
            
            $gettransport = $gmodel->get_data_table('transport',array('id'=>$row['transport']),'name');
            $getcity = $gmodel->get_data_table('cities',array('id'=>$row['city']),'name');
            $getvehicle = $gmodel->get_data_table('vehicle',array('id'=>$row['vehicle_modeno']),'name');
            
            if(empty($getbroker)){
                $getbroker['name'] = '';
            }
            if($row['lr_date'] == '0000-00-00'){
                $row['lr_date'] = '';
            }else{
                $dt = date_create($row['lr_date']);
                $row['lr_date'] = date_format($dt,'d-m-Y');
            }
            if(empty($gettransport)){
                $gettransport['code'] = '';
            }
            if(empty($getcity)){
                $getcity['name'] = '';
            }
            if(empty($getvehicle)){
                $getvehicle['name'] = '';
            }
            $row['broker_name']=@$getbroker['name'];
            $row['fix_brokrage']=@$getbroker['brokrage'];
            $row['delivery_name']=@$getdelivery['name'];

            $row['transport_name']=@$gettransport['name'];
            $row['city_name']=@$getcity['name'];
            $row['vehicle_name']=@$getvehicle['name'];
            
            $item_builder =$db->table('sales_item st');
            $item_builder->select('st.*,i.id,i.type,i.item_mode,i.name,i.sku,i.purchase_cost,i.hsn,i.code,i.uom as item_uom ,st.uom as uom');
            $item_builder->join('item i','i.id = st.item_id');
            $item_builder->where(array('st.parent_id' => $row['id'],'st.type' => 'challan' , 'st.is_delete' => 0 ));
            $query= $item_builder->get();
            $item1 = $query->getResultArray();

            $total_challan_qty = 0;
            foreach($item1 as $row2){
                $total_challan_qty += $row2['qty'];
                $uom =  explode(',',$row2['item_uom']);
                foreach($uom as $row1){
                    $getuom = $gmodel->get_data_table('uom',array('id'=>$row1),'code');
                    $uom_arr[] =$getuom['code']; 
                }
                
                $coma_uom = implode(',',$uom_arr);
                $row2['item_uom'] =$coma_uom; 
                $item[] = $row2;
                $uom_arr = array();
            }   

            $builder = $db->table('sales_invoice si'); 
            $builder->select('si.*,ac.name as account_name');
            $builder->join('account ac','ac.id = si.account');
            $builder->where('si.is_delete','0');
            $builder->where('si.challan_no',$row['id']);
            $query = $builder->get();
            $invoice = $query->getResultArray();
            
            $total_qty =0;
            foreach($invoice as $row1)
            {
                $item_builder =$db->table('sales_item st');
                $item_builder->select('SUM(qty) as qty');
                $item_builder->join('item i','i.id = st.item_id');
                $item_builder->where(array('st.parent_id' => $row1['id'],'st.type' => 'invoice' , 'st.is_delete' => 0 ));
                $query= $item_builder->get();
                $item2 = $query->getRowArray();
                
                $total_qty += $item2['qty'];
            }
            //print_r($total_qty);
           
            $dt = date_create($row['challan_date']);
            $date = date_format($dt,'d-m-Y');

            $text = $row['challan_no'].' ('.$row['account_name'].') /'.$date;
            if($total_qty<$total_challan_qty)
            {
                $result[] = array("text" => $text,"id" => $row['id'] ,'challan'=>$row ,'item'=>$item);
            }else{
                $result = array();
            }
            unset($item);
        }
        // echo '<pre>';print_r($result);exit;
        return $result;
    }


    public function get_sales_challan($id) {
        
        $db = $this->db;
        $db->setDatabase(session('DataSource'));
        $builder = $db->table('sales_challan sc');
        $builder->select('sc.*,ac.name as account_name');
        $builder->join('account ac','ac.id = sc.account');
        $builder->where(array('sc.id'=>$id));
        $query = $builder->get();
        $challan = $query->getResultArray();
                
        $getdata['challan'] = $challan[0];
        
        $gmodel=new GeneralModel();

        foreach($challan as $row){
            
            $getbroker = $gmodel->get_data_table('account',array('id'=>$row['broker']),'name');
            $getdelivery = $gmodel->get_data_table('account',array('id'=>$row['delivery_code']),'name');
           
            $gettransport = $gmodel->get_data_table('transport',array('id'=>$row['transport']),'name');
            $getcity = $gmodel->get_data_table('cities',array('id'=>$row['city']),'name');
            $getvehicle = $gmodel->get_data_table('vehicle',array('id'=>$row['vehicle_modeno']),'name');
            $getvoucher = $gmodel->get_data_table('account',array('id'=>$row['voucher_type']),'name');
            $getround = $gmodel->get_data_table('account',array('id'=>@$row['round']),'name');

            $getdata['challan']['broker_name']=@$getbroker['name'];
            $getdata['challan']['delivery_name']=@$getdelivery['name'];
            $getdata['challan']['transport_name']=@$gettransport['name'];
            $getdata['challan']['city_name']=@$getcity['name'];
            $getdata['challan']['vehicle_name']=@$getvehicle['name'];
            $getdata['challan']['voucher_name']=@$getvoucher['name'];
            $getdata['challan']['round_name']=@$getround['name'];

        }
        
        $item_builder =$db->table('sales_item st');
        $item_builder->select('st.*,i.id,i.type,i.item_mode,i.name,i.sku,i.purchase_cost,i.hsn,i.code,i.uom as item_uom ,st.uom as uom');
        $item_builder->join('item i','i.id = st.item_id');
        $item_builder->where(array('st.parent_id' => $id,'st.type' => 'challan' , 'st.is_delete' => 0 ));
        $query= $item_builder->get();
        $getdata1 = $query->getResultArray();
        // echo '<pre>';print_r($getdata1);exit;
        foreach($getdata1 as $row){
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
            $pdata['created_by'] = session('uid');
            
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
                        'created_by'=> session('uid'),
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
    
    public function get_salesinvoice_data($get){
        $dt_search = array(
            "si.id",
            "si.invoice_date",
            "(select name from account a where a.id = si.account) as account_name",
            "si.account",
            "(select name from account a where a.id = si.broker) as broker_name",
            "si.broker",
            "si.other",
            
        );

        $dt_col = array(
            "si.id",
            "si.invoice_date",
            "si.invoice_no",
            "(select name from account a where a.id = si.account) as account_name",
            "si.account",
            "(select name from account a where a.id = si.broker) as broker_name",
            "si.broker",
            "si.other",
            "si.is_cancle",
            "si.is_delete",
        );
    
        $filter = $get['filter_data'];
        $tablename = "sales_invoice si";
        $where = '';
        
        $where .= " and is_delete=0";
    
        $rResult = getManagedData($tablename, $dt_col, $dt_search, $where);
        $sEcho = $rResult['draw'];
    
        $encode = array();  
    
        $gmodel = new GeneralModel();
        foreach ($rResult['table'] as $row) {
            $DataRow = array();
            
            $statusarray = array("1" => "Cancled", "0" => "Cancle");

            $btn_cancle = '<a target="_blank" title=" ' . $row['account_name'] . '" onclick="editable_os(this)"  data-val="' . $row['is_cancle'] . '"  data-pk="' . $row['id'] . '" tabindex="-1" class="btn btn-link pd-6" title="'.$statusarray[$row['is_cancle']].'"><i class="far fa-times-circle"></i></a>';
            $btnedit = '<a href="' . url('sales/add_salesinvoice/') . $row['id'] . '" class="btn btn-link pd-6"><i class="far fa-edit"></i></a> ';
            $btnview = '<a href="' . url('sales/invoice_detail/') . $row['id'] . '"    class="btn btn-link pd-6"><i class="far fa-eye"></i></a> ';
            $btndelete = '<a data-toggle="modal" target="_blank"   title="challan : ' . $row['id'] . '"  onclick="editable_remove(this)"  data-val="' . $row['id'] . '"  data-pk="' . $row['id'] . '" tabindex="-1" class="btn btn-link pd-6"><i class="far fa-trash-alt"></i></a> ';
            
            $getMax = $gmodel->get_data_table('sales_invoice',array('is_delete'=>0),'MAX(invoice_no) as max_no');


            if($row['is_cancle'] ==1 || $row['is_delete'] == 1){
                $btn =  $btnview;
            }else{
                $btn =  $btnedit . $btnview;
            }
            
            if($getMax['max_no'] == $row['invoice_no']){
                if($row['is_cancle'] != 1){
                    $btn .= $btndelete;
                }
            }else{
                if($row['is_cancle'] == 0){
                    $btn .= $btn_cancle;
                }
            }
            
            $date = user_date($row['invoice_date']);
            $DataRow[] = $row['invoice_no'];
            $DataRow[] = $date;
            $DataRow[] = $row['account_name'];
            $DataRow[] = $row['broker_name'];
            $DataRow[] = ($row['is_cancle'] == 1) ? '<p class="tx-danger">Cancled</p>' : '<p class="tx-success">Approved</p>';
            $DataRow[] = $btn;
    
            $encode[] = $DataRow;
        }
    
        $json = json_encode($encode);
        echo '{ "draw": ' . intval($sEcho) . ',"recordsTotal": ' . $rResult['total'] . ',"recordsFiltered": ' . $rResult['total'] . ',"data":' . $json . '}';
        exit;
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

    public function insert_edit_salesreturn($post){ 
        
        $db = $this->db;
        $db->setDatabase(session('DataSource'));
        $builder = $db->table('sales_return');
        $builder->select('*');
        $builder->where(array("id" => $post['id']));
        $builder->limit(1);
        $result = $builder->get();
        $result_array = $result->getRow();

        $pid=$post['pid'];
        $qty=$post['qty'];
        $price=$post['price'];
        $item_disc=$post['item_disc'];
        $discount=$post['discount'];
        $amty=$post['amty'];
        $cess=$post['cess'];
        $total=0.0;

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

        $msg = array();
        $pdata = array(
            'voucher_type' => $post['voucher_type'],
            'return_no' => $post['return_no'],
            'return_date'=> db_date($post['return_date']),
            'account' => $post['account'],
            'tds_limit' => $post['tds_limit'],
            'acc_state' => $post['acc_state'],
            'gst' => @$post['gst'],
            'broker' => @$post['broker'],
            'other' => @$post['other'], 
            'method' => @$post['method'], 
            'invoice' => @$post['invoice'] ? $post['invoice'] : '' , 
            'total' => $total,
            'discount' => $discount,
            'disc_type' => $post['disc_type'],
            'amty' => $amty,
            'amty_type' => $post['amty_type'],
            'cess' => $cess,
            'cess_type' => $post['cess_type'],
            'tds_amt' => $post['tds_amt'],        
            'tds_per' => $post['tds_per'], 
            'net_amount' => round($netamount),
            'delivery_code' => @$post['delivery_code'],
            'taxes' => json_encode(@$post['taxes']),
            'net_amount'=> round($netamount),
            'lr_no' => $post['lrno'], 
            'lr_date'=> $post['lr_date'],
            'weight' => $post['weight'],
            'freight' => $post['freight'], 
            'transport'=> @$post['transport'],
            'city'=> @$post['city'],
            'transport_mode' => @$post['trasport_mode'],
            'vehicle_no' => @$post['vhicle_modeno'],
            'tot_igst' => $post['tot_igst'],
            'tot_sgst' => $post['tot_sgst'],
            'tot_cgst' => $post['tot_cgst'],
            'round' => @$post['round'],
            'round_diff' => @$post['round_diff'],
            'taxable' => @$post['taxable'],
        );

     if (!empty($result_array)) {
         
         $pdata['update_at'] = date('Y-m-d H:i:s');
         $pdata['update_by'] = session('uid');
         $builder->where(array("id" => $post['id']));
         $result = $builder->Update($pdata);
         
         $item_builder=$db->table('sales_item');
         $item_result = $item_builder->select('GROUP_CONCAT(item_id) as item_id')->where(array("parent_id" => $post['id'],"type" => 'return'))->get();
         $getItem = $item_result->getRow();

         $getpid = explode(',', $getItem->item_id);
         $delete_itemid = array_diff($getpid,$pid);
         

         if(!empty($delete_itemid)){
             foreach($delete_itemid as $key => $del_id){
                 $del_data = array('is_delete' => '1');
                 $item_builder->where(array('item_id' => $del_id , 'parent_id' => $post['id'] , 'type' => 'return'));
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

                 $item_data = array(
                     'parent_id'=> $post['id'],
                     'item_id'=> $post['pid'][$i],
                     'type'=> 'return',
                     'uom'=> $post['uom'][$i],
                     'rate'=> $post['price'][$i],
                     'qty'=> $post['qty'][$i],
                     'igst'=> $post['igst'][$i],
                     'cgst'=> $post['cgst'][$i],
                     'sgst'=> $post['sgst'][$i],
                     'item_disc' => $post['item_disc'][$i],
                     'remark'=> $post['remark'][$i],
                     'created_at'=> date('Y-m-d H:i:s'),
                     'created_by'=> session('cid'),
                 );
                 $res = $item_builder->insert($item_data);
             }
             $item_builder->where(array('parent_id' => $post['id'] , 'item_id'=> $post['pid'][$i], "type" => 'return'));
             $result1=$item_builder->update($item_data);
             
         }
         $builder = $db->table('sales_return');

         if ($result) {
             $msg = array('st' => 'success', 'msg' => "Your Details updated Successfully!!!");
             //return view('master/account_view');
         } else {
             $msg = array('st' => 'fail', 'msg' => "Your Details Updated fail");
         }
     }else {
         
         $pdata['created_at'] = date('Y-m-d H:i:s');
         $pdata['created_by'] = session('uid');
         
         if (empty($msg)) {
             $result = $builder->Insert($pdata);
             // print_r($result);
             $id = $db->insertID();
             
             for($i=0;$i<count($pid);$i++)
             {
                 $item_data[]=array(
                     'parent_id'=> $id,
                     'item_id'=> $post['pid'][$i],
                     'type'=> 'return',
                     'uom'=> $post['uom'][$i],
                     'rate'=> $post['price'][$i],
                     'qty'=> $post['qty'][$i],
                     'igst'=> $post['igst'][$i],
                     'cgst'=> $post['cgst'][$i],
                     'sgst'=> $post['sgst'][$i],
                     'item_disc' => $post['item_disc'][$i],
                     'remark'=> $post['remark'][$i],
                     'created_at'=> date('Y-m-d H:i:s'),
                     'created_by'=> session('uid'),
                 );
             }
             $item_builder=$db->table('sales_item');
             $result1=$item_builder->insertBatch($item_data);
            
             if ($result &&  $result1) {
                 $msg = array('st' => 'success', 'msg' => "Your Details Added Successfully!!!");
             } else {
                 $msg = array('st' => 'fail', 'msg' => "Your Details Updated fail");
             }
         }
     }
     return $msg;
    }
    
    public function get_salesreturn_data($get){

        $dt_search = array( 
            "sr.id",
            "sr.return_no",
            "sr.return_date",
            "(select name from account a where a.id = sr.account) as account_name",
            "sr.account",
            "(select name from account a where a.id = sr.broker) as broker_name",
            "sr.broker",
            "sr.other",
           
        );
    
        
        $dt_col = array(
            "sr.id",
            "sr.return_no",
            "sr.return_date",
            "(select name from account a where a.id = sr.account) as account_name",
            "sr.account",
            "(select name from account a where a.id = sr.broker) as broker_name",
            "sr.broker",
            "sr.other",
            "sr.is_cancle",
            "sr.is_delete",
        );
    
        $filter = $get['filter_data'];
        $tablename = "sales_return sr";
        $where = '';
        // if ($filter != '' && $filter != 'undefined') {
        //     $where .= ' and UserType ="' . $filter . '"';
        // }
        $where .= " and is_delete=0";
    
        $rResult = getManagedData($tablename, $dt_col, $dt_search, $where);
        $sEcho = $rResult['draw'];
    
        $encode = array(); 
        $gmodel = new GeneralModel();
        foreach ($rResult['table'] as $row) {
            $statusarray = array("1" => "Cancled", "0" => "Cancle");

            $DataRow = array();
            
            $btn_cancle = '<a target="_blank" title=" ' . $row['account_name'] . '" onclick="editable_os(this)"  data-val="' . $row['is_cancle'] . '"  data-pk="' . $row['id'] . '" tabindex="-1" class="btn btn-link pd-6" title="'.$statusarray[$row['is_cancle']].'"><i class="far fa-times-circle"></i></a>';
            $btnedit = '<a   href="' . url('sales/add_salesreturn/') . $row['id'] . '" data-title="Edit Sales Return : ' . $row['id'] . '" class="btn btn-link pd-10"><i class="far fa-edit"></i></a> ';
            $btnview = '<a href="' . url('sales/return_detail/') . $row['id'] . '"    class="btn btn-link pd-10"><i class="far fa-eye"></i></a> ';
            $btndelete = '<a  target="_blank"   title="Sales Return: ' . $row['account_name'] . '"  onclick="editable_remove(this)"  data-val="' . $row['id'] . '"  data-pk="' . $row['id'] . '" tabindex="-1" class="btn btn-link pd-10"><i class="far fa-trash-alt"></i></a> ';
            
            $getMax = $gmodel->get_data_table('sales_return',array('is_delete'=>0),'MAX(return_no) as max_no');

            if($row['is_cancle'] ==1 || $row['is_delete'] == 1){
                $btn =  $btnview;
            }else{
                $btn =  $btnedit . $btnview;
            }

            if($getMax['max_no'] == $row['return_no']){
                if($row['is_cancle'] != 1){
                    $btn .= $btndelete;
                }
            }else{
                if($row['is_cancle'] == 0){
                    $btn .= $btn_cancle;
                }
            }
            
            $date = user_date($row['return_date']);
            $DataRow[] = $row['id'];
            $DataRow[] = $date;
            $DataRow[] = $row['account_name'];
            $DataRow[] = $row['broker_name'];
            $DataRow[] = ($row['is_cancle'] == 1) ? '<p class="tx-danger">Cancled</p>' : '<p class="tx-success">Approved</p>';
            $DataRow[] = $btn;
            
            $encode[] = $DataRow;
        }
    
        $json = json_encode($encode);
        echo '{ "draw": ' . intval($sEcho) . ',"recordsTotal": ' . $rResult['total'] . ',"recordsFiltered": ' . $rResult['total'] . ',"data":' . $json . '}';
        exit;
    }
    
    public function UpdateData($post) {
        $result = array();
        // echo '<pre>';print_r($post);exit;
        if ($post['type'] == 'Remove') {
            if ($post['method'] == 'challan') {
                $gnmodel = new GeneralModel();
                $sales_invoice = $gnmodel->get_array_table('sales_invoice',array('challan_no'=>$post['pk']),'is_delete,is_cancle');
                
                    foreach($sales_invoice as $row){
                        if(@$row['is_delete'] == 0 && @$row['is_cancle'] == '0'){
                            $is_delete = 0;
                        }
                    }
                if(isset($is_delete) && $is_delete == 0){
                    $result = array('st'=>'fail' ,'msg'=>'Please First Delete Invoice');
                }else{
                    $result = $gnmodel->update_data_table('sales_challan', array('id' => $post['pk']), array('is_delete' => '1'));
                }
            }

            if ($post['method'] == 'c_note') {
                $gnmodel = new GeneralModel();
                $result = $gnmodel->update_data_table('credit_note', array('id' => $post['pk']), array('is_delete' => '1'));
            }
            
            if ($post['method'] == 'salesinvoice'){
                $gnmodel = new GeneralModel();
                $result = $gnmodel->update_data_table('sales_invoice', array('id' => $post['pk']), array('is_delete' => '1'));
            }

            if ($post['method'] == 'salesreturn') {
                $gnmodel = new GeneralModel();
                $result = $gnmodel->update_data_table('sales_return', array('id' => $post['pk']), array('is_delete' => '1'));
            }
            
            if ($post['method'] == 'ac_challan') {
                $gnmodel = new GeneralModel();
                $result = $gnmodel->update_data_table('sales_ACchallan', array('id' => $post['pk']), array('is_delete' => '1'));
            }
            
            if ($post['method'] == 'ac_invoice') {
                $gnmodel = new GeneralModel();
                $result = $gnmodel->update_data_table('sales_ACinvoice', array('id' => $post['pk']), array('is_delete' => '1'));
            }
        }

        if ($post['type'] == 'Status') {
            
            if ($post['method'] == 'item_invoice') {
                $gnmodel = new GeneralModel();
                $result = $gnmodel->update_data_table('sale_iteminvoice', array('id' => $post['pk']), array('status' => $post['val']));
            }
            if ($post['method'] == 'item_challan') {
                $gnmodel = new GeneralModel();
                $result = $gnmodel->update_data_table('sales_itemchallan', array('id' => $post['pk']), array('status' => $post['val']));
            }

        }

        if($post['type'] == 'Cancle'){
            if ($post['method'] == 'challan') {
                $gnmodel = new GeneralModel();
                $sales_invoice = $gnmodel->get_array_table('sales_invoice',array('challan_no'=>$post['pk']),'is_cancle,is_delete');            
                
                foreach($sales_invoice as $row){
                    if(@$row['is_cancle'] == 0 && @$row['is_delete'] == 0){
                        $is_cancle = 0;
                    }
                }

                if(isset($is_cancle) && $is_cancle == 0){
                    $result = array('st'=>'fail' ,'msg'=>'Please First Cancle Invoice');                    
                }else{
                    $result = $gnmodel->update_data_table('sales_challan', array('id' => $post['pk']), array('is_cancle' => 1));
                }

            }
            
            if ($post['method'] == 'salesinvoice') {
                $gnmodel = new GeneralModel();
                $result = $gnmodel->update_data_table('sales_invoice', array('id' => $post['pk']), array('is_cancle' => 1));
            }
            
            if ($post['method'] == 'salesreturn') {
                $gnmodel = new GeneralModel();
                $result = $gnmodel->update_data_table('sales_return', array('id' => $post['pk']), array('is_cancle' => 1));
            }


            if ($post['method'] == 'ac_invoice') {
                $gnmodel = new GeneralModel();
                $result = $gnmodel->update_data_table('sales_ACinvoice', array('id' => $post['pk']), array('is_cancle' => 1));
            }
        }
        return $result;
    }

    public function get_master_data($method, $id) {
        
        $gnmodel=new GeneralModel;
        
       
        if($method == 'salesinvoice') {
            $result['salesinvoice'] = $gnmodel->get_data_table('sales_invoice', array('id' => $id));
        }
        if($method == 'salesreturn') {
            $result['s_return'] = $gnmodel->get_data_table('sales_return', array('id' => $id));
        }
        
        return $result;
    }

    public function get_Saleinvoice_databyid($post){
        
        $db=$this->db;
        $db->setDatabase(session('DataSource'));
        $builder=$db->table('sales_invoice si');
        $builder->select('si.*,ac.name as account_name');
        $builder->join('account ac','ac.id = si.account');
        $builder->where(array('si.account' => $post['id']));

        if(@$post['searchTerm'] != ''){
            $builder->where(array('si.invoice_no' => @$post['searchTerm']));
        }

        $builder->orderBy('si.id','desc');
        $query=$builder->get();
        $sale_invoice=$query->getResultArray();
        
        $gmodel=new GeneralModel();
        

        foreach($sale_invoice as $row){
            
            $getbroker = $gmodel->get_data_table('account',array('id'=>$row['broker']),'name,brokrage');
            $getdelivery = $gmodel->get_data_table('account',array('id'=>$row['delivery_code']),'name');
            
            $gettransport = $gmodel->get_data_table('transport',array('id'=>@$row['transport']),'name');
            $getvehicle = $gmodel->get_data_table('vehicle',array('id'=>@$row['vehicle_modeno']),'name');
            
            if(empty($getbroker)){
                $getbroker['name'] = '';
            }
            if($row['lr_date'] == '0000-00-00'){
                $row['lr_date'] = '';
            }else{
                $dt = date_create($row['lr_date']);
                $row['lr_date'] = date_format($dt,'d-m-Y');
            }
            if(empty($gettransport)){
                $gettransport['code'] = '';
            }
            if(empty($getcity)){
                $getcity['name'] = '';
            }
            if(empty($getvehicle)){
                $getvehicle['name'] = '';
            }
            $row['broker_name']=@$getbroker['name'];
            $row['fix_brokrage']=@$getbroker['brokrage'];
            $row['delivery_name']=@$getdelivery['name'];

            $row['transport_name']=@$gettransport['name'];
            $row['city_name']=@$getcity['name'];
            $row['vehicle_name']=@$getvehicle['name'];
            
            $item_builder =$db->table('sales_item st');
            $item_builder->select('st.*,i.id,i.type,i.item_mode,i.name,i.sku,i.purchase_cost,i.hsn,i.code,i.uom as item_uom ,st.uom as uom');
            $item_builder->join('item i','i.id = st.item_id');
            $item_builder->where(array('st.parent_id' => $row['id'],'st.type' => 'invoice' , 'st.is_delete' => 0 ));
            $query= $item_builder->get();
            $item1 = $query->getResultArray();

            $total_challan_qty = 0;

            foreach($item1 as $row2){
                $total_challan_qty += $row2['qty'];
                $uom =  explode(',',$row2['item_uom']);
                foreach($uom as $row1){
                    $getuom = $gmodel->get_data_table('uom',array('id'=>$row1),'code');
                    $uom_arr[] =$getuom['code']; 
                }
                
                $coma_uom = implode(',',$uom_arr);
                $row2['item_uom'] =$coma_uom; 
                $item[] = $row2;
                $uom_arr = array();
            }   

            $builder = $db->table('sales_return si'); 
            $builder->select('si.*,ac.name as account_name');
            $builder->join('account ac','ac.id = si.account');
            $builder->where('si.is_delete','0');
            $builder->where('si.invoice',$row['id']);
            $query = $builder->get();
            $return = $query->getResultArray();
            
            $total_qty =0;

            foreach($return as $row1)
            {
                $item_builder =$db->table('sales_item st');
                $item_builder->select('SUM(qty) as qty');
                $item_builder->join('item i','i.id = st.item_id');
                $item_builder->where(array('st.parent_id' => $row1['id'],'st.type' => 'return' , 'st.is_delete' => 0 ));
                $query= $item_builder->get();
                $item2 = $query->getRowArray();
                
                $total_qty += $item2['qty'];
            }
           
            $dt = date_create($row['invoice_date']);
            $date = date_format($dt,'d-m-Y');

            $text = $row['invoice_no'].' ('.$row['account_name'].') /'.$date;
            // print_r($total_qty);
            // print_r($total_challan_qty);exit;
            if($total_qty < $total_challan_qty)
            {
                $result[] = array("text" => $text, "id" => $row['id'] , 'return' => $row ,'item'=>$item);
                // print_r($result);exit;
            }else{

                $result[] = array();
            }

            unset($item);
        }


        // foreach($sale_invoice as $row){

        //     $text = '('.$row['invoice_no'] .') -'.user_date($row['invoice_date']).'-'.$row['account_name'] .' - ₹'.$row['net_amount'];
        //     $data[] = array(
        //         'id'=>$row['id'],
        //         'text'=>$text,
        //         'table'=>'sales_invoice'
        //     );

        // }
        
        return $result; 
        
    }

    public function get_Salegeneral_databyid($post){

        $db=$this->db;
        $db->setDatabase(session('DataSource'));
        $builder=$db->table('sales_ACinvoice sa');
        $builder->select('sa.*,ac.name as account_name');
        $builder->join('account ac','ac.id = sa.party_account');
        $builder->where('sa.party_account',$post['id']);
        $builder->where(array('sa.party_account' => $post['id']));
        $builder->where(array('sa.v_type' => 'general'));
        if(@$post['searchTerm'] != ''){
            $builder->where(array('sa.id' => @$post['searchTerm']));
        }
        $builder->orderBy('sa.id','desc');
        $builder->limit(5);
        $query=$builder->get();
        $sale_general=$query->getResultArray();
        
        $gmodel=new GeneralModel();
        $result = array();
        foreach($sale_general as $row){
            
            $item_builder =$db->table('sales_ACparticu st');
            $item_builder->select('st.*,ac.id as id,ac.name,ac.hsn');
            $item_builder->join('account ac','ac.id = st.account');
            $item_builder->where(array('st.parent_id' => $row['id'],'st.type' => 'general' , 'st.is_delete' => 0 ));
            $query= $item_builder->get();
            $item = $query->getResultArray();

            $total_challan_qty = 0;
           
            $dt = date_create($row['invoice_date']);
            $date = date_format($dt,'d-m-Y');

            $row['supp_inv_date'] = $row['supp_inv_date'] ? user_date($row['supp_inv_date']) : ''; 

            $text = $row['invoice_no'].' ('.$row['account_name'].') /'.$date;
            
           
            $result[] = array("text" => $text, "id" => $row['id'] , 'return' => $row ,'item'=>$item,'table'=>'sales_ACinvoice' );
           
            unset($item);
        }
        
       
        return $result; 
        
    }
}
?>