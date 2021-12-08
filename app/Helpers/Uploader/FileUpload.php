<?php

namespace App\Helpers\Uploader;

use App\Helpers\Collections\Config\ConfigCollection;
use App\Helpers\Collections\Files\FileCollection;
use App\Models\Masters\File;
use Illuminate\Support\Facades\File as SupportFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUpload
{

    /**
     * @param UploadedFile|UploadedFile[] $files
     * @return FileUpload
     * */
    static public function upload($files)
    {
        return new FileUpload($files);
    }

    /* @var ConfigCollection */
    protected $refType;

    /* @var integer */
    protected $refId;

    /* @var FileCollection */
    protected $collection;

    /* @var UploadedFile|UploadedFile[] */
    protected $files;

    protected $uploadedFiles = [];

    /* @var FileCollection[] */
    protected $savedFiles = [];

    /**
     * @param UploadedFile|UploadedFile[] $files
     * */
    public function __construct($files)
    {
        $this->files = is_array($files) ? $files : [$files];
    }

    public function setReference($refType, $refId)
    {
        $this->refType = $refType;
        $this->refId = $refId;

        return $this;
    }

    /**
     * @param string $directory
     * @param callable|null $filename
     * @return FileUpload
     *
     * @throws \Exception
     */
    public function moveTo($directory, $callable = null)
    {
        foreach($this->files as $file)
        {
            $filename = sprintf("%s.%s", date('YmdHis'), $file->getClientOriginalExtension());
            if(!is_null($callable))
                $filename = call_user_func_array($callable, [$file]);

            $file->move(storage_path($directory), $filename);

            $this->uploadedFiles[] = (object) [
                'file_name' => $filename,
                'directory' => $directory,
                'file' => $file,
            ];
        }

        return $this;
    }

    /**
     * @return FileUpload
     * */
    public function save()
    {
        if(is_null($this->refType))
            throw new \Exception("Tidak dapat menyimpan sebelum upload file diketahui. Gunakan setReference untuk mengidentifikasi upload file");

        if(is_null($this->refId))
            throw new \Exception("Tidak dapat menyimpan sebelum reference id diketahui. Gunakan setReference untuk mengidentifikasi upload file");

        foreach($this->uploadedFiles as $file) {
            /* @var UploadedFile $binaryFile */
            $binaryFile = $file->file;

            $this->savedFiles[] = FileCollection::create([
                'ref_type_id' => $this->refType->getId(),
                'ref_id' => $this->refId,
                'directory' => $file->directory,
                'file_name' => $file->file_name,
                'file_size' => SupportFile::size(storage_path($file->directory) . DIRECTORY_SEPARATOR . $file->file_name),
                'mime_type' => $binaryFile->getClientMimeType(),
                'created_at' => currentDate(),
                'updated_at' => currentDate(),
            ]);
        }

        return $this;
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
