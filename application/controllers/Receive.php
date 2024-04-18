<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receive extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('stockinfo_model', 'sim');
    }

    public function getReceiveInfo(){
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        if($month == 'all'){
            $result = $this->sim->getReceiveInfoAllMonth($year);

        }else{
        $result = $this->sim->getReceiveInfo($year,$month);
        }
        //  echo "<pre>";
        // print_r($result);
        //  exit;
        echo json_encode($result);
    } 

    public function getEditReceiveDetail(){
        $isd_id = $this->input->get('isd_id');
        $result1 = $this->sim->getEditReceiveDetail($isd_id);
        $result2 = $this->sim->getEditReceiveDetailAll();
        $results = array(
            'result1' => $result1,
            'result2' => $result2
        );
        
        // Return the array containing both results
        echo json_encode($results);
    } 

    // public function getReceiveEdit(){
    //     $result = $this->sim->getReceiveEdit();
    //     echo json_encode($result);
    // } 

    public function getReceiveDetail(){
        $inv = $this->input->get('inv');
        $result = $this->sim->getReceiveDetail($inv);
        //  echo "<pre>";
        // print_r($result);
        //  exit;
        echo json_encode($result);
    } 

    public function getProductDetail(){
        $mpc_id = $this->input->get('mpc_id');
        $result = $this->sim->getProductDetail($mpc_id);
        //  echo "<pre>";
        // print_r($result);
        //  exit;
        echo json_encode($result);
    } 

    public function getModelById(){
        $id = $this->input->post('id');
        $result = $this->sim->getModelById($id);
        echo json_encode($result);
    } 

    public function ListProductDetail(){
        $id = $this->input->post('doc_id');
        $result = $this->sim->getListProductDetail($id);
        //  echo "<pre>";
        // print_r($result);
        //  exit;
        echo json_encode($result);
    } 

    public function ListProductDetailAll(){
        $result = $this->sim->getReceiveDetailAll();
        //  echo "<pre>";
        // print_r($result);
        //  exit;
        echo json_encode($result);
    } 

    public function getSelProductCode(){
        $result = $this->sim->getSelProductCode();
        //  echo "<pre>";
        // print_r($result);
        //  exit;
        echo json_encode($result);
    } 
    

    public function getSelProductCodeIssue(){
        $result = $this->sim->getSelProductCodeIssue();
        //  echo "<pre>";
        // print_r($result);
        //  exit;
        echo json_encode($result);
    } 

    public function getSelIndexBox(){
        $result = $this->sim->getSelIndexBox();
        //  echo "<pre>";
        // print_r($result);
        //  exit;
        echo json_encode($result);
    } 

    public function getselBrand(){
        $result = $this->sim->getselBrand();
        echo json_encode($result);
    } 

    public function insertReceive() {
        $data = [
            'isd_doc_number' => $this->input->post('doc_number'),
            'isd_doc_date' => $this->input->post('doc_date'),
            'isd_inv_no' => $this->input->post('invoice_number'),
            'isd_inv_date' => $this->input->post('invoice_date'),
            'isd_po_number' => $this->input->post('purchase_order'),
            'isd_po_date' => $this->input->post('purchase_order_date'),
            'isd_customer' => $this->input->post('supplier_name'),
            'mb_id' => $this->input->post('brand_id'),
            'mpc_id' => $this->input->post('product_id'),
            'isd_qty' => $this->input->post('qty'),
            'isd_price_unit' => $this->input->post('price'),

        ];
        $file_inventory = $_FILES['file_inventory']['name'];
        $data['isd_file'] = $file_inventory;
        $response = $this->sim->insertReceive($data);

        return json_encode($response);

    }

    public function updateReceive() {
        $isd_id = $this->input->post('ProductId');
        $data = [

            'mpc_id' => $this->input->post('product_id'),
            'isd_qty' => $this->input->post('qty'),
            'isd_price_unit' => $this->input->post('price'),

        ];
        // var_dump($data);exit();
        $response = $this->sim->getUpdateReceive($data,$isd_id);

        return json_encode($response);

    }
    public function deleteReceive() {
        $isd_id = $this->input->post('id');
        // var_dump($data);exit();
        $response = $this->sim->getDeleteReceive($isd_id);

        return json_encode($response);

    }

    public function show_Edit_Ac(){
        $result = $this->apimd->get_account();
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 
    

    public function showAccount($id = null) {
        $id=1;
        // ส่งพารามิเตอร์ $id ไปยังฟังก์ชัน get_Shipping_db ของ Model
        $result = $this->dash->get_account($id);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    }
    public function chk_login() {

        $data = unserialize($this->input->post("data"));
        //$sess = unserialize($this->input->post("session"));
        $result = $this->dash->chk_login_db($data['requestData']);
        echo json_encode($result);
       
    }

    public function logout() {

        $data = unserialize($this->input->post("data"));
        //$sess = unserialize($this->input->post("session"));
        $result = $this->dash->logout_db($data);
        echo json_encode($result);
       
    }

}