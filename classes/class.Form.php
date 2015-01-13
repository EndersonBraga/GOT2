<?php

/**
 * Responsável pela construção dos formulários e relatórios da aplicação
 * 
 * @author Luiz Leão <luizleao@gmail.com>
 */
class Form {
    /**
     * Gera componentes input
     * 
     * @param string $obj Objeto a ser editado
     * @param string $campo Nome do atributo
     * @param string $label Rótulo do atributo
     * @param string $tipoTela Tipo de formulário: CAD - Cadastro, EDIT - Edição
     * @param string $tipoDado Tipo de dado do atributo
     * @return string
     */
    static function geraInput($obj, $campo, $label, $tipoTela, $tipoDado) {
        $tipoInput = "text";
        switch ($tipoTela) {
            case 'CAD':
                $value = "";
                $valueRadioM = $valueRadioF = "";
            break;
            case 'EDIT':
                $value = "<?=$obj"."->$campo?>";
                $valueRadioM = "<?=($obj"."->$campo == 'M') ? \"Checked\" : \"\" ?>";
                $valueRadioF = "<?=($obj"."->$campo == 'F') ? \"Checked\" : \"\" ?>";
            break;
        }
        
        // URL
        if(preg_match("#(?:url|link)#is", $campo)){
            $tipoInput = "url";
        }
        
        //CPF
        if(preg_match("#cpf#is", $campo)){
            $classCss = " cpf";
        }
        
        //CPF
        if(preg_match("#(?:cnpj|cgc)#is", $campo)){
            $classCss = " cnpj";
        }
        
        //Telefone
        if(preg_match("#(?:tel_|fone|telefone)#is", $campo)){
            $classCss = " telefone";
        }
        
        //CEP
        if(preg_match("#(?:cep)#is", $campo)){
            $classCss = " cep";
        }
        
        // Email
        if(preg_match("#e?-?mail#is", $campo)){
            $retorno = "<label class=\"control-label\" for=\"$campo\">$campo</label>
                        <div class=\"controls\">
                            <div class=\"input-prepend\">
                                <span class=\"add-on\"><i class=\"icon-envelope\"></i></span>
                                <input type=\"email\" class=\"input-xlarge\" name=\"$campo\" id=\"$campo\" value=\"$value\" />
                            </div>
                        </div>";
        
        // Analisado o Tipo de Dados
        // Moeda
        }elseif (preg_match("#(?:valor|pre[cç]o|moeda|va?l_|desconto|despesa)#is", $campo)){
            if(preg_match("#(?:money|float|double|number)#is", $tipoDado)){
                $retorno = "<label class=\"control-label\" for=\"$campo\">$campo</label>
                            <div class=\"controls\">
                                <div class=\"input-prepend\">
                                    <span class=\"add-on\">R$</span>
                                    <input type=\"text\" class=\"input-xlarge money\" name=\"$campo\" id=\"$campo\" value=\"$value\" />
                                </div>
                            </div>";
            }
        } elseif(preg_match("#(?:genero|genre|sexo)#is", $campo)){
            $retorno = "<label>$campo</label>
                        <label class=\"radio\">
                            <input type=\"radio\" name=\"$campo\" id=\"".$campo."M\" value=\"M\" $valueRadioM /> Masculino
                        </label>
                        <label class=\"radio\">
                            <input type=\"radio\" name=\"$campo\" id=\"".$campo."F\" value=\"F\" $valueRadioF /> Feminino
                        </label>";
        
        } else {
            $retorno = "<label for=\"$campo\">$label</label>
                        <input type=\"$tipoInput\" class=\"input-xlarge$classCss\" id=\"$campo\" name=\"$campo\" value=\"$value\" />";
        }
        return $retorno;
    }
    
    /**
     * Gera campo de senha
     * 
     * @param string $obj Objeto selecionado
     * @param string $campo atributo a ser analisado
     * @param string $label Rótulo do atributo
     * @param string $tipoTela Tipo de formulário: CAD - Cadastro, EDIT - Edição
     * @return string
     */
    static function geraPassword($obj, $campo, $label, $tipoTela) {
        switch ($tipoTela) {
            case 'CAD':
                $retorno = "<label for=\"$campo\">$label</label>
                            <input type=\"password\" class=\"input-xlarge\" id=\"$campo\" name=\"$campo\" />";

                break;
            case 'EDIT':
                $retorno = "<label for=\"$campo\">$label</label>
                            <input type=\"password\" class=\"input-xlarge\" id=\"$campo\" name=\"$campo\" value=\"<?=$obj"."->$campo?>\" />";
                break;
        }
        return $retorno;
    }
    
    /**
     * Gera campo textarea
     * 
     * @param string $obj Objeto selecionado
     * @param string $campo atributo a ser analisado
     * @param string $label Rótulo do atributo
     * @param string $tipoTela Tipo de formulário: CAD - Cadastro, EDIT - Edição
     * @return string
     */
    static function geraTextArea($obj, $campo, $label, $tipoTela) {
        switch ($tipoTela) {
            case 'CAD':
                $retorno = "<label for=\"$campo\">$label</label>
                            <textarea name=\"$campo\" class=\"input-xlarge\" id=\"$campo\" cols=\"80\" rows=\"10\"></textarea>";
                break;
            case 'EDIT':
                $retorno = "<label for=\"$campo\">$label</label>
                            <textarea name=\"$campo\" class=\"input-xlarge\" id=\"$campo\" cols=\"80\" rows=\"10\"><?=$obj" . "->$campo?></textarea>";
                break;
        }
        return $retorno;
    }
    /**
     * Gera combo dinâmico, alimentado por lista de valores provenientes da Chave Estrangeira (FK)
     * 
     * @param string $obj Nome do Objeto
     * @param string $campo atributo a ser analisado
     * @param string $label Rótulo do atributo
     * @param string $nomeFKClasse Nome da Classe da Chae Estrangeira
     * @param string $campoFK Campo chave estrangeira
     * @param string $descricaoFK Atributo que representa a classe em questão
     * @param string $tipoTela Tipo de Formulário: CAD - Cadastro, EDIT - Edição
     * @return string
     */
    static function geraSelect($obj, $campo, $label, $nomeFKClasse, $campoFK, $descricaoFK, $tipoTela) {
        switch ($tipoTela) {
            case 'CAD':
                $retorno = "
                            <label for=\"$campo\">$label</label>
                            <select name=\"$campo\" id=\"$campo\" class=\"input-xlarge\">
                                <option value=\"\">Selecione</option>
<?php
foreach(\$a$label as \$o$label){
?>
                                <option value=\"<?=\$o$label" . "->$campo?>\"><?=\$o$label" . "->$descricaoFK?></option>
<?php
}
?>
                            </select>";
                break;
            case 'EDIT':
                $retorno = "
                            <label for=\"$campo\">$label</label>
                            <select name=\"$campo\" id=\"$campo\" class=\"input-xlarge\">
                                <option value=\"\">Selecione</option>
<?php
foreach(\$a$label as \$o$label){
?>
                                <option value=\"<?=\$o$label" . "->$campo?>\"<?=(\$o$label" . "->$campo == $obj" . "->o$label" . "->$campoFK) ? \" selected\" : \"\"?>><?=\$o$label" . "->$descricaoFK?></option>
<?php
}
?>
                            </select>";
                break;
        }
        return $retorno;
    }
    
    /**
     * Gera combobox, alimentado por valores do Enum (MySQL Apenas)
     * 
     * @param string $obj Nome do Objeto
     * @param string $campo atributo a ser analisado
     * @param string $enum Lista de valores recuperadas
     * @param string $label Rótulo do atributo
     * @param string $tipoTela Tipo de Formulário: CAD - Cadastro, EDIT - Edição
     * @return string
     */
    static function geraEnum($obj, $campo, $enum, $label, $tipoTela) {
        switch ($tipoTela) {
            case 'CAD':
                if (preg_match_all("#'(.*?)'#i", $enum, $aCampo)) {
                    //print_r($aCampo);
                    $retorno = "
                                <label for=\"$campo\">$label</label>
                                <select name=\"$campo\" id=\"$campo\">";
                    foreach ($aCampo[1] as $sCampo) {
                        $retorno .= "
                                        <option value=\"$sCampo\">$sCampo</option>";
                    }
                    $retorno .= "
                                </select>";
                }
                break;

            case 'EDIT':
                if (preg_match_all("#'(.*?)'#i", $enum, $aCampo)) {
                    //print_r($aCampo);
                    $retorno = "
                                <label for=\"$campo\">$label</label>
                                <select name=\"$campo\" id=\"$campo\">";
                    foreach ($aCampo[1] as $sCampo) {
                        $retorno .= "
                                        <option value=\"$sCampo\"<?=($obj" . "->$campo == \"$sCampo\") ? \" selected\" : \"\"?>>$sCampo</option>";
                    }
                    $retorno .= "
				</select>";

                }
                break;
        }
        return $retorno;
    }
    
    /**
     * Gera campo de calendário (Data)
     * 
     * @param string $obj Nome do Objeto
     * @param string $campo atributo a ser analisado
     * @param string $label Rótulo do atributo
     * @param string $tipoTela Tipo de Formulário: CAD - Cadastro, EDIT - Edição
     * @return string
     */
    static function geraCalendario($obj, $campo, $label, $tipoTela) {
        switch ($tipoTela) {
            case 'ADM':
                $retorno = "Util::formataDataBancoForm($obj" . "->$campo)";
                break;
            case 'CAD':
                $retorno = "
                            <label for=\"$campo\">$label</label>
                            <?php \$oControle" . "->componenteCalendario('$campo')?>";
                break;
            case 'EDIT':
                $retorno = "
                            <label for=\"$campo\">$label</label>
                            <?php \$oControle" . "->componenteCalendario('$campo', Util::formataDataBancoForm($obj" . "->$campo))?>";
                break;
        }
        return $retorno;
    }
    
    /**
     * Gera campo de calendário (Data/hora)
     * 
     * @param string $obj Nome do Objeto
     * @param string $campo atributo a ser analisado
     * @param string $label Rótulo do atributo
     * @param string $tipoTela Tipo de Formulário: CAD - Cadastro, EDIT - Edição
     * @return string
     */
    static function geraCalendarioDataHora($obj, $campo, $label, $tipoTela) {
        switch ($tipoTela) {
            case 'ADM':
                $retorno = "Util::formataDataHoraBancoForm($obj" . "->$campo)";
                break;
            case 'CAD':
                $retorno = "
                            <label for=\"$campo\">$label</label>
                            <?php \$oControle" . "->componenteCalendario('$campo', NULL, NULL, true)?>";
                break;
            case 'EDIT':
                $retorno = "
                            <label for=\"$campo\">$label</label>
                            <?php \$oControle" . "->componenteCalendario('$campo', Util::formataDataHoraBancoForm($obj" . "->$campo), NULL, true)?>";
                break;
        }
        return $retorno;
    }
    
    /**
     * Gera campo checkbox
     * 
     * @param string $obj Nome do Objeto
     * @param string $campo atributo a ser analisado
     * @param string $label Rótulo do atributo
     * @param string $tipoTela Tipo de Formulário: CAD - Cadastro, EDIT - Edição
     * @return string
     */    
    static function geraCheckBox($obj, $campo, $label, $tipoTela) {
        switch ($tipoTela) {
            case 'CAD':
                $retorno = "<label for=\"$campo\">$label</label>
                            <input type=\"checkbox\" name=\"$campo\" id=\"$campo\" value=\"1\" />";
                break;
            case 'EDIT':
                $retorno = "<label for=\"$campo\">$label</label>
                            <input type=\"checkbox\" name=\"$campo\" id=\"$campo\" value=\"1\"<?=($obj" . "->$campo == 1) ? ' checked=\"checked\"' : '' ?> />";
                break;
        }
        return $retorno;
    }
    
    /**
     * Gera campo hidden
     * 
     * @param string $campo nome do campo
     * @return string
     */
    static function geraHidden($campo) {
        $retorno = "\t\t\t\t<input name=\"$campo\" type=\"hidden\" id=\"$campo\" value=\"<?=\$_REQUEST['$campo']?>\" />";
        return $retorno;
    }
    
    /**
     * Gera botão de acesso a tela de edição
     * 
     * @param string $nomeClasse Nome da Classe
     * @param string $idPK Nome do campo PK do objeto a ser editado
     * @param string $PK Nome da chave primária da classe
     * @return string
     */
    static function geraAdmEdit($nomeClasse, $idPK, $PK) {
        $retorno = "<td><a class=\"btn btn-success btn-small\" href=\"edit$nomeClasse.php?$idPK=<?=\$o$nomeClasse->$PK;?>\" title=\"Editar\"><i class=\"icon-white icon-edit\"></i></a></td>";
        return $retorno;
    }

    /**
     * Gera botão de exclusão
     * 
     * @param string $nomeClasse Nome da Classe
     * @param string $idPK Nome do campo PK do objeto a ser editado
     * @param string $PK Nome da chave primária da classe
     * @return string
     */
    static function geraAdmDelete($nomeClasse, $idPK, $PK) {
        $retorno = "<td><a class=\"btn btn-danger btn-small\" href=\"javascript: void(0);\" onclick=\"excluir('$idPK','<?=\$o$nomeClasse->$PK;?>')\" title=\"Excluir\"><i class=\"icon-white icon-trash\"></i></a></td>";
        return $retorno;
    }
}