<?php

class DBText
{

    const noAction = '<i>Tidak ada aksi tersedia</i>';

    static public function renderAction($roles)
    {
        $action = false;
        foreach ($roles as $role) {
            if($role != false) {
                $action = true;
                break;
            }
        }

        return $action ? implode("", $roles) : DBText::noAction;
    }

    static public function inputPlaceholder($label)
    {
        return sprintf("Ketik %s disini ...", strtolower($label));
    }

    static public function datePlaceholder($format = 'dd / mm / YYYY')
    {
        return sprintf($format);
    }
}
