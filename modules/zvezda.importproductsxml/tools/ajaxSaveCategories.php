<?php
header("Content-type: application/json; charset=utf-8");
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Catalog\CatalogIblockTable;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Bitrix\Main\Config\Option;
CModule::IncludeModule("highloadblock");
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
$arResult = array();
$arResult['STATUS'] = 1;

$categoryId = 2;
$iblock_id_ar = array();
$section_id_ar = array();
$HlBlockIdCategories = 4;//hb категория id

$hlblock = HL\HighloadBlockTable::getById($HlBlockIdCategories)->fetch();
$entity = HL\HighloadBlockTable::compileEntity($hlblock);
$entityDataClass = $entity->getDataClass();
//TODO сначала проверить есть ли запись, после обновляем или добавляеи

$catData = $entityDataClass::getList(["select" => ["ID"], "filter" => ["UF_CATEGORY_ID" => $categoryId]]);
if($ar_hd = $catData->fitch()){//если запись существует
    //update
    $entityDataClass::update($ar_hd, ["UF_IBLOCK_ID" => $iblockId]);

}
else {//если записи нет
    $arData = [
        "UF_CATEGORY_NAME" => $arCategory["NAME"],
        "UF_XML_ID" => $xmlId,
        "UF_CATEGORY_ID" => $categoryId
    ];
    /*
[UF_CATEGORY_NAME] => Услуга
[UF_XML_ID] => sadteh24.ru_2622
[UF_CATEGORY_ID] => 2622
[UF_IBLOCK_ID] => 66
[UF_SECTION_ID] =>
*/
//add
    $result = $entityDataClass::add($arData);

}

//test 2
echo json_encode($arResult);
