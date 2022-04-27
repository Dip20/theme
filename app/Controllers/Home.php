<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\GeneralModel;
use App\Models\SalesModel;

class Home extends BaseController
{

    public function add_sales_invoice($id = '')
    {
        $this->model  = new UserModel();
        $this->gmodel = new GeneralModel();

        $data = array();
        $post = $this->request->getPost();

        /* Create OR Update the invoice */
        if (!empty($post)) 
        {
            $msg = $this->model->insert_edit_salesinvoice($post);
            return $this->response->setJSON($msg);
        }

        
        /* Get the invoice details */
        if ($id != '') {
             $data = $this->model->get_sales_invoice($id);
        }
        
        /* Get the Taxes */
        $tax_id = $this->gmodel->get_data_table('gl_group',array('name' => 'Duties and taxes'),'id');
        $tax = $this->gmodel->get_array_table('account',array('gl_group' =>$tax_id['id']),'name');
        
        $data['tax'] = $tax; 

        /* Get the last ID */
        $getId = $this->gmodel->get_saleInv_id('sales_invoice');
        $data['current_id'] = $getId + 1;
        $data['id'] = $id;


        /*Return View */
        $data['title'] = "Add Sales Invoice";
        return view('add_sales_invoice', $data);
    }
    













    public function addItem()
    {
        
        return view('addItem');
    }

    public function uom()
    {   
        $data['title'] = "Unit Of Measurement";
        return view('uom', $data);
    }

    public function addaccount()
    {   
        $post = $this->request->getPost();
        if (!empty($post)) 
        {
            $this->model = new UserModel();
            $msg = $this->model->insert_edit_account($post);
            return $this->response->setJSON($msg);

        } else {

            $data['title'] = "Add Account Data";
            return view('addaccount', $data);

        }
        
    }


    public function add_uom()
    {   
        $post = $this->request->getPost();

       if (!empty($post)) 
       {
            $this->model = new UserModel();
            $msg = $this->model->insert_edit_uom($post);
            if ($msg['st']=="success") 
            {
                return redirect()->to(url('Home/uom'));
            }

      }else
      {
        $data['title']="Add Unit Of Measurement";
        return view('add_uom', $data);
      }
    }



     /**
     * ==================== Get Data ==========================
     */

    public function Getdata($method = '',$type='') {
        // if (!session('uid')) {
        //     return redirect()->to(url('auth'));
        // }
        
        $this->model = new UserModel();
        $this->gmodel = new GeneralModel();


        if ($method == 'uom') {
            $get = $this->request->getGet();
            $this->model->get_uom_data($get);
        }
  
        if ($method == 'search_uom') {
            $post = $this->request->getPost();
            $data = $this->model->search_finishuom_data($post);
            return $this->response->setJSON($data);
        }

        if ($method == 'search_uom_data') {
            $post = $this->request->getPost();
            $data = $this->model->search_uom_data($post);
            return $this->response->setJSON($data);
        }

        if ($method == 'glgrp') {
            $get = $this->request->getGet();
            $this->model->get_glgrp_data($get);
        }


        if ($method == 'parent_glgrp') {
            $post = $this->request->getPost();
            $data = $this->model->search_parent_glgrp_data($post);
            return $this->response->setJSON($data);
        }


        if ($method == 'search_party') {
            $post = $this->request->getPost();
            $data = $this->model->search_party_data($post);
            return $this->response->setJSON($data);
        }

        if($method == 'search_account') {
            $post = $this->request->getPost();
            $data = $this->model->search_account_data($post);
            return $this->response->setJSON($data);
        }

        if($method == 'search_country') {
            $post = $this->request->getPost();
            $data=$this->model->getCountry($post);
            return $this->response->setJSON($data);
        }

        if($method == 'search_state') {
            $post = $this->request->getPost();
            // print_r($post);exit;
            $data=$this->model->getStates($post);
            return $this->response->setJSON($data);
        }

        if($method == 'search_city') {
            $post = $this->request->getPost();
            $data=$this->model->getCities($post);
            return $this->response->setJSON($data);
        }

        if ($method == 'search_bank') {
            $post = $this->request->getPost();
            //print_r($post);exit;
            $data=$this->model->search_bank_data($post);
            return $this->response->setJSON($data);
        }

        if ($method == 'Item') {
            $post= $this->request->getPost();
            $data = $this->model->search_item_data(@$post['searchTerm']);
            return $this->response->setJSON($data);
        }

        if ($method == 'search_salevouchertype') {
            $post = $this->request->getPost();
            $data = $this->gmodel->search_salevouchertype_data($post);
            return $this->response->setJSON($data);
        }

        if ($method == 'search_sun_debtor') {
            $post = $this->request->getPost();
            $data = $this->model->search_sun_debtor(@$post);
            // print_r($data);exit;
            return $this->response->setJSON($data);
            
        }





    }

    /**
     * ==================== Create Item ==========================
     */

    public function CreateItem($id = '')
    {
        
        // if (!session('uid')) {
        //     return redirect()->to(url('company'));
        // }

        $data = array();
        $post = $this->request->getPost();
        if(!empty($post))
        {
            $this->model = new UserModel();
            $msg=$this->model->insert_edit_item($post);
            return $this->response->setJSON($msg);
        }
         
    }
}
