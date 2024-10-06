<?php

declare(strict_types=1);

namespace Franckitho\Textract;

use Aws\Result;
use Aws\Textract\TextractClient;
use Franckitho\Exceptions\FileOrBucketNotFoundException;
use Franckitho\Exceptions\InvalidMethodChainException;
use Illuminate\Support\Collection;

class AnalyseDocument
{
    private TextractClient $client;

    private string|array $features;

    private ?string $file = null;

    private Result $result;

    private ?array $s3Object = null;

    private bool $wantMetadata = false;

    public function __construct()
    {
        $this->client = new TextractClient(
            [
                'region' => config('aws-textract.region'),
                'version' => config('aws-textract.version'),
                'credentials' => [
                    'key' => config('aws-textract.credentials.key'),
                    'secret' => config('aws-textract.credentials.secret'),
                ],
            ]
        );
    }

    /**
     * Set the features for the document analysis.
     *
     * @param  string|array  $features  The features to be set for the analysis. It can be a single feature as a string or multiple features as an array.
     * @return self
     */
    public function features(string|array $features)
    {
        $this->features = $features;

        return $this;
    }

    /**
     * Sets the file property.
     *
     * @param  string  $file  The byte data to be set.
     * @return self
     *
     * @throws InvalidMethodChainException If the file method has already been called.
     */
    public function file(string $file)
    {
        if ($this->s3Object) {
            throw new InvalidMethodChainException;
        }

        $this->file = $file;

        return $this;
    }

    /**
     * Sets the S3 object details.
     *
     * @param  string  $bucket  The name of the S3 bucket.
     * @param  string  $filepath  The path to the file in the S3 bucket.
     * @return self Returns the instance of the class for method chaining.
     *
     * @throws InvalidMethodChainException If the file method has already been called.
     */
    public function s3(string $bucket, string $filepath)
    {
        if ($this->file) {
            throw new InvalidMethodChainException;
        }

        $this->s3Object = [
            'Bucket' => $bucket,
            'Name' => $filepath,
        ];

        return $this;
    }

    /**
     * Enable metadata retrieval for the document analysis.
     *
     * This method sets the internal flag to indicate that metadata should be included
     * in the analysis results. It returns the current instance to allow for method chaining.
     *
     * @return self The current instance with metadata retrieval enabled.
     */
    public function withMetaData()
    {
        $this->wantMetadata = true;

        return $this;
    }

    /**
     * Analyzes the document and returns the result as a collection of blocks.
     *
     * @return array|Collection The analyzed document blocks.
     */
    public function analyze(): array|Collection
    {
        $this->result = $this->fetchAwsTextract();

        return $this->formatResult();
    }

    /**
     * Fetches the document analysis from AWS Textract.
     *
     * @return Result The result of the document analysis.
     */
    protected function fetchAwsTextract(): Result
    {
        return $this->client->analyzeDocument([
            'FeatureTypes' => $this->features,
            'Document' => $this->getFileOrBucket(),
        ]);
    }

    /**
     * Formats the result of the document analysis.
     *
     * This method returns an array or a Collection based on the value of the
     * $this->wantMetadata property. If $this->wantMetadata is true, it returns
     * an array containing 'Blocks', 'DocumentMetadata', and '@metadata'.
     * Otherwise, it returns a Collection of 'Blocks'.
     *
     * @return array|Collection The formatted result of the document analysis.
     */
    protected function formatResult(): array|Collection
    {
        if ($this->wantMetadata) {
            return [
                'Blocks' => collect($this->result->get('Blocks')),
                'DocumentMetadata' => $this->result->get('DocumentMetadata'),
                '@metadata' => $this->result->get('@metadata'),
            ];
        } else {
            return collect($this->result->get('Blocks'));
        }
    }

    /**
     * Retrieves the file or bucket information.
     *
     * This method checks if the bytes property is set and returns an array with the 'Bytes' key.
     * If the bytes property is not set, it checks if the s3Object property is set and returns an array with the 'S3Object' key.
     * If neither property is set, it throws a FileOrBucketNotFoundException.
     *
     * @return array The file or bucket information.
     *
     * @throws FileOrBucketNotFoundException If neither bytes nor s3Object properties are set.
     */
    protected function getFileOrBucket(): array
    {
        if ($this->file) {
            return [
                'Bytes' => file_get_contents($this->file),
            ];
        } elseif ($this->s3Object) {
            return [
                'S3Object' => $this->s3Object,
            ];
        } else {
            throw new FileOrBucketNotFoundException;
        }
    }
}
