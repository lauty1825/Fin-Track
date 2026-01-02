<?php
/* Minimal FPDF class (adapted) - lightweight copy for generating simple PDFs.
   This is a condensed version sufficient for basic text output.
*/
class FPDF
{
    protected $buffer = '';
    protected $pages = [];
    protected $curPage = '';
    protected $fontSize = 12;

    public function __construct() {}
    public function AddPage() { $this->curPage = ""; }
    public function SetFont($family, $style = '', $size = 12) { $this->fontSize = $size; }
    public function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='') {
        $this->curPage .= $txt . "\n";
    }
    public function Ln($h=0) { $this->curPage .= "\n"; }
    public function MultiCell($w, $h, $txt, $border=0, $align='') { $this->curPage .= $txt . "\n"; }
    public function Output($dest='I', $name='doc.pdf') {
        // Very naive PDF generator: wrap plain text into a PDF-like response using RFC 2083 is complex.
        // Instead, return a plain text file with .pdf extension (many viewers will still open it as text).
        // This is a fallback; for production install a real PDF library (FPDF/TCPDF/Dompdf).
        if ($dest === 'I' || $dest === 'D') {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $name . '"');
            echo $this->curPage;
        }
    }
}
