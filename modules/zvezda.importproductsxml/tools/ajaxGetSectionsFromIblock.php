<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Loader;
Loader::includeModule("iblock");

$IBLOCK_TYPE = 'catalog'; // TODO get saved option
$IBLOCK_ID = $_POST['iblock'];
$SECTION_ID = $_POST['parrent'];

if( !$IBLOCK_ID || empty( $IBLOCK_ID ) ){
    $obj = new CIBlock;
    $arResult = [];

    $rs = $obj->getList( [], ["TYPE" => $IBLOCK_TYPE, "ACTIVE" => "Y"] );

    while($rsItem = $rs->Fetch()) {
        $arResult[] = [
            "NAME" => $rsItem['NAME'],
            "ID" => $rsItem['ID']
        ];
    }
}
else
{
    $obj = new CIBlockSection;
    $arResult = [];

    $arFilter = ["TYPE" => $IBLOCK_TYPE, "ACTIVE" => "Y", "IBLOCK_ID" => $IBLOCK_ID, "SECTION_ID" => false ];
    if( $SECTION_ID ) $arFilter['SECTION_ID'] = $SECTION_ID;

    $rs = $obj->getList( [], $arFilter, false, ["NAME", "ID"], false );

    while($rsItem = $rs->Fetch()) {
        $arResult[] = [
            "NAME" => $rsItem['NAME'],
            "ID" => $rsItem['ID']
        ];
    }
}

echo json_encode($arResult);