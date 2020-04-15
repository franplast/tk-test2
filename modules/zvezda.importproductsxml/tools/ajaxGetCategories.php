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
$rsData = $entityDataClass::getList(["select" => ["ID","UF_IBLOCK_ID","UF_SECTION_ID","UF_CATEGORY_NAME","UF_CATEGORY_ID"], "filter" => ["ID" => $categoryId]]);
while($arData = $rsData->Fetch())
{
    $arResult["ITEM"][$arData["ID"]] = array(
        "ID" => $arData["ID"],
        "BLOCK_ID" => $arData["UF_IBLOCK_ID"],
        "SECTION_ID" => $arData["UF_SECTION_ID"],
        "NAME" => $arData["UF_CATEGORY_NAME"],
        "CATEGORY_ID" => $arData["UF_CATEGORY_ID"],//id категории yaml
    );
/*
[ID] => 2
[UF_CATEGORY_NAME] => Услуга
[UF_XML_ID] => sadteh24.ru_2622
[UF_CATEGORY_ID] => 2622
[UF_IBLOCK_ID] => 66
[UF_SECTION_ID] =>
*/

}
echo json_encode($arResult);
