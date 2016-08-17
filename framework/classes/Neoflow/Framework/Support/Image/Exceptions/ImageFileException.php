<?php

namespace Neoflow\Framework\Support\Image\Exceptions;

use \Neoflow\Framework\Support\Filesystem\Exceptions\FileException;

class ImageFileException extends FileException
{

    const NOT_SUPPORTED_IMAGE_TYPE = 1001;

}
