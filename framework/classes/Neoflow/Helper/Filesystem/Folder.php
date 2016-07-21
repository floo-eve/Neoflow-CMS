<?php

namespace Neoflow\Helper\Filesystem;

use \Neoflow\Helper\Filesystem\Exceptions\FolderException;

class Folder
{

    /**
     * Folder path.
     *
     * @var string
     */
    protected $folderPath;

    /**
     * Constructor.
     *
     * @param string $folderPath Folder path
     */
    public function __construct($folderPath)
    {
        if (is_string($folderPath)) {
            $this->load($folderPath);
        }
    }

    /**
     * Load folder path.
     *
     * @param string $folderPath Folder path
     *
     * @return bool
     *
     * @throws FolderException
     */
    public function load($folderPath)
    {
        if (is_dir($folderPath)) {
            if (is_readable($folderPath)) {
                $this->folderPath = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $folderPath);
                return true;
            }
            throw new FolderException('Cannot load folder path, because ' . $folderPath . ' isn\'t readable', FolderException::NOT_READABLE);
        }
        throw new FolderException('Cannot load the folder path, because ' . $folderPath . ' don\'t exist', FolderException::DONT_EXIST);
    }

    /**
     * Get folder path
     *
     * @return string
     */
    public function getFolderPath()
    {
        return $this->folderPath;
    }

    /**
     * Get folder name
     * @return string
     */
    public function getFolderName()
    {
        return pathinfo($this->folderPath, PATHINFO_BASENAME);
    }

    /**
     * Get the folder directory.
     *
     * @return string
     */
    public function getFolderDirectory()
    {
        return pathinfo($this->filePath, PATHINFO_DIRNAME);
    }

    /**
     * Delete folder.
     *
     * @param bool $recursivly Set FALSE to prevent deleting all files and subfolders recursivly
     *
     * @return bool
     */
    public function delete($recursivly = true)
    {
        if ($recursivly) {
            foreach (glob($this->folderPath . '/*') as $file) {
                if (is_dir($file)) {
                    rrmdir($file);
                } else {
                    unlink($file);
                }
            }
        }

        return rmdir($this->folderPath);
    }

    /**
     * Move folder to new folder path.
     *
     * @param string $newFolderPath New folder path
     *
     * @return \self
     *
     * @throws FolderException
     */
    public function move($newFolderPath)
    {
        if (is_dir($newFolderPath)) {
            if (rename($this->folderPath, $newFolderPath)) {
                return new self($newFolderPath);
            }
            throw new FolderException('Moving folder to new folder path ' . $newFolderPath . ' failed');
        }
        throw new FolderException('Cannot move the folder, because the new folder path ' . $newFolderPath . ' already exist', FolderException::ALREADY_EXIST);
    }

    /**
     * Rename folder with new folder name.
     *
     * @param string $newFolderName New folder name
     *
     * @return \self
     *
     * @throws FolderException
     */
    public function rename($newFolderName)
    {
        $newFolderPath = $this->getFolderDirectory() . DIRECTORY_SEPARATOR . $newFolderName;
        if (is_dir($newFolderPath)) {
            return $this->move($newFolderPath);
        }
        throw new FolderException('Cannot move the folder, because the new folder path ' . $newFolderPath . ' already exist', FolderException::ALREADY_EXIST);
    }

    /**
     * Move folder to new directory path.
     *
     * @param string $newDirectoryPath New directory path
     *
     * @return bool
     *
     * @throws FolderException
     */
    public function moveToDirectory($newDirectoryPath)
    {
        if (is_dir($newDirectoryPath)) {
            if (is_writeable($newDirectoryPath)) {
                $newFolderPath = $newDirectoryPath . DIRECTORY_SEPARATOR . $this->getFileName();
                if (!is_dir($newFolderPath)) {
                    return $this->move($newFolderPath);
                }
                throw new FolderException('Cannot move the folder, because a folder with the same folder name in the new directory path ' . $newDirectoryPath . ' already exist', FolderException::ALREADY_EXIST);
            }
            throw new FolderException('Cannot move the folder, because the new directory path ' . $newDirectoryPath . ' isn\'t writeable', FolderException::NOT_WRITEABLE);
        }
        throw new FolderException('Cannot move the folder, because the new directory path ' . $newDirectoryPath . ' don\'t exist', FolderException::DONT_EXIST);
    }

    /**
     * Static method: Create new folder.
     *
     * @param string $folderPath
     *
     * @return self
     *
     * @throws FolderException
     */
    public static function create($folderPath)
    {
        if (!is_dir($folderPath)) {
            if (mkdir($folderPath)) {
                return new self($folderPath);
            }
            throw new FolderException('Creating folder ' . $folderPath . ' failed');
        }
        throw new FolderException('Cannot create folder, because the folder path ' . $folderPath . ' already exist', FolderException::ALREADY_EXIST);
    }
}
