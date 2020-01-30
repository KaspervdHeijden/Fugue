<?php

declare(strict_types=1);

namespace Fugue\Imaging;

use UnexpectedValueException;
use RuntimeException;

use function image_type_to_extension;
use function imagecreatefromstring;
use function imagecreatetruecolor;
use function imagecopyresampled;
use function file_get_contents;
use function mb_strtolower;
use function imagedestroy;
use function getimagesize;
use function file_exists;
use function is_readable;
use function is_resource;
use function imagejpeg;
use function is_string;
use function imagegif;
use function is_array;
use function imagepng;
use function is_dir;
use function mkdir;
use function round;
use function rtrim;
use function ltrim;

final class Image
{
    /** @var array */
    private $imageData;

    /**
     * Creates an Image object from a file.
     *
     * @param string $filename  The image file to load.
     * @return Image            The created Image object.
     */
    public static function fromFile(string $filename): Image
    {
        if (! is_readable($filename)) {
            throw new RuntimeException("Cannot read '{$filename}'.");
        }

        $info = getimagesize($filename);
        if (! is_array($info)) {
            throw new RuntimeException("Invalid image '{$filename}'.");
        }

        $image = self::getImageFromFile($filename);
        return new Image($image, $info[0], $info[1], $info[2]);
    }

    /**
     * Loads a image resource for a given filename.
     *
     * @param string $filename The filename to load the image for.
     * @return resource        The image resource.
     */
    private static function getImageFromFile(string $filename)
    {
        $contents = file_get_contents($filename);
        if (! is_string($contents)) {
            throw new RuntimeException("Could not read file '{$filename}'.");
        }

        $image = imagecreatefromstring($contents);
        if (! is_resource($image)) {
            throw new RuntimeException("Invalid image '{$filename}'.");
        }

        return $image;
    }

    /**
     * Instantiates a new Image object.
     *
     * @param resource $image  The pointer to the image.
     * @param int      $width  The image width.
     * @param int      $height The image height.
     * @param int      $type   The image type.
     */
    private function __construct($image, int $width, int $height, int $type)
    {
        $this->imageData = [
            'height' => $height,
            'width'  => $width,
            'image'  => $image,
            'type'   => $type,
        ];
    }

    /**
     * Checks the type of image. Throws on an invalid image type.
     */
    private function checkImageType(): void
    {
        $imageType = $this->getImageType();
        switch ($imageType) {
            case IMAGETYPE_JPEG:
            case IMAGETYPE_GIF:
            case IMAGETYPE_PNG:
                break;
            default:
                throw new UnexpectedValueException(
                    "Unsupported image type ({$imageType})."
                );
        }
    }

    /**
     * Stores this image to disk.
     *
     * @param string $filename The filename to save to.
     * @return bool            TRUE on success, FALSE otherwise.
     */
    private function storeImage(string $filename): bool
    {
        $imageType = $this->getImageType();
        switch ($imageType) {
            case IMAGETYPE_GIF:
                return imagegif($this->getImage(), $filename);
            case IMAGETYPE_PNG:
                return imagepng($this->getImage(), $filename);
            case IMAGETYPE_JPEG:
                return imagejpeg($this->getImage(), $filename, 90);
            default:
                throw new UnexpectedValueException(
                    "Unsupported image type ({$imageType})."
                );
        }
    }

    /**
     * Gets the file extension for this image.
     *
     * @return string The file extension, including a leading dot.
     */
    private function getFileExtension(): string
    {
        $extension = '.' . ltrim(mb_strtolower(image_type_to_extension($this->getImageType())), '.');
        if ($extension === '.jpeg') {
            return '.jpg';
        }

        return $extension;
    }

    /**
     * Gets the image type for this image.
     *
     * @return int The image type.
     */
    private function getImageType(): int
    {
        return (int)($this->imageData['type'] ?? 0);
    }

    /**
     * Gets the image resource.
     *
     * @return resource|null The image resource.
     */
    private function getImage()
    {
        return $this->imageData['image'] ?? null;
    }

    /**
     * Gets the image width.
     *
     * @return int The width of this image.
     */
    private function getWidth(): int
    {
        return (int)($this->imageData['width'] ?? 0);
    }

    /**
     * Gets the image height.
     *
     * @return int The height of this image.
     */
    private function getHeight(): int
    {
        return (int)($this->imageData['height'] ?? 0);
    }

    /**
     * Resizes this image and returns it in a new Image object.
     *
     * @param int $maxSize The maximum image size.
     * @return Image       The new resized Image.
     */
    public function resize(int $maxSize): Image
    {
        $curHeight = $this->getHeight();
        $curWith   = $this->getWidth();

        $ratio = $curWith / $curHeight;
        if ($ratio < 1) {
            // Height is greater then the width
            $newWidth  = (int)round($maxSize * $ratio);
            $newHeight = $maxSize;
        } else {
            // Width is greater or equal to the height
            $newHeight = (int)round($maxSize / $ratio);
            $newWidth  = $maxSize;
        }

        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        if (! is_resource($newImage)) {
            throw new RuntimeException('Could not dynamically create a new image.');
        }

        $result = imagecopyresampled(
            $newImage,
            $this->getImage(),
            0,
            0,
            0,
            0,
            $newWidth,
            $newHeight,
            $curWith,
            $curHeight
        );

        if ($result === false) {
            throw new RuntimeException('Could not resize image.');
        }

        return new Image($newImage, $newWidth, $newHeight, $this->getImageType());
    }

    /**
     * Releases all resources maintained by this Image object.
     */
    public function dispose(): void
    {
        $image = $this->getImage();
        if (is_resource($image)) {
            imagedestroy($image);
        }

        $this->imageData = [];
    }

    /**
     * Saves an image.
     *
     * @param string $path   A relative path. (Without DOCUMENT_ROOT or filename!)
     * @param string $prefix Prefix name of filename.
     *
     * @return string        The generated filename.
     */
    public function save(string $path, string $prefix): string
    {
        $this->checkImageType();

        $path = rtrim($path, '/') . '/';
        if (! is_dir($path) && ! mkdir($path, 0777, true)) {
            throw new RuntimeException("Couldn't create path '{$path}'.");
        }

        $filename = $prefix . $this->getFileExtension();
        $fullPath = $path . $filename;
        
        if (file_exists($fullPath)) {
            throw new RuntimeException('File already exists.');
        }

        if (! $this->storeImage($fullPath)) {
            throw new RuntimeException('Error saving image.');
        }

        return $filename;
    }
}
