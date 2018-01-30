<?php
    //do some logging
    $line = date('Y-m-d H:i:s') . " - $_SERVER[REMOTE_ADDR]";
    file_put_contents('log', $line . PHP_EOL, FILE_APPEND);

    $file = 'app.apk';

    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($file).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }