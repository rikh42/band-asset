<?php
/**
* This file is part of the Small Neat Box Framework
* Copyright (c) 2011-2012 Small Neat Box Ltd.
* For the full copyright and license information, please view the LICENSE.txt
* file that was distributed with this source code.
*/

namespace asset;
use asset\assets\AssetInterface;


/**
* An asset that represents a single file
*/
class AssetManager
{
    protected $asset;
    protected $name;
    protected $writeTo;
    protected $url;
    protected $targetFilename;


    /**
     * @param assets\AssetInterface $asset
     */
    public function __construct(AssetInterface $asset)
    {
        $this->asset = $asset;
        $this->name = '';
        $this->writeTo = '';
        $this->url = '';
        $this->targetFilename = '';
    }


    /**
     * Sets the base URL that appears at the start of output name
     * @param $url
     */
    public function setBaseUrl($url)
    {
        $this->url = $url;
    }


    /**
     * Sets the path that files will be written to
     * @param $path
     */
    public function setWriteTo($path)
    {
        $this->writeTo = $path;
    }


    /**
     * The name of the asset
     * @param string $name
     */
    public function setOutputName($name)
    {
        $this->name = $name;
    }


    /**
     * @param $token
     * @return mixed
     */
    protected function generateTargetFilename($token)
    {
        $filename = $this->writeTo . $this->name;
        $this->targetFilename = str_replace('%token%', $token, $filename);
    }


    /**
     * @param $token
     * @return mixed|string
     */
    protected function getTargetUrl($token)
    {
        // Get the basic URL
        $url = $this->url . $this->name;

        // If it contains the token marker, replace it with the token
        // but if not, attach the token as an argument instead
        if (strstr($url, '%token%') === false) {
            $url .= '?'.$token;
        } else {
            $url = str_replace('%token%', $token, $url);
        }

        // return the URL
        return $url;
    }


    /**
     * Returns the name of the output file.
     * Will generate the file if needed
     * @return string
     */
    public function refresh()
    {
        // Find the files token (changes when any src files or filters are changed)
        $token = $this->asset->getToken();

        // generate the target filename, optionally using the token
        $this->generateTargetFilename($token);

        $refresh = true;
        if (file_exists($this->targetFilename)) {
            // Figure out the last modified time for the asset and target file
            $lastMod = $this->asset->getLastModified();
            $targetLastMod = filemtime($this->targetFilename);

            // Determine if we need to refresh the target file
            if (($targetLastMod !== false) && ($lastMod < $targetLastMod)) {
                $refresh = false;
            }
        }

        // update the file?
        if ($refresh) {
            $this->write();
        }

        // build the filename
        return $this->getTargetUrl($token);
    }


    /**
     * Writes the file to disk
     */
    protected function write()
    {
        // See if the directory exists, and if not, attempt to create it
        $dir = dirname($this->targetFilename);
        if (!is_dir($dir)) {
            if (@mkdir($dir, 0777, true) === false) {
                throw new \RuntimeException('Unable to create directory for Assets: '.$dir);
            }
        }

        // try and write the file out.
        if (@file_put_contents($this->targetFilename, $this->asset->getContent()) === false) {
            throw new \RuntimeException('Unable to write file '.$this->targetFilename);
        }
    }
}

