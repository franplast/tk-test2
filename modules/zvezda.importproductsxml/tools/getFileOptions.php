<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
Loader::includeModule("highloadblock");

$file_path = $_POST['file_path'];

$hlbl_file = 5; // TODO перенести в настройки недоступные пользователю
$hlbl_sect = 6; // TODO перенести в настройки недоступные пользователю
$hlbl_prop = 7; // TODO перенести в настройки недоступные пользователю

$arFile = [];
$arSections = [];
$arProperties = [];

$obFileTypes = new \CUserFieldEnum;
$rsFileTypes = $obFileTypes->GetList(array(), array("USER_FIELD_ID" => 264));  // TODO  264 Получать автоматичесски
$rsPropTypes = $obFileTypes->GetList(array(), array("USER_FIELD_ID" => 271)); // TODO  271 Получать автоматичесски

$file_types = array();
$prop_types = array();

while($arFileTypes = $rsFileTypes->Fetch())
    $file_types[$arFileTypes["ID"]] = $arFileTypes;
while($arPropTypes = $rsPropTypes->Fetch())
    $prop_types[$arPropTypes["ID"]] = $arPropTypes;

$hlblock_file = HL\HighloadBlockTable::getById($hlbl_file)->fetch();
$entity_file = HL\HighloadBlockTable::compileEntity($hlblock_file);
$entity_data_class_file = $entity_file->getDataClass();
$arFile = $entity_data_class_file::getList(array(
    "select" => array("*"),
    "order" => array("ID" => "ASC"),
    "filter" => array("UF_LINK"=> $file_path )  // Задаем параметры фильтра выборки
))->fetch();

$hlblock_sect = HL\HighloadBlockTable::getById($hlbl_sect)->fetch();
$entity_sect = HL\HighloadBlockTable::compileEntity($hlblock_sect);
$entity_data_class_sect = $entity_sect->getDataClass();
$resSections = $entity_data_class_sect::getList(array(
    "select" => array("*"),
    "order" => array("ID" => "ASC"),
    "filter" => array("UF_FILE"=> $arFile['ID'])  // Задаем параметры фильтра выборки
));
while( $obSections = $resSections->fetch()){
    $arSections[$obSections['UF_FILE_SECT_ID']] = $obSections;
};

$hlblock_prop = HL\HighloadBlockTable::getById($hlbl_prop)->fetch();
$entity_prop = HL\HighloadBlockTable::compileEntity($hlblock_prop);
$entity_data_class_prop = $entity_prop->getDataClass();
$resProperties = $entity_data_class_prop::getList(array(
    "select" => array("*"),
    "order" => array("ID" => "ASC"),
    "filter" => array("UF_FILE"=> $arFile['ID'])  // Задаем параметры фильтра выборки
));
while( $obProperties = $resProperties->fetch()){
    $arProperties[$obProperties['UF_FILE_PROP_NAME']] = $obProperties;
    $arProperties[$obProperties['UF_FILE_PROP_NAME']]['UF_TYPE_PROP'] = $prop_types[$arProperties[$obProperties['UF_FILE_PROP_NAME']]['UF_TYPE_PROP']]['VALUE'];
};

$arResult = [
    'STATUS' => 1,
    'ENUMS' => [
        'FILE_TYPES' => $file_types,
    ],
    'FILE_INFO' => $arFile,
    'SECTIONS' => $arSections,
    'PROPERTIES' => $arProperties
];

echo json_encode($arResult);
