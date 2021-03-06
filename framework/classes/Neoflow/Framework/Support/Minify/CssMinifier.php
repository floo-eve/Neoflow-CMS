<?php

namespace Neoflow\Framework\Support\Minify;

class CssMinifier extends AbstractMinifier
{

    /**
     * Minify CSS code
     *
     * @param string $targetFilePath
     * @return string
     */
    protected function minify($targetFilePath = null)
    {
        // Remove comments
        $this->code = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $this->code);

        // Remove space after colons
        $this->code = str_replace(': ', ':', $this->code);

        // Remove whitespace
        $this->code = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $this->code);

        // Save to file
        if ($targetFilePath) {
            file_put_contents($targetFilePath, $this->code);
        }

        return $this->code;
    }
}
