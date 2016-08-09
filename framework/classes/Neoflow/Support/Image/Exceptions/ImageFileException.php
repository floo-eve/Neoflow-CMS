<?php

namespace Neoflow\Support\Image\Exceptions;

use \Neoflow\Support\Filesystem\Exceptions\FileException;

class ImageFileException extends FileException
{

    const NOT_SUPPORTED_IMAGE_TYPE = 1001;

}
