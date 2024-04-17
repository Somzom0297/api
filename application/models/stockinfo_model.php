<?php
defined('BASEPATH') or exit('No direct script access allowed');

class stockinfo_model extends CI_Model
{

    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    public function getStockinfo(){
        $sql = "SELECT
        mst_brand.mb_name, 
        mst_product_code.mpc_name,
        mst_product_code.mpc_model,
        info_stock_detail.description,
        info_stock_detail.Qty,
        sum(info_item_reserve.iir_reserve_qty)as Total,
        info_item_reserve.isd_id
        
        FROM
        info_stock_detail
        LEFT JOIN mst_brand ON mst_brand.mb_id = info_stock_detail.mb_id
        LEFT JOIN mst_product_code ON mst_product_code.mpc_id = info_stock_detail.mpc_id
        LEFT JOIN info_item_reserve on info_item_reserve.isd_id = info_stock_detail.isd_id
        GROUP BY info_item_reserve.isd_id";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }
    public function getReceiveInfo($year, $month){
        $sql = "SELECT 
                isd_doc_number,
                isd_inv_date,
                isd_inv_no,
                isd_po_number, 
                COUNT(isd_inv_no) as total
                FROM  info_stock_detail as isd
                WHERE YEAR(isd_inv_date) = '$year' AND MONTH(isd_inv_date) = '$month'
                GROUP BY isd_inv_no
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function getReceiveInfoAllMonth($year){
        $sql = "SELECT 
                isd_doc_number,
                isd_inv_date,
                isd_inv_no,
                isd_po_number, 
                COUNT(isd_inv_no) as total
                FROM  info_stock_detail as isd
                WHERE YEAR(isd_inv_date) = '$year'
                GROUP BY isd_inv_no
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function getReceiveDetail($data){
        $sql = "SELECT 
                    isd_doc_number,
                    isd_inv_date,
                    isd_inv_no,
                    isd_po_number,
                    mb.mb_name,
                    mpc.mpc_name,
                    mpc.mpc_model,
                    mpc.mpc_discription,
                    isd.isd_id,
                    isd_customer,
                    isd_po_date,
                    isd_doc_date,
                    isd_qty,
                    isd_price_unit

                FROM  info_stock_detail as isd
                LEFT JOIN mst_product_code mpc ON mpc.mpc_id = isd.mpc_id
                LEFT JOIN mst_brand mb ON mb.mb_id = mpc.mb_id
                    where isd_inv_no = '$data'
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function getProductDetail($data){
        $sql = "SELECT 
                    mb.mb_name,
                    mpc.mpc_name,
                    mib.mib_number,
                    mib.mib_size,
                    mpc.mpc_model,
                    mpc.mpc_discription,
                    isd.isd_id,
                    isd.isd_qty,
                    (
                        SELECT SUM(isd_qty) 
                        FROM info_stock_detail 
                        WHERE mpc_id = '1'
                    ) AS total_qty,
                    isd_price_unit

                FROM  info_stock_detail as isd
                LEFT JOIN mst_product_code mpc ON mpc.mpc_id = isd.mpc_id
                LEFT JOIN mst_index_box mib ON mib.mib_id = isd.mib_id
                LEFT JOIN mst_brand mb ON mb.mb_id = mpc.mb_id
                    where isd.mpc_id = '$data'
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function getEditReceiveDetail($data){
        $sql = "SELECT
                    info_stock_detail.isd_id,
                    info_stock_detail.mpc_id,
                    mpc_name,
                    mib_number,
                    mib_size,
                    mb_name,
                    mpc_model,
                    mpc_discription,
                    isd_qty,
                    isd_price_unit
                FROM
                    `info_stock_detail`
                    LEFT JOIN mst_product_code ON info_stock_detail.mpc_id = mst_product_code.mpc_id
                    LEFT JOIN mst_index_box ON mst_product_code.mib_id = mst_index_box.mib_id
                    LEFT JOIN mst_brand ON mst_product_code.mb_id = mst_brand.mb_id
                WHERE isd_id = '$data'
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function getEditReceiveDetailAll(){
        $sql = "SELECT
                    mpc_id,
                    mpc_name,
                    mib_number,
                    mib_size,
                    mb_name,
                    mpc_model,
                    mpc_discription
                FROM
                    `mst_product_code`

                INNER JOIN mst_index_box ON mst_product_code.mib_id = mst_index_box.mib_id
                INNER JOIN mst_brand ON mst_product_code.mb_id = mst_brand.mb_id
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function getReceiveDetailAll(){
        $sql = "SELECT 
        mpc.mpc_name,
        isd.isd_doc_number,
        isd.isd_inv_date,
        isd.isd_inv_no,
        isd.isd_po_number,
        mb.mb_name,
        mpc.mpc_id,
        mpc.mpc_model,
        mpc.mpc_discription,
        COALESCE(isd.isd_qty, 0) AS isd_qty,
        isd.isd_created_date
    FROM  
        mst_product_code AS mpc
    LEFT JOIN 
        (
            SELECT 
                isd.*, 
                MIN(isd_created_date) AS min_created_date
            FROM 
                info_stock_detail AS isd
            GROUP BY 
                isd.mpc_id
        ) AS min_isd ON mpc.mpc_id = min_isd.mpc_id
    LEFT JOIN 
        info_stock_detail AS isd ON min_isd.isd_id = isd.isd_id
    LEFT JOIN 
        mst_brand AS mb ON mb.mb_id = mpc.mb_id
    ORDER BY 
        isd_qty DESC
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function getListProductDetail(){

        $sql = "SELECT
                    mb_name,
                    mpc_name,
                    mpc_model,
                    mpc_discription,
                    isd_qty
                FROM
                    `info_stock_detail`
                LEFT JOIN mst_brand ON mst_brand.mb_id = info_stock_detail.mb_id
                LEFT JOIN mst_product_code ON mst_product_code.mpc_id = info_stock_detail.mpc_id
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function getSelProductCode(){
        $sql = "SELECT 
                    mpc_id,
                    mpc_name
                FROM  mst_product_code
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function getSelIndexBox(){
        $sql = "SELECT 
                    mib_id,
                    mib_number,
                    mib_size
                FROM  mst_index_box
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function getselBrand(){
        $sql = "SELECT 
                    mb_id,
                    mb_name

                FROM  mst_brand
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function getModelById($id){
        $sql = "SELECT 
                    mpc_model,
                    mpc_discription,
                    mpc_name,
                    mib_number,
                    mst_index_box.mib_id,
                    mst_brand.mb_id,
                    mib_size,
                    mb_name
                FROM  mst_product_code
                INNER JOIN mst_index_box ON mst_index_box.mib_id = mst_product_code.mib_id
                INNER JOIN mst_brand ON mst_brand.mb_id = mst_product_code.mb_id
                WHERE mpc_id = $id
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function insertReceive($data) {
        // Perform database insert operation
        $this->db->insert('info_stock_detail', $data);

        // Check if insert was successful
        return $this->db->affected_rows() > 0 ? true : false;
    }

    public function getUpdateReceive($data,$id) {
        
        $mpc_id = $data["mpc_id"];
        $isd_qty = $data["isd_qty"];
        $isd_price_unit = $data["isd_price_unit"];
        // return $data;
        // exit;
        $sql_show_acc = "UPDATE info_stock_detail
        SET mpc_id = '$mpc_id', isd_qty = '$isd_qty', isd_price_unit = '$isd_price_unit'
        WHERE isd_id = '$id';
        ";

        $query = $this->db->query($sql_show_acc);
        // var_dump($sql_show_acc);exit();
        if ($this->db->affected_rows() > 0) {
            return array('result' => 1);
        } else {
            return array('result' => 0);
        }
        // Perform database insert operation

    }
    public function getDeleteReceive($id) {
        // Perform the delete operation
        $this->db->where('isd_id', $id);
        $this->db->delete('info_stock_detail');
        
        // Check if the delete operation was successful
        return $this->db->affected_rows() > 0;
    }

    public function show_drop_down(){
        $sql1 = "SELECT spg_id,spg_name From sys_permission_group";
        $query = $this->db->query($sql1);

        foreach ($query->result() as $key => $value) {
            $arr['permission'][] = $value;
        }
        $sql2 = "SELECT mpc_id,mpc_code,mpc_name From mst_plant_code";
        $query = $this->db->query($sql2);

        foreach ($query->result() as $key => $value) {
            $arr['plantcode'][] = $value;
        }

        return $arr;
    }

    public function insert_user($data, $sess){
        $empcode = $data["EmpCode"];
        $password = md5($data["EmpPassword"]);
        $firstname = $data["EmpFirstName"];
        $lastname = $data["EmpLastName"];
        $email = $data["EmpEmail"];
        $permisgroup = $data["EmpPermission"];
        $plant = $data["EmpPlantCode"];

        $sql_check_duplicate = "SELECT * FROM sys_account WHERE sa_emp_code = '$empcode'";
        $query_check_duplicate = $this->db->query($sql_check_duplicate);

        // ใช้ num_rows() เพื่อนับจำนวนแถวที่ถูกพบ
        if ($query_check_duplicate->num_rows() > 0) {
            return array('result' => 9); // มีข้อมูลซ้ำ
        } else {
            $sql_insert = "INSERT INTO sys_account (sa_emp_code, sa_emp_password, sa_firstname, sa_lastname, sa_email, spg_id, mpc_id, sa_created_by, sa_created_date, sa_status_flg) 
                           VALUES ('$empcode', '$password', '$firstname', '$lastname', '$email', '$permisgroup', '$plant', '$sess', NOW(), 1)";

            $query = $this->db->query($sql_insert);

            if ($this->db->affected_rows() > 0) {
                return array('result' => 1); // Insert สำเร็จ
            } else {
                return array('result' => 0); // Insert ล้มเหลว
            }
        }
    }

    public function show_show_acc($data){
        $id = $data["id"];
        // return $id;
        // exit;

        $sql_show_acc = "SELECT * FROM sys_account WHERE sa_id = '$id';";

        $query = $this->db->query($sql_show_acc);
        $data = $query->row();
        if ($this->db->affected_rows() > 0) {
            return array('result' => true, 'data' => $data);
        } else {
            return array('result' => false);
        }
    }

    public function update_status($data){
        $id = $data["saId"];
        $stt = $data["newStatus"];


        // return $data;
        // exit;
        $sql_show_acc = "UPDATE sys_account
        SET sa_status_flg = '$stt'
        WHERE sa_id = '$id';
        ";

        $query = $this->db->query($sql_show_acc);
        if ($this->db->affected_rows() > 0) {
            return array('result' => 1);
        } else {
            return array('result' => 0);
        }
    }


    public function update_flg($data){
        $stFlg = $data["newStatus"];
        $saId = $data["saId"];

        $sql = "UPDATE sys_account 
        SET sa_status_flg = '$stFlg'
        WHERE sa_id = '$saId';";

        $query = $this->db->query($sql);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }


    public function show_update_acc($data, $sess){
        $id = $data["EmpId"];
        $empcode = $data["EmpCode"];
        $password = md5($data["EmpPassword"]);
        $permisgroup = $data["EmpPermission"];
        $firstname = $data["EmpFirstName"];
        $lastname = $data["EmpLastName"];
        $email = $data["EmpEmail"];
        $flag = $data["EmpFlag"];
        $plant = $data["EmpPlantCode"];

        // return $data;
        // exit;
        $sql_show_acc = "UPDATE sys_account
        SET sa_emp_code = '$empcode', 
        sa_emp_password = '$password', 
        spg_id = '$permisgroup', 
        sa_firstname = '$firstname',
        sa_lastname = '$lastname',
        sa_email = '$email',
        sa_status_flg = '$flag',
        sa_updated_date = NOW(),
        sa_updated_by = '$sess',
        mpc_id = '$plant'
        WHERE sa_id = '$id';
        ";

        $query = $this->db->query($sql_show_acc);
        if ($this->db->affected_rows() > 0) {
            return array('result' => 1);
        } else {
            return array('result' => 0);
        }
    }


    public function update_user($data, $sess){
        $empcode = $data["EmpCode"];
        $password = ($data["EmpPassword"] != '') ? md5($data["EmpPassword"]) : NULL;
        $firstname = $data["EmpFirstName"];
        $lastname = $data["EmpLastName"];
        $email = $data["EmpEmail"];
        $permisgroup = $data["EmpPermission"];
        $plant = $data["EmpPlantCode"];

        $data_chk_user = $this->get_user_data($empcode);

        if ($data_chk_user->sa_emp_password == $password || $password === NULL) {
            $sql_update_nopass = "
                UPDATE sys_account
                SET sa_emp_code= '$empcode', 
                    sa_firstname= '$firstname',
                    sa_lastname= '$lastname',
                    sa_email= '$email',
                    spg_id= '$permisgroup',
                    mpc_id= '$plant',
                    sa_updated_date= NOW(),
                    sa_updated_by= '$sess'
                WHERE sa_emp_code= '$empcode';
            ";

            $query_nopass = $this->db->query($sql_update_nopass);

            if ($this->db->affected_rows() > 0) {
                return array('result' => 1);
            } else {
                return array('result' => 0);
            }
        } else {
            $sql_update = "
                UPDATE sys_account
                SET sa_emp_code= '$empcode', 
                    sa_emp_password= '$password',
                    sa_firstname= '$firstname',
                    sa_lastname= '$lastname',
                    sa_email= '$email',
                    spg_id= '$permisgroup',
                    mpc_id= '$plant',
                    sa_updated_date= NOW(),
                    sa_updated_by= '$sess'
                WHERE sa_emp_code= '$empcode';
            ";

            $query_update = $this->db->query($sql_update);

            if ($this->db->affected_rows() > 0) {
                return array('result' => 1); // อัปเดตสำเร็จ
            } else {
                return array('result' => 0); // ไม่สามารถอัปเดต
            }
        }
    }

    private function get_user_data($empcode){
        $sql_select = "
            SELECT *
            FROM sys_account
            WHERE sa_emp_code = '$empcode'
        ";

        $query_select = $this->db->query($sql_select);
        return $query_select->row();
    }


    public function show_upd_User($data, $sess){
        $empcode = $data["EmpCode"];
        $password = md5($data["EmpPassword"]);
        $firstname = $data["EmpFirstName"];
        $lastname = $data["EmpLastName"];
        $email = $data["EmpEmail"];
        $permisgroup = $data["EmpPermission"];
        $plant = $data["EmpPlantCode"];

        $sql_check_duplicate = "SELECT * FROM sys_account WHERE sa_emp_code = '$empcode'";
        $query_check_duplicate = $this->db->query($sql_check_duplicate);

        // ใช้ num_rows() เพื่อนับจำนวนแถวที่ถูกพบ
        if ($query_check_duplicate->num_rows() > 0) {
            return array('result' => 9); // มีข้อมูลซ้ำ
        } else {
            $sql_insert = "INSERT INTO sys_account (sa_emp_code, sa_emp_password, sa_firstname, sa_lastname, sa_email, spg_id, mpc_id, sa_created_by, sa_created_date, sa_status_flg) 
                           VALUES ('$empcode', '$password', '$firstname', '$lastname', '$email', '$permisgroup', '$plant', '$sess', NOW(), 1)";

            $query = $this->db->query($sql_insert);

            if ($this->db->affected_rows() > 0) {
                return array('result' => 1); // Insert สำเร็จ
            } else {
                return array('result' => 0); // Insert ล้มเหลว
            }
        }
    }
}
