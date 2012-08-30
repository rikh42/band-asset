<?php
/**
 * This file is part of the Small Neat Box Framework
 * Copyright (c) 2011-2012 Small Neat Box Ltd.
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

/*
 * Some of this file based on minify project...
 * https://github.com/mrclay/minify
 * Copyright (c) 2008 Ryan Grove <ryan@wonko.com>
 * Copyright (c) 2008 Steve Clay <steve@mrclay.org>
 * This code is covered by the BSD New License.
 * See https://github.com/mrclay/minify/blob/master/LICENSE.txt for more details.
 */

namespace asset\filters;
use asset\filters\FilterInterface;

class CssMinifyFilter implements FilterInterface
{

    /**
     * @return string
     */
    public function getName()
    {
        // If you change the filter, change this to have
        // all files using it be regenerated.
        return 'Css Minify Filter v1.0.2';
    }


    /**
     * @param $content
     * @return mixed
     */
    public function filter($content)
    {
        // map all white space characters to space for simplicity
        $css = preg_replace('/\s+/u', ' ', $content);

        // Strip out comments that don't start /*!
        $css = preg_replace('/\/\*[^!].*?\*\/\s*/u', '', $css);

        // remove ; off the last item in a rule
        $css = preg_replace('/;+\s*\}/u', '}', $css);

        // remove unwanted spaces around { and }
        $css = preg_replace('/\s*\{\s*/u', '{', $css);
        $css = preg_replace('/\s*\}\s*/u', '}', $css);

        // convert 0px to 0
        $css = preg_replace('/([\s\:])(0)(?:px|em|%|in|cm|mm|pc|pt|ex)/iu', '$1$2', $css);

        // Replace 0 0 0 0; with 0.
        $css = preg_replace('/\:0 0 0 0(;|\})/u', ':0$1', $css);
        $css = preg_replace('/\:0 0 0(;|\})/u', ':0$1', $css);

        // simplify floating point numbers
        $css = preg_replace('/(\:|\s)0+\.(\d+)/u', '$1.$2', $css);

        // remove empty rules
        $css = preg_replace('/[^\};\{\/]+\{\}/u', '', $css);

        // remove multiple ;
        $css = preg_replace('/;;+/u', ';', $css);

        // place comments on their own line
        $css = preg_replace('%\s*\/\*%u', "\n/*", $css);
        $css = preg_replace('%\*\/\s*%u', "*/\n", $css);

        // add a line break after each rule
        $css = preg_replace('/(.{1024,}?)\}\s*/u', "$1}\n", $css);
        //$css = preg_replace('/\}\s*/u', "}\n", $css);

        // trim
        $css = preg_replace('/^\s+|\s+$/u', '', $css);
        return $css;
    }
}
