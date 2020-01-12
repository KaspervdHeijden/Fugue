<?php

declare(strict_types=1);

namespace Fugue\Persistence\IO;

use Fugue\Localization\NumberFormatterInterface;
use InvalidArgumentException;
use RuntimeException;

use function file_get_contents;
use function file_put_contents;
use function clearstatcache;
use function is_readable;
use function is_resource;
use function is_string;
use function is_file;
use function dirname;
use function readdir;
use function opendir;
use function unlink;
use function is_int;
use function rmdir;

final class FileSystemUtil
{
    /**
     * Checks to see if a directory is empty.
     *
     * @param string $dir The directory to check.
     * @return bool       TRUE if the directory is empty, FALSE otherwise.
     */
    public function isDirectoryEmpty(string $dir): bool
    {
        // @ required because opendir() generates an E_WARNING
        $handle = @opendir($dir);
        if (! is_resource($handle)) {
            return false;
        }

        $rv = true;
        while (($entry = readdir($handle)) !== false) {
            switch ($entry) {
                case '..':
                case '.':
                    break;
                default:
                    $rv = false;
                    break;
            }
        }

        closedir($handle);
        return $rv;
    }

    /**
     * Deletes a file and it's directory and it's parents until a non-empty directory is encountered.
     *
     * @param string $file The file to delete.
     */
    public function deleteFileAndDirectoryIfNotEmpty(string $file): void
    {
        if ($file === '') {
            throw new InvalidArgumentException(
                'File should not be empty.'
            );
        }

        clearstatcache();
        if (is_readable($file) && is_file($file)) {
            unlink($file);
        }

        $dir = dirname($file);
        while ($this->isDirectoryEmpty($dir)) {
            rmdir($dir);
            $dir = dirname($dir);
        }
    }

    /**
     * Gets the size unit description of a number.
     * EG: 25 MB, 1.32 GB. 25kB.
     *
     * @param int                      $size            The size in bytes.
     * @param NumberFormatterInterface $numberFormatter The number formatter to use.
     * @return string                                   The unit description.
     */
    public function getSizeUnit(int $size, NumberFormatterInterface $numberFormatter): string
    {
        $sizesAndUnits = [
            'TB' => 1000 * 1000 * 1000 * 1000,
            'GB' => 1000 * 1000 * 1000,
            'MB' => 1000 * 1000,
            'kB' => 1000,
        ];

        foreach ($sizesAndUnits as $unit => $unitMinValue) {
            if ($size >= $unitMinValue) {
                return "{$numberFormatter->format($size / $unitMinValue, 2)} {$unit}";
            }
        }

        return "{$numberFormatter->format($size, 2)} bytes";
    }

    /**
     * Checks if the passed path is a file and exists.
     *
     * @param string $fileName The filename to check.
     * @return bool            TRUE if $fileName is a file and exist, FALSE otherwise.
     */
    public function fileExists(string $fileName): bool
    {
        if ($fileName === '') {
            throw new InvalidArgumentException(
                'Filename should not be empty.'
            );
        }

        if (is_file($fileName) && is_readable($fileName)) {
            return true;
        }

        return false;
    }

    /**
     * Checks if the passed path is a file and exists.
     *
     * @param string $path The path to the directory to check.
     * @return bool        TRUE if $path is a directory, FALSE otherwise.
     */
    public function directoryExists(string $path): bool
    {
        if ($path === '') {
            throw new InvalidArgumentException(
                'Path should not be empty.'
            );
        }

        if (is_dir($path)) {
            return true;
        }

        return false;
    }

    public function read(string $fileName): string
    {
        $contents = file_get_contents($fileName);
        if (! is_string($contents)) {
            throw new RuntimeException(
                "Could not read from '{$fileName}'."
            );
        }

        return $contents;
    }

    public function save(string $fileName, $contents): int
    {
        $bytesWritten = file_put_contents($fileName, $contents);
        if (! is_int($bytesWritten)) {
            throw new RuntimeException(
                "Could not write to '{$fileName}'."
            );
        }

        return $bytesWritten;
    }
}
