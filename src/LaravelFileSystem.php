<?php
namespace GulzarAli\LaravelFileSystem;

use Illuminate\Support\Facades\Storage;
use Webpatser\Uuid\Uuid;

class LaravelFileSystem
{
    /**
     * Can be either data url or file binary
     */
    public $file;

    /**
     * @param boolean true/false
     */
    public $is_data_url;

    /**
     * @param string extention without "." operator: png, jpg, json...
     */
    public $extension;

    /**
     * @param string file-name.extension
     */
    public $file_name;
    
    /**
     * File visibility at s3 bucket
     * @param string public/private
     */
    public $visibility;

    /**
     * @param string file path relative to the bucket folder: bucket-folder/file-name.extension
     */
    public $file_path;

    /**
     * @param string file full path including the s3 bucket storage path: https://bucket-storage-url.com/bucket-folder/file-name.extension
     */
    public $file_full_path;

    /**
     * @param boolean true/false
     */
    public $is_binary;

    /**
     * @param string s3 relative path of file: for copy method
     */
    public $origin_file_path;

    /**
     * @param string s3 bucket folder
     */
    public $bucket_folder;

    /**
     * @param string file specific folder inside the bucket folder
     */
    public $file_folder;

    /**
     * @param string root path of the files relative to aws s3 bucket: projectname/storage
     */
    public $aws_root_path;

    public function __construct()
    {
        $this->file_name            =   (string)Uuid::generate(4);
        if (!$this->bucket_folder &&    config()->has('app.aws_folder')) {
            $this->bucket_folder    =   config('app.aws_folder');
        }
        if (!$this->aws_root_path &&    config()->has('app.aws_root_path')) {
            $this->aws_root_path    =   config('app.aws_root_path');
        }
    }

    /**
     * @param string  $visibility     private/public
     * @param string  $file           dataUrl, binary contents
     * @param string  $file_folder    storage folder: folder/innerfolder
     * 
     * @return  void
     */
    public function store($file, $file_folder, $visibility = 'public')
    {
        $this->visibility       =   $visibility;
        $this->file             =   $file;
        $this->file_folder      =   $file_folder;
        $this->getExtension()
            ->fileNameWithExtension()
            ->uploadFile()
            ->uploadedFilePath()
            ->uploadedFileFullPath();
    }
    /**
     * copy the image from one location to new location
     * @param string    $origin_path project_folder/dev/ef0bc72c-jiko-hyju-8f7e-761dcd133af3.png
     * @param string    $file_folder storage folder: folder/innerfolder
     * @param string    $origin_path   
     */
    public function copy($origin_path, $file_folder)
    {
        $this->origin_file_path =   $origin_path;
        $this->file_folder      =   $file_folder;
        $this->fileNameWithExtension()
            ->copyFile()
            ->uploadedFilePath()
            ->uploadedFileFullPath();
    }

    /**
     * @return object $this
     */
    protected function copyFile()
    {
        Storage::disk('s3')->copy(
            $this->origin_file_path,
            $this->formatedPath()
        );
        return $this;
    }

    /**
     * file extension: png, jpg, json ...
     * @return object $this
     */
    protected function getExtension()
    {
        if (!$this->extension) {
            $this->extension = $this->file->getClientOriginalExtension();
        }
        return $this;
    }

    /**
     * @param string extension: png, jpg, pdf
     * @param string file_name: name of the file without extension
     */
    protected function fileNameWithExtension()
    {
        $this->file_name        =   $this->file_name . '.' . $this->extension;
        return $this;
    }

    /**
     * @return object $this
     */
    protected function uploadFile()
    {
        $file = $this->file;
        if (!$this->is_data_url && !$this->is_binary) {
            $file = file_get_contents($this->file);
        }
        Storage::disk('s3')->put(
            $this->formatedPath(),
            $file,
            $this->visibility
        );
        return $this;
    }

    /**
     * Returns the relative path of the uploaded file in the S3 bucket.
     * @return $this The current instance of the object.
     * The full path is in the format: bucket-folder/file-name.extension.
     */
    protected function uploadedFilePath()
    {
        $this->file_path =  $this->formatedPath();
        return $this;
    }

    /**
     * Returns the full path of the uploaded file in the S3 bucket.
     * @return $this The current instance of the object.
     * The full path is in the format: https://s3url/bucket-folder/file-name.extension.
     */
    public function uploadedFileFullPath()
    {
        $this->file_full_path = $this->reFormatePath($this->file_path);
        return $this;
    }

    /**
     * Delete the file from s3 bucket against given path
     * @param string relative path of bucket: bucket-folder/file-name.extension
     * @return void
     */
    public function deleteFile($path)
    {
        Storage::disk('s3')->delete($path);
    }

    /**
     * @param string https://bucket-storage-url.com/bucket-folder/file-name.extension
     * its benificial incases where the root path changes project to project
     */
    public function reFormatePath($path){
        if ($this->aws_root_path) {
            return str_replace($this->aws_root_path.'\\', $this->aws_root_path.'/', Storage::disk('s3')->url($path));
        }
        return $path;
    }

    /**
     * formated file path including the bucket-folder/file-folder/file-name.extension
     * @return string bucket-folder/file-folder/file-name.extension
     */
    public function formatedPath(){
        $path = '';
        if($this->bucket_folder){
            $path .= '/'.$this->bucket_folder;
        }
        if($this->file_folder){
            $path .= '/'.$this->file_folder;
        }
        if($this->file_name){
            $path .= '/'.$this->file_name;
        }
        $path = trim($path, "/");
        return $path;
    }
}
