<?php

namespace Neoflow\Support\Filesystem;

use \Neoflow\Support\Filesystem\Exceptions\FileException;

class File
{

    /**
     * File path.
     *
     * @var string
     */
    protected $filePath;

    /**
     * Constructor.
     *
     * @param string $filePath File path
     */
    public function __construct($filePath = null)
    {
        if (is_string($filePath)) {
            $this->load($filePath);
        }
    }

    /**
     * Load file path.
     *
     * @param string $filePath File path
     *
     * @return bool
     *
     * @throws FileException
     */
    public function load($filePath)
    {
        if (is_file($filePath)) {
            if (is_readable($filePath)) {
                $this->filePath = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $filePath);
                return true;
            }
            throw new FileException('Cannot load file path, because ' . $filePath . ' isn\'t readable', FileException::NOT_READABLE);
        }
        throw new FileException('Cannot load the file path, because ' . $filePath . ' don\'t exist', FileException::DONT_EXIST);
    }

    /**
     * Get the file extension.
     *
     * @return string
     */
    public function getFileExtension()
    {
        return pathinfo($this->filePath, PATHINFO_EXTENSION);
    }

    /**
     * Get the file name.
     *
     * @return string
     */
    public function getFileName()
    {
        return pathinfo($this->filePath, PATHINFO_BASENAME);
    }

    /**
     * Get the file directory.
     *
     * @return string
     */
    public function getFileDirectory()
    {
        return pathinfo($this->filePath, PATHINFO_DIRNAME);
    }

    /**
     * Get the file path.
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Delete file.
     *
     * @return bool
     *
     * @throws FileException
     */
    public function delete()
    {
        if (is_file($this->filePath)) {
            if (is_writable($this->filePath)) {
                return unlink($this->filePath);
            }
            throw new FileException('Cannot delete the file, beacause the file path ' . pathinfo($this->filePath, PATHINFO_EXTENSION) . ' isn\'t writeable', FileException::NOT_WRITEABLE);
        }
        return true;
    }

    /**
     * Move file to new file path.
     *
     * @param string $newFilePath New file path
     * @param bool   $overwrite   Set FALSE to prevent overwriting, when the a file with the new file path already exist
     *
     * @return \self
     *
     * @throws FileException
     */
    public function move($newFilePath, $overwrite = true)
    {
        if ($overwrite || !is_file($newFilePath)) {
            if (rename($this->filePath, $newFilePath)) {
                return new self($newFilePath);
            }
            throw new FileException('Moving file to new file path ' . $newFilePath . ' failed');
        }
        throw new FileException('Cannot move the file, because the new file path ' . $newFilePath . ' already exist', FileException::ALREADY_EXIST);
    }

    /**
     * Rename file with new file name.
     *
     * @param string $newFileName New file name
     * @param bool   $overwrite   Set FALSE to prevent overwriting, when the a file with the new file name already exist
     *
     * @return \self
     *
     * @throws FileException
     */
    public function rename($newFileName, $overwrite = true)
    {
        $newFilePath = $this->getFileDirectory() . DIRECTORY_SEPARATOR . $newFileName;
        if ($overwrite || !is_file($newFilePath)) {
            return $this->move($newFilePath);
        }
        throw new FileException('Cannot rename the file, because the new file name ' . $newFileName . ' already exist', FileException::ALREADY_EXIST);
    }

    /**
     * Move file to new directory path.
     *
     * @param string $newDirectoryPath New directory path
     * @param bool   $overwrite        Set FALSE to prevent overwriting, when a file with same name in the new directory path already exist
     *
     * @return bool
     *
     * @throws FileException
     */
    public function moveToDirectory($newDirectoryPath, $overwrite = true)
    {
        if (is_dir($newDirectoryPath)) {
            if (is_writeable($newDirectoryPath)) {
                $newFilePath = $newDirectoryPath . DIRECTORY_SEPARATOR . $this->getFileName();
                if ($overwrite || !is_file($newFilePath)) {
                    return $this->move($newFilePath, $overwrite);
                }
                throw new FileException('Cannot move the file, because a file with the same file name in the new directory path ' . $newDirectoryPath . ' already exist', FileException::ALREADY_EXIST);
            }
            throw new FileException('Cannot move the file, because the new directory path ' . $newDirectoryPath . ' isn\'t writeable', FileException::NOT_WRITEABLE);
        }
        throw new FileException('Cannot move the file, because the new directory path ' . $newDirectoryPath . ' don\'t exist', FileException::DONT_EXIST);
    }
}
