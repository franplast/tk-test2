<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

Class zvezda_importproductsxml extends CModule
{
    var $MODULE_ID = "zvezda.importproductsxml";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $MODULE_GROUP_RIGHTS = "N";
    var $MODULE_DIR;

    function zvezda_importproductsxml()
    {
        $arModuleVersion = array();
        include(substr(__FILE__, 0,  -10)."/version.php");
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = "YML импорт товаров магазинов";
        $this->MODULE_UNINSTALL_TITLE = "YML импорт товаров магазинов";
        $this->MODULE_DESCRIPTION = "YML импорт товаров магазинов";
        $this->PARTNER_NAME = "Студия ZVEZDA";
        $this->PARTNER_URI = "https://zvezda-studio.ru";
        $this->MODULE_DIR = "zvezda.importproductsxml";
    }

    function InstallFiles()
    {
        return true;
    }

    function UnInstallFiles()
    {
        return true;
    }

    function DoInstall()
    {
        $this->InstallFiles();
        RegisterModule("zvezda.importproductsxml");
    }

    function DoUninstall()
    {
        $this->UnInstallFiles();
        UnRegisterModule("zvezda.importproductsxml");
    }
}
?>