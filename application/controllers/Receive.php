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
    
    public function getIssueInfo(){
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        if($month == 'all'){
            $result = $this->sim->getIssueInfoAllMonth($year);

        }else{
        $result = $this->sim->getIssueInfo($year,$month);
        }
        //  echo "<pre>";
        // print_r($result);
        //  exit;
        echo json_encode($result);
    } 
    // function pdf()
    // {
    //     $this->load->helper('pdf_helper');
    //     /*
    //         ---- ---- ---- ----
    //         your code here
    //         ---- ---- ---- ----
    //     */
    //     $this->load->view('pdfreport', $data);
    // }
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

    public function getStockInfo(){

        $result = $this->sim->getStockInfo();
        //  echo "<pre>";
        // print_r($result);
        //  exit;
        echo json_encode($result);
    } 

    public function getIssueDetail(){
        $doc_id = $this->input->get('doc_id');
        $result = $this->sim->getIssueDetail($doc_id);
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

    public function getModelByIdPname(){
        $id = $this->input->post('id');
        $result = $this->sim->getModelByIdPname($id);
        echo json_encode($result);
    } 

    public function showProductIssue(){
        $result = $this->sim->getProductIssue();
        echo json_encode($result);
    } 
    

    public function getBrandAll(){
        $result = $this->sim->getBrandAll();
        echo json_encode($result);
    } 

    public function getIndexAll(){
        $result = $this->sim->getIndexAll();
        echo json_encode($result);
    } 

    public function getIndexSize(){
        $result = $this->sim->getIndexSize();
        echo json_encode($result);
    } 

    public function insertProduct(){
        $productName = $this->input->post('product');
        $BrandName = $this->input->post('brand');
        $ModelName = $this->input->post('model');

        // Check if the product name already exists in the database
        $productExists = $this->sim->checkProductExists($productName);
        $brandExists = $this->sim->checkBrandExists($BrandName);
        $ModelExists = $this->sim->checkModelExists($ModelName);
        // If product exists, return an alert
        // var_dump($brandExists);exit();
        if($productExists) {
            echo json_encode(array('success' => 'false'));
            return;
        }
        if($ModelExists) {
            echo json_encode(array('success' => 'falseModel'));
            return;
        }
        $maxValue = 0;
        if($brandExists !== NULL) {
           $maxValue = $brandExists;
        }else{
            $databBrand = [
                'mb_name' => $BrandName,
                'mb_status_flg' => '1',
            ];
            $this->sim->insertBrand($databBrand);
            $maxValue = $this->sim->getMaxValue();
        }

        $data = [
            'mpc_name' => $productName,
            'mib_id' => $this->input->post('index'),
            'mb_id' => $maxValue,
            'mpc_model' => $ModelName,
            'mpc_discription' => $this->input->post('dis'),
            'mpc_status_flg' => '1',
            'mpc_unit' => $this->input->post('unit'),
            'mpc_sell_price' => $this->input->post('unitprice'),
        ];

        $dataIndex = [
            'mib_number' => $this->input->post('index'),
            'mib_size' => $this->input->post('size'),
            'mib_status_flg' => '1',
        ];

//         $this->load->library('upload');

// $target_dir = "./assets/img/";
// $target_file = $target_dir . basename($_FILES["file_product"]["name"]);

// if (move_uploaded_file($_FILES["file_product"]["tmp_name"], $target_file)) {
    $file_product = $_FILES['file_product']['name'];
    $data['mpc_img'] = $file_product;
   // Store the file name in your database
// } else {
//     echo json_encode(array('success' => 'filee', 'message' => $target_file . 'Error uploading file.'));
//     return;
// }

        
        $this->sim->insertProduct($data);
        $this->sim->insertIndex($dataIndex);
        echo json_encode(array('success' => 'true'));
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

    public function getConfirmProductDetail(){
        $result = $this->sim->getConfirmProductDetail();
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

        $result1 = $this->sim->getSelProductCodeIssue();
        $result2 = $this->sim->getSelProductCodeIssueAll();
        $results = array(
            'result1' => $result1,
            'result2' => $result2
        );
        
        // Return the array containing both results
        echo json_encode($results);
    } 
        
        //  echo "<pre>";
        // print_r($result);
        //  exit;

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
    public function insertIssueConfirm() {

        $response = $this->sim->insertIssueConfirm();

        return json_encode($response);

    }

    public function insertIssue() {

        $data = [
            'isd_id' => $this->input->post('isd_id'),
            'isi_document' => $this->input->post('doc_number'),
            'isi_document_date' => $this->input->post('doc_date'),
            'isi_invoice' => $this->input->post('invoice_number'),
            'isi_invoice_date' => $this->input->post('invoice_date'),
            'isi_purchase_order' => $this->input->post('purchase_order'),
            'isi_purchase_order_date' => $this->input->post('purchase_order_date'),
            'isi_customer' => $this->input->post('customer'),
            'isi_qty' => $this->input->post('qty'),
            'isi_unit_type' => $this->input->post('Unit'),
            'isi_priceofunit' => $this->input->post('price'),
            'lsi_status_flg' => '0',

        ];

        $response = $this->sim->insertIssue($data);

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

        echo json_encode(['success' => true]);

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