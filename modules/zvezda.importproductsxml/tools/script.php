<?
set_time_limit(0);
ini_set('memory_limit', '1000M');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Loader;
Loader::includeModule("iblock");

//require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/xml.php');
//
//CModule::IncludeModule("highloadblock");
//
//use Bitrix\Highloadblock as HL;
//use Bitrix\Main\Entity;
//use Bitrix\Main\Config\Option;
//
//CModule::IncludeModule("iblock");
//CModule::IncludeModule("sale");
//CModule::IncludeModule("catalog");
//
//$el = new CIBlockElement;
//$ibp = new CIBlockProperty;

class importProductsXml
{
    // перенести в настройки модуля

    private $el;
    private $filePath; // Адрес файла, зачем
    private $options; // Полный массив опций
    private $offersInIteration; // количество оферов на итерацию
    private $firstOfferIter; // Номер первого офера в итерации
    private $sections; // Номер первого офера в итерации
    private $product_update; // Номер первого офера в итерации
    private $sku_update; // Номер первого офера в итерации
//    private fileInfo; // Номер первого офера в итерации

    private $arOffers; // массив текущих оферов. Определяется в getOffers()

    // Св-ва
    private $tovarProps; // список св-в товаров для проверки и добавления в ИБ
    private $skuProps; // список св-в ску для проверки и добавления в ИБ
    private $store_iblock = 13;//iblick_id магазинов
    public $xmlUrl; // url магазина из xml



    // Методы
// Распечатка входящего массива $options
/*
$OPTIONS = Array(
    'file_path' => 'http://doorhan-moscow.ru/export/yamarket.yml?rnd=0.7145943480817281'
    'options' => Array(
        'offersInIteration' => 10, // по сколько за итерацию
        'firstOfferIter' => 0,// начать с ... оффера
        'object_id' => '', //TODO id магазина, если выбран
        'file_id' => '', //TODO id файла из hlbl если это не магазин
        'sections' => Array(  // Массив связей для разделов
            5705 => Array(
                'iblock_id' => 63,
                'section_id' => 805,
            ),
            5746 => Array(
                'iblock_id' => 24,
            ),
            5747 => Array(
                'iblock_id' => 24,
            ),
            5748 => Array(
                'iblock_id' => 24,
            ),
            5749 => Array(
                'iblock_id' => 24,
            ),
            5750 => Array(
                'iblock_id' => 24,
            ),
            5751 => Array(
                'iblock_id' => 24,
            ),
        ),
        'relations_iblocks' => Array( // Задействованные ИБ. Не уверен что это нужно скрипту
            '0' => Array(
                'NAME' => 'Освещение',
                'ID' => 24,
            ),
        ),
        'options' => Array( // Массив связей для полей и св-в
            'fields' array( // Массив связей для полей
                'id' => Array( // Название поля в файле. перечислены все возможные варианты. Пустым быть не может, но могут писутствовать не все варианты
                    'prod_field_id' => 'SORT', // prod_field_id - загружаем в поле товара
                    'prod_prop_id' => 131, // prod_prop_id - загружаем в св-во товара
                    'sku_prop_id' => 542, // sku_prop_id - загружаем в св-во SKU
                ),
            ),
            'params' => array( // Массив связей для св-в
                //... Аналогично fields ....
            );
        ),
        'update_options' => Array( // Опции обновления (еречислены все)
            'product' => Array( // Опции для обновления товаров
                0 => 'prod_props_upd', // Обновлять св-ва и поля товара
                1 => 'prod_image_upd', // Обновлять картинки
                2 => 'force_moving_upd', // Принудительно переносить в ИБ и раздел указанный в опциях. При переносе в др ИБ помни, что у товаров есть SKU
            ),
            'sku' => Array( // Опции для обновления SKU
                0 => 'sku_price_upd', // Обновлять цену
                1 => 'sku_props_upd', // Обновлять св-ва
            )
        )
    )
)
*/

    public function __construct( $options, $filePath ){
        // Входящие опции
        $this->el = new CIBlockElement;

        $this->options = $options;
        $this->filePath = $filePath;
//        $this->offersInIteration = $options['offersInIteration']; // TODO перенести в настройки модуля
//        $this->firstOfferIter = $options['firstOfferIter'];
////        $this->fileInfo = $options['fileInfo'];
//        $this->sections = $options["sections"];
//        $this->product_update = $options['update_options']['product'];
//        $this->sku_update = $options['update_options']['sku'];

        /* Не задействованы
        $options['object_id'] //TODO id магазина, если выбран
        $options['file_id'] //TODO id файла, если выбран
        $options['options']['fields'] //TODO связи для полей из файла
        $options['options']['params'] //TODO связи для params из файла
        $options['update_options']['product']  //TODO опции обновления товаров
        $options['update_options']['sku'] //TODO опции обновления SKU
         */

        // Генерируемые опции
        $this->score = array();
        $this->xmlUrl = $this->getShopUrlXml($options["shop_url"]);
    }

    public function updateOffers(){

        // TODO ведём счётчики для добавленных, обновлённых, проигнорированных и тп
        $countUpdateOffers = 0; // обновленные ТП
        $countAddOffers = 0; // добавленные ТП
        $countAddOffersExistProducts = 0; // добавленные ТП к существующим товарам
        $countNotAddOffersReasonNoName = 0; // не добавленные товары
        $countAddProducts = 0; // добавленные товары
        $countAddBrands = 0; // добавленные бренды

//        $arOffersIter = $this->getOffers();

        foreach ($this->getOffers() as $offer ){

            $offer_id = $offer->attributes()->id->__toString();

            $arPaterns = [ // Перенести в конструктор типы полей?
                "%XML_ID" => $offer_id,
                "XML_ID" => "shopURL_12345",
                "NAME" => "test",
            ];

            $arSku = $this->getSkuId( $arPaterns );  // $result = [ 'sku_id' => 1, 'sku_iblock_id' => 1, 'prod_id' => 1,  'prod_id' => 1 ];

            if( $sku_id && !empty($this->options['update_options']['sku']) ){
                $sku_update_res = skuUpdate($sku_id, $this->options['update_options']['sku']  );
            }

            if( !empty($this->options['update_options']['product']) ){
                $sku_id['prod_id']
            }






            if( !$sku_id ){
                $product_id = $this->getProductId($arPaterns);
            }





            /*


            $SKU_XML_ID = $offer->attributes()->id->__toString();
            // TODO если в файле указан параметр привязки к магазину - получаем его id
            // если нет используем id магаза или файла изи hlbl, получаем это в опциях
            if($arend = $offer->prop->attributes()->login_arendator->__toString()){
                $xml_id = getArendator($arend)."_".$SKU_XML_ID;
            }
            else {
                $xml_id = "...";//file_<id_записи из hlbl>_<id_оффера>
            }


            $categoty = (int)$offer->categoryId;
            $iblock_id = $this->getIblockIdCategory($categoty);


            $sku_id = $this->getIdSKU($iblock_id);
            //TODO код для поиска товара по старому варианту xml_id url_id
            $old_xml_id = $this->xmlUrl."_".$SKU_XML_ID;
            // TODO готовим варианты XML_ID для поиска и замены:
            // <ссылка на магаз из файла>_<id оффера>,
            // <id_магаза>_<id_оффера> или  file_<id_записи из hlbl>_<id_оффера> в ависимости от ислочника
            $sku_prod_id = $this->findByXmlId($old_xml_id,$sku_id);//TODO если не ношел вернуть false


            // TODO ищем оффер в ИБ SKU, ищем по xml_id, перенести в обновление ску
            if($sku_prod_id){
                $this->updateXmlId($sku_prod_id,$xml_id);
            }

            if($sku_prod_id) {
                $sku_prod_id = $this->findByXmlId($xml_id,$sku_id);
            }

            // TODO если есть - обновляем соответственно параметрам, ->отдельная функция
            // допом меняем xml_id на <id_магаза>_<id_оффера> или  file_<id_записи из hlbl>_<id_оффера> в ависимости от ислочника
            if($sku_prod_id){
                $this->updateSKU($sku_prod_id,$offer);
            }
            else {//если нет ску
                // TODO если нет - ищем товат в ИБ товаров
                $prod_id = findProduct($iblock_id,$offer->name->__toString());
                if(!$prod_id){//если нет товара
                    $this->addProduct($iblock_id,$sku_prod_id,$offer);
                }
                else{//товар уже есть
                    $this->addSkuProduct(iblock_id,$sku_prod_id,prod_id,$offer);
                }

            }


*/

            // TODO  Все обновления и добавления св-в должны уметь определять тип своего св-ва и действовать соответственно.
            // Например при связи с бренодом - проверяем наличие бренда, если нет - создаём и привязываем
            // пока отрабатываем строки, списки и связанные элементы
            /*
             * Типы свойств инфоблоков:
             *  S — строка
             *  N — число
             *  L — список
             *  F — файл
             *  G — привязка к разделу
             *  E — привязка к элементу
             *  S:UserID — Привязка к пользователю
             *  S:DateTime — Дата/Время
             *  E:EList — Привязка к элементам в виде списка
             *  S:FileMan — Привязка к файлу (на сервере)
             *  S:map_yandex — Привязка к Яndex.Карте
             *  S:HTML — HTML/текст
             *  S:map_google — Привязка к карте Google Maps
             *  S:ElementXmlID — Привязка к элементам по XML_ID
             */

        }

    }





    private function getSkuId( $arPatterns ){
        $result = false;
        // ищем СКУ в своём ИБ, если нет - ищем в других
        // возвращаем sku_id iblock_id
        //Можно искать не по полному XML_ID а по id из файла, а потом сравкивать с ID или uRL магазина или типа того
        $result = [ 'sku_id' => 1, 'iblock_id' => 1, 'prod_id' => 1 ];
        return $result ;
    }

    private function getProductId( $arPatterns ){
        $result = false;
        // ищем СКУ в своём ИБ, если нет - ищем в других
        // возвращаем sku_id iblock_id
        //
        $result = [ 'sku_id' => 1, 'iblock_id' => 1, 'prod_id' => 1 ];
        return $result ;
    }


    private function skuUpdate($sku_id, $update_options  ){
        $result = false;
        $data = []; // TODO готовим поля, св-ва и тп c оглядкой на $update_options


        if( true ){
            $result = true;
        }

        return $result;
    }


/*
    //замена старого xml_id на новый
    private function updateXmlId($id_sky_prod,$new_xml){
        $prod_ar = CCatalogSku::GetProductInfo($id_sky_prod);
        CIBlockElement::SetPropertyValuesEx($id_sky_prod, false, array('ELEMENT_XML_ID'=>$new_xml));
        //CIBlockElement::SetPropertyValuesEx($prod_ar["ID"], false, array('ELEMENT_XML_ID'=>$new_xml));
        return true;
    }
    //добавление товара
    private function addProduct($ibl_product,$ibl_sku,$offer){
        $SCRIPT_CREATE_ENUM_ID = $this->getPropIdEnum($ibl_product,"SCRIPT_CREATE","Y");
        $arProp = [
            "OBJECT" => $this->shopId, // магазин
            //"SECTION_YML" => $breadcrumbs, // хлебные крошки категорий из xml
            "MODEL" => $offer->model->__toString(),
            "WEIGHT" => $offer->weight->__toString(),
            "ARTICLE" => $offer->vendorCode->__toString(),
            "OLD_PRICE" => $offer->oldprice->__toString(),
            "BARCODE" => $offer->barcode->__toString(),
            "ITERATION" => date("Y-m-d"), // дата итерации
            'SCRIPT_CREATE' => ["VALUE" => $SCRIPT_CREATE_ENUM_ID]
        ];
        $picture = false;//картинка для детальной и превью
        if($offer->picture->count()>1)
        {
            $arPictures = [];
            foreach($offer->picture as $pictureUrl)
            {
                $pictureUrl_str = $pictureUrl->__toString();
                if(!$picture) $picture =  CFile::MakeFileArray($pictureUrl_str);//если несколько фото то первую добавим в детальную и превью

                $arPictures[] = CFile::MakeFileArray($pictureUrl);
            }
            $arProp["MORE_PHOTO"] = $arPictures;
        }
        if(!$picture) CFile::MakeFileArray($offer->picture->__toString());//если не создана картинка не массив фото и берем одну фотку

        // добавляем, проставляем св-ва из param
        $arParamsProduct = $this->getArPropertiesParams($productIblockId, $categoryId, $arParams);

        foreach($arParamsProduct as $propertyCode => $propertyValue)
        {
            $arProp[$propertyCode] = $propertyValue;
        }

        // проставляем бренд товару
        if(!empty($offer->vendor))
        {
            if($brandId = $this->isExistBrand($offer->vendor->__toString()))
            {
                $arProp["BRAND"] = $brandId;
            }
            else
            {
                if($brandId = $this->addBrand($offer->vendor->__toString()))
                {
                    $arProp["BRAND"] = $brandId;

                    $countAddBrands++;
                }
            }
        }

        $arFields = [
            "IBLOCK_ID" => $ibl_product,
            "PROPERTY_VALUES"=> $arProp,
            "XML_ID" => $xmlId,
            "NAME" => $name,
            "CODE" => Cutil::translit($name, "ru", ["replace_space" => "-","replace_other" => "-"]),
            "ACTIVE" => "N",
            "DETAIL_PICTURE" => $picture,
            "PREVIEW_PICTURE" => $picture,
            "DETAIL_TEXT" => $offer->description->__toString(),
            "DETAIL_TEXT_TYPE" => "html",
        ];

        if(!empty($arParamsProduct["DETAIL_TEXT"]))
        {
            $arFields["DETAIL_TEXT"] = $arParamsProduct["DETAIL_TEXT"];
            $arFields["DETAIL_TEXT_TYPE"] = "html";
        }

        if(!empty($arParamsProduct["PREVIEW_TEXT"]))
        {
            $arFields["PREVIEW_TEXT"] = $arParamsProduct["PREVIEW_TEXT"];
            $arFields["PREVIEW_TEXT_TYPE"] = "html";
        }

        if($productId = $this->el->add($arFields)) // если добавлен товар, добавляем ТП
        {
            $this->addSkuProduct($ibl_product, $ibl_sku, $productId, $offer);
        }
    }
    private function isExistBrand($brandName) // проверяем существует ли бренд по имени
    {
        $rsElement = $this->el->getList([], ["IBLOCK_ID" => 22, "ACTIVE" => "Y", "NAME" => $brandName], false, false, ["ID"]);
        if($arElement = $rsElement->Fetch())
        return false;
    }

    private function addBrand($brandName)
    {
        if(empty($brandName))
            return false;

        global $el;

        $arFields = [
            "NAME" => $brandName,
            "CODE" => Cutil::translit($brandName, "ru", ["replace_space" => "-","replace_other" => "-"]),
            "IBLOCK_ID" => 22,
            "ACTIVE" => "Y"
        ];

        if($brandId = $this->el->add($arFields))
            return $brandId;
        else
            return false;
    }

    private function getPropIdEnum($idl_id,$prop,$val){
        $script_create_res = CIBlockPropertyEnum::GetList( [], Array("IBLOCK_ID"=>$idl_id, "CODE"=>$prop, "VALUE" => $val));
        if( $script_create_ob = $script_create_res->GetNext() ) {
            return $script_create_ob['ID'];
        }
        return false;
    }
    //добавление предложения
    private function addSkuProduct($ibl_product,$ibl_sku,$id_product,$offer){

    }
    //поиск товара
    private function findProduct($id,$name){
        $rsProduct = $this->el->GetList([], ["IBLOCK_ID" => $id, "NAME" => $name], false, false, ["ID"]);

        return $rsProduct->Fetch(); // если товар найден по названию, добавляем ТП к этому товару
    }
    //обновление предложения
    private function updateSKU($id,$offer){
        // TODO Если нашли - проверяем есть ли СКУ от данного магазина и обновляем товар если есть указания в опциях
        // TODO если SKU  от магаза есть - добавляем SKU к товару и записываем в отчёт, что тут могут быть дубли

        // TODO если нет - создаём товар и SKU  ->отдельная функция

    }
    //получение sku id
    private function getIdSKU($id){
        $mxResult = CCatalogSKU::GetInfoByProductIBlock($id);
        return $mxResult ? $mxResult["IBLOCK_ID"] : false;
    }
    //получение iblock_id товара по категории
    private function getIblockIdCategory($cat){
        return $this->sections[$cat]["iblock_id"]?:false;
    }
    //поиск товара по xml_id
    private function findByXmlId($xml_id,$IBlock_id){
        //TODO искать в одном id если нет то ищем во всех

        $arFilter = Array("IBLOCK_ID" => $xml_id, "XML_ID" => $xml_id);
        $arSelect = Array("ID", "NAME");
        $res = $this->el->GetList(Array(), $arFilter, false, Array(), $arSelect);//ищем по xml_id предложения
        $el = $res->GetNext();
        return $el;
    }
    //поиск арендатора
    private function getArendator($name){
        if($id = array_search($name,$this->store)){
            return $id;
        }
        $arSelect = Array("ID", "NAME",);
        $arFilter = Array("IBLOCK_ID" => 13, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "NAME" => $name);
        $res = $this->el-GetList(Array(), $arFilter, false, Array(), $arSelect);
        $el = $res->GetNext();
        if ($el) {
            $this->store[$el["ID"]] = $name;
            return $el["ID"];
        }
        return false;
    }
    private function getOffers(){
        if( !$this->arOffers ){
            $xmlPath = $this->filePath;
            $xmlContent = file_get_contents($xmlPath);
            $arXml = simplexml_load_string($xmlContent);
            $this->arOffers = array_slice($arXml->xpath("/yml_catalog/shop/offers/offer"), $this->firstOfferIter,  $this->offersInIteration );
        }
        return $this->arOffers;
    }
    private function getShopUrlXml($url) // получаем url магазина из xml
    {
        return "tk-konstruktor.ru";
        if($this->xmlContent)
        {
            $arXml = simplexml_load_string($this->xmlContent);
            $arXml = json_encode($arXml);
            $arXml = json_decode($arXml, true);

            $xmlUrl = $arXml["shop"]["url"];

            if(!empty($xmlUrl))
            {
                $xmlUrl = str_replace("http:", "", $xmlUrl);
                $xmlUrl = str_replace("https:", "", $xmlUrl);
                $xmlUrl = str_replace("www.", "", $xmlUrl);
                $xmlUrl = str_replace("/", "", $xmlUrl);

                return $xmlUrl;
            }
        }

        return false;
    }
*/
    //
//    private function addBrand($brandName)
//    {
//        if(empty($brandName))
//            return false;
//
//
//        $arFields = [
//            "NAME" => $brandName,
//            "CODE" => Cutil::translit($brandName, "ru", ["replace_space" => "-","replace_other" => "-"]),
//            "IBLOCK_ID" => 22,
//            "ACTIVE" => "Y"
//        ];
//
//        if($brandId = $this->el->add($arFields))
//            return $brandId;
//        else
//            return false;
//    }


}