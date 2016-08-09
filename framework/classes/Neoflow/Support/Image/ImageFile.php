<?php

namespace Neoflow\Support\Image;

use \Neoflow\Support\Filesystem\File;
use \Neoflow\Support\Image\Exceptions\ImageFileException;

class ImageFile extends File
{

    /**
     * Image resource.
     *
     * @var type
     */
    protected $image;

    /**
     * Load file path and image.
     *
     * @param string $filePath Image file path
     *
     * @return \self
     *
     * @throws ImageFileException
     */
    public function load($filePath)
    {
        if (parent::load($filePath)) {

            switch ($this->getImageType()) {
                case IMAGETYPE_JPEG:
                    $this->setRequiredMemory();
                    $this->image = imagecreatefromjpeg($this->filePath);
                    break;
                case IMAGETYPE_PNG:
                    $this->image = imagecreatefrompng($this->filePath);

                    break;
                case IMAGETYPE_GIF:
                    $this->image = imagecreatefromgif($this->filePath);

                    break;
                case IMAGETYPE_BMP:
                    $this->image = imagecreatefromwbmp($this->filePath);

                    break;
                default:
                    throw new ImageFileException('Cannot load image file path, because ' . $this->filePath . ' is not a PNG, GIF, BMP or JPEG-based image or an incompatible file', ImageFileException::NOT_SUPPORTED_IMAGE_TYPE);
            }
            $this->fixOrientation();
        }

        return $this;
    }

    /**
     * Get image type.
     *
     * @return int
     */
    public function getImageType()
    {
        return @getimagesize($this->filePath)[2];
    }

    /**
     * Support method: Set required memory
     *
     * @return int
     */
    protected function setRequiredMemory()
    {
        $imageInfo = @getimagesize($this->filePath);
        if (is_array($imageInfo)) {
            $MB = Pow(1024, 2);
            $K64 = Pow(2, 16);
            $TWEAKFACTOR = 1.8;
            $memoryNeeded = round(( $imageInfo[0] * $imageInfo[1] * $imageInfo['bits'] * $imageInfo['channels'] / 8 + $K64) * $TWEAKFACTOR);
            $memoryUsage = memory_get_usage();
            $memoryLimit = (integer) ini_get('memory_limit') * $MB;

            if ($memoryUsage + $memoryNeeded > $memoryLimit) {
                $newMemoryLimit = ($memoryLimit + ceil($memoryUsage + $memoryNeeded - $memoryLimit)) / $MB;
                return (bool) @ini_set('memory_limit', $newMemoryLimit . 'M');
            }
        }
        return false;
    }

    /**
     * Get image width.
     *
     * @return int
     */
    public function getImageWidth()
    {
        return imagesx($this->image);
    }

    /**
     * Get image height.
     *
     * @return int
     */
    public function getImageHeight()
    {
        return imagesy($this->image);
    }

    /**
     * Save image.
     *
     * @param null|string $imageFilePath File path of image
     * @param int|string  $imageType     Type or extension of image
     * @param int         $quality       Quality rate from 1 to 100
     *
     * @return \self
     *
     * @throws ImageFileException
     */
    public function save($imageFilePath = null, $imageType = null, $quality = 80)
    {
        // Fallback to get image file path
        if (!is_string($imageFilePath)) {
            $imageFilePath = $this->filePath;
        }

        // Fallback to get image type
        if (!in_array($imageType, array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_BMP))) {
            if (is_string($imageType)) {
                $imageType = $this->fileExtensionToImageType($imageType);
            } else {
                $extension = pathinfo($imageFilePath, PATHINFO_EXTENSION);
                if (!$extension) {
                    $extension = $this->getFileExtension();
                }

                $imageType = $this->fileExtensionToImageType($extension);
            }
        }

        if ($this->createImageFile($imageFilePath, $imageType, $quality)) {
            return new self($imageFilePath);
        }
        throw new ImageFileException('Saving image file to file path ' . $imageFilePath . ' failed');
    }

    /**
     * Resize image to height.
     *
     * @param int $height
     *
     * @return self
     */
    public function resizeToHeight($height)
    {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width, $height);

        return $this;
    }

    /**
     * Resize image to width.
     *
     * @param int $width
     *
     * @return self
     */
    public function resizeToWidth($width)
    {
        $ratio = $width / $this->getWidth();
        $height = $this->getHeight() * $ratio;
        $this->resize($width, $height);

        return $this;
    }

    /**
     * Scale image.
     *
     * @param int $scale
     *
     * @return self
     */
    public function scale($scale)
    {
        $newWidth = $this->getImageWidth() * $scale / 100;
        $newHeight = $this->getImageHeight() * $scale / 100;

        return $this->resize($newWidth, $newHeight);
    }

    /**
     * Resize image to height and width.
     *
     * @param int $newWidth  New image width
     * @param int $newHeight New image height
     *
     * @return \self
     */
    public function resize($newWidth, $newHeight)
    {
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        $newImage = $this->preserveTransparency($newImage);

        imagecopyresampled($newImage, $this->image, 0, 0, 0, 0, $newWidth, $newHeight, $this->getImageWidth(), $this->getImageHeight());

        $this->image = $newImage;

        return $this;
    }

    /**
     * Resize image to best fitting width and height (proportional).
     *
     * @param int $newWidth  New image width
     * @param int $newHeight New image height
     *
     * @return \self
     */
    public function resizeBestFit($newWidth, $newHeight)
    {
        $ratio = min($newWidth / $this->getImageWidth(), $newHeight / $this->getImageHeight());

        return $this->resize($this->getImageWidth() * $ratio, $this->getImageHeight() * $ratio);
    }

    /**
     * Support method: Create image file.
     *
     * @param string $imageFilePath File path of image
     * @param int    $imageType     Type of image
     * @param int    $compression   Quality rate from 1 to 100
     * @param bool   $overwrite     Set FALSE to prevent overwriting, when the a file with the image file name already exist
     *
     * @return bool
     *
     * @throws ImageFileException
     */
    protected function createImageFile($imageFilePath, $imageType, $compression = 100, $overwrite = true)
    {
        if ($overwrite || !is_file($imageFilePath)) {
            switch ($imageType) {
                case IMAGETYPE_JPEG:
                    imagejpeg($this->image, $imageFilePath, $compression);
                    break;
                case IMAGETYPE_PNG:
                    imagepng($this->image, $imageFilePath, round(9 / 100 * $compression));
                    break;
                case IMAGETYPE_GIF:
                    imagegif($this->image, $imageFilePath);
                    break;
                case IMAGETYPE_BMP:
                    image2wbmp($this->image, $imageFilePath, round(255 / 100 * $compression));
                    break;
                default:
                    throw new ImageFileException('Image type is not supported', ImageFileException::NOT_SUPPORTED_IMAGE_TYPE);
            }

            return true;
        }
        throw new ImageFileException('Cannot create image file, because the image file path ' . $imageFilePath . ' already exist', ImageFileException::ALREADY_EXIST);
    }

    /**
     * Support method: Convert file extension to image type.
     *
     * @param string $fileExtension
     *
     * @return string
     *
     * @throws ImageFileException
     */
    protected function fileExtensionToImageType($fileExtension)
    {
        switch (strtolower($fileExtension)) {
            case 'jpeg':
            case 'jpg':
                return IMAGETYPE_JPEG;
            case 'png':
                return IMAGETYPE_PNG;
            case 'gif':
                return IMAGETYPE_GIF;
            case 'bmp':
                return IMAGETYPE_BMP;
            default:
                throw new ImageFileException('File extension ' . $fileExtension . 'is not supported as image type', ImageFileException::NOT_SUPPORTED_IMAGE_TYPE);
        }
    }

    /**
     * Support method: Preserve transparency of new image resource.
     *
     * @param resource $newImage New image resource
     *
     * @return resource
     */
    protected function preserveTransparency($newImage)
    {
        switch ($this->getImageType()) {
            case IMAGETYPE_GIF: {
                    $transparentIndex = imagecolortransparent($this->image);
                    $palletsize = imagecolorstotal($this->image);
                    if ($transparentIndex >= 0 && $transparentIndex < $palletsize) {
                        $transparentColor = imagecolorsforindex($this->image, $transparentIndex);
                        $transparentIndex = imagecolorallocate($newImage, $transparentColor['red'], $transparentColor['green '], $transparentColor['blue']);
                        imagefill($newImage, 0, 0, $transparentIndex);
                        imagecolortransparent($newImage, $transparentColor);
                    }
                }
            case IMAGETYPE_PNG: {
                    imagealphablending($newImage, false);
                    imagesavealpha($newImage, true);
                }
        }

        return $newImage;
    }

    /**
     * Support method: Fix image orientation.
     *
     * @return bool
     */
    public function fixOrientation()
    {
        // Correct image rotation
        if (function_exists('exif_read_data')) {
            $exif = @exif_read_data($this->filePath);
            if (!empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 8:
                        $this->image = imagerotate($this->image, 90, 0);
                        break;
                    case 3:
                        $this->image = imagerotate($this->image, 180, 0);
                        break;
                    case 6:
                        $this->image = imagerotate($this->image, -90, 0);
                        break;
                }
            }

            return true;
        }

        return false;
    }
}
