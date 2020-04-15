<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

pre('test');
$xml = new CDataXML();
$xml_string = "http://tk-konstruktor.ru/export/xml.php?iblock_id=18";

pre($xml_string);

$xml->LoadString(file_get_contents($xml_string));

if ($node = $xml->SelectNodes('/yml_catalog/shop/offers/')) {
    pre($node->getPosition());
}
//
