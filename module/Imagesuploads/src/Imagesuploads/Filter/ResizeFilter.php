<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ResizeFilter
 *
 * @author abass
 */

namespace Imagesuploads\Filter;

use Zend\Filter\AbstractFilter;
use Zend\Filter\Exception;

class ResizeFilter extends AbstractFilter {

    /**
     * @var array
     */
    protected $options = array(
        'target' => null,
        'width' => 360,
        'height' => 480,
        'keepRatio' => true,
        'directory' => null,
        'keepSmaller' => true,
    );

    /**
     * Store already filtered values, so we can filter multiple
     * times the same file without being block by move_uploaded_file
     * internal checks
     *
     * @var array
     */
    protected $alreadyFiltered = array();

    /**
     * Constructor
     *
     * @param array|string $targetOrOptions The target file path or an options array
     */
    public function __construct($targetOrOptions) {
        if (is_array($targetOrOptions)) {
            $this->setOptions($targetOrOptions);
        } else {
            $this->setTarget($targetOrOptions);
        }
    }

    /**
     * @param  string $target Target file path or directory
     * @return self
     */
    public function setTarget($target) {
        if (!is_string($target)) {
            throw new Exception\InvalidArgumentException(
            'Invalid target, must be a string'
            );
        }
        $this->options['target'] = $target;
        return $this;
    }

    /**
     * @return string Target file path or directory
     */
    public function getTarget() {
        return $this->options['target'];
    }

    public function setWidth($width) {
        if (!is_numeric($width)) {
            throw new Exception\InvalidArgumentException(
            'Invalid target, must be a number'
            );
        }
        $this->options['width'] = $width;
        return $this;
    }

    public function getWidth() {
        return $this->options[''];
    }

    public function setHeight($height) {
        if (!is_numeric($height)) {
            throw new Exception\InvalidArgumentException(
            'Invalid target, must be a number'
            );
        }
        $this->options['height'] = $height;
        return $this;
    }

    public function getHeight() {
        return $this->options['height'];
    }

    public function setKeepRatio($keepRatio) {
        if (!is_bool($keepRatio)) {
            throw new Exception\InvalidArgumentException(
            'Invalid target, must be a boolean'
            );
        }
        $this->options['keepRatio'] = $keepRatio;
        return $this;
    }

    public function getKeepRatio() {
        return $this->options['keepRatio'];
    }

    public function setDirectory($directory) {
        if (!is_string($directory)) {
            throw new Exception\InvalidArgumentException(
            'Invalid target, must be a String'
            );
        }
        $this->options['directory'] = $directory;
        return $this;
    }

    public function getDirectory() {
        return $this->options['directory'];
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Resizes the file $value according to the defined settings
     *
     * @param  string $value Full path of file to change
     * @return string The filename which has been set, or false when there were errors
     */
    public function filter($value) {
        //echo '<pre>';print_r($value);die;
        if(!is_array($value)){
            $value['tmp_name']=$value;
        }
        if ($this->options['directory']) {
            $target = $this->options['directory'] . '/' . basename($value['tmp_name']);
        } else {
            $target = $value;
        }
        //echo '<pre>';print_r($target);die;

        return $this->resize($this->options['width'], $this->options['height'], $this->options['keepRatio'], $value, $target, $this->options['keepSmaller']);
    }
    
    public function resize($width, $height, $keepRatio, $file, $target, $keepSmaller = true)
    {
        list($oldWidth, $oldHeight, $type) = getimagesize($file['tmp_name']);
 
        switch ($type) {
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($file['tmp_name']);
                break;
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($file['tmp_name']);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($file['tmp_name']);
                break;
        }
 
        if (!$keepSmaller || $oldWidth > $width || $oldHeight > $height) {
            if ($keepRatio) {
                list($width, $height) = $this->calculateWidth($oldWidth, $oldHeight, $width, $height);
            }
        } else {
            $width = $oldWidth;
            $height = $oldHeight;
        }
 
        $thumb = imagecreatetruecolor($width, $height);
 
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
 
        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $width, $height, $oldWidth, $oldHeight);
 
        switch ($type) {
            case IMAGETYPE_PNG:
                imagepng($thumb, $target);
                break;
            case IMAGETYPE_JPEG:
                imagejpeg($thumb, $target, 90);
                break;
            case IMAGETYPE_GIF:
                imagegif($thumb, $target);
                break;
        }
        return $file;
    }

    protected function calculateWidth($oldWidth, $oldHeight, $width, $height) {
        // now we need the resize factor
        // use the bigger one of both and apply them on both
        $factor = max(($oldWidth / $width), ($oldHeight / $height));
        return array($oldWidth / $factor, $oldHeight / $factor);
    }

    /**
     * @param  string $targetFile Target file path
     * @throws Exception\InvalidArgumentException
     */
    protected function checkFileExists($targetFile) {
        if (file_exists($targetFile)) {
            if ($this->getOverwrite()) {
                unlink($targetFile);
            } else {
                throw new Exception\InvalidArgumentException(
                sprintf("File '%s' could not be renamed. It already exists.", $targetFile)
                );
            }
        }
    }
}