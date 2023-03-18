# laravel-file-system
Laravel File System is a Laravel package that provides a simple API to store and manage files on an S3 bucket. It allows you to easily upload, copy, and delete files, and provides useful methods to get the URL or path of a file in the S3 bucket.

## Installation
Installation
```
composer require gulzarali/laravel-file-system
```

## Usage
First, create a new instance of the LaravelFileSystem class:
```
namespace GulzarAli\LaravelFileSystem;

$fileSystem = new LaravelFileSystem();
```

## Upload a file
To upload a file, call the store method with the file data, the file folder, and the file visibility (optional, default is "public"):

```
$fileSystem->file = ''; //can be data url or file binary
$fileSystem->is_data_url = false; //true/false, default=false
$fileSystem->store($fileData, 'my-folder', 'public');
```

## Copy a file
```
$fileSystem->copy('my-folder/my-file.jpg', 'new-folder/new-file.jpg');
```

## Delete a file
To delete a file, call the delete method with the file path:
```
$fileSystem->delete('my-folder/my-file.jpg');
```

## Get the URL of a file
```
$fileSystem->file_full_path; // full file path including the aws folder
```

## Get the path of a file
```
$fileSystem->file_path; // file relative path including the aws folder
```

## Attributes
| Attribute       | Is Required     | Description|
| --------------- | --------------- | ---------- |
| file            | true            | public attribute to set the file to be uploaded, can be data url or file binary|
| is_data_url     | optional        | if going to upload the base64 url then set it to true, otherwise it will be considered as binary|
| extension       | optional        | required in case of data url, otherwise it will be extracted from the uploaded file |
| file_name       | optional        | The name of the target file without extension, if not provided a unique file name will get generated|
| visibility      | optional        | can be public/private. Visibility of the file at s3 bucket. default: public |
| file_path       | returned        | the relative path of the file. aws-folder/aws-file-folder/file-name.extension|
| file_full_path  | returned        | the full file path: https://bucket-storage-url.com/bucket-folder/file-name.extension |
| origin_file_path| optional        | s3 relative path of file: for copy method |
| bucket_folder   | optional        | project folder in the s3 bucket |
| file_folder     | optional        | s3 folder to group files in s3 bucket |
| aws_root_path   | optional        | aws root folder: if not provided then it can be fetched from .env file|



