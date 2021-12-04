<?php

class DBText
{

    static public function inputPlaceholder($label)
    {
        return sprintf("Ketik %s disini ...", strtolower($label));
    }

    static public function datePlaceholder($format = 'dd / mm / YYYY')
    {
        return sprintf($format);
    }
}
