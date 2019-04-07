<?php
    function FileHandler($mode) {
        function init($mode) {
            $filename = "";
            $content = "";

            if ($mode === "r") {
                $filename = $_POST("filename");
                if (!isset($filename))
                    echo "Filename don't declared";
                else
                    getFileContent($filename);
            } else if ($mode === "w") {
                $content = $_POST["content"];
                if (!isset($content))
                    echo "Content don't declared";
                else
                    putFileContent($content);
            } else {
                echo file_get_contents("shaders/count.txt");
            }
        }

        function getFileContent($filename) {
            echo file_get_contents($filename);
        }

        function putFileContent($content) {
            $filename = $_POST["filename"];
            $count_filename = "shaders/count.txt";
            $count = file_get_contents($count_filename) + 1;

            if ($filename >= 0 && $filename <= $count) {
                if ($filename == $count)
                    file_put_contents($count_filename, $count);
                file_put_contents("shaders/" . $filename, $content);
            }
        }

        init($mode);
    }

    $action = $_POST["action"];
    if (isset($action))
        FileHandler($action);