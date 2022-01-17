<?php

namespace App\Helpers\Collections\Projects;

use App\Documents\DocumentPayload;
use App\Helpers\Collections\Files\FileArray;
use App\Helpers\Collections\Files\FileCollection;

class PayloadDocumentSK extends  DocumentPayload
{

    private $defaultContent = "Dear Pemegang Saham,
Setelah melalui tahap campaign, kami dari tim {title} memberikan keputusan mengenai
pembagian porsi saham pada {title} sebagaimana yang tertera pada lampiran SK No
{nomor}. Pemegang Saham yang tercantum pada lampiran diwajibkan untuk membaca dan mengikuti syarat
dan ketentuan berinvestasi {title}.

Syarat dan Ketentuan Berinvestasi di {title}:
1. Pemegang Saham yang tertera namanya pada lampiran Surat Keputusan Porsi Saham ini merupakan yang sudah
menyetorkan dana investasinya.
2. Dalam menjalankan bisnis terdapat risiko atau potensi kerugian yang harus dipahami baik oleh pihak penerbit
maupun pihak pemegang saham.
3. Pembagian surplus kas dikemudian hari berdasarkan persentase modal yang telah disetorkan oleh masing-masing
Pemegang Saham.";

    public function getTitle($default = null)
    {
        return $this->get('title', $default);
    }

    public function getRegards($default = null)
    {
        return $this->get('regards', $default);
    }

    public function getAddress($default = null)
    {
        return $this->get('address', $default);
    }

    public function getNoDocument($default = null)
    {
        return $this->get('nomor', $default);
    }

    public function getNumberOfAttachment($default = 0)
    {
        return $this->get('number_of_attachment', $default);
    }

    public function getPlace($default = null)
    {
        return $this->get('place', $default);
    }

    public function getDate($format = 'd/m/Y', $defaultDate = null)
    {
        $date = dbDate($this->get('date'), $format);
        return !is_null($date) ? $date : dbDate($defaultDate, $format);
    }

    public function getContent($formatted = false, $default = null)
    {
        $content = $this->get('content', is_null($default) ? $this->defaultContent : $default);

        if($formatted) {
            $formatted = [
                '{title}' => $this->getTitle(),
                '{nomor}' => $this->getNoDocument(),
            ];

            foreach($formatted as $key => $value)
                $content = str_replace($key, $value, $content);
        }

        return $content;
    }

    public function getSignatureName($default = null)
    {
        return $this->get('signature_name', $default);
    }

    public function getSignatureJson()
    {
        return $this->get('signature');
    }

    public function getSignature()
    {
        return new FileArray(json_decode($this->getSignatureJson()));
    }
}
