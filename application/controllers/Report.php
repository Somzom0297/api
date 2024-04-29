<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '/libraries/fpdf.php'; // Include FPDF library file

class Report extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // No need to load the library here
        $this->load->model('stockinfo_model', 'sim');
    }
    
    public function export_pdf($invNumber) {
        // Retrieve data from the model
        $records = $this->sim->getReceiveDetail($invNumber);
        
        // Define maximum number of records per page
        $maxRecordsPerPage = 10;
        
        // Create PDF
        $pdf = new FPDF('P', 'mm', 'A4');
        
        // Function to generate content for each page
        $generatePageContent = function($data) use ($pdf) {
            // Add content directly to PDF
            $pdf->AddPage();
            $pdf->SetFont('Arial','B',15);
            $currentX = $pdf->GetX();
            $currentY = $pdf->GetY();
            
            // Add the image
            $pdf->Image("http://127.0.0.1/api/assets/images/messageImage_1714395597305.jpg", $currentX, $currentY, 0, 40);
            
            // Move to the right to make space for the image
            $pdf->SetX($currentX + 10); // Adjust as needed
            
            // Add the text
            $pdf->Cell(0, 10, 'PROPLUS CORPORATION CO.,LTD.', 0, 1, 'C');
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(0, 10, '22/354 Village No. 4 Sub-district Bueng Sub-area Sriracha Province Chonburi 20230', 0, 1, 'R');
            $pdf->Cell(0, 10, 'TEL : 033-157879, 089-9364990 Tax ID 0205561030718', 0, 1, 'C');
            $pdf->SetFont('Arial','',9);
            $pdf->Ln();
            $pdf->Cell(0, 10, 'Good From Receive', 0, 1, 'C');

            $pdf->Write(10, 'Document No. : ' . $data['records'][0]->isd_doc_number);
            $pdf->Ln();
            $pdf->Write(10, 'Document Date. : ' . $data['records'][0]->isd_doc_date);
            $pdf->Ln();
            $pdf->SetFont('Arial','U',9);
            $pdf->Write(10, 'Document References ');
            $pdf->SetFont('Arial','',9);
            $pdf->Ln();
            $pdf->Write(10, 'Supplier Name : ' . $data['records'][0]->isd_customer);
            $pdf->Ln();
            $pdf->Write(10, 'IV : ' . $data['records'][0]->isd_inv_no);
            $pdf->Write(10, '                   Date : ' . $data['records'][0]->isd_inv_date);
            $pdf->Ln();
            $pdf->Write(10, 'PO : ' . $data['records'][0]->isd_po_number);
            $pdf->Write(10, '           Date : ' . $data['records'][0]->isd_po_date);
            $pdf->Ln();
        
            $pdf->Cell(10, 10, 'NO.', 1, 0, 'C');
            $pdf->Cell(30, 10, 'BRAND', 1, 0, 'C');
            $pdf->Cell(30, 10, 'PRODUCT ID', 1, 0, 'C');
            $pdf->Cell(30, 10, 'MODEL', 1, 0, 'C');
            $pdf->Cell(30, 10, 'DISCREPTION', 1, 0, 'C');
            $pdf->Cell(10, 10, 'QTY', 1, 0, 'C');
            $pdf->Cell(20, 10, 'PRICE', 1, 0, 'C');
            $pdf->Cell(30, 10, 'AMOUNT', 1, 0, 'C');
            $pdf->Ln();
            $rowNumber = 1;
            foreach ($data['records'] as $record) {
                $pdf->Cell(10, 10, $rowNumber++, 1, 0, 'C');
                $pdf->Cell(30, 10, $record->mb_name, 1, 0, 'C');
                $pdf->Cell(30, 10, $record->mpc_name, 1, 0, 'C');
                $pdf->Cell(30, 10, $record->mpc_model, 1, 0, 'C');
                $pdf->Cell(30, 10, $record->mpc_discription, 1, 0, 'C');
                $pdf->Cell(10, 10, $record->isd_qty, 1, 0, 'C');
                $totalAmount1 = $record->isd_price_unit;
                // Format the total amount with commas and decimal point
                $formattedAmount1 = number_format($totalAmount1, 2, '.', ',');
                $pdf->Cell(20, 10, $formattedAmount1, 1, 0, 'C');
                $totalAmount = $record->isd_qty * $record->isd_price_unit;

                // Format the total amount with commas and decimal point
                $formattedAmount = number_format($totalAmount, 2, '.', ',');
                $pdf->Cell(30, 10, $formattedAmount, 1, 0, 'C');
                $pdf->Ln();
            }
            $pdf->SetY(236); // Adjust Y position as needed
            $pdf->SetFont('Arial','',9);
            // Add additional text
            $pdf->Write(10, '                 Received by');
            $pdf->Write(10, '                                                                  Check by');
            $pdf->Write(10, '                                                      Approval by');
            $pdf->Ln();
            $pdf->Write(10, '         ____________________');
            $pdf->Write(10, '                                         ____________________');
            $pdf->Write(10, '                               ____________________');
            $pdf->Ln();
            $pdf->Write(10, '     ( MISS '.$record->sa_firstname.' '.$record->sa_lastname.' )');
            $pdf->Write(10, '                                    ( MISS KANJANA BUNTA )');
            $pdf->Write(10, '                       ( MISS PAPAPRON PIMKROO )');
            $pdf->Ln();

            $pdf->Write(10, '    Date____________________ ');
            $pdf->Write(10, '                                 Date____________________ ');
            $pdf->Write(10, '                      Date____________________ ');
        };
        
        // Generate content for the first page
        $firstPageData = array_slice($records, 0, $maxRecordsPerPage);
        $generatePageContent(['records' => $firstPageData]);
    
        // Generate content for subsequent pages if necessary
        $remainingRecords = array_slice($records, $maxRecordsPerPage);
        while (!empty($remainingRecords)) {
            $generatePageContent(['records' => $remainingRecords]);
            $remainingRecords = array_slice($remainingRecords, $maxRecordsPerPage);
        }
    
        // Output PDF
        $pdf->Output();
    }
    
    public function export_pdf_issue($isi_document) {
        // Retrieve data from the model
        $records = $this->sim->getIssueDetail($isi_document);
        
        // Define maximum number of records per page
        $maxRecordsPerPage = 10;
        
        // Create PDF
        $pdf = new FPDF('P', 'mm', 'A4');
        
        // Function to generate content for each page
        $generatePageContent = function($data) use ($pdf) {
            // Add content directly to PDF
            $pdf->AddPage();
            $pdf->SetFont('Arial','B',15);
            $currentX = $pdf->GetX();
            $currentY = $pdf->GetY();
            
            // Add the image
            $pdf->Image("http://127.0.0.1/api/assets/images/messageImage_1714395597305.jpg", $currentX, $currentY, 0, 40);
            
            // Move to the right to make space for the image
            $pdf->SetX($currentX + 10); // Adjust as needed
            
            // Add the text
            $pdf->Cell(0, 10, 'PROPLUS CORPORATION CO.,LTD.', 0, 1, 'C');
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(0, 10, '22/354 Village No. 4 Sub-district Bueng Sub-area Sriracha Province Chonburi 20230', 0, 1, 'R');
            $pdf->Cell(0, 10, 'TEL : 033-157879, 089-9364990 Tax ID 0205561030718', 0, 1, 'C');
            $pdf->SetFont('Arial','',9);
            $pdf->Ln();
            $pdf->Cell(0, 10, 'Good From Issue', 0, 1, 'C');



            $pdf->Write(10, 'Document No. : ' . $data['records'][0]->isi_document);
            $pdf->Ln();
            $pdf->Write(10, 'Document Date. : ' . $data['records'][0]->isi_document_date);
            $pdf->Ln();
            $pdf->SetFont('Arial','U',9);
            $pdf->Write(10, 'Document References ');
            $pdf->SetFont('Arial','',9);
            $pdf->Ln();
            $pdf->Write(10, 'Customer Name : ' . $data['records'][0]->isi_customer);
            $pdf->Ln();
            $pdf->Write(10, 'IV : ' . $data['records'][0]->isd_inv_no);
            $pdf->Write(10, '                   Date : ' . $data['records'][0]->isd_inv_date);
            $pdf->Ln();
            $pdf->Write(10, 'PO : ' . $data['records'][0]->isd_po_number);
            $pdf->Write(10, '           Date : ' . $data['records'][0]->isd_po_date);
            $pdf->Ln();
        
            $pdf->Cell(10, 10, 'NO.', 1, 0, 'C');
            $pdf->Cell(30, 10, 'BRAND', 1, 0, 'C');
            $pdf->Cell(30, 10, 'PRODUCT ID', 1, 0, 'C');
            $pdf->Cell(30, 10, 'MODEL', 1, 0, 'C');
            $pdf->Cell(30, 10, 'DISCREPTION', 1, 0, 'C');
            $pdf->Cell(10, 10, 'QTY', 1, 0, 'C');
            $pdf->Cell(20, 10, 'PRICE', 1, 0, 'C');
            $pdf->Cell(30, 10, 'AMOUNT', 1, 0, 'C');
            $pdf->Ln();
            $rowNumber = 1;
            foreach ($data['records'] as $record) {
                $pdf->Cell(10, 10, $rowNumber++, 1, 0, 'C');
                $pdf->Cell(30, 10, $record->mb_name, 1, 0, 'C');
                $pdf->Cell(30, 10, $record->mpc_name, 1, 0, 'C');
                $pdf->Cell(30, 10, $record->mpc_model, 1, 0, 'C');
                $pdf->Cell(30, 10, $record->mpc_discription, 1, 0, 'C');
                $pdf->Cell(10, 10, $record->isd_qty, 1, 0, 'C');
                $totalAmount1 = $record->isd_price_unit;
                // Format the total amount with commas and decimal point
                $formattedAmount1 = number_format($totalAmount1, 2, '.', ',');
                $pdf->Cell(20, 10, $formattedAmount1, 1, 0, 'C');
                $totalAmount = $record->isd_qty * $record->isd_price_unit;

                // Format the total amount with commas and decimal point
                $formattedAmount = number_format($totalAmount, 2, '.', ',');
                $pdf->Cell(30, 10, $formattedAmount, 1, 0, 'C');
                $pdf->Ln();
            }
            $pdf->SetY(236); // Adjust Y position as needed
            $pdf->SetFont('Arial','',9);
            // Add additional text
            $pdf->Write(10, '                 Received by');
            $pdf->Write(10, '                                                                  Check by');
            $pdf->Write(10, '                                                      Approval by');
            $pdf->Ln();
            $pdf->Write(10, '         ____________________');
            $pdf->Write(10, '                                         ____________________');
            $pdf->Write(10, '                               ____________________');
            $pdf->Ln();
            $pdf->Write(10, '     ( MISS '.$record->sa_firstname.' '.$record->sa_lastname.' )');
            $pdf->Write(10, '                                    ( MISS KANJANA BUNTA )');
            $pdf->Write(10, '                       ( MISS PAPAPRON PIMKROO )');
            $pdf->Ln();

            $pdf->Write(10, '    Date____________________ ');
            $pdf->Write(10, '                                 Date____________________ ');
            $pdf->Write(10, '                      Date____________________ ');
        };
        
        // Generate content for the first page
        $firstPageData = array_slice($records, 0, $maxRecordsPerPage);
        $generatePageContent(['records' => $firstPageData]);
    
        // Generate content for subsequent pages if necessary
        $remainingRecords = array_slice($records, $maxRecordsPerPage);
        while (!empty($remainingRecords)) {
            $generatePageContent(['records' => $remainingRecords]);
            $remainingRecords = array_slice($remainingRecords, $maxRecordsPerPage);
        }
    
        // Output PDF
        $pdf->Output();
    }

    public function export_pdf_stockInffo() {
        // Retrieve data from the model
        $records = $this->sim->getStockInfo();
        
        // Define maximum number of records per page
        $maxRecordsPerPage = 15;
        
        // Create PDF
        $pdf = new FPDF('L', 'mm', 'A4');
        
        // Function to generate content for each page
        $generatePageContent = function($data) use ($pdf) {
            // Add content directly to PDF
            $pdf->AddPage();
            $pdf->SetFont('Arial','B',11);
            $currentX = $pdf->GetX();
            $currentY = $pdf->GetY();
            
            // Add the image
            $pdf->Image("http://127.0.0.1/api/assets/images/messageImage_1714395597305.jpg", $currentX, $currentY, 0, 40);
            
            // Move to the right to make space for the image
            $pdf->SetX($currentX + 10); // Adjust as needed
            
            // Add the text
            $pdf->Cell(0, 10, 'PROPLUS CORPORATION CO.,LTD.', 0, 1, 'C');
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(0, 10, '22/354 Village No. 4 Sub-district Bueng Sub-area Sriracha Province Chonburi 20230', 0, 1, 'C');
            $pdf->Cell(0, 10, 'TEL : 033-157879, 089-9364990 Tax ID 0205561030718', 0, 1, 'C');
            $pdf->SetFont('Arial','',9);
            $pdf->Ln();
            $pdf->Ln();


            $pdf->Cell(10, 10, 'NO.', 1, 0, 'C');
            $pdf->Cell(50, 10, 'BRAND', 1, 0, 'C');
            $pdf->Cell(50, 10, 'PRODUCT ID', 1, 0, 'C');
            $pdf->Cell(60, 10, 'MODEL', 1, 0, 'C');
            $pdf->Cell(60, 10, 'DISCREPTION', 1, 0, 'C');
            $pdf->Cell(40, 10, 'REMAIN', 1, 0, 'C');

            $pdf->Ln();
            $rowNumber = 1;
            foreach ($data['records'] as $record) {
                 $qtyyy = "0";
                if($record->isd_qty == null){
                    $qtyyy = 0;
                }else{
                  $qtyyy = $record->isd_qty;
                }
                
                $pdf->Cell(10, 10, $rowNumber++, 1, 0, 'C');
                $pdf->Cell(50, 10, $record->mb_name, 1, 0, 'C');
                $pdf->Cell(50, 10, $record->mpc_name, 1, 0, 'C');
                $pdf->Cell(60, 10, $record->mpc_model, 1, 0, 'C');
                $pdf->Cell(60, 10, $record->mpc_discription, 1, 0, 'C');
                $pdf->Cell(40, 10, ($record->qtyy == null) ? $qtyyy : $record->qtyy , 1, 0, 'C');
                $pdf->Ln();
            }

        };
        
        // Generate content for the first page
        $firstPageData = array_slice($records, 0, $maxRecordsPerPage);
        $generatePageContent(['records' => $firstPageData]);
    
        // Generate content for subsequent pages if necessary
        $remainingRecords = array_slice($records, $maxRecordsPerPage);
        while (!empty($remainingRecords)) {
            $generatePageContent(['records' => $remainingRecords]);
            $remainingRecords = array_slice($remainingRecords, $maxRecordsPerPage);
        }
    
        // Output PDF
        $pdf->Output();
    }

    public function export_pdf_product($isi_document) {
        // Retrieve data from the model
        $records = $this->sim->getProductDetail($isi_document);
        
        // Define maximum number of records per page
       // Define maximum number of records per page
       $maxRecordsPerPage = 10;
        
       // Create PDF
       $pdf = new FPDF('P', 'mm', 'A4');
       
       // Function to generate content for each page
       $generatePageContent = function($data) use ($pdf) {
           // Add content directly to PDF
           $pdf->AddPage();
           $pdf->SetFont('Arial','B',15);
           $currentX = $pdf->GetX();
           $currentY = $pdf->GetY();
           
           // Add the image
           $pdf->Image("http://127.0.0.1/api/assets/images/messageImage_1714395597305.jpg", $currentX, $currentY, 0, 40);
           
           // Move to the right to make space for the image
           $pdf->SetX($currentX + 10); // Adjust as needed
           
           // Add the text
           $pdf->Cell(0, 10, 'PROPLUS CORPORATION CO.,LTD.', 0, 1, 'C');
           $pdf->SetFont('Arial','',10);
           $pdf->Cell(0, 10, '22/354 Village No. 4 Sub-district Bueng Sub-area Sriracha Province Chonburi 20230', 0, 1, 'R');
           $pdf->Cell(0, 10, 'TEL : 033-157879, 089-9364990 Tax ID 0205561030718', 0, 1, 'C');
           $pdf->SetFont('Arial','',9);
           $pdf->Ln();
           $pdf->Cell(0, 10, 'Product Report', 0, 1, 'C');



           $pdf->Write(10, 'Total : ' . $data['records'][0]->total_qty);
           $pdf->Ln();
           $pdf->Write(10, 'Product Code : ' . $data['records'][0]->mpc_name);
           $pdf->Ln();
           $pdf->Write(10, 'Brand : ' . $data['records'][0]->mb_name);
           $pdf->Ln();
           $pdf->Write(10, 'Model : ' . $data['records'][0]->mpc_model);
           $pdf->Ln();
           $pdf->Write(10, 'Discription : ' . $data['records'][0]->mpc_discription);
           $pdf->Ln();
       
           $pdf->Cell(10, 10, 'NO.', 1, 0, 'C');
           $pdf->Cell(40, 10, 'Document', 1, 0, 'C');
           $pdf->Cell(40, 10, 'Document Date', 1, 0, 'C');
           $pdf->Cell(40, 10, 'Create By', 1, 0, 'C');
           $pdf->Cell(20, 10, 'QTY', 1, 0, 'C');
           $pdf->Cell(30, 10, 'PRICE', 1, 0, 'C');
           $pdf->Ln();
           $rowNumber = 1;
           foreach ($data['records'] as $record) {
               $pdf->Cell(10, 10, $rowNumber++, 1, 0, 'C');
               $pdf->Cell(40, 10, $record->isd_doc_number, 1, 0, 'C');
               $pdf->Cell(40, 10, $record->isd_doc_date, 1, 0, 'C');
               $pdf->Cell(40, 10, $record->sa_firstname.' '.$record->sa_lastname, 1, 0, 'C');
               $pdf->Cell(20, 10, $record->isd_qty, 1, 0, 'C');
               $totalAmount1 = $record->isd_price_unit;
               // Format the total amount with commas and decimal point
               $formattedAmount1 = number_format($totalAmount1, 2, '.', ',');
               $pdf->Cell(30, 10, $formattedAmount1, 1, 0, 'C');
               $pdf->Ln();
           }
        
       };
       
       // Generate content for the first page
       $firstPageData = array_slice($records, 0, $maxRecordsPerPage);
       $generatePageContent(['records' => $firstPageData]);
   
       // Generate content for subsequent pages if necessary
       $remainingRecords = array_slice($records, $maxRecordsPerPage);
       while (!empty($remainingRecords)) {
           $generatePageContent(['records' => $remainingRecords]);
           $remainingRecords = array_slice($remainingRecords, $maxRecordsPerPage);
       }
   
       // Output PDF
       $pdf->Output();
    }
}