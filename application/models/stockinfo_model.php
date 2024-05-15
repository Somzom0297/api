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
        SUM(COALESCE(info_stock_detail.isd_qty, 0)) - COALESCE((
            SELECT
                SUM(COALESCE(isiiner.isi_qty, 0)) AS total_qty
            FROM
                info_stock_issue isiiner
            WHERE
                info_stock_detail.isd_id = isiiner.isd_id
        ), 0) AS qtyy 
    FROM
        mst_product_code
        LEFT JOIN info_stock_detail ON mst_product_code.mpc_id = info_stock_detail.mpc_id
        LEFT JOIN mst_brand ON mst_brand.mb_id = mst_product_code.mb_id 
    GROUP BY
        mst_product_code.mpc_id
        ORDER BY qtyy DESC";

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
        COUNT(isd_id) as total,
        sys_account.sa_firstname,
        sys_account.sa_lastname
        FROM  info_stock_issue as isi
        LEFT JOIN sys_account ON sys_account.sa_id = isi.isi_created_by
        WHERE YEAR(isi_document_date) = '$year' AND MONTH(isi_document_date) = '$month'
        GROUP BY isi_document
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
        sys_account.sa_firstname,
        sys_account.sa_lastname,
        COUNT(isd_id) as total
        FROM  info_stock_issue as isi
        LEFT JOIN sys_account ON sys_account.sa_id = isi.isi_created_by
        WHERE YEAR(isi_document_date) = '$year'
        
        GROUP BY isi_document
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
                sys_account.sa_firstname,
                sys_account.sa_lastname,
                COUNT(isd_inv_no) as total
                FROM  info_stock_detail as isd
                LEFT JOIN sys_account ON sys_account.sa_id = isd.isd_created_by
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
                    sys_account.sa_firstname,
                    sys_account.sa_lastname,
                    isd_price_unit

                FROM  info_stock_detail as isd
                LEFT JOIN mst_product_code mpc ON mpc.mpc_id = isd.mpc_id
                LEFT JOIN mst_brand mb ON mb.mb_id = mpc.mb_id
                LEFT JOIN sys_account ON sys_account.sa_id = isd.isd_created_by
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
                mpc.mpc_img,
                mb.mb_name,
				mpc.mpc_name,
				mpc.mpc_model,
				isi_qty,
				isi_unit_type,
				isi_priceofunit,
				isi_purchase_order,
                isi_purchase_order_date,
				isd_customer,
                isi_customer,
				isi_invoice,
				isi_invoice_date,
				mib_number,
				mib_size,
                isd_inv_date,
                isd_inv_no,
                isd_po_number,
                mpc.mpc_discription,
                isd.isd_id,
                isd_po_date,
                isd_doc_date,
                isd_qty,
                sys_account.sa_firstname,
                    sys_account.sa_lastname,
				isd_price_unit
        FROM  info_stock_issue as isi
        LEFT JOIN info_stock_detail isd ON isi.isd_id = isd.isd_id
        LEFT JOIN mst_product_code mpc ON mpc.mpc_id = isd.mpc_id
        LEFT JOIN mst_brand mb ON mb.mb_id = mpc.mb_id
	    LEFT JOIN mst_index_box ON mst_index_box.mib_id = mpc.mib_id
        LEFT JOIN sys_account ON sys_account.sa_id = isi.isi_created_by
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
                    sys_account.sa_firstname,
                    sys_account.sa_lastname,
                    (
                        SELECT SUM(isd_qty) 
                        FROM info_stock_detail 
                        WHERE mpc_id = '$id'
                    ) AS total_qty,
                    mpc.mpc_qty,
                    isd_price_unit

                FROM  info_stock_detail as isd
                LEFT JOIN mst_product_code mpc ON mpc.mpc_id = isd.mpc_id
                LEFT JOIN mst_index_box mib ON mib.mib_id = mpc.mib_id
                LEFT JOIN mst_brand mb ON mb.mb_id = mpc.mb_id
                LEFT JOIN sys_account ON sys_account.sa_id = isd.isd_created_by
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
            mpc.mpc_qty,
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
                    mpc_img,
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
    public function getSelProductCodebyID($data)
    {
        $sql = "SELECT 
                    mpc_id,
                    mpc_name
                FROM  mst_product_code
                WHERE mpc_id = '$data'
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function getBrandAll()
    {
        $sql = "SELECT 
                    mb_id,
                   mb_name,
                   sys_account.sa_firstname,
                   sys_account.sa_lastname,
                   mb_created_date,
                   mb_status_flg
                FROM  mst_brand
                LEFT JOIN sys_account ON sys_account.sa_id = mst_brand.mb_created_by
                WHERE mb_status_flg = 1
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }
    public function getBrandListAll()
    {
        $sql = "SELECT 
                    mb_id,
                   mb_name,
                   sys_account.sa_firstname,
                   sys_account.sa_lastname,
                   mb_created_date,
                   mb_status_flg
                FROM  mst_brand
                LEFT JOIN sys_account ON sys_account.sa_id = mst_brand.mb_created_by
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }
    public function getBrand($id)
    {
        $sql = "SELECT 
                mb_id,
                mb_name
                FROM  mst_brand
                WHERE mb_id = '$id'
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function updateBrand($id, $data) {
        $this->db->where('mb_id', $id);
        return $this->db->update('mst_brand', $data);
    }

    public function updateStautus($id, $data) {
        $this->db->where('mb_id', $id);
        return $this->db->update('mst_brand', $data);
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

    public function getListProduct()
    {
        $sql = "SELECT 
                    mpc_id,
                    mpc_img,
                    mb_name,
                    mpc_name,
                    mpc_model,
                    mpc_discription

                FROM  mst_product_code
                LEFT JOIN mst_brand ON mst_brand.mb_id = mst_product_code.mb_id
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
                            mpc_img,
                            mpc_model,
                            mpc_discription,
                            mpc_name,
                            mib_number,
                            mst_index_box.mib_id,
                            mst_brand.mb_id,
                            mib_size,
                            mb_name,
                            isd_id,
                            mpc_unit,
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
        mpc_cost_price,
       info_stock_detail.isd_id,
       (mpc_qty - 
	COALESCE ((
	        	SELECT
	        		SUM(
	        		COALESCE ( log.isi_qty, 0 )) AS total_qtyyy
	        	FROM
	        		log_stock_issue log 
	        	WHERE
	        		info_stock_detail.isd_id = log.isd_id 
	        		AND log.lsi_status_flg = '0' 
	        		),
	        	0 
	        )) AS qtyy,
        SUM(COALESCE(info_stock_detail.isd_qty, 0)) - COALESCE((
            SELECT
                SUM(COALESCE(isiiner.isi_qty, 0)) AS total_qty
            FROM
                info_stock_issue isiiner
            WHERE
                info_stock_detail.isd_id = isiiner.isd_id
            ), 0) - COALESCE((
            SELECT
                SUM(COALESCE(log.isi_qty, 0)) AS total_qtyy
            FROM
                log_stock_issue log
            WHERE
                info_stock_detail.isd_id = log.isd_id
                AND log.lsi_status_flg = '0'
            ), 0) AS total 
    
        FROM
            mst_product_code
        LEFT JOIN mst_index_box ON mst_index_box.mib_id = mst_product_code.mib_id
        LEFT JOIN mst_brand ON mst_brand.mb_id = mst_product_code.mb_id
        LEFT JOIN info_stock_detail ON info_stock_detail.mpc_id = mst_product_code.mpc_id
        LEFT JOIN info_stock_issue ON info_stock_detail.isd_id = info_stock_issue.isd_id 
        WHERE mst_product_code.mpc_name = '$id'
        GROUP BY
        mst_product_code.mpc_id,
        mpc_img,
        mb_name,
        mpc_name,
        mpc_model,
        mpc_discription,
        mib_number,
        mib_size
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
        (mpc_qty - 
	COALESCE ((
		SELECT
			SUM(
			COALESCE ( log.isi_qty, 0 )) AS total_qtyy 
		FROM
			log_stock_issue log 
		WHERE
			info_stock_detail.isd_id = log.isd_id 
			AND log.lsi_status_flg = '0' 
			),
		0 
	)) AS qtyy
    
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
        mib_size
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
        lsi_id,
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
    public function showChart(){
        $sql = "SELECT
        mpc.mpc_name,
        COALESCE(SUM(isi.isi_qty), 0) AS total
    FROM
        mst_product_code AS mpc
    LEFT JOIN
        info_stock_detail AS isd ON isd.mpc_id = mpc.mpc_id
    LEFT JOIN
        info_stock_issue AS isi ON isi.isd_id = isd.isd_id
    GROUP BY
        mpc.mpc_name
        LIMIT 7;;
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;

        
    }
    public function getTotalProductIssue(){
        $sql = "SELECT
        SUM(total) as totalAll
    FROM (
        SELECT
            SUM(isi_priceofunit * isi_qty) as total
        FROM
            info_stock_issue
            LEFT JOIN info_stock_detail ON info_stock_detail.isd_id = info_stock_issue.isd_id
            LEFT JOIN mst_product_code ON info_stock_detail.mpc_id = mst_product_code.mpc_id
        GROUP BY
            mst_product_code.mpc_id
    ) AS subquery;
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;

        
    }
    public function getTotalProduct(){
        $sql = "SELECT
       SUM( isi_qty ) as total
   FROM
       info_stock_issue
       LEFT JOIN info_stock_detail ON info_stock_detail.isd_id = info_stock_issue.isd_id
       LEFT JOIN mst_product_code ON info_stock_detail.mpc_id = mst_product_code.mpc_id
                ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;

        
    }

    public function insertIssueConfirm() {
        // Retrieve the data to be inserted into info_stock_issue
        $sqlInsert = "
            INSERT INTO info_stock_issue (isd_id, isi_document, isi_document_date, isi_invoice, isi_invoice_date, isi_purchase_order, isi_purchase_order_date, isi_qty, isi_customer, isi_priceofunit, isi_unit_type, isi_created_by)
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
                isi_unit_type,
                isi_created_by  
            FROM 
                log_stock_issue
            WHERE 
                lsi_status_flg = '0'
        ";
    
        // Execute the insert query
        $this->db->query($sqlInsert);
    
        $sql1 = "SELECT
        mst_product_code.mpc_id,
        SUM(log_stock_issue.isi_qty) as total
    FROM
        `log_stock_issue`
    LEFT JOIN info_stock_detail ON info_stock_detail.isd_id = log_stock_issue.isd_id
    LEFT JOIN mst_product_code ON info_stock_detail.mpc_id = mst_product_code.mpc_id
		WHERE lsi_status_flg = '0'
    GROUP BY mst_product_code.mpc_id";

// Execute the select query
    $query1 = $this->db->query($sql1);

    if ($query1->num_rows() > 0) {
    foreach ($query1->result() as $row) {
        $mpc_id = $row->mpc_id;
        $total = $row->total;
        
        // Update query to decrement mpc_qty based on the retrieved total
        $sqlUpdateQty = "
            UPDATE mst_product_code 
            SET mpc_qty = mpc_qty - $total 
            WHERE mpc_id = $mpc_id;
        ";
        
        // Execute the update query
        $this->db->query($sqlUpdateQty);
    }
    }
        // Update lsi_status_flg to '1' in log_stock_issue for the records that were inserted
        $sqlUpdate = "
            UPDATE log_stock_issue 
            SET lsi_status_flg = '1' 
            WHERE lsi_status_flg = '0'
        ";
        $this->db->query($sqlUpdate);
        

    
        // Check if any rows were affected by the updates
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

    public function insertProductQTY($id, $mpc_qty)
    {
        // Perform database update operation
        $data = array(
            'mpc_qty' => $mpc_qty, // Set mpc_qty to the given quantity
        );
        
        // Perform the update
        $this->db->where('mpc_id', $id);
        $this->db->set('mpc_qty', 'mpc_qty+' . (int)$mpc_qty, FALSE); // Increment mpc_qty by the given quantity
        $this->db->update('mst_product_code');
        
        // Check if update was successful
        return $this->db->affected_rows() > 0 ? true : false;
    }

    public function updateProductQTY($id, $mpc_qty)
    {
        // Perform database update operation
        $data = array(
            'mpc_qty' => $mpc_qty, // Set mpc_qty to the given quantity
        );
        
        // Perform the update
        $this->db->where('mpc_name', $id);
        $this->db->set('mpc_qty', 'mpc_qty-' . (int)$mpc_qty, FALSE); // Increment mpc_qty by the given quantity
        $this->db->update('mst_product_code');
        
        // Check if update was successful
        return $this->db->affected_rows() > 0 ? true : false;
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
    public function getDeleteIssueConfirm($id)
    {
        // Perform the delete operation
        $this->db->where('lsi_id', $id);
        $this->db->delete('log_stock_issue');

        // Check if the delete operation was successful
        return true;
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
    public function checkBrandExists($BrandName) {
        // Assuming you have a table named 'products' in your database
        $this->db->where('mb_name', $BrandName);
        $query = $this->db->get('mst_brand');

        // If there is a row with the given product name, return true
        return $query->num_rows() > 0;
    }
    public function checkModelExists($ModelName) {
        // Assuming you have a table named 'products' in your database
        $this->db->where('mpc_name', $ModelName);
        $query = $this->db->get('mst_product_code');

        // If there is a row with the given product name, return true
        return $query->num_rows() > 0;
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
