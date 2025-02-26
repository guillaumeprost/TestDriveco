<?php

function assertEqual($expected, $actual, $message = ''): void {
    if ($expected !== $actual) {
        throw new Exception(
            "Assertion failed : {$message}. expecting: " . var_export($expected, true) . ", got: " . var_export($actual, true). PHP_EOL
        );
    }
}