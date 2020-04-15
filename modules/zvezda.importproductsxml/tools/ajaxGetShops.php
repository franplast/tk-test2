<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Loader;
Loader::includeModule("iblock");

$IBLOCK_ID = 13; // TODO "get saved option"
$PROPERTY_FILE_REFERENCE = "PROPERTY_FILE_REFERENCE"; // TODO "get saved option"

$el = new CIBlockElement;
$arShops = [];
$rsElement = $el->getList( [], ["IBLOCK_ID" => $IBLOCK_ID, "ACTIVE" => "Y", "!".$PROPERTY_FILE_REFERENCE => false] , false, false, ["IBLOCK_ID", "ID", "NAME", $PROPERTY_FILE_REFERENCE] );

while($arElement = $rsElement->Fetch())
    $arShops[] = [ "ID" => $arElement["ID"], "NAME" => $arElement["NAME"], "FILE_REFERENCE" => $arElement[$PROPERTY_FILE_REFERENCE."_VALUE"] ];

echo json_encode($arShops);
