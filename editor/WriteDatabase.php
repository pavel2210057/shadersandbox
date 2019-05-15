<?php
    $filename = $_POST["filename"];
    $content = $_POST["content"];

    if(!isset($filename) || !isset($content))
        echo "Data incorrect";

    file_put_contents($filename, $content);