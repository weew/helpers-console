<?php

if ( ! function_exists('cli_color')) {
    /**
     * Change color of text.
     *
     * @param $string
     * @param null $color
     *
     * @return string
     */
    function cli_color($string, $color = null) {
        $colors = [
            'default'          => '39',
            'black'            => '30',
            'red'              => '31',
            'green'            => '32',
            'yellow'           => '33',
            'blue'             => '34',
            'magenta'          => '35',
            'cyan'             => '36',
            'light_gray'       => '37',
            'dark_gray'        => '90',
            'light_red'        => '91',
            'light_green'      => '92',
            'light_yellow'     => '93',
            'light_blue'       => '94',
            'light_magenta'    => '95',
            'light_cyan'       => '96',
            'white'            => '97',
        ];

        if ($color === null or ! array_has($colors, $color)) {
            return $string;
        } else {
            $color = $colors[$color];
        }

        return s("\033[%sm%s\033[0m", $color, $string);
    }
}

if ( ! function_exists('cli_highlight')) {
    /**
     * Change background color of text.
     *
     * @param $string
     * @param null $color
     *
     * @return string
     */
    function cli_highlight($string, $color = null) {
        $colors = [
            'default'       => '49',
            'black'         => '40',
            'red'           => '41',
            'green'         => '42',
            'yellow'        => '43',
            'blue'          => '44',
            'magenta'       => '45',
            'cyan'          => '46',
            'light_gray'    => '47',
            'dark_gray'     => '100',
            'light_red'     => '101',
            'light_green'   => '102',
            'light_yellow'  => '103',
            'light_blue'    => '104',
            'light_magenta' => '105',
            'light_cyan'    => '106',
            'white'         => '107',
        ];

        if ($color === null or  ! array_has($colors, $color)) {
            return $string;
        } else {
            $color = $colors[$color];
        }

        return s("\033[%sm%s\033[0m", $color, $string);
    }
}

if ( ! function_exists('cli_format')) {
    /**
     * Change formatting of text.
     *
     * @param $string
     * @param null $format
     *
     * @return string
     */
    function cli_format($string, $format = null) {
        $formats = [
            'reset'            => '0',
            'bold'             => '1',
            'dark'             => '2',
            'italic'           => '3',
            'underline'        => '4',
            'blink'            => '5',
            'reverse'          => '7',
            'concealed'        => '8',
        ];

        if ($format === null or ! array_has($formats, $format)) {
            return $string;
        } else {
            $format = $formats[$format];
        }

        return s("\033[%sm%s\033[0m", $format, $string);
    }
}

if ( ! function_exists('cli_style')) {
    /**
     * Change color, background color and formatting of text.
     *
     * @param $string
     * @param null $color
     * @param null $highlight
     * @param null $format
     *
     * @return string
     */
    function cli_style($string, $color = null, $highlight = null, $format = null) {
        return cli_highlight(
            cli_color(
                cli_format($string, $format), $color
            ), $highlight
        );
    }
}
