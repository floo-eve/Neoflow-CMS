<?php

namespace Neoflow\Helper\Image\Exceptions;

use \Neoflow\Helper\Filesystem\Exceptions\FileException;

class ImageFileException extends FileException
{

    const NOT_SUPPORTED_IMAGE_TYPE = 1001;

}
