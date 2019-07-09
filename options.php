<?
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

use Spichka\Importcsv\Main;


Loc::loadMessages(__FILE__);

$request = HttpApplication::getInstance()->getContext()->getRequest();

$module_id = htmlspecialcharsbx($request["mid"] != "" ? $request["mid"] : $request["id"]);

Loader::includeModule($module_id);

global $APLICATION;



$sectionOb = new Main;

$section = $sectionOb->getSectionSite(107);

$secRes = array("check" => "Выберите раздел");

foreach ($section as $key => $value) {
	$secRes[$value["ID"]] = $value["NAME"];
}


$aTabs = array(
	array(
		"DIV" => "edit",
		"TAB" => Loc::getMessage("SPICHKA_IMPORTCSV_OPTION_TAB_NAME"),
		"TITLE" => Loc::getMessage("SPICHKA_IMPORTCSV_OPTION_TAB_NAME"),
		"OPTIONS" => array(
			Loc::getMessage("SPICHKA_IMPORTCSV_OPTION_PART_NAME"),
			array(
				"section",
				Loc::getMessage("SPICHKA_IMPORTCSV_CHOOSE_SECTION"),
				"left",
				array("selectbox", $secRes)
			),
			array(
				"form_import",
				Loc::getMessage("SPICHKA_IMPORTCSV_OPTION_INPUT_NAME"),
				'<input type="file" name="file_data" ></input>',
				array('statichtml')
			),
		),
	)
);




$tabControl = new CAdminTabControl(
	"tabControl",
	$aTabs
);

$tabControl->Begin();
?>

<form action="<?="/bitrix/admin/".$module_id."/importcsv.php"?>" method="post" enctype="multipart/form-data">

	<?
	foreach ($aTabs as $aTab) {
		if($aTab["OPTIONS"]){

			$tabControl->BeginNextTab();

			__AdmSettingsDrawList($module_id, $aTab["OPTIONS"]);
		}
	}

	$tabControl->Buttons();
	?>
	<input type="hidden" name="url_main_file" value="<?=$APPLICATION->GetCurUri()?>"></input>
	<input type="submit" name="apply" value="<?=Loc::getMessage("SPICHKA_IMPORTCSV_OPTION_INPUT_APPLY")?>" class="adm-btn-save" />
	<input type="submit" name="default" value="<?=Loc::getMessage("SPICHKA_IMPORTCSV_OPTION_INPUT_DEFAULT")?>" />

	<?
	echo(bitrix_sessid_post());
	?>
	
</form>

<?$tabControl->End();?>


