<?php

if ( ! defined('STDIN')) {
    define('STDIN', fopen('php://stdin', 'r'));
}

if ( ! defined('STDOUT')) {
    define('STDOUT', fopen('php://stdout', 'w'));
}

if ( ! defined('STDERR')) {
    define('STDERR', fopen('php://stderr', 'w'));
}

if ( ! function_exists('cli_say')) {
    /**
     * Echo a terminal friendly message.
     *
     * @param $key
     * @param null $string
     */
    function cli_say($key = null, $string = null) {
        if ($string == null) {
            $string = $key;
        } else {
            $string = s('[%s] %s', $key, $string);
        }

        fwrite(STDOUT, $string . PHP_EOL);
    }
}

if ( ! function_exists('cli_prompt')) {
    /**
     * Ask for input through the terminal.
     *
     * @param $string
     * @param null $default
     *
     * @return string
     */
    function cli_prompt($string, $default = null) {
        if ($default !== null) {
            $string = s('%s (%s): ', $string, $default);
        } else {
            $string = s('%s: ', $string);
        }

        fwrite(STDOUT, $string);
        $line = trim(fgets(STDIN));

        if (strlen(trim($line)) == 0) {
            $line = $default;
        }

        return $line;
    }
}

if ( ! function_exists('cli_ask')) {
    /**
     * Ask a question trough the terminal.
     *
     * @param $string
     *
     * @return bool
     */
    function cli_ask($string) {
        $line = cli_prompt(
            s('%s (y/n)', $string)
        );

        if ($line == 'y' or $line == 'yes') {
            return true;
        } else {
            return false;
        }
    }
}

if ( ! function_exists('cli_wait_for_yes')) {
    /**
     * Ask the same question over and over again until the user says YES :)
     *
     * @param $string
     *
     * @return bool
     */
    function cli_wait_for_yes($string) {
        while ( ! cli_ask($string)) {}

        return true;
    }
}

if ( ! function_exists('cli_wait_for_no')) {
    /**
     * Ask the same question over and over again until the user says NO :)
     *
     * @param $string
     *
     * @return bool
     */
    function cli_wait_for_no($string) {
        while (cli_ask($string)) {}

        return false;
    }
}

if ( ! function_exists('cli_choose')) {
    function cli_choose($string, array $options) {
        system('stty -icanon');
        $up = '^[[A';
        $down = '^[[B';
        $up = 'u';

        cli_say($string);

        $selectedIndex = 0;

        foreach ($options as $index => $option) {
            $tick = ' ';

            if ($selectedIndex === $index) {
                $tick = 'x';
            }

            cli_say(sprintf('[%s] %s', $tick, $option));
        }

        while ($c = fgetc(STDIN)) {

            if ($c == $up) {
                exit;
            }
        }
    }
}

if ( ! function_exists('cli_erase_char')) {
    /**
     * Erase previous character on the same line.
     */
    function cli_erase_char() {
        echo "\r";
    }
}

if ( ! function_exists('cli_erase_line')) {
    /**
     * Erase previous line.
     */
    function cli_erase_line() {
        echo "\x1b[A";
    }
}

if ( ! function_exists('cli_draw_table')) {
    /**
     * Draw a very simple table based on headers and data.
     *
     * @param $headers
     * @param $rows
     */
    function cli_draw_table($headers, $rows) {
        $widths = [];
        $minPadding = 4;

        // calculate max header length
        foreach ($headers as $index => $cell) {
            $width = 0;

            if (array_key_exists($index, $widths)) {
                $width = $widths[$index];
            }

            $cellWidth = strlen($cell) + $minPadding;

            if ($cellWidth > $width) {
                $widths[$index] = $cellWidth;
            }
        }

        // calculate max row length
        foreach ($rows as $row) {
            foreach ($row as $index => $cell) {
                $width = 0;

                if (array_key_exists($index, $widths)) {
                    $width = $widths[$index];
                }

                $cellWidth = strlen($cell) + $minPadding;

                if ($cellWidth > $width) {
                    $widths[$index] = $cellWidth;
                }
            }
        }


        $lines = [];

        // draw header section
        $line = '|';
        foreach ($headers as $index => $cell) {
            $width = $widths[$index];
            $text = $cell;

            if (strlen($text) < $width) {
                $text.= str_repeat(' ', $width - strlen($text));
            }

            $line.= sprintf(' %s |', $text);
        }

        $separator = '+' . str_repeat('-', strlen($line) - 2) . '+';

        $lines[] = $separator;
        $lines[] = $line;
        $lines[] = $separator;

        // draw data section
        foreach ($rows as $row) {
            $line = '|';

            foreach ($row as $index => $cell) {
                $width = $widths[$index];
                $text = $cell;

                if (strlen($text) < $width) {
                    $text.= str_repeat(' ', $width - strlen($text));
                }

                $line.= sprintf(' %s |', $text);
            }

            $lines[] = $line;
        }

        $lines[] = $separator;

        // draw table
        foreach ($lines as $line) {
            cli_say($line);
        }
    }
}
