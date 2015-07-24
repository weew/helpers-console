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
     * @param null $defaultDisplay
     *
     * @return string
     */
    function cli_prompt($string, $default = null, $defaultDisplay = null) {
        if ($defaultDisplay === null) {
            $defaultDisplay = $default;
        }

        if ($default !== null) {
            $string = s('%s (%s): ', $string, $defaultDisplay);
        } else {
            $string = s('%s: ', $string);
        }

        fwrite(STDOUT, $string);
        $input = trim(fgets(STDIN));

        if (strlen(trim($input)) == 0) {
            $input = $default;
        }

        return $input;
    }
}

if ( ! function_exists('cli_force_prompt')) {
    /**
     * Force user to enter some input.
     *
     * @param $string
     *
     * @return string
     */
    function cli_force_prompt($string) {
        do {
            $input = cli_prompt($string);

            if ($input !== null) {
                return $input;
            }
        } while (true);
    }
}

if ( ! function_exists('cli_ask')) {
    /**
     * Ask a question trough the terminal.
     *
     * @param $string
     * @param bool $default
     *
     * @return bool
     */
    function cli_ask($string, $default = false) {
        $suffix = $default ? '(Y/n)' : '(y/N)';

        $input = cli_prompt(s('%s %s', $string, $suffix));

        if ($input == 'y' or $input == 'yes') {
            return true;
        } else if (empty($input) and $default) {
            return true;
        }else {
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
    function cli_choose($string, array $options, $default = null, $newline = true) {
        $options = array_values($options);
        $indent = str_repeat(" ", strspn($string, " "));
        $indent.= str_repeat("\t", strspn($string, "\t"));


        $choices = [];
        foreach ($options as $key => $value) {
            if ($newline) {
                $value = $indent . s("- [%s] %s", $key, $value);
            } else {
                $value = s("[%s] %s", $key, $value);
            }


            $choices[] = $value;
        }

        if ($newline) {
            $choicesString = implode("\n", $choices);
        } else {
            $choicesString = $indent . implode(', ', $choices);
        }

        do {
            cli_say($string);
            cli_say($choicesString);
            $choice = cli_prompt(s("%sYour choice?", $indent), $default);

            if ($option = array_get($options, $choice)) {
                return $option;
            }

            cli_say();
            cli_say(s("%sInvalid option %s.", $indent, $choice));
            cli_say();
        } while(true);
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
