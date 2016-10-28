<?php

namespace Neoflow\CMS\Model;

use Exception;
use Neoflow\Framework\ORM\AbstractEntityModel;
use Neoflow\Framework\Support\Filesystem\Folder;
use Neoflow\Framework\Support\Validation\ValidationException;
use ZipArchive;
use function translate;

abstract class AbstractExtensionModel extends AbstractEntityModel
{

    /**
     * @var string
     */
    protected $tempFolder;

    public function __construct(array $data = array(), $isReadOnly = false)
    {
        parent::__construct($data, $isReadOnly);
    }

    public function delete()
    {
        if (parent::delete()) {
            if (is_dir($this->getPath())) {
                $folder = new Folder($this->getPath());
                $folder->delete();
            }
            return true;
        }
        return false;
    }

    public function save()
    {
        if (parent::save()) {
            if (!is_dir($this->getPath())) {

            }
            return true;
        }
        return false;
    }

    /**
     * Set package config (package.ini)
     *
     * @return self
     * @throws Exception
     */
    protected function setPackageConfig($configPath = null)
    {
        if (!is_file($configPath)) {
            $configPath = $this->getPath('package.ini');
        }

        if (is_file($configPath)) {
            foreach (parse_ini_file($configPath, true) as $key => $value) {
                $this->{$key} = $value;
            }
            return $this;
        }
        throw new ValidationException(translate('Package config ({0}) not found', array(basename($configPath))));
    }

    protected function moveUploadedPackage()
    {
        if (isset($this->package['tmp_name']) && $this->package['tmp_name'] !== '') {

            $packageName = basename($this->package['name']);
            $packagePath = $this->config()->getTempPath(DIRECTORY_SEPARATOR . $packageName);

            if (move_uploaded_file($this->package['tmp_name'], $packagePath)) {
                return $packagePath;
            }
            throw new Exception('Could not move uploaded package to temp folder');
        }
        throw new Exception('There is no package to install');
    }

    /**
     * Extract package to temp folder
     *
     * @param string $packagePath
     *
     * @return bool
     *
     * @throws ValidationException
     */
    protected function extractPackage($packagePath)
    {
        $tempModulePath = $this->config()->getTempPath(uniqid());

        if (is_file($packagePath) && strtolower(pathinfo($packagePath, PATHINFO_EXTENSION)) === 'zip') {
            $zip = new ZipArchive();
            if ($zip->open($packagePath) === true) {
                $zip->extractTo($tempModulePath);
                $zip->close();
                return $tempModulePath;
            }
        }
        throw new ValidationException(translate('{0} is not a valid package (zip archive)', array(basename($packagePath))));
    }

    public function install()
    {
        try {
            $packagePath = $this->moveUploadedPackage();
            $tempModulePath = $this->extractPackage($packagePath);

            $this->setPackageConfig($tempModulePath . DIRECTORY_SEPARATOR . 'package.ini');

            if (!is_dir($this->getPath())) {
                $folder = new Folder($tempModulePath);
                $folder->move($this->getPath());
                return true;
            }

            throw new ValidationException(translate('Folder name ({0}) is already in use', array($this->folder)));
        } finally {
            unlink($packagePath);
            if (is_dir($tempModulePath)) {
                Folder::unlink($tempModulePath, true);
            }
        }
    }

    public abstract function getPath();
}
