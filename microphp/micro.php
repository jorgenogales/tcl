<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Spiral\RoadRunner;
use Google\Cloud\Storage\StorageClient;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require 'vendor/autoload.php';

$log = new Logger('microphp');

// Configure Monolog to output to stdout in the format RoadRunner expects
$streamHandler = new StreamHandler('php://stderr', Logger::DEBUG);
$streamHandler->setFormatter(new \Monolog\Formatter\LineFormatter()); 

$log->pushHandler($streamHandler);
$log->info("About to create RoadRunner worker");
$worker = RoadRunner\Worker::create();
$log->info("worker created");
$psr7 = new RoadRunner\Http\PSR7Worker($worker, new \Nyholm\Psr7\Factory\Psr17Factory(), new \Nyholm\Psr7\Factory\Psr17Factory(), new \Nyholm\Psr7\Factory\Psr17Factory());
$log->info("Accepting requests");


while ($req = $psr7->waitRequest()) {
    $log->info("Received request: " . json_encode($req->getServerParams()));
    try {
        // Generate a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'upload');
        $log->debug("Temporary file created: " . $tempFile);
        $fileSize = 4 * 1024; // 4KB
        $fileContent = str_repeat('a', $fileSize);
        file_put_contents($tempFile, $fileContent);
        $log->debug("Temporary file populated with " . $fileSize . " bytes.");

        // Upload to Google Cloud Storage
        $storage = new StorageClient();
        $bucketName = 'tklis';
        $bucket = $storage->bucket($bucketName);
        $objectName = uniqid() . '_temp_file.txt';
        $log->info("Uploading to GCS bucket: " . $bucketName . ", object name: " . $objectName);
        $object = $bucket->upload(
            fopen($tempFile, 'r'),
            [
                //'predefinedAcl' => 'publicRead' // Example: making the file public
            ]
        );
        $log->info("File uploaded successfully. Media link: " . $object->info()['mediaLink']);

        // Call to another microservice after successful upload
        $client = new GuzzleHttp\Client();  // Or any other HTTP client
        $log->info("Calling other microservice: http://other-microservice-url");
        $response = $client->request('POST', 'http://other-microservice-url', [
            'json' => [
                'fileUrl' => $object->info()['mediaLink'], // Pass the file URL
                // ... any other data needed by the other microservice
            ]
        ]);
        $log->info("Response from other microservice: " . $response->getStatusCode());

        // Check the response from the other microservice
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            // Successful response
            $log->info("Successful response from other microservice.");
            $psr7->respond(
                new \Nyholm\Psr7\Response(
                    200,
                    [],
                    json_encode(['message' => 'File uploaded and processed successfully', 'url' => $object->info()['mediaLink']])
                )
            );
            $log->info("Sending 200 OK response.");
        } else {
           // Handle errors from the other microservice
            $log->error("Error response from other microservice: " . $response->getStatusCode());
             $psr7->respond(new \Nyholm\Psr7\Response(500, [], json_encode(['error' => 'Error communicating with other microservice: ' . $response->getBody()])));
             $log->error("Sending 500 Internal Server Error response.");
        }

    } catch (\Throwable $e) {
        $log->error("Exception caught: " . $e->getMessage());
        $log->error("Stack trace: " . $e->getTraceAsString());
        $psr7->respond(new \Nyholm\Psr7\Response(500, [], json_encode(['error' => $e->getMessage()])));
        $log->error("Sending 500 Internal Server Error response.");
    }
    if (file_exists($tempFile)) {
        unlink($tempFile);
        $log->debug("Temporary file deleted: " . $tempFile);
    } else {
        $log->warning("Temporary file does not exist: " . $tempFile);
    }

}
