<?php

namespace App\Helpers\Uploader;

use App\Helpers\Collections\Config\ConfigCollection;
use App\Helpers\Collections\Files\FileCollection;
use App\Models\Masters\File;
use Illuminate\Support\Facades\File as SupportFile;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUpload
{

    /**
     * @param string $index
     * @return FileUpload
     * */
    static public function upload($index)
    {
        return new FileUpload($index);
    }

    /* @var ConfigCollection */
    protected $refType;

    /* @var integer */
    protected $refId;

    /* @var FileCollection */
    protected $collection;

    protected $index;

    /* @var UploadedFile|UploadedFile[] */
    protected $files;

    protected $id;

    protected $descCreate = [];

    protected $descUpdate = [];

    protected $uploadedFiles = [];

    protected $deleted = [];

    /* @var FileCollection[] */
    protected $savedFiles = [];

    /**
     * @param string $index
     * */
    public function __construct($index)
    {
        $this->index = $index;
        $this->files = Request::file($index);
        $this->id = Request::input(sprintf("%s_id", $index));
        $this->descCreate = Request::input(sprintf("%s_desc_create", $index));
        $this->descUpdate = Request::input(sprintf("%s_desc_update", $index));
        $this->deleted = Request::input(sprintf("%s_deleted", $index));
    }

    public function setReference($refType, $refId)
    {
        $this->refType = $refType;
        $this->refId = $refId;

        return $this;
    }

    /**
     * @param string $directory
     * @param null $callable
     * @return FileUpload
     *
     */
    public function moveTo($directory, $callable = null)
    {
        if(Request::hasFile($this->index)) {
            foreach($this->files as $i => $file)
            {
                $filename = sprintf("%s-%s.%s", date('YmdHis'), $i, $file->getClientOriginalExtension());
                if(!is_null($callable))
                    $filename = call_user_func_array($callable, [$file, $i]);

                $file->move(storage_path($directory), $filename);

                $this->uploadedFiles[] = (object) [
                    'file_name' => $filename,
                    'directory' => $directory,
                    'file' => $file,
                ];
            }
        }

        return $this;
    }

    /**
     * @return FileUpload
     *
     * @throws \Exception
     */
    public function save()
    {
        if(is_null($this->refType))
            throw new \Exception("Tidak dapat menyimpan sebelum upload file diketahui. Gunakan setReference untuk mengidentifikasi upload file");

        if(is_null($this->refId))
            throw new \Exception("Tidak dapat menyimpan sebelum reference id diketahui. Gunakan setReference untuk mengidentifikasi upload file");

        foreach($this->uploadedFiles as $i => $file) {
            /* @var UploadedFile $binaryFile */
            $binaryFile = $file->file;

            $description = $this->descCreate[$i] ?? null;

            $this->savedFiles[] = FileCollection::create([
                'ref_type_id' => $this->refType->getId(),
                'ref_id' => $this->refId,
                'directory' => $file->directory,
                'file_name' => $file->file_name,
                'file_size' => SupportFile::size(storage_path($file->directory) . DIRECTORY_SEPARATOR . $file->file_name),
                'mime_type' => $binaryFile->getClientMimeType(),
                'description' => $description,
                'created_at' => currentDate(),
                'updated_at' => currentDate(),
            ]);
        }

        return $this;
    }

    public function update()
    {
        if(is_null($this->refType))
            throw new \Exception("Tidak dapat menyimpan sebelum upload file diketahui. Gunakan setReference untuk mengidentifikasi upload file");

        if(is_null($this->refId))
            throw new \Exception("Tidak dapat menyimpan sebelum reference id diketahui. Gunakan setReference untuk mengidentifikasi upload file");

        $deletedFile = File::query()->whereIn('id', $this->deleted)->get();
        fileUnlink($deletedFile);

        File::query()->whereIn('id', $this->deleted)->delete();

        $this->save();

        return $this;
    }

    public function updateDesc()
    {
        foreach($this->id as $i => $id) {
            File::query()->where('id', $id)
                ->update([
                    'description' => $this->descUpdate[$i] ?? null,
                ]);
        }
    }

    public function rollBack()
    {
        fileUnlink($this->uploadedFiles);

        $deletedId = [];
        foreach($this->savedFiles as $saved) {
            if(!in_array($saved->getId(), $deletedId))
                $deletedId[] = $saved->getId();
        }

        File::query()->whereIn('id', $deletedId)
            ->delete();
    }
}
