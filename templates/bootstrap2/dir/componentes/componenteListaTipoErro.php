<?php
$oControle = new Controle();
$aTipoErro = $oControle->getAllTipoErro();
?>
<select name="<?=$nomeCampo?>" id="<?=$nomeCampo?>" <?=$complemento?>>
    <option value="">Selecione</option>
<?php
foreach($aTipoErro as $oTipoErro){
?>
    <option value="<?=$oTipoErro->get_idTipoErro();?>"<?=($oTipoErro->get_idTipoErro() == $valorInicial) ? " selected" : "";?>><?=$oTipoErro->get_descricaoTipoErro();?></option>
<?php
}
?>
</select>