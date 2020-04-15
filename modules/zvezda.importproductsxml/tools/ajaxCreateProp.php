<?php

header("Content-type: application/json; charset=utf-8");
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Catalog\CatalogIblockTable;
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");

$IBLOCK_ID = $_POST['iblock_id'];
$name = $_POST["name"];//название поля
$code = $_POST["code"];//код поля

$arResult['ITEM'] = '';

if( !$name)
{
    $arResult['MESSAGE'] = "Имя св-ва обязательно";
}
else {
    $code = $code ? strtoupper($code) : strtoupper(Cutil::translit($name, "ru", ["replace_space" => "_", "replace_other" => "_"]));//код поля
//    $prop_exists = checkProp($IBLOCK_ID, $code);

    if ($prop_exists = checkProp($IBLOCK_ID, $code)) {
        $numberCopy = getNunberCopy($IBLOCK_ID, $code);
        $newName = $name . ' (' . $numberCopy . ')';
        $newCode = $code . '_' . $numberCopy;

        $string = 'Связать с существующим "<span class="bind-into-create" data-id="' . $prop_exists['ID'] . '" data-name="' . $prop_exists['NAME'] . '">' . $prop_exists['NAME'] . '</span>"<br>';
        $string .= 'Или создать другое:';

        $arResult['STATUS'] = 0;
        $arResult['MESSAGE'] = $string;
        $arResult['ITEM'] = [
            'NAME' => $newName,
            'CODE' => $newCode
        ];
    } else {

        $ibp = new CIBlockProperty;
        $arFields = Array(
            "NAME" => $name,
            "CODE" => $code,
            "IBLOCK_ID" => $IBLOCK_ID,
            "PROPERTY_TYPE" => "S",
        );

        if (!$prop_id = $ibp->Add($arFields)) {
            $arResult['STATUS'] = 0;
            $arResult['MESSAGE'] = $ibp->LAST_ERROR;
        } else {
            $arResult['STATUS'] = 1;
            $arResult['ITEM'] = [
                'ID' => $prop_id,
                'NAME' => $name
            ];
        }
    }
}

echo json_encode($arResult);

//получение свойств
function checkProp( $iblock_id, $code )
{
    $prop_ib = CIBlock::GetProperties($iblock_id, Array(), Array("CODE" => $code));
    if ( !$arProperty = $prop_ib->Fetch() ){
        return false;
    }
    return $arProperty;
}

//получаем новый код
function getNunberCopy($iblock_id,$code){
    $num = array();
    $prop_ib = CIBlock::GetProperties($iblock_id, Array(), Array("CODE"=>$code."%"));
    while($pr = $prop_ib->fetch()){
        $num[] = (int)str_replace($code."_","",$pr["CODE"]);
    }
    $n = max($num)+1;
    return $n;
}
