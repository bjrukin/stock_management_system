<?php
namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
trait StoreImageTrait {

    //$folder = '/folder_name/'
    public function StoreImage(UploadedFile $file, $file_name, $folder, $disk = 'public') {
        $fileName = $file_name . '.' . $file->getClientOriginalExtension();
        $file->storeAs($folder, $fileName, $disk);
        return $fileName;
    }

    public function StoreResizeImage(UploadedFile $file, $file_name, $folder, $width, $height, $disk = 'public') {
        $fileName = $file_name . '.' . $file->getClientOriginalExtension();
        $img = Image::make($file->getRealPath());
        $img->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->stream();
        Storage::disk($disk)->put($folder . $fileName, $img);
        return $fileName;
    }

    public function StoreResizeImageWithThumb(UploadedFile $file, $file_name, $folder, $width, $height, $thumb_width, $thumb_height, $disk = 'public') {
        $fileName = $file_name . '.' . $file->getClientOriginalExtension();
        $img = Image::make($file->getRealPath());
        $img->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->stream();
        Storage::disk($disk)->put($folder . $fileName, $img);
        //for thumb
        $img = Image::make($file->getRealPath());
        $img->resize($thumb_width, $thumb_height, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->stream();
        Storage::disk($disk)->put($folder . 'thumb/' . $fileName, $img);
        return $fileName;
    }

    public function StoreBase64Image($file, $folder, $file_name, $disk = 'public') {
        $mime = substr($file, 5, strpos($file, ';') - 5);
        $extension = explode('/', $mime)[1];
        $filename = $file_name . '.' . $extension;
        $base64_string = substr($file, strpos($file, ',') + 1);
        $image_data = base64_decode($base64_string);        
        $folder = rtrim($folder, '/') . '/';
        Storage::disk($disk)->put($folder . $filename, $image_data);
        return $filename;
    }

    public function StoreBase64ResizeImage($file, $folder, $file_name, $width, $height, $disk = 'public') {

        $mime = substr($file, 5, strpos($file, ';') - 5);
        $extension = explode('/', $mime)[1];
        $filename = $file_name . '.' . $extension;
        $base64_string = substr($file, strpos($file, ',') + 1);
        $image_data = base64_decode($base64_string);
        $folder = rtrim($folder, '/') . '/';
        Storage::disk($disk)->put($folder . $filename, $image_data);

        $sourcePath = Storage::disk($disk)->path($folder . $filename);
        $resizeFullPath = Storage::disk($disk)->path($folder . $filename);

        $image = Image::make($sourcePath)->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $image->save($resizeFullPath, 100, $extension);
        return $filename;
    }

    public function StoreBase64ImageWithThumb($file, $folder, $file_name, $thumb_width, $thumb_height, $disk = 'public') {
        $mime = substr($file, 5, strpos($file, ';') - 5);
        $extension = explode('/', $mime)[1];
        $filename = $file_name . '.' . $extension;
        $base64_string = substr($file, strpos($file, ',') + 1);
        $image_data = base64_decode($base64_string);
        $folder = rtrim($folder, '/') . '/';
        Storage::disk($disk)->put($folder . $filename, $image_data);

        $sourcePath = Storage::disk($disk)->path($folder . $filename);
        $thumbFullPath = Storage::disk($disk)->path($folder . 'thumb/'. $filename);

        $image = Image::make($sourcePath)->resize($thumb_width, $thumb_height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $image->save($thumbFullPath, 100, $extension);
        return $filename;
    }
}