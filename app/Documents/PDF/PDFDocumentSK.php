<?php

namespace App\Documents\PDF;

use App\Helpers\Collections\Projects\ProjectCollection;
use App\Helpers\Collections\Projects\ProjectSKCollection;
use Symfony\Component\HttpKernel\DataCollector\AjaxDataCollector;

class PDFDocumentSK extends DocumentBuilder
{

    static public $structure = [
        'title',
        'address',
        'nomor',
        'number_of_attachment',
        'regards',
        'place',
        'date',
        'content',
        'signature_name',
        'signature',
    ];

    protected $fontSize = 10;

    /**
     * @var ProjectCollection
     * */
    protected $project;

    /* @var ProjectSKCollection */
    protected $sk;

    public function __construct()
    {
        parent::__construct('P', 'mm', 'A4');
        $this->SetMargins(25.4, 15.4, 25.4);
        $this->init();
    }

    /**
     * @param ProjectCollection
     * */
    public function setProject($project)
    {
        $this->project = $project;
    }

    /**
     * @param AjaxDataCollector $sk
     * */
    public function setSK($sk)
    {
        $this->sk = $sk;
    }

    public function header()
    {
        $this->SetFont($this->fontFamily, 'B', $this->fontSize + 10);
        $this->Cell(0, $this->wordHeight + 2, $this->sk->getPdfPayload()->getTitle(), 0, 1, 'C');

        $this->SetFont($this->fontFamily, '', $this->fontSize);
        $this->Cell(0, $this->wordHeight + 2, $this->sk->getPdfPayload()->getAddress(), 0, 1, 'C');

        $this->Ln(1);

        $this->SetLineWidth(0.5);
        $this->Line($this->GetX(), $this->GetY(), $this->GetX() + $this->paperWidth, $this->GetY());

        $this->Ln(5);
    }

    public function build()
    {
        $this->AliasNbPages();
        $this->AddPage();

        $this->SetTitle(sprintf("SK %s", $this->project->getName()));

        $CellInfo = 0.75 * $this->paperWidth;
        $CellLabel = 0.15 * $CellInfo;
        $CellValue = $CellInfo - ($CellLabel + 5);

        $YPos = $this->GetY();

        $this->Cell($CellLabel, $this->wordHeight, 'Nomor', 0, 0, 'L');
        $this->Cell(5, $this->wordHeight, ':', 0, 0, 'C');
        $this->MultiCell($CellValue, $this->wordHeight, $this->sk->getPdfPayload()->getNoDocument(), 0, 'L');

        $attachment = $this->sk->getPdfPayload()->getNumberOfAttachment();
        $this->Cell($CellLabel, $this->wordHeight, 'Lampiran', 0, 0, 'L');
        $this->Cell(5, $this->wordHeight, ':', 0, 0, 'C');
        $this->MultiCell($CellValue, $this->wordHeight, !empty($attachment) ? "$attachment Lembar" : "", 0, 'L');

        $this->Cell($CellLabel, $this->wordHeight, 'Perihal', 0, 0, 'L');
        $this->Cell(5, $this->wordHeight, ':', 0, 0, 'C');

        $this->SetFont($this->fontFamily, 'BU', $this->fontSize);
        $this->MultiCell($CellValue, $this->wordHeight, $this->sk->getPdfPayload()->getRegards(), 0, 'L');

        $this->SetFont($this->fontFamily, '', $this->fontSize);

        $YPosReturn = $this->GetY();

        $this->SetXY($CellInfo + $this->lMargin, $YPos);
        $this->Cell($this->paperWidth - $CellInfo, $this->wordHeight, sprintf("%s, %s", $this->sk->getPdfPayload()->getPlace(), $this->sk->getPdfPayload()->getDate('d F Y')), 0, 1, 'L');

        $this->SetY($YPosReturn);

        $this->Ln(10);

        $this->SetFont($this->fontFamily, 'B', $this->fontSize + 1);
        $this->MultiCell(0, $this->wordHeight + 1, "SURAT KEPUTUSAN PORSI SAHAM", 0, 'C');
        $this->MultiCell(0, $this->wordHeight + 1, $this->sk->getPdfPayload()->getRegards(), 0, 'C');

        $this->Ln();

        $this->SetFont($this->fontFamily, '', $this->fontSize);
        $this->AutoFormat(0, $this->wordHeight, $this->sk->getPdfPayload()->getContent(true), 0, 'J');

        $this->Ln(15);

        if($this->GetY() + 30 > $this->paperHeight)
            $this->AddPage();

        $this->Cell(0, $this->wordHeight, "Hormat Kami", 0, 1, 'L');

        $file = $this->sk->getPdfPayload()->getSignature()->first();
        if(!is_null($file)) {
            $this->SetY($this->GetY() - 5);
            $this->Image(storage_path($file->getDir() . DIRECTORY_SEPARATOR . $file->getFileName()), null, null, 50, 30);
            $this->SetY($this->GetY() - 5);
        }

        $this->SetFont($this->fontFamily, 'BU', $this->fontSize);
        $this->Cell(0, $this->wordHeight, $this->sk->getPdfPayload()->getSignatureName(), 0, 1, 'L');

        $this->AddPage();

        $this->SetFont($this->fontFamily, 'U', $this->fontSize + 1);
        $this->Cell(0, $this->wordHeight + 1, "PEMEGANG SAHAM", 0, 1, 'C');
        $this->Cell(0, $this->wordHeight + 1, strtoupper($this->sk->getPdfPayload()->getRegards()), 0, 1, 'C');

        $this->Ln();

        $this->SetFont($this->fontFamily, '', $this->fontSize);

        $YPos = $this->GetY();
        $shares = round($this->project->getValue()/$this->project->getSharesValue(), 2);
        $this->MultiCell(0.5 * $this->paperWidth, $this->wordHeight, sprintf("Lembar Saham : %s", IDR($shares, '')), 0, 'L');

        $YPosReturn = $this->GetY();
        $this->SetXY($this->GetX() + 0.5 * $this->paperWidth, $YPos);
        $this->MultiCell(0.5 * $this->paperWidth, $this->wordHeight, sprintf("Harga PAR : %s", IDR($this->project->getSharesValue(), '')), 0, 'R');

        if($YPosReturn > $this->GetY())
            $this->SetY($YPosReturn);

        $WNo = 0.05 * $this->paperWidth;
        $WInvestor = 0.35 * $this->paperWidth;
        $WShares = 0.2 * $this->paperWidth;
        $WNominal = 0.2 * $this->paperWidth;
        $WPercentage = 0.2 * $this->paperWidth;

        $this->SetWidths([$WNo, $WInvestor, $WShares, $WNominal, $WPercentage]);
        $this->SetAligns(['C', 'C', 'C', 'C', 'C']);

        $this->SetFont($this->fontFamily, 'B', $this->fontSize - 1);
        $this->Cell($WNo, $this->wordHeight + 2, "No", 1, 0, 'C');
        $this->Cell($WInvestor, $this->wordHeight + 2, "Nama Pemegang Saham", 1, 0, 'C');
        $this->Cell($WShares, $this->wordHeight + 2, "Lembar Saham", 1, 0, 'R');
        $this->Cell($WNominal, $this->wordHeight + 2, "Nominal", 1, 0, 'R');
        $this->Cell($WPercentage, $this->wordHeight + 2, "Prosentase", 1, 1, 'R');

        $this->SetAligns(['C', 'L', 'R', 'R', 'R']);
        $this->SetFont($this->fontFamily, '', $this->fontSize - 1);
        foreach($this->project->getInvestors()->all() as $i => $investor) {
            $this->Row([
                $i + 1,
                $investor->getInvestor()->getName(),
                IDR($investor->getSharesValue(), ''),
                IDR($investor->getInvestmentValue(), ''),
                round($investor->getSharesPercentage(), 2)."%"
            ]);
        }

        $this->SetFont($this->fontFamily, 'B', $this->fontSize - 1);
        $this->Cell($WNo + $WInvestor, $this->wordHeight + 2, "TOTAL", 1, 0, 'C');

        $shares = round($this->project->getValue()/$this->project->getSharesValue(), 2);
        $this->SetFont($this->fontFamily, '', $this->fontSize - 1);
        $this->Cell($WShares, $this->wordHeight + 2, IDR($shares, ''), 1, 0, 'R');
        $this->Cell($WNominal, $this->wordHeight + 2, IDR($this->project->getValue(), ''), 1, 0, 'R');
        $this->Cell($WPercentage, $this->wordHeight + 2, "100%", 1, 1, 'R');
    }
}
