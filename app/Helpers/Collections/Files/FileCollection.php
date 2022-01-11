<?php

namespace App\Helpers\Collections\Files;

use App\Helpers\Collections\Collection;
use App\Helpers\Collections\Config\ConfigCollection;
use App\Models\Masters\File;
use Illuminate\Database\Eloquent\Relations\Relation;

class FileCollection extends Collection
{

    static public function create($values)
    {
        /* @var File|Relation $file */
        $file = new File();
        return new FileCollection($file->create($values));
    }

    static public function update($id, $values)
    {
        /* @var File|Relation $file */
        $file = new File();

        return $file->where('id', $id)
            ->update($values);
    }

    public function getId()
    {
        return $this->get('id');
    }

    public function getRefId()
    {
        return $this->get('ref_id');
    }

    public function getRefTypeId()
    {
        return $this->get('ref_type_id');
    }

    /**
     * @return ConfigCollection
     * */
    public function getRefType()
    {
        if($this->hasNotEmpty('ref_type'))
            return new ConfigCollection($this->get('ref_type'));

        return new ConfigCollection();
    }

    public function getDir()
    {
        return $this->get('directory');
    }

    public function getFileName()
    {
        return $this->get('file_name');
    }

    public function getFileSize()
    {
        return $this->get('file_size');
    }

    public function getMimeType()
    {
        return $this->get('mime_type');
    }

    public function getPreview()
    {
        return $this->get('preview');
    }

    public function toJson()
    {
        return json_encode($this->toData());
    }
}
