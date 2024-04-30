<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '/libraries/fpdf.php';

class Pdf_generator extends FPDF {

    private $data;

    public function setData($data) {
        $this->data = $data;
    }
    function Header() {
        // Set font for the header
        $this->SetFont('Arial', 'B', 12);
        
        // Add text to the header
        $this->Cell(40, 10, 'Document Header', 0, 1, 'C');
    }
    public function generate() {
        // Dump the data for debugging purposes
        var_dump($this->data);
    
        $this->AddPage();
    
        if (!empty($this->data)) {
            $header = array('Column 1', 'Column 2', 'Column 3', 'Column 4');
            foreach($header as $col) {
                $this->Cell(40, 7, $col, 1);
            }
            $this->Ln();
    
            foreach($this->data as $row) {
                foreach($row as $col) {
                    $this->Cell(40, 6, $col, 1);
                }
                $this->Ln();
            }
        } else {
            $this->Cell(0, 10, 'No data available', 0, 1);
        }
    
        $this->Output();
    }
}
?>