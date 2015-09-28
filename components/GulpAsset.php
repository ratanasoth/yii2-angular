<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use InvalidArgumentException;

class GulpAsset extends Component
{
    /**
     * @var string Asset directory on server
     */
    public $assetDir = "@webroot/compiled/";

    /**
     * @var string Web directory
     */
    public $webDir = "/compiled/";

    /**
     * @var string Manifest file
     */
    public $manifestFile = "rev-manifest.json";

    /**
     * @var bool Whether or not to use the manifest file. This is useful for dev
     *           (where you typically disable the cache and/or refresh constantly)
     */
    public $useManifest = true;

    /**
     * @var array
     */
    private $manifest;

    /**
     * Get asset file
     * @param $file
     * @return string
     * @throws InvalidConfigException|InvalidArgumentException
     */
    public function getFile($file)
    {
        // check if we should return the file
        $webDir = rtrim($this->webDir, "/");
        if (!$this->useManifest) {
            return "{$webDir}/{$file}";
        }

        // get manifest data if needed
        $assetDir = Yii::getAlias($this->assetDir);
        $assetDir = rtrim($assetDir, "/");
        $manifestPath = "$assetDir/{$this->manifestFile}";
        if ($this->manifest === null) {
            if (!is_file($manifestPath)) {
                throw new InvalidConfigException("Manifest file {$manifestPath} does not exist.");
            }
            $manifest = json_decode(file_get_contents("$assetDir/rev-manifest.json"), true);
        }

        if (isset($manifest[$file])) {
            return "{$webDir}/{$manifest[$file]}";
        }

        throw new InvalidArgumentException("File {$file} not defined in asset manifest.");
    }
}