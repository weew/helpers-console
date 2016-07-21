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
     * @param null $defaultValueMask
     *
     * @return string
     */
    function cli_prompt($string, $default = null, $defaultValueMask = null) {
        if ($defaultValueMask === null) {
            $defaultValueMask = $default;
        }

        if ($default !== null) {
            $string = s('%s (%s): ', $string, $defaultValueMask);
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
