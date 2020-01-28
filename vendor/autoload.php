<?php
    spl_autoload_extensions(".php"); // comma-separated list
    spl_autoload_register();

    function my_autoload ($pClassName) {
        include(__DIR__ . DIRECTORY_SEPARATOR . $pClassName . ".php");
    }
    spl_autoload_register("my_autoload");
?>