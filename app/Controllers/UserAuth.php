<?php

namespace App\Controllers;
use App\Models\UserModel;

class UserAuth extends BaseController
{
    public function index()
    {   
        return view('login');
    }

    public function register()
    {   
        helper('form');
        $request = \Config\Services::request();
        $validation =  \Config\Services::validation();

        /* Validation */
        if ($request->getPost() && $this->validate([
            'name'     => 'required|min_length[3]|max_length[255]',
            'email'    => 'required|min_length[3]|max_length[255]|valid_email|is_unique[tbl_users.email]',
            'password' => 'required|min_length[3]|max_length[255]',
        ],
        [   
            // Errors
            'name' => [
                'required' => 'Name is required',
            ],
            'email' => [
                'required' => 'Email is required',
                'is_unique' => 'Email Already Registered',
            ],
            'password' => [
                'required' => 'Password is required'
            ]
        ]
        
        ))
        {   
            /* Successfully validated */
            $this->model = new UserModel();
            $post = $request->getPost();
            $msg = $this->model->insert_data($post, "tbl_users");
            if ($msg['st']=="success") 
            {
                echo $msg['msg'];
            } else 
            {
                echo $msg['msg'];
            }
            

            

        }else
        {
            return view('register');
        }
    }
}
