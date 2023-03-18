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



