<?php

namespace Neoflow\Framework\Support\Minify;

class JsMinifier extends AbstractMinifier
{

    /**
     * Minify Javascript code
     *
     * @param string $targetFilePath
     * @return string
     */
    protected function minify($targetFilePath = null)
    {
        /* remove comments */
        $this->code = preg_replace('/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/', '', $this->code);

        /* remove tabs, spaces, newlines, etc. */
        $this->code = str_replace(array("\r\n", "\r", "\t", "\n", '  ', '    ', '     '), '', $this->code);

        /* remove other spaces before/after ) */
        $this->code = preg_replace(array('(( )+\))', '(\)( )+)'), ')', $this->code);

        // Save to file
        if ($targetFilePath) {
            file_put_contents($targetFilePath, $this->code);
        }

        return $this->code;
    }
}
