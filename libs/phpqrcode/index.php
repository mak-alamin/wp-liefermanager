<?php
/*
 * PHP QR Code encoder
 */

function wp_liefer_generate_table_qr_code($table_id, $data)
{
    //set it to writable location, a place for temp generated PNG files
    $PNG_TEMP_DIR = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR;

    //html PNG location prefix
    $PNG_WEB_DIR = 'temp/';

    include "qrlib.php";

    //ofcourse we need rights to create temp dir
    if (!file_exists($PNG_TEMP_DIR)) {
        mkdir($PNG_TEMP_DIR);
    }

    $filename = $PNG_TEMP_DIR . 'table_qr_code_' . $table_id . '.png';

    //processing form input
    //remember to sanitize user input in real-life solution !!!
    // $errorCorrectionLevel = 'L';
    // if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L', 'M', 'Q', 'H')))
    //     $errorCorrectionLevel = $_REQUEST['level'];

    // $matrixPointSize = 4;
    // if (isset($_REQUEST['size'])) {
    //     $matrixPointSize = min(max((int)$_REQUEST['size'], 1), 20);
    // }

    if (isset($data['table_product_url'])) {
        // user data

        // $filename = $PNG_TEMP_DIR . 'test' . md5($data['table_product_url'] . '|' . $errorCorrectionLevel . '|' . $matrixPointSize) . '.png';

        QRcode::png($data['table_product_url'], $filename, 'L', 14, 2);
    }

    $qrcode_url = $PNG_WEB_DIR . basename($filename);

    return $qrcode_url;
}
