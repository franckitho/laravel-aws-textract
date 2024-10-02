<?php

declare(strict_types=1);

namespace Franckitho\Textract;

use Aws\Textract\TextractClient;

class AnalyseDocument
{
    private TextractClient $client;

    private string|array $features;

    private string $bytes;

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

    public function analyze()
    {
        return $this->client->analyzeDocument([
            'FeatureTypes' => $this->features,
            'Document' => [
                'Bytes' => file_get_contents($this->bytes),
            ],
        ]);
    }
}
