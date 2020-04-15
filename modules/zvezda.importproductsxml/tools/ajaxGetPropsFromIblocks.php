<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Catalog\CatalogIblockTable;
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
/*
 * $NEED:
 *  field-product поля инфоблока
 *  prop-product свойства инфоблока
 *  prop-sku свойства предложений
 */

// Исключаем системные поля
$arFieldExclude = [
    'IBLOCK_SECTION',
    'ACTIVE',
    'ACTIVE_FROM',
    'ACTIVE_TO',
    'PREVIEW_TEXT_TYPE',
    'DETAIL_TEXT_TYPE',
    'TAGS',
    'SECTION_NAME',
    'SECTION_PICTURE',
    'SECTION_DESCRIPTION_TYPE',
    'SECTION_DESCRIPTION',
    'SECTION_DETAIL_PICTURE',
    'SECTION_XML_ID',
    'SECTION_CODE',
    'LOG_SECTION_ADD',
    'LOG_SECTION_EDIT',
    'LOG_SECTION_DELETE',
    'LOG_ELEMENT_ADD',
    'LOG_ELEMENT_EDIT',
    'LOG_ELEMENT_DELETE',
    'XML_IMPORT_START_TIME',
    'DETAIL_TEXT_TYPE_ALLOW_CHANGE',
    'PREVIEW_TEXT_TYPE_ALLOW_CHANGE',
    'SECTION_DESCRIPTION_TYPE_ALLOW_CHANGE',
];
$arProductPropsExclude = [];
$arSkuPropsExclude = [];

$NEED = $_POST['need'];
$PRODUCT_IBLOCK_ID = $_POST['iblock_id'];

$arResult['ITEMS'] = array();

$iblockIterator = Bitrix\Catalog\CatalogIblockTable::getList(array(
    'select' => array('IBLOCK_ID'),
    'filter' => array('=PRODUCT_IBLOCK_ID' => $PRODUCT_IBLOCK_ID)
));
$arResultSKU = $iblockIterator->fetch();
$SKU_IBLOCK_ID = $arResultSKU["IBLOCK_ID"];

if(!isset($NEED)){
    $arResult['STATUS'] = 0;
}
else {
    switch ($NEED){
        case "field-product"://поля инфоблока
            $ib_field = getField($PRODUCT_IBLOCK_ID, $arFieldExclude);
            break;
        case "prop-product"://свойства инфоблока
            $ib_prop = getProp($PRODUCT_IBLOCK_ID, $arProductPropsExclude);
            break;
        case "prop-sku"://свойства предложений
            $sku_prop = getProp($SKU_IBLOCK_ID, $arSkuPropsExclude);
            break;
    }
}
if($ib_field){

    $arResult['ITEMS'] = $ib_field;
    $arResult['HTML'] = getHtml(  $arResult['ITEMS'], 'CODE' );
}
if($ib_prop){

    $arResult['ITEMS'] = $ib_prop;
    $arResult['HTML'] = getHtml( $arResult['ITEMS'] );
}
if($sku_prop){

    $arResult['ITEMS'] = $sku_prop;
    $arResult['HTML'] = getHtml( $arResult['ITEMS'] );
}

echo json_encode($arResult);

function getHtml( $ar_items, $value_id = 'ID', $text_id = "NAME" ){
    $html_string = '<select>';
    foreach( $ar_items as $option )
        $html_string .= '<option value="'.$option[$value_id].'">'.$option[$text_id].'</option>';
    $html_string .= '</select>';
    return $html_string;
};

//получение свойств
function getProp($id, $arExclude){
    $arProps = [];
    $resProps = CIBlock::GetProperties($id);
    while ($obProps = $resProps->Fetch()){
        if( in_array( $obProps["CODE"], $arExclude ) ||  in_array( $obProps["ID"], $arExclude )  )
            continue;
        $arProps[] = array(
            "ID" => $obProps["ID"],
            "NAME" => $obProps["NAME"],
            "CODE" => $obProps["CODE"],
        );
    }
    return $arProps;
}

//получение полей
function getField($id, $arExclude ){
    $arFields = array();
    $resFields = CIBlock::GetFields($id);
    foreach ($resFields as $code => $field){
        if( in_array( $code, $arExclude ) )
            continue;
        $arFields[] = array(
            "CODE" => $code,
            "NAME" => $field["NAME"]
        );
    }
    return $arFields;
}
