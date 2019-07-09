<?
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Application;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\IO\File;

Loc::loadMessages(__FILE__);


class spichka_importcsv extends CModule{

	public function __construct(){

		if(file_exists(__DIR__."/version.php")){
			$arModuleVersion = array();

			include_once(__DIR__."/version.php");

			$this->MODULE_ID = str_replace("_", ".", get_class($this));
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
			$this->MODULE_NAME = Loc::getMessage("SPICHKA_IMPORTCSV_NAME");
			$this->MODULE_DESCRIPTION = Loc::getMessage("SPICHKA_IMPORTCSV_DESCRIPTION");
			$this->PARTNER_NAME = Loc::getMessage("SPICHKA_IMPORTCSV_PARTNER_NAME");
			$this->PARTNER_URI = Loc::getMessage("SPICHKA_IMPORTCSV_PARTNER_URI");
		}

		return false;
	}

	public function DoInstall(){
		global $APPLICATION;

		if(CheckVersion(ModuleManager::getVersion("main"), "14.00.00")){
			$this->InstallFiles();
			$this->InstallDB();

			ModuleManager::registerModule($this->MODULE_ID);

			$this->InstallEvents();
		}else{
			$APPLICATION->ThrowException(
				Loc::getMessage("SPICHKA_IMPORTCSV_ERROR_VERSION")
			);
		}

		$APPLICATION->IncludeAdminFile(
			Loc::getMessage("SPICHKA_IMPORTCSV_INSTALL_TITLE")." \"".Loc::getMessage("SPICHKA_IMPORTCSV_NAME")."\"",
			__DIR__."/step.php"
		);



		return false;
	}

	public function InstallFiles(){

		CopyDirFiles(
			__DIR__."/assets/include",
			Application::getDocumentRoot()."/bitrix/admin/".$this->MODULE_ID."/",
			true,
			true
		);

		return false;
	}

	public function InstallDB(){

		return false;
	}

	public function InstallEvents(){

		return false;
	}

	public function DoUninstall(){

		global $APPLICATION;

		$this->UnInstallFiles();
		$this->UnInstallDB();
		$this->UnInstallEvents();

		ModuleManager::unRegisterModule($this->MODULE_ID);

		$APPLICATION->IncludeAdminFile(
			Loc::getMessage("SPICHKA_IMPORTCSV_UNINSTALL_TITLE")." \"".Loc::getMessage("SPICHKA_IMPORTCSV_NAME")."\"",
			__DIR__."/unset.php"
		);

		return false;
	}

	public function UnInstallFiles(){

		// File::deleteFile(
		//  	Application::getDocumentRoot()."/ajax/".$this->MODULE_ID."/importcsv.php"
		// );

		Directory::deleteDirectory(
			Application::getDocumentRoot()."/bitrix/admin/".$this->MODULE_ID
		);


		return false;
	}

	public function UnInstallDB(){
		Option::delete($this->MODULE_ID);

		return false;
	}

	public function UnInstallEvents(){

		return false;
	}

}


?>