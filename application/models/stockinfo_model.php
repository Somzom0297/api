<?php
defined('BASEPATH') or exit('No direct script access allowed');

class stockinfo_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function getStockinfoRe()
    {
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
    public function getStockinfo()
    {
        $sql = "SELECT
        info_stock_detail.*,
        mst_product_code.*,
        mst_brand.*,
        (SUM(isd_qty) - ( SELECT SUM( isi_qty ) FROM info_stock_issue LEFT JOIN info_stock_detail isdiner ON isdiner.isd_id = info_stock_issue.isd_id WHERE info_stock_detail.mpc_id = info_stock_detail.mpc_id )) as qtyy
        FROM
            mst_product_code
        
              LEFT JOIN info_stock_detail ON mst_product_code.mpc_id = info_stock_detail.mpc_id
                    LEFT JOIN mst_brand ON mst_brand.mb_id = mst_product_code.mb_id
        GROUP BY mst_product_code.mpc_id";

            $query = $this->db->query($sql);
            $data = $query->result();
            return $data;
    }
    public function getReceiveInfo($year, $month)
    {
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
    public function getIssueInfo($year, $month)
    {
        $sql = "SELECT 
        isi_document,
        isi_document_date,
        COUNT(isd_id) as total
        FROM  info_stock_issue as isi
        WHERE YEAR(isi_document_date) = '$year' AND MONTH(isi_document_date) = '$month'
        GROUP BY isi_document_date
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function getIssueInfoAllMonth($year)
    {
        $sql = "SELECT 
        isi_document,
        isi_document_date,
        COUNT(isd_id) as total
        FROM  info_stock_issue as isi
        WHERE YEAR(isi_document_date) = '$year'
        GROUP BY isi_document_date
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function getReceiveInfoAllMonth($year)
    {
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

    public function getReceiveDetail($data)
    {
        $sql = "SELECT 
                    isd_doc_number,
                    isd_inv_date,
                    isd_inv_no,
                    isd_po_number,
                    mb.mb_name,
                    mpc.mpc_img,
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

    public function getIssueDetail($data)
    {
        $sql = "SELECT 
                isi_document,
                isi_document_date,
                mb.mb_name,
				mpc.mpc_name,
				mpc.mpc_model,
				isi_qty,
				isi_unit_type,
				isi_priceofunit,
				isi_purchase_order,
				isd_customer,
				isi_invoice,
				isi_invoice_date,
				mib_number,
				mib_size
                isd_inv_date,
                isd_inv_no,
                isd_po_number,
                mpc.mpc_img,
                mpc.mpc_discription,
                isd.isd_id,
                isd_po_date,
                isd_doc_date,
                isd_qty,
				isd_price_unit
        FROM  info_stock_issue as isi
        LEFT JOIN info_stock_detail isd ON isi.isd_id = isd.isd_id
        LEFT JOIN mst_product_code mpc ON mpc.mpc_id = isd.mpc_id
        LEFT JOIN mst_brand mb ON mb.mb_id = mpc.mb_id
	    LEFT JOIN mst_index_box ON mst_index_box.mib_id = mpc.mib_id
        where isi_document = '$data'
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function getProductDetail($id)
    {
        $sql = "SELECT 
                    mb.mb_name,
                    mpc.mpc_name,
                    mib.mib_number,
                    mib.mib_size,
                    mpc.mpc_model,
                    mpc.mpc_discription,
                    isd.isd_id,
                    isd.isd_doc_number,
                    isd.isd_doc_date,
                    isd.isd_qty,
                    (
                        SELECT SUM(isd_qty) 
                        FROM info_stock_detail 
                        WHERE mpc_id = '$id'
                    ) AS total_qty,
                    isd_price_unit

                FROM  info_stock_detail as isd
                LEFT JOIN mst_product_code mpc ON mpc.mpc_id = isd.mpc_id
                LEFT JOIN mst_index_box mib ON mib.mib_id = mpc.mib_id
                LEFT JOIN mst_brand mb ON mb.mb_id = mpc.mb_id
                    where isd.mpc_id = '$id'
                ";

        $query = $this->db->query($sql);
        $data = $query->result();

        if (empty($data)) {
            $sql = "SELECT 
            * 
            FROM mst_product_code 
                LEFT JOIN mst_index_box mib ON mib.mib_id = mst_product_code.mib_id
                LEFT JOIN mst_brand mb ON mb.mb_id = mst_product_code.mb_id
            WHERE mpc_id = '$id'
            
            ";
            $query = $this->db->query($sql);
            $data = $query->result();
        }
        return $data;
    }

    public function getEditReceiveDetail($data)
    {
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

    public function getEditReceiveDetailAll()
    {
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

    public function getReceiveDetailAll()
    {
        $sql = "SELECT 
            mpc.mpc_img,
            mpc.mpc_name,
            isd.isd_doc_number,
            isd.isd_inv_date,
            isd.isd_inv_no,
            isd.isd_po_number,
            mb.mb_name,
            mpc.mpc_id,
            mpc.mpc_model,
            mpc.mpc_discription,
            COALESCE(SUM(isd.isd_qty), 0) AS isd_qty,
            MIN(isd.isd_created_date) AS isd_created_date
        FROM  
            mst_product_code AS mpc
        LEFT JOIN 
            info_stock_detail AS isd ON mpc.mpc_id = isd.mpc_id
                LEFT JOIN 
            mst_brand AS mb ON mb.mb_id = mpc.mb_id
        GROUP BY 
            mpc.mpc_id, mpc.mpc_name, mpc.mpc_model, mpc.mpc_discription
        ORDER BY 
            isd_qty DESC;
                    ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function getListProductDetail($data)
    {

        $sql = "SELECT
                    mb_name,
                    mpc_name,
                    mpc_model,
                    mpc_discription,
                    isd_qty,
                    isd_price_unit
                FROM
                    `info_stock_detail`
                LEFT JOIN mst_product_code ON mst_product_code.mpc_id = info_stock_detail.mpc_id
                LEFT JOIN mst_brand ON mst_brand.mb_id = mst_product_code.mb_id
                WHERE isd_doc_number = '$data'
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function getSelProductCode()
    {
        $sql = "SELECT 
                    mpc_id,
                    mpc_name
                FROM  mst_product_code
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function getBrandAll()
    {
        $sql = "SELECT 
                    mb_id,
                    mb_name
                FROM  mst_brand
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function getIndexAll()
    {
        $sql = "SELECT 
                   MAX(mib_number)+1 AS mib_number
                FROM  mst_index_box
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function getSelProductCodeIssue()
    {
        $sql = "SELECT 
                    info_stock_detail.isd_id,
                    info_stock_detail.mpc_id,
                    mst_product_code.mpc_name
                FROM  info_stock_detail
                LEFT JOIN mst_product_code ON mst_product_code.mpc_id = info_stock_detail.mpc_id
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function getSelProductCodeIssueAll()
    {
        $sql = "SELECT 
                    mst_product_code.mpc_id,
                    mst_product_code.mpc_name,
                    info_stock_detail.isd_id
                FROM  mst_product_code
                LEFT JOIN info_stock_detail ON mst_product_code.mpc_id = info_stock_detail.mpc_id
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function getSelIndexBox()
    {
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

    public function getIndexSize()
    {
        $sql = "SELECT 
                    mib_size
                FROM  mst_index_box
                GROUP BY mib_size
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function getselBrand()
    {
        $sql = "SELECT 
                    mb_id,
                    mb_name

                FROM  mst_brand
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function getModelById($id)
    {
        $sql = "SELECT 
                            mpc_model,
                            mpc_discription,
                            mpc_name,
                            mib_number,
                            mpc_unit,
                            mst_index_box.mib_id,
                            mst_brand.mb_id,
                            mib_size,
                            mb_name,
                            isd_id,
                            mpc_cost_price,
                            (SUM(isd_qty) - ( SELECT SUM( isi_qty ) FROM info_stock_issue LEFT JOIN info_stock_detail isdiner ON isdiner.isd_id = info_stock_issue.isd_id WHERE info_stock_detail.mpc_id = info_stock_detail.mpc_id )) as total

                            
                            FROM  mst_product_code
                            LEFT JOIN mst_index_box ON mst_index_box.mib_id = mst_product_code.mib_id
                            LEFT JOIN mst_brand ON mst_brand.mb_id = mst_product_code.mb_id
                            LEFT JOIN info_stock_detail ON info_stock_detail.mpc_id = mst_product_code.mpc_id
                            WHERE mst_product_code.mpc_id = '$id'
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }
    

    public function getModelByIdPname($id)
    {
        $sql = "SELECT 
                            mpc_model,
                            mpc_discription,
                            mpc_name,
                            mib_number,
                            mst_index_box.mib_id,
                            mst_brand.mb_id,
                            mib_size,
                            mb_name,
                            isd_id,
                            mpc_cost_price,
                            (SUM(isd_qty) - ( SELECT SUM( isi_qty ) FROM info_stock_issue LEFT JOIN info_stock_detail isdiner ON isdiner.isd_id = info_stock_issue.isd_id WHERE info_stock_detail.mpc_id = info_stock_detail.mpc_id )) as total

                            
                            FROM  mst_product_code
                            LEFT JOIN mst_index_box ON mst_index_box.mib_id = mst_product_code.mib_id
                            LEFT JOIN mst_brand ON mst_brand.mb_id = mst_product_code.mb_id
                            LEFT JOIN info_stock_detail ON info_stock_detail.mpc_id = mst_product_code.mpc_id
                            WHERE mst_product_code.mpc_name = '$id'
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function getProductIssue(){
        $sql = "SELECT DISTINCT
                    mst_product_code.mpc_id,
                    mpc_img,
                    mb_name,
                    mpc_name,
                    mpc_model,
                    mpc_discription,
                    mib_number,
                    mib_size,
                    info_stock_detail.isd_id,
                    ( SELECT SUM( isd_qty ) FROM info_stock_detail WHERE info_stock_detail.mpc_id = mst_product_code.mpc_id ) AS qty,
                    ( SELECT SUM( isi_qty ) FROM info_stock_issue LEFT JOIN info_stock_detail isdiner ON isdiner.isd_id = info_stock_issue.isd_id WHERE info_stock_detail.mpc_id = info_stock_detail.mpc_id ) AS out_qty 
                FROM
                    mst_product_code
                LEFT JOIN mst_index_box ON mst_index_box.mib_id = mst_product_code.mib_id
                LEFT JOIN mst_brand ON mst_brand.mb_id = mst_product_code.mb_id
                LEFT JOIN info_stock_detail ON info_stock_detail.mpc_id = mst_product_code.mpc_id
                LEFT JOIN info_stock_issue ON info_stock_detail.isd_id = info_stock_issue.isd_id 
                GROUP BY
                    mst_product_code.mpc_id,
                    mpc_img,
                    mb_name,
                    mpc_name,
                    mpc_model,
                    mpc_discription,
                    mib_number,
                    mib_size;
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function getConfirmProductDetail(){
        $sql = "SELECT 
        mpc.mpc_img,
        mb.mb_name,
        mpc.mpc_name,
        mpc.mpc_model,
        mpc.mpc_discription,
        lsi.isd_id,
        isi_document,
        isi_document_date,
        isi_invoice,
        isi_invoice_date,
        isi_purchase_order,
        isi_purchase_order_date,
        isi_qty,
        isi_customer,
        isi_priceofunit,
        isi_unit_type

        FROM  log_stock_issue as lsi
        LEFT JOIN info_stock_detail isd ON lsi.isd_id = isd.isd_id
        LEFT JOIN mst_product_code mpc ON mpc.mpc_id = isd.mpc_id
        LEFT JOIN mst_brand mb ON mb.mb_id = mpc.mb_id
        LEFT JOIN mst_index_box ON mst_index_box.mib_id = mpc.mib_id
            WHERE lsi_status_flg = '0'
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function insertIssueConfirm(){
        $sql = "INSERT INTO info_stock_issue (isd_id, isi_document, isi_document_date,isi_invoice,isi_invoice_date,isi_purchase_order,isi_purchase_order_date,isi_qty,isi_customer,isi_priceofunit,isi_unit_type)
        SELECT 
            isd_id,
            isi_document,
            isi_document_date,
            isi_invoice,
            isi_invoice_date,
            isi_purchase_order,
            isi_purchase_order_date,
            isi_qty,
            isi_customer,
            isi_priceofunit,
            isi_unit_type
        FROM 
            log_stock_issue
        WHERE 
            lsi_status_flg = '0';
                ";
        $sqlUpdate = "UPDATE log_stock_issue SET lsi_status_flg = '1' WHERE lsi_status_flg = '0';";
        $this->db->query($sql);
        $this->db->query($sqlUpdate);
        // $this->db->insert('info_stock_detail', $data);

        return $this->db->affected_rows() > 0 ? true : false;
    }
    public function getMaxValue() {
        // Perform a SELECT query to retrieve the maximum value of a column
        $this->db->select_max('mb_id');
        $query = $this->db->get('mst_brand');
        
        // Fetch the result
        $result = $query->row();
        
        // Return the maximum value
        return $result->mb_id;
    }

    public function insertReceive($data)
    {
        // Perform database insert operation
        $this->db->insert('info_stock_detail', $data);

        // Check if insert was successful
        return $this->db->affected_rows() > 0 ? true : false;
    }
    public function insertBrand($data)
    {
        // Perform database insert operation
        $this->db->insert('mst_brand', $data);

        // Check if insert was successful
        return $this->db->affected_rows() > 0 ? true : false;
    }

    public function insertProduct($data)
    {
        // Perform database insert operation
        $this->db->insert('mst_product_code', $data);

        // Check if insert was successful
        return $this->db->affected_rows() > 0 ? true : false;
    }
    public function insertIndex($data)
    {
        // Perform database insert operation
        $this->db->insert('mst_index_box', $data);

        // Check if insert was successful
        return $this->db->affected_rows() > 0 ? true : false;
    }

    public function insertIssue($data)
    {
        // Perform database insert operation
        $this->db->insert('log_stock_issue', $data);

        // Check if insert was successful
        return $this->db->affected_rows() > 0 ? true : false;
    }

    public function getUpdateReceive($data, $id)
    {

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
    public function getDeleteReceive($id)
    {
        // Perform the delete operation
        $this->db->where('isd_id', $id);
        $this->db->delete('info_stock_detail');

        // Check if the delete operation was successful
        return 1;
    }

    public function show_drop_down()
    {
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

    public function insert_user($data, $sess)
    {
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

    public function show_show_acc($data)
    {
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

    public function update_status($data)
    {
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

    public function checkProductExists($productName) {
        // Assuming you have a table named 'products' in your database
        $this->db->where('mpc_name', $productName);
        $query = $this->db->get('mst_product_code');
        // If there is a row with the given product name, return true
        return $query->num_rows() > 0;
    }
    public function checkModelExists($modelName) {
        // Assuming you have a table named 'products' in your database
        $this->db->where('mpc_model', $modelName);
        $query = $this->db->get('mst_product_code');
        // If there is a row with the given product name, return true
        return $query->num_rows() > 0;
    }
    public function checkBrandExists($BrandName) {    
        $this->db->select('mb_id'); // Select only the ID
        $this->db->where('mb_name', $BrandName);
        $query = $this->db->get('mst_brand');
    
        // If there is a row with the given brand name, return the ID
        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->mb_id;
        } else {
            // If no brand found, return null or handle the situation accordingly
            return null;
        }
    }

    public function update_flg($data)
    {
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


    public function show_update_acc($data, $sess)
    {
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


    public function update_user($data, $sess)
    {
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

    private function get_user_data($empcode)
    {
        $sql_select = "
            SELECT *
            FROM sys_account
            WHERE sa_emp_code = '$empcode'
        ";

        $query_select = $this->db->query($sql_select);
        return $query_select->row();
    }


    public function show_upd_User($data, $sess)
    {
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
