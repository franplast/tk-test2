<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Iblock;

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arParams["DEPTH_LEVEL"] = intval($arParams["DEPTH_LEVEL"]);
if($arParams["DEPTH_LEVEL"] <= 0)
	$arParams["DEPTH_LEVEL"] = 4;

$arParams["COUNT_ELEMENTS"] = trim($arParams["COUNT_ELEMENTS"]);

if($arParams["COUNT_ELEMENTS"] != "Y")
	$arParams["COUNT_ELEMENTS"] = false;

if(empty($arParams["IBLOCK_TYPE"]))
    return;

$arResult["SECTIONS"] = array();
$arResult["IBLOCKS"] = array();

$arParams["IBLOCK_ID"] = array_diff($arParams["IBLOCK_ID"], array('')); // delete empty values

foreach($arParams["IBLOCK_ID"] as $iblockId)
{
    if($arParams["TRADE_CATALOG"] == "Y") // delete ib which are not a trade catalog
    {
        if(!CCatalog::GetByID($iblockId))
            unset($arParams["IBLOCK_ID"][array_search($iblockId, $arParams["IBLOCK_ID"])]);
    }

    if(CCatalogSKU::GetInfoByOfferIBlock($iblockId)) // delete ib sku
        unset($arParams["IBLOCK_ID"][array_search($iblockId, $arParams["IBLOCK_ID"])]);

}

if($this->StartResultCache(false, $arParams["CACHE_GROUPS"] === "N" ? false: $USER->GetGroups())) {
	if(!CModule::IncludeModule("iblock")) {
		$this->AbortResultCache();
	} else {

        // get info block fields
        $rsIblock = CIBlock::GetList(
            Array("sort" => "ASC"),
            Array(
                "TYPE"=> $arParams["IBLOCK_TYPE"],
                "SITE_ID" => SITE_ID,
                "ACTIVE"=> "Y",
                "CNT_ACTIVE"=> "Y",
                "ID" => $arParams["IBLOCK_ID"]
            ), true
        );

        while($arIblock = $rsIblock->GetNext())
        {
            $iblockUrl = $arIblock["LIST_PAGE_URL"];
            $iblockUrl = str_replace("#SITE_DIR#", "/", $iblockUrl);
            $iblockUrl = str_replace("#IBLOCK_CODE#", $arIblock["CODE"], $iblockUrl);

            Iblock\Component\Tools::getFieldImageData(
                $arIblock,
                array("PICTURE"),
                Iblock\Component\Tools::IPROPERTY_ENTITY_SECTION,
                "IPROPERTY_VALUES"
            );

            $arResult["IBLOCKS"][$arIblock["ID"]] = [
                "NAME" => htmlspecialcharsbx($arIblock["~NAME"]),
                "URL" => $iblockUrl,
                "PICTURE" => $arIblock["PICTURE"],
                "ELEMENT_CNT" => $arIblock["ELEMENT_CNT"]
            ];

            //SECTIONS//

            $arOrder = array(
                "left_margin" => "asc"
            );

            $arFilter = array(
                "GLOBAL_ACTIVE" => "Y",
                "<="."DEPTH_LEVEL" => $arParams["DEPTH_LEVEL"],
                "IBLOCK_ID" => $arIblock["ID"],
                "IBLOCK_ACTIVE" => "Y",
                "CNT_ACTIVE" => $arParams["COUNT_ELEMENTS"]
            );

            $arSelect = array("ID", "IBLOCK_ID", "NAME", "PICTURE", "DEPTH_LEVEL", "SECTION_PAGE_URL", "UF_ICON");

            $rsSections = CIBlockSection::GetList($arOrder, $arFilter, $arParams["COUNT_ELEMENTS"], $arSelect);

            while($arSection = $rsSections->GetNext()) {

                Iblock\Component\Tools::getFieldImageData(
                    $arSection,
                    array("PICTURE"),
                    Iblock\Component\Tools::IPROPERTY_ENTITY_SECTION,
                    "IPROPERTY_VALUES"
                );

                $isParent = CIBlockSection::GetList([], ["IBLOCK_ID" => $arIblock["ID"], "SECTION_ID" => $arSection["ID"], "ACTIVE"=> "Y"])->Fetch();

                $arResult["IBLOCKS"][$arIblock["ID"]]["SECTIONS"][] = array(
                    "IS_PARENT" => !empty($isParent) ? true : false,
                    "DEPTH_LEVEL" => $arSection["DEPTH_LEVEL"],
                    "~NAME" => $arSection["~NAME"],
                    "~SECTION_PAGE_URL" => $arSection["~SECTION_PAGE_URL"],
                    "PICTURE" => $arSection["PICTURE"],
                    "ICON" => $arSection["UF_ICON"],
                    "ELEMENT_CNT" => $arSection["ELEMENT_CNT"]
                );
            }
        }

		$this->EndResultCache();
	}
}

//MENU_LINKS//
$aMenuLinksNew = array();
$menuIndex = 0;

foreach($arResult["IBLOCKS"] as $arIblock)
{
    $aMenuLinksNew[$menuIndex++] = array(
        htmlspecialcharsbx($arIblock["NAME"]),
        $arIblock["URL"],
        array(),
        array(
            "FROM_IBLOCK" => true,
            "IS_PARENT" => !empty($arIblock["SECTIONS"]) ? true : false,
            "PICTURE" => $arIblock["PICTURE"],
            "ELEMENT_CNT" => $arIblock["ELEMENT_CNT"],
            "DEPTH_LEVEL" => 1,
        )
    );

    foreach($arIblock["SECTIONS"] as $arSection) {
        $aMenuLinksNew[$menuIndex++] = array(
            htmlspecialcharsbx($arSection["~NAME"]),
            $arSection["~SECTION_PAGE_URL"],
            array(),
            array(
                "FROM_IBLOCK" => true,
                "IS_PARENT" => $arSection["IS_PARENT"],
                "DEPTH_LEVEL" => $arSection["DEPTH_LEVEL"] + 1,
                "PICTURE" => $arSection["PICTURE"],
                "ICON" => $arSection["ICON"],
                "ELEMENT_CNT" => $arSection["ELEMENT_CNT"]
            )
        );
    }
}

return $aMenuLinksNew;
?>