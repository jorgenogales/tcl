<?php

use Spiral\RoadRunner;
use Google\Cloud\Storage\StorageClient;

require 'vendor/autoload.php';

$worker = RoadRunner\Worker::create();
$psr7 = new RoadRunner\Http\PSR7Worker($worker, new \Nyholm\Psr7\Factory\Psr17Factory(), new \Nyholm\Psr7\Factory\Psr17Factory(), new \Nyholm\Psr7\Factory\Psr17Factory());


while ($req = $psr7->waitRequest()) {
    try {
        // Generate a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'upload');
        $fileSize = 4 * 1024; // 4KB
        $fileContent = str_repeat('a', $fileSize);
        file_put_contents($tempFile, $fileContent);

        // Upload to Google Cloud Storage
        $storage = new StorageClient();
        $bucketName = 'gs://tklis';
        $bucket = $storage->bucket($bucketName);
        $objectName = uniqid() . '_temp_file.txt';
        $object = $bucket->upload(
            fopen($tempFile, 'r'),
            [
                'predefinedAcl' => 'publicRead' // Example: making the file public
            ]
        );

        // Call to another microservice after successful upload
        $client = new GuzzleHttp\Client();  // Or any other HTTP client
        $response = $client->request('POST', 'http://other-microservice-url', [
            'json' => [
                'fileUrl' => $object->info()['mediaLink'], // Pass the file URL
                // ... any other data needed by the other microservice
            ]
        ]);

        // Check the response from the other microservice
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            // Successful response
            $psr7->respond(
                new \Nyholm\Psr7\Response(
                    200,
                    [],
                    json_encode(['message' => 'File uploaded and processed successfully', 'url' => $object->info()['mediaLink']])
                )
            );
        } else {
           // Handle errors from the other microservice
             $psr7->respond(new \Nyholm\Psr7\Response(500, [], json_encode(['error' => 'Error communicating with other microservice: ' . $response->getBody()])));
        }

    } catch (\Throwable $e) {
        $psr7->respond(new \Nyholm\Psr7\Response(500, [], json_encode(['error' => $e->getMessage()])));
    }
    unlink($tempFile); //Clean up the temporary file

}
