<?php

namespace App\Helpers;
use Illuminate\Http\File;
class encodeImageUrl
{
    public static function UrlConvert($imageData)
    {
  // Extract file type from Base64 string
  if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
    $imageType = $matches[1]; // jpeg, png, jpg, etc.
    $imageData = substr($imageData, strpos($imageData, ',') + 1);
    $imageData = base64_decode($imageData);

    if ($imageData === false) {
        throw new \Exception('Base64 decode failed.');
    }

    // Create a temporary file
    $fileName = uniqid() . '.' . $imageType;
    $tempFilePath = sys_get_temp_dir() . '/' . $fileName;

    // Save the decoded image data to the temporary file
    file_put_contents($tempFilePath, $imageData);

    // Upload to Firebase Storage
    $file = new File($tempFilePath);
    return $file;
    }else{
    return  $imageData;
    }
}
}