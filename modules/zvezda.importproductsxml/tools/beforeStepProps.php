<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Loader;
Loader::includeModule("iblock");
$status = "1"; // Пока доступен файл или нет, позже сюда же можно добавить обновлён удалённый или нет. 0- недоступен, 1 - доступен
$message =""; // Заполняем, если есть что сказать, например, что файл не обновлялся с последней проверки, или что он не доступен
$IBLOCK_TYPE = 'catalog'; // TODO get saved option
$IBLOCK_IDS = $_POST['iblock_ids']; // TODO get saved option
if(!$IBLOCK_IDS){
    $status = 0;
}
else {
    $arResult = [];
    $obj = new CIBlock;
    $rs = $obj->getList([], ["TYPE" => $IBLOCK_TYPE, "ACTIVE" => "Y", "ID" => array_unique($IBLOCK_IDS)]);

    while ($rsItem = $rs->Fetch()) {
        $arResult['ITEMS'][] = [
            "NAME" => $rsItem['NAME'],
            "ID" => $rsItem['ID']
        ];
    }
}
$arResult['STATUS'] = $status;
$arResult['MASSAGE'] = $message;


echo json_encode($arResult);
