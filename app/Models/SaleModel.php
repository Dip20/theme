<?php

namespace App\Models;
use CodeIgniter\Model;

class SaleModel extends Model
{

    public function Get_Item()
    {
        $db = $this->db;
        $db->setDatabase("jen2022f1uo"); 
        $builder = $db->table("item");
        $builder->select('id,code,name,sales_price');
        $builder->where('is_delete',0);
        return $builder->get();
    }
    
}