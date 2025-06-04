<?php

namespace App\Helpers;

use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;
use Google\Cloud\Storage\StorageClient;

class FirebaseStorageHelper
{
    // Method to check if a file exists in Firebase Storage
    public static function checkFileExists($filePath)
    {
        $serviceAccountPath = storage_path('app/firebase-auth.json');
        if (!file_exists($serviceAccountPath)) {
            throw new \Exception('Firebase service account file does not exist at path: ' . $serviceAccountPath);
        }

        $factory = (new Factory)->withServiceAccount($serviceAccountPath);
        $storage = $factory->createStorage();
        $bucket = $storage->getBucket();

        $object = $bucket->object($filePath);
        return $object->exists(); // Returns true if the file exists, false otherwise
    }
    
    // Method to upload a regular file and return a signed URL
    public static function uploadFile($file, $type)
    {
        $serviceAccountPath = storage_path('app/firebase-auth.json');
        if (!file_exists($serviceAccountPath)) {
            throw new \Exception('Firebase service account file does not exist at path: ' . $serviceAccountPath);
        }
    
        $factory = (new Factory)->withServiceAccount($serviceAccountPath);
        $storage = $factory->createStorage();
        $bucket = $storage->getBucket();
    
        $fileName = $file->getClientOriginalName();
        $filePath = self::generateFilePath($type, $fileName);
    
        $firebaseFile = fopen($file->getPathname(), 'r');
        // Set predefinedAcl to publicRead
        $bucket->upload($firebaseFile, [
            'name' => $filePath,
            'predefinedAcl' => 'publicRead'
        ]);
    
        // Generate a public URL for the uploaded file
        return self::getSignedUrl($filePath);
    }
    
    // Method to upload a base64-encoded image and return a signed URL
    public static function uploadBase64Image($base64Image, $type, $fileName)
    {
        $serviceAccountPath = storage_path('app/firebase-auth.json');
        if (!file_exists($serviceAccountPath)) {
            throw new \Exception('Firebase service account file does not exist at path: ' . $serviceAccountPath);
        }

        // Decode the base64 image
        $imageData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $base64Image));

        // Generate file path for storage
        $filePath = self::generateFilePath($type, $fileName);

        // Initialize Firebase Storage
        $factory = (new Factory)->withServiceAccount($serviceAccountPath);
        $storage = $factory->createStorage();
        $bucket = $storage->getBucket();

        // Create a file resource for the image
        $firebaseFile = fopen('php://memory', 'r+');
        fwrite($firebaseFile, $imageData);
        rewind($firebaseFile);

        // Upload the image to Firebase Storage with public-read access
        $bucket->upload($firebaseFile, [
            'name' => $filePath,
            'predefinedAcl' => 'publicRead'
        ]);

        // Return public URL for the uploaded file
        return self::getSignedUrl($filePath);
    }

    // Method to delete a file from Firebase Storage
    public static function deleteFile($fileUrl)
    {
        Log::info("Deleting file:", ['path' => $fileUrl]);

        try {
            // Parse URL to get the path
            $parsedUrl = parse_url($fileUrl);
            if (!isset($parsedUrl['path'])) {
                throw new \Exception('Invalid URL provided.');
            }
    
            // Decode and clean file path
            $bucketName = 'sama-pet.appspot.com';
            $filePath = urldecode(ltrim(str_replace($bucketName, '', $parsedUrl['path']), '/'));
    
            // Log file path for debugging
            Log::info('Extracted File Path: ' . $filePath);
    
            // Validate Firebase service account file
            $serviceAccountPath = storage_path('app/firebase-auth.json');
            if (!file_exists($serviceAccountPath)) {
                throw new \Exception('Firebase service account file does not exist at path: ' . $serviceAccountPath);
            }
    
            // Initialize Firebase Storage
            $factory = (new Factory)->withServiceAccount($serviceAccountPath);
            $storage = $factory->createStorage();
            $bucket = $storage->getBucket();
    
            // Attempt to delete the file
            $object = $bucket->object($filePath);
            if ($object->exists()) {
                $object->delete();
                Log::info('File successfully deleted: ' . $filePath);
            } else {
                throw new \Exception('File does not exist at path: ' . $filePath);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting file: ' . $e->getMessage());
            throw $e; // Rethrow the exception for higher-level handling
        }
    }

    public static function getFilePathFromUrl($url)
    {
        // Extract the file path from the public URL
        $parsedUrl = parse_url($url);
        if (isset($parsedUrl['path'])) {
            // Remove the leading "/" from the path
            return ltrim($parsedUrl['path'], '/');
        }
        throw new \Exception('Invalid URL: Unable to extract file path.');
    }

    public static function deleteFileByUrl($url)
    {
        try {
            // Extract the file path from the URL
            $filePath = self::getFilePathFromUrl($url);
    
            // Call deleteFile with the extracted file path
            self::deleteFile($filePath);
        } catch (\Exception $e) {
            throw new \Exception('Error deleting file: ' . $e->getMessage());
        }
    }
    
    // Helper method to generate file path based on type
    private static function generateFilePath($type, $fileName)
    {
        $pathMap = [
            'profileImage' => 'profile_images/',
            'petImage' => 'pet_images/',
            'petDocuments' => 'pet_documents/',
            'providerDocuments' => 'provider_documents/',
            'productDocuments' => 'product_documents/',
            'serviceDocuments' => 'service_documents/',
            'doctorImages' => 'doctor_images/',
            'CategoryDocument' => 'category_document/',
            'GalleryData' => 'gallery_data/'
        ];

        if (!array_key_exists($type, $pathMap)) {
            throw new \Exception('Invalid file type: ' . $type);
        }

        return $pathMap[$type] . uniqid() . '_' . $fileName;
    }

    // Method to get a public URL for accessing a file (instead of a signed URL)
    public static function getSignedUrl($filePath)
    {
        $serviceAccountPath = storage_path('app/firebase-auth.json');
        if (!file_exists($serviceAccountPath)) {
            throw new \Exception('Firebase service account file does not exist at path: ' . $serviceAccountPath);
        }
    
        $factory = (new Factory)->withServiceAccount($serviceAccountPath);
        $storage = $factory->createStorage();
        $bucket = $storage->getBucket();
    
        // Construct and return the public URL.
        // Ensure that files are uploaded with public-read access.
        return "https://storage.googleapis.com/" . $bucket->name() . "/" . $filePath;
    }
}
