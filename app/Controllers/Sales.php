<?php 
namespace App\Controllers;
use App\Models\GeneralModel;
use App\Models\SalesModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Sales extends BaseController{

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger){
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        header("Access-Control-Allow-Origin: * ");
        header("Access-Control-Allow-Methods: *");
        header("Access-Control-Allow-Headers: * ");
        $this->model = new SalesModel();
        $this->gmodel = new GeneralModel();
        
    }

    public function add_challan($id = '')
    {
        if (!session('cid')) {
            return redirect()->to(url('company'));
        }
        $data = array();
        $post = $this->request->getPost();
        
        if(!empty($post)){
            $msg=$this->model->insert_edit_challan($post);
            return $this->response->setJSON($msg);
        }

        if($id != '') {
            $data = $this->model->get_sales_challan($id);
        }
        // echo '<pre>';print_r($data);exit;
        $tax_id = $this->gmodel->get_data_table('gl_group',array('name' => 'Duties and taxes'),'id');
        $tax = $this->gmodel->get_array_table('account',array('gl_group' =>$tax_id['id']),'name');
        //$getId = $this->gmodel->get_lastId('sales_challan');

        $getId = $this->gmodel->get_voucher_id('sales_challan');
        
        $data['tax'] = $tax; 
        $data['id'] = $id;
        $data['current_id'] = $getId + 1;
        $data['title'] = "Sales Challan";
        // echo '<pre>';print_r($data);exit;
        return view('Sales/challan',$data);
    }

    public function challan_detail($id){
        if (!session('uid')) {
            return redirect()->to(url('auth'));
        }

        if($id != ''){
            $data = $this->gmodel->get_sales_challan($id);   
        }
        // echo '<pre>';print_r($data);exit; 
        $data['title']="Challan Detail";
        return view('Sales/challan_detail', $data);
    }

    public function pdf_challan($id){
        if (!session('uid')) {
            return redirect()->to(url('auth'));
        }

        if($id != ''){
            $data = $this->model->get_sales_challan($id);   
            $data['account'] = $this->gmodel->get_data_table('account',array('id'=>$data['challan']['account']),'*');
            $data['delivery'] = $this->gmodel->get_data_table('account',array('id'=>@$data['challan']['delivery_code']),'*');
            $data['company'] = $this->gmodel->get_data_table('account',array('id'=>@$data['challan']['delivery_code']),'*');
        }

        // echo '<pre>';print_r($data);exit;
        ini_set('memory_limit', '-1');
        $html =  view('pdf/challan_detail',$data);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('fontHeightRatio', 1);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A3', 'portrait');
        $dompdf->render();  

        //if($post['type'] == 'print'){
            $dompdf->stream('challan.pdf', array("Attachment" => 0));
            return $this->response->setHeader('Content-Disposition','inline; filename="invoice.pdf"')
                                ->setContentType('application/pdf');
        // }else{
            // $dompdf->stream('challan.pdf', array("Attachment" => 1));
        // }
    }

    public function return_detail($id){
        if (!session('uid')) {
            return redirect()->to(url('auth'));
        }

        if($id != ''){
            $data = $this->gmodel->get_sales_return($id);   
        }
        // echo '<pre>';print_r($data);exit; 
        $data['title']="Return Detail";
        return view('Sales/sales_return_detail', $data);
    }
    
    public function invoice_detail($id){
        if (!session('uid')) {
            return redirect()->to(url('auth'));
        }

        if($id != ''){
            $data = $this->model->get_sales_invoice($id);   
        }
        //echo '<pre>';print_r($data);exit; 
        $data['title']="Invoice Detail";
        return view('Sales/sales_invoice_detail', $data);
    }

    public function general_detail($id){
        if (!session('uid')) {
            return redirect()->to(url('auth'));
        }

        if($id != ''){
            $data = $this->model->get_ACinvoice_byid($id);   
        }
        // echo '<pre>';print_r($data);exit; 
        $data['title']="General Detail";
        return view('Sales/sales_general_detail', $data);
    }

    public function add_salesinvoice($id=''){

        if (!session('cid')) {
            return redirect()->to(url('company'));
        }
        
        $data = array();
        $post = $this->request->getPost();

        if (!empty($post)) {
            $msg = $this->model->insert_edit_salesinvoice($post);
            return $this->response->setJSON($msg);
        }
        
        if ($id != '') {
             $data = $this->model->get_sales_invoice($id);
        }
        // echo '<pre>';print_r($data);exit;

        $tax_id = $this->gmodel->get_data_table('gl_group',array('name' => 'Duties and taxes'),'id');
        $tax = $this->gmodel->get_array_table('account',array('gl_group' =>$tax_id['id']),'name');
        
        $data['tax'] = $tax; 

        $getId = $this->gmodel->get_saleInv_id('sales_invoice');
        $data['current_id'] = $getId + 1;
        $data['id'] = $id;
        $data['title'] = "Add SalesInvoice";

        return view('Sales/create_salesinvoice', $data);
    }    


    public function add_salesreturn($id = ''){
        if (!session('cid')) {
             return redirect()->to(url('company'));
        }
        $data = array();
        $post = $this->request->getPost();
        
        if(!empty($post)) {
            $msg = $this->model->insert_edit_salesreturn($post);
            return $this->response->setJSON($msg);
        }
        if($id != '') {
            $data = $this->gmodel->get_sales_return($id);
        }

        $tax_id = $this->gmodel->get_data_table('gl_group',array('name' => 'Duties and taxes'),'id');
        $tax = $this->gmodel->get_array_table('account',array('gl_group' => $tax_id['id']),'name');
        
        $data['tax'] = $tax;
        $getId = $this->gmodel->get_return_id('sales_return');
        $data['current_id'] = $getId + 1;
        $data['id'] = $id;
        $data['title']="Sales Return";
        
        return view('Sales/create_salesreturn', $data);
    }

    public function add_ACinvoice($type,$id = '')
    {

        if (!session('uid')) {
            return redirect()->to(url('auth'));
        }
        $data = array();
        $post = $this->request->getPost();
        if (!empty($post)) {
            $msg = $this->model->insert_edit_ACinvoice($post);
            return $this->response->setJSON($msg);
        }

        if($id != '') {
            $data= $this->model->get_ACinvoice_byid($id);
        }

        $tax_id = $this->gmodel->get_data_table('gl_group',array('name' => 'Duties and taxes'),'id');
        $tax = $this->gmodel->get_array_table('account',array('gl_group' =>$tax_id['id']),'name');
          
        $data['tax'] = $tax; 
        $getId = $this->gmodel->get_general_id($type,'sales_ACinvoice');
        $data['current_id'] = $getId + 1;
        
        $data['id'] = $id;
        $data['type'] = $type;
        
        $data['title']="General Sales";
        return view('Sales/create_ac_invoice', $data);
    }

    

    public function ac_invoice(){
        if (!session('uid')) {
            return redirect()->to(url('Auth'));
        }
        
        $data['title']="General Sales";
        return view('Sales/ac_invoice',$data);
    }

    public function challan()
    {
        if (!session('cid')) {
            return redirect()->to(url('company'));
        }
        $data['title']="Sales Challan";
        
        return view('Sales/challan_view',$data);
    }
    public function salesinvoice()
    {
        if (!session('cid')) {
            return redirect()->to(url('company'));
        }
        $data['title']="Sales Invoice";
        return view('Sales/salesinvoice', $data);
    }
   
    public function salesreturn()
    {
        if (!session('cid')) {
            return redirect()->to(url('company'));
        }

        $data['title']="Sales Return";
        return view('Sales/salesreturn',$data);
    }
    
  
    
    public function Action($method = '') {
        $result = array();
       // print_r($method);exit;
        if ($method == 'Update') {
            $post = $this->request->getPost();
            $result = $this->model->UpdateData($post);
        }
        return $this->response->setJSON($result);
    }

    public function Getdata($method = '') {
        
        // if (!session('cid')) {
        //     return redirect()->to(url('Company'));
        // }

        $cid = session('cid');
        
        if ($method == 'challan') {
            $get = $this->request->getGet();
            $get['cid']=$cid;
            $this->model->get_challan_detail($get);
        }   
       
        if ($method == 'get_challan') {
            $post = $this->request->getPost();
            $data = $this->model->search_challan_data($post);
            return $this->response->setJSON($data);
        }

        if ($method == 'salesinvoice') {
            $get = $this->request->getGet();
            $get['cid']=$cid;
            $this->model->get_salesinvoice_data($get);
        }

        if ($method == 'salesreturn') {
            $get = $this->request->getGet();
            $this->model->get_salesreturn_data($get);
        }

        if($method == 'ac_invoice') {
            $get = $this->request->getGet();
            $this->model->get_ac_invoice($get);
        }
        
        if ($method == 'Item') {
            $post= $this->request->getPost();
            $data = $this->model->search_item_data(@$post['searchTerm']);
            return $this->response->setJSON($data);
        }

        if ($method == 'bank_cashAdvance') {
            $post= $this->request->getPost();
            $data = $this->model->get_BankCashAdvance($post);
            return $this->response->setJSON($data);
        }

        if ($method == 'search_sales_invoice') {
            $post = $this->request->getPost();
            $result = $this->model->get_Saleinvoice_databyid($post);
            return $this->response->setJSON($result);
        }
        if ($method == 'search_sale_general') {
            $post = $this->request->getPost();
            // print_r($post);exit;
            $result = $this->model->get_Salegeneral_databyid($post);
            return $this->response->setJSON($result);
        }
    }
}
?>