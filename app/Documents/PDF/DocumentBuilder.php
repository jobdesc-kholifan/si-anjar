<?php


namespace App\Documents\PDF;


use Codedge\Fpdf\Fpdf\Fpdf;

class DocumentBuilder extends Fpdf
{

    protected $paperWidth;

    protected $paperHeight;

    protected $wordHeight = 5;

    protected $fontFamily = 'Arial';

    protected $fontSize = 9;

    private $widths = array();

    private $rowHeight = 5;

    private $aligns = array();

    public function __construct($orientation, $unit, $size)
    {
        parent::__construct($orientation, $unit, $size);
    }

    public function init()
    {
        $this->paperWidth = $this->w - ($this->lMargin + $this->rMargin);
        $this->paperHeight = $this->h - ($this->tMargin + $this->bMargin);
    }

    public function SetWidths($w)
    {
        //Set the array of column widths
        $this->widths=$w;
    }

    public function SetAligns($a)
    {
        //Set the array of column alignments
        $this->aligns=$a;
    }

    public function SetRowHeight($height)
    {
        $this->rowHeight = $height;
    }

    public function Row($data, $borders = array())
    {
        //Calculate the height of the row
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h=$this->rowHeight*$nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for($i=0;$i<count($data);$i++)
        {
            $w=$this->widths[$i];
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();
            //Draw the border
            $this->Rect($x,$y,$w,$h);
            //Print the text
            $this->MultiCell($w,$this->rowHeight,$data[$i],0,$a);
            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    public function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    public function NbLines($w,$txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb)
        {
            $c=$s[$i];
            if($c=="\n")
            {
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }

    function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false, $maxline=0)
    {
        //Output text with automatic or explicit line breaks, at most $maxline lines
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 && $s[$nb-1]=="\n")
            $nb--;
        $b=0;
        if($border)
        {
            if($border==1)
            {
                $border='LTRB';
                $b='LRT';
                $b2='LR';
            }
            else
            {
                $b2='';
                if(is_int(strpos($border,'L')))
                    $b2.='L';
                if(is_int(strpos($border,'R')))
                    $b2.='R';
                $b=is_int(strpos($border,'T')) ? $b2.'T' : $b2;
            }
        }
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $ns=0;
        $nl=1;
        while($i<$nb)
        {
            //Get next character
            $c=$s[$i];
            if($c=="\n")
            {
                //Explicit line break
                if($this->ws>0)
                {
                    $this->ws=0;
                    $this->_out('0 Tw');
                }
                $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $ns=0;
                $nl++;
                if($border && $nl==2)
                    $b=$b2;
                if($maxline && $nl>$maxline)
                    return substr($s,$i);
                continue;
            }
            if($c==' ')
            {
                $sep=$i;
                $ls=$l;
                $ns++;
            }
            $l+=$cw[$c];
            if($l>$wmax)
            {
                //Automatic line break
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                    if($this->ws>0)
                    {
                        $this->ws=0;
                        $this->_out('0 Tw');
                    }
                    $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
                }
                else
                {
                    if($align=='J')
                    {
                        $this->ws=($ns>1) ? ($wmax-$ls)/1000*$this->FontSize/($ns-1) : 0;
                        $this->_out(sprintf('%.3F Tw',$this->ws*$this->k));
                    }
                    $this->Cell($w,$h,substr($s,$j,$sep-$j),$b,2,$align,$fill);
                    $i=$sep+1;
                }
                $sep=-1;
                $j=$i;
                $l=0;
                $ns=0;
                $nl++;
                if($border && $nl==2)
                    $b=$b2;
                if($maxline && $nl>$maxline)
                {
                    if($this->ws>0)
                    {
                        $this->ws=0;
                        $this->_out('0 Tw');
                    }
                    return substr($s,$i);
                }
            }
            else
                $i++;
        }
        //Last chunk
        if($this->ws>0)
        {
            $this->ws=0;
            $this->_out('0 Tw');
        }
        if($border && is_int(strpos($border,'B')))
            $b.='B';
        $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
        $this->x=$this->lMargin;
        return '';
    }

    public function GetWidthMultiCell($w, $text, $line = null)
    {
        $newtext = '';
        if(empty($w))
            $w = $this->paperWidth;

        $addedText = '';
        $word = '';

        $arrayResult = array();
        for($j = 0; $j < strlen($text); $j++) {
            $newtext .= $text[$j];

            if($text[$j] != ' ')
                $word .= $text[$j];

            if($text[$j] == ' ') {
                $addedText .= $word . " ";
                $word = '';
            }

            if($this->GetStringWidth($newtext) >= $w) {
//                echo strlen($text) . "<br />";
//                echo $newtext."<br \>";
//                echo $addedText."<br \>";
//                echo $this->GetStringWidth($newtext) . " >= " . $w . "<br />";
//                echo strlen($addedText) . "<br />";
                $arrayResult[] = $addedText;
                $newtext = $word;
                $addedText = '';
            }

            if($j == strlen($text) - 1) {
                $arrayResult[] = $addedText;
            }
        }

        return is_null($line) ? $this->GetStringWidth(end($arrayResult)) : $this->GetStringWidth($arrayResult[$line]);
    }

    public function CalcWidth($width)
    {
        return $this->paperWidth * $width;
    }

    public function Numbering($seq, $format)
    {
        if(trim($format) == '1.') {
            return "$seq. ";
        }

        return $seq;
    }

    public function CellNumber($width, $height, $text, $border = 0, $align = 'L')
    {
        preg_match_all('/\n[0-9][.|\s]/', $text, $matches);

        foreach($matches[0] as $i => $match) {
            $strPos = strpos($text, $match);
            if($i < count($matches[0]) - 1) {
                $endPos = strpos($text, $matches[0][$i + 1]);
                $value = substr($text, $strPos, $endPos - $strPos) . "\n";
            } else $value = substr($text, $strPos) . "\n";

            $value = trim(str_replace($match, '', $value));

            $this->Cell(5, $height, $match, 0, 0, 'L');
            $this->MultiCell($width, $height, $value, $border, $align);
        }
    }

    public function AutoFormat($width, $height, $text, $border = 0, $align = 'L')
    {
        $documents = $this->_match($text);
        foreach($documents as $document) {

            // When result mapping is \n (Enter) create break line
            // according to height parameter
            if ($document == "\n") {
                $this->Ln($height);
            }

            // When result mapping is sentences
            else {

                // Defined format number
                preg_match('/^\d+[.|\s+]/', $document, $match);
                foreach ($match as $key) {

                    // Create number label
                    $this->Cell(5, $height, sprintf("%s. ", str_replace(".", "", $key)), $border, 0, 'L');

                    // Create value beside of number label
                    $this->MultiCell($width > 0 ? $width - 5 : $width, $height, trim(str_replace($key, "", $document)), $border, $align);

                    // Clear rendered text
                    $document = str_replace($document, '', $document);
                }

                // When there is text that was not beed rendered
                // Will rendered using MultiCell
                if (!empty($document))
                    $this->MultiCell($width, $height, $document, $border, $align);

                // Recovery break line of MultiCell
                $this->SetY($this->GetY() - $height);
            }
        }
    }

    /**
     * @param string $text
     * @param array $documents
     * @return array
     * */
    private function _match($text, $mapped = [])
    {
        // Mapping text based on enter to array
        preg_match('/[\n]/', $text, $match);

        // When there is text have \n (Enter)
        if(count($match) > 0) {

            // Searching for the index position of the text found
            $strPos = strpos($text, $match[0]);

            // If text found is \n (Enter) will push to array document with value \n
            // Add remove the first \n (Enter) found
            if($strPos == 0) {
                $mapped[] = "\n";

                preg_match_all('/\n\w+/', $text, $subMatch);

                $explode = explode("\n", $text);
                if(count($subMatch[0]) == 0) {
                    // When only found \n on text, remove first and second array explode
                    array_splice($explode, 0, 2);
                    $explode = implode("\n", array_fill(0, count($explode), "\n"));
                } else {
                    // When not only found \n on text, remove only first array explode
                    array_splice($explode, 0, 1);
                }

                // Set text according to array explode
                $text = implode("\n", $explode);
            }

            // If text contains another words
            else {
                $value = substr($text, 0, $strPos);
                $mapped[] = $value;

                $text = substr($text, strlen($value), strlen($text));
            }

            // Loop until all the text has been mapped
            if(!empty($text)) {
                return $this->_match($text, $mapped);
            }
        }

        // When there is text not only \n (Enter)
        else {
            $mapped[] = $text;
        }

        // Returned mapped data
        return $mapped;
    }

    public function response()
    {
        $this->Output();
        exit;
    }
}
