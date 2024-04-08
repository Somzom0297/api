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

    public function getReceiveEdit(){
        $result = $this->sim->getReceiveE();
        echo json_encode($result);
    } 

    public function getReceiveDetail(){
        $inv = $this->input->post('inv');
        $result = $this->sim->getReceiveDetail($inv);
        //  echo "<pre>";
        // print_r($result);
        //  exit;
        echo json_encode($result);
    } 

    public function getModelById(){
        $id = $this->input->post('id');
        $result = $this->sim->getModelById($id);
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

    public function addReceive() {
        // Handle form submission
        // Retrieve form data

        $data = [
            'documentNumber' => $this->input->post('inpAddDoc'),
            'documentDate' => $this->input->post('inpAddDocDate'),
            'isd_inv_no' => $this->input->post('inpAddInv'),
            'isd_inv_date' => $this->input->post('inpAddInvDate'),
            'purchaseOrder' => $this->input->post('inpAddPo'),
            'purchaseOrderDate' => $this->input->post('inpAddPoDate'),
            'supplierName' => $this->input->post('inpAddSupplier')
        ];


        // Handle file upload
        if (!empty($_FILES['inpAddFileInv']['name'])) {
            $config['upload_path'] = './uploads/'; // Specify the upload directory
            $config['allowed_types'] = 'pdf|doc|docx'; // Specify allowed file types
            $config['max_size'] = 1024; // Maximum file size in kilobytes

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('inpAddFileInv')) {
                $fileData = $this->upload->data();
                $filePath = $fileData['file_name'];

                // Process file upload - save file path to database or perform any other action
            } else {
                // Handle file upload error
                $uploadError = $this->upload->display_errors();
                echo $uploadError;
            }
        }

        // Perform any additional processing - e.g., saving data to database

        // Send response back to the client
        $response = array(
            'status' => 'success',
            'message' => 'Data received successfully'
        );
        echo json_encode($response);
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