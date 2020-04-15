<?
header("Content-type: application/json; charset=utf-8");
$status = "1"; // Пока доступен файл или нет, позже сюда же можно добавить обновлён удалённый или нет. 0- недоступен, 1 - доступен
$message =""; // Заполняем, если есть что сказать, например, что файл не обновлялся с последней проверки, или что он не доступен
$arResult['STATUS'] = $status;
$arResult['MASSAGE'] = $message;
$arResult['RESULT'] = array();

// TODO перенести в настройки, использовать и здесь и в ядре модуля
// TODO дописать Исключения и массив предопределённых
// Исключаемые св-ва
$arFieldsExclude = [
    'delivery',
    'store',
    'categoryId',
    'available'
];
$arFieldsHard = [
    'currencyId',
    'name' => 'IE_NAME',
    'id' => 'IE_XML_ID',
    'url' => 'PROPERTY_URL',
    'vendor' => 'PROPERTY_BRAND',
    'country_of_origin' => 'PROPERTY_COUNTRY',
    'model' => 'PROPERTY_MODEL',
    'vendorCode' => 'PROPERTY_ARTICLE',
    'price' => 'CATALOG_PRICE',
    'weight' => 'CATALOG_WEIGHT'
];

$FILE = $_POST["file_path"];
//$FILE = "http://tk-konstruktor.ru/export/xml.php?iblock_id=16";//удалить
$d = file_get_contents($FILE);
$fields = array();
$props = array();
if($d){
    $data = simplexml_load_string($d);
    foreach ($data->shop->offers->offer as $items) {
        $el = json_decode(json_encode($items), true);
        $par = get_params($items);
        if(is_array($par)){
            $props = array_merge($props,$par);
        }
        //pre($el["param"]);
        unset($el["param"]);
        $p = get_properties($el);
        if(is_array($p)){
            $fields = array_merge($fields,$p);
        }
        //pre(array_keys($el));
    }
}
$fields = array_unique($fields);
$props = array_unique($props);
$arResult['ITEMS']["FIELDS"] = $fields;
$arResult['ITEMS']['PROPS'] = $props;
echo json_encode($arResult);
//оплучение свойств
function get_properties($el,$all = false){
    $ar = array();
    foreach ($el as $name=>$val){
        if(is_array($val)){
            if($all || ($name!="@attributes" && !is_numeric($name)))$ar[] = $name;
            $t = get_properties($val,$all);
            $ar = array_merge($ar,$t);
        }
        else {
            if($all || ($name!="@attributes" && !is_numeric($name)))$ar[] = $name;
        }
    }
    return $ar;
}
//получение параметров
function get_params($items){
    foreach($items->param as $par){
        foreach ($par->attributes() as $name=>$val){
            //pre($name);
            $params[] = (string)$val;
        };
    }
    return $params;

}
