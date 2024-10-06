<?php

use Aws\Result;
use Aws\Textract\TextractClient;
use Franckitho\Exceptions\FileOrBucketNotFoundException;
use Franckitho\Exceptions\InvalidMethodChainException;
use Franckitho\Textract\AnalyseDocument;
use Illuminate\Support\Collection;

beforeEach(function () {
    // Créer un mock pour TextractClient
    $this->mockTextractClient = Mockery::mock(TextractClient::class);
    
    // Simuler le résultat du document
    $mockResult = Mockery::mock(Result::class);
    $mockResult->shouldReceive('get')
        ->with('Blocks')->andReturn([]);
    $mockResult->shouldReceive('get')
        ->with('DocumentMetadata')->andReturn([]);
    $mockResult->shouldReceive('get')
        ->with('@metadata')->andReturn([]);

    // Configurer le mock pour analyzeDocument
    $this->mockTextractClient->shouldReceive('analyzeDocument')
        ->andReturn($mockResult);

    // Instancier AnalyseDocument
    $this->analyseDocument = new AnalyseDocument();
    
    // Utiliser Reflection pour accéder à la propriété privée $client
    $reflection = new ReflectionClass($this->analyseDocument);
    $clientProperty = $reflection->getProperty('client');
    $clientProperty->setAccessible(true);
    $clientProperty->setValue($this->analyseDocument, $this->mockTextractClient);
});


it('can set features', function () {
    $this->analyseDocument->features(['TABLES', 'FORMS']);

    $reflection = new ReflectionClass($this->analyseDocument);
    $featuresProperty = $reflection->getProperty('features');
    $featuresProperty->setAccessible(true);

    $features = $featuresProperty->getValue($this->analyseDocument);

    expect($features)->toEqual(['TABLES', 'FORMS']);
});

it('can set file', function () {
    $this->analyseDocument->file('path/to/file');

    $reflection = new ReflectionClass($this->analyseDocument);
    $fileProperty = $reflection->getProperty('file');
    $fileProperty->setAccessible(true);

    $file = $fileProperty->getValue($this->analyseDocument);

    expect($file)->toEqual('path/to/file');
});

it('can set S3 object', function () {
    $this->analyseDocument->s3('bucket', 'file');

    $reflection = new ReflectionClass($this->analyseDocument);
    $s3ObjectProperty = $reflection->getProperty('s3Object');
    $s3ObjectProperty->setAccessible(true);

    $s3Object = $s3ObjectProperty->getValue($this->analyseDocument);

    expect($s3Object)->toEqual(['Bucket' => 'bucket', 'Name' => 'file']);
});

it('throws exception when setting file after s3Object', function () {
    $this->analyseDocument->s3('bucket', 'file');
    $this->analyseDocument->file('path/to/file');
})->throws(InvalidMethodChainException::class);

it('can enable metadata retrieval', function () {
    $this->analyseDocument->withMetaData();

    $reflection = new ReflectionClass($this->analyseDocument);
    $wantMetadataProperty = $reflection->getProperty('wantMetadata');
    $wantMetadataProperty->setAccessible(true);

    $wantMetadata = $wantMetadataProperty->getValue($this->analyseDocument);

    expect($wantMetadata)->toBeTrue();
});

it('throws exception if no file or s3 object is set', function () {
    $this->analyseDocument->features(['TABLES', 'FORMS'])->analyze();
})->throws(FileOrBucketNotFoundException::class);

it('analyzes document and formats result', function () {
    // Créer un fichier temporaire avec du contenu factice
    $tempFile = tempnam(sys_get_temp_dir(), 'testfile');
    file_put_contents($tempFile, 'fake file content');

    $mockResult = Mockery::mock(Result::class);
    $mockResult->shouldReceive('get')
        ->with('Blocks')->andReturn([]);
    $mockResult->shouldReceive('get')
        ->with('DocumentMetadata')->andReturn([]);
    $mockResult->shouldReceive('get')
        ->with('@metadata')->andReturn([]);

    $this->mockTextractClient->shouldReceive('analyzeDocument')
        ->andReturn($mockResult);

    $this->analyseDocument->features(['TABLES']);
    $this->analyseDocument->file($tempFile);
    $result = $this->analyseDocument->analyze();

    expect($result)->toBeInstanceOf(Collection::class);

    unlink($tempFile);
});
