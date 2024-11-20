<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Spiral\RoadRunner;
use Google\Cloud\Storage\StorageClient;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use OpenTelemetry\SDK\Trace\TracerProviderFactory;
use OpenTelemetry\API\Trace\Propagation\TraceContextPropagator;
use OpenTelemetry\SDK\Common\Log\LoggerHolder;
use OpenTelemetry\Context\Context; 

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
    $tracerProvider = (new TracerProviderFactory())->create();
    $tracer = $tracerProvider->getTracer('tcl');
    $log->info("Received request: " . json_encode($req->getServerParams()));


    $parent = TraceContextPropagator::getInstance()->extract($req->getHeaders());
    $rootSpan = $tracer
        ->spanBuilder('root')
        ->setParent($parent)
        ->startSpan();
    $scope = $rootSpan->activate();
    $ctx = $rootSpan->storeInContext(Context::getCurrent());

    $log->info("OTEL TraceID: " . $rootSpan->getContext()->getTraceId());
    $log->info("OTEL SpanID: " . $rootSpan->getContext()->getSpanId());

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
        $childSpan = $tracer
                ->spanBuilder('gcs')
                ->startSpan();
        $log->info("OTEL TraceID: " . $rootSpan->getContext()->getTraceId());
        $log->info("OTEL SpanID: " . $rootSpan->getContext()->getSpanId());
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
        $childSpan->end();

<<<<<<< HEAD
        // Call to another microservice after successful upload
        $client = new GuzzleHttp\Client();  // Or any other HTTP client
        $log->info("Calling other microservice: https://micropython-43662665854.europe-southwest1.run.app/predict");
        $response = $client->request('GET', 'https://micropython-43662665854.europe-southwest1.run.app/predict', []);
        $log->info("Response from other microservice: " . $response->getStatusCode() . " Body: " . $response->getBody()->getContents());
=======
        // Wrap the microservice call in a span
        $childSpan = $tracer
                ->spanBuilder('micropython')
                ->startSpan();
        $log->info("Parent OTEL TraceID: " . $rootSpan->getContext()->getTraceId() . " Child OTEL TraceID: " . $childSpan->getContext()->getTraceId());
        $log->info("Parent OTEL SpainID: " . $rootSpan->getContext()->getSpanId() . " Child OTEL SpanId: " . $childSpan->getContext()->getSpanId());
        
        $carrier = [];
        TraceContextPropagator::getInstance()->inject($carrier, null, $ctx);
        foreach ($carrier as $name => $value) {
            $log->info("Header name: " . $name);
            $log->info("Header value: " . $value);
        }
        

        // Create the Guzzle client *with* the instrumented handler stack
        $client = new GuzzleHttp\Client();
        $log->info("Calling other microservice: http://0.0.0.0:8081/predict");
        $log->info("headers" . $carrier);
        $response = $client->request('GET', 'http://0.0.0.0:8081/predict',  [
            'headers' => $carrier,
        ]);
        $prediction = $response->getBody()->getContents();
        $log->info("Response from other microservice: " . $response->getStatusCode() . " Body: " . $prediction);

        $childSpan->end();
        $rootSpan->end();
>>>>>>> 80f8ce8 (first draft)

        // Check the response from the other microservice
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            // Successful response

            $log->info("Successful response from other microservice.");
            $log->info("Sending 200 OK response.");
            $psr7->respond(
                new \Nyholm\Psr7\Response(
                    200,
                    [],
<<<<<<< HEAD
                    json_encode(['message' => 'File uploaded and processed successfully', 'content' => $response->getBody()->getContents()])
=======
                    json_encode(['message' => 'File uploaded and processed successfully', 'content' => $prediction])
>>>>>>> 80f8ce8 (first draft)
                )
            );
            
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
    } finally {
        $scope->detach();
    }
    if (file_exists($tempFile)) {
        unlink($tempFile);
        $log->debug("Temporary file deleted: " . $tempFile);
    } else {
        $log->warning("Temporary file does not exist: " . $tempFile);
    }
}
$tracerProvider->shutdown();
