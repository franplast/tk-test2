<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
include($_SERVER["DOCUMENT_ROOT"]."/local/modules/zvezda.importproductsxml/tools/script.php");

$filePath = $_POST['file_path']; //Адрес файла;
$options =  $_POST['options'];



$import = new importProductsXml($options, $filePath);



pre($_POST);


echo json_encode($_POST);
