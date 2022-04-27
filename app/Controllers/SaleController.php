<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\SaleModel;
use App\Models\GeneralModel;


class SaleController extends BaseController
{

    public function index()
    {
        $this->model = new SaleModel();
        
        $post = $this->request->getPost();
        /*Add invoice */
        if (!empty($post)) 
        {
            echo "<pre>";
            print_r($_POST);die();
        } else {

            /* Get Item */
            $data['products'] = $this->model->Get_Item();
            $data['title'] = "Sale Product";
    
            return view("sale", $data);
        }
        
      
    }

}