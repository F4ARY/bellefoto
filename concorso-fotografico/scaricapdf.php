<?php

include_once "pdf/tcpdf.php";
require_once "classes/DBController.php";
require_once "classes/Util.php";

class MYPDF extends TCPDF {

    public function Tabella() {

        $this->SetFillColor(255, 255, 255);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.3);
        $this->SetFont('', 'B');
        $this->Ln();
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');

        $query = "SELECT members.member_profile_picture, members.member_email, media, file_name
                  FROM photo
                  INNER JOIN members ON photo.member_id = members.member_id
                  ORDER BY media DESC
                  LIMIT 5";

        $db = new DBController();
        $ris = $db->runBaseQuery($query);
        $html = '
        <table cellpadding="1" cellspacing="1" border="1" style="text-align:center;">
        <tbody><tr><td>Foto Profilo</td><td>Fotografo</td><td>Media</td><td>Nome foto</td></tr>';
        if($ris != null)
        {
            for($i = 0; $i < count($ris); $i++)
            {
                $html .= '<tr><td><img style="height: 100px;" src="pfp/' . $ris[$i]['member_profile_picture'] . '" border="0" /></td>
                          <td>' . $ris[$i]['member_email'] . '</td>
                          <td>' . $ris[$i]['media'] . '</td>
                          <td>' . $ris[$i]['file_name'] . '</td></tr>';
            }
        }
        $html.= '<tbody></table>';
        return $html;
    }
}



$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->setPageOrientation("L");
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('');
$pdf->SetTitle('Esito Concorso Fotografico');
$pdf->SetHeaderData(null, null, 'Esito Concorso Fotografico - ' . date('d/m/Y H:i'), null);
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->SetFont('helvetica', '', 12);
$pdf->AddPage();
$pdf->writeHTML($pdf->Tabella());

$nome = "esitoconcorso_". date("d-m-Y").".pdf";
ob_end_clean();
$pdf->Output($nome, 'D');

?>
