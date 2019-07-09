<?
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if(!check_bitrix_sessid()){
	return;
}

if($errorException = $APPLICATION->GetException()){
	echo(CAdminMessage::ShowMessage($errorException->GetString()));
}else{
	echo(CAdminMessage::ShowNote(Loc::getMessage("SPICHKA_IMPORTCSV_STEP_BEFORE")." ".Loc::getMessage("SPICHKA_IMPORTCSV_STEP_AFTER")));
}
?>

<form action="<?=$APPLICATION->GetCurPage();?>">
	<input type="hidden" name="lang" value="<?=LANG?>" />
	<input type="submit" value="<?=Loc::getMessage("SPICHKA_IMPORTCSV_STEP_SUBNIT_BACK")?>" />
</form>

