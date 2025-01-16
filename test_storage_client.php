<?php

require 'vendor/autoload.php';

use Google\Cloud\Storage\StorageClient;

try {
    $storage = new StorageClient([
        'keyFilePath' => __DIR__ . '/storage/app/firebase-auth.json'
    ]);

    echo "Storage Client created successfully.\n";
} catch (Exception $e) {
    echo "Error creating Storage Client: " . $e->getMessage() . "\n";
}
