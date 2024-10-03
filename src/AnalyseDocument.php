<?php

declare(strict_types=1);

namespace Franckitho\Textract;

use Aws\Result;
use Aws\Textract\TextractClient;
use Illuminate\Support\Collection;

class AnalyseDocument
{
    private TextractClient $client;

    private string|array $features;

    private string $bytes;

    private Result $result;

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
     * @return $this
     */
    public function features(string|array $features)
    {
        $this->features = $features;

        return $this;
    }

    /**
     * Sets the bytes property.
     *
     * @param  string  $bytes  The byte data to be set.
     * @return $this
     */
    public function file(string $bytes)
    {
        $this->bytes = $bytes;

        return $this;
    }

    /**
     * Enable metadata retrieval for the document analysis.
     *
     * This method sets the internal flag to indicate that metadata should be included
     * in the analysis results. It returns the current instance to allow for method chaining.
     *
     * @return $this The current instance with metadata retrieval enabled.
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
     * @return Aws\Result The result of the document analysis.
     */
    protected function fetchAwsTextract(): Result
    {
        return $this->client->analyzeDocument([
            'FeatureTypes' => $this->features,
            'Document' => [
                'Bytes' => file_get_contents($this->bytes),
            ],
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
        return when($this->wantMetadata, function () {
            return [
                'Blocks' => collect($this->result->get('Blocks')),
                'DocumentMetadata' => $this->result->get('DocumentMetadata'),
                '@metadata' => $this->result->get('@metadata'),
            ];
        }, collect($this->result->get('Blocks')));
    }
}
