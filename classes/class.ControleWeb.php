<?php
/**
 * @package classes
 * @filesource
 */
require_once(dirname(__FILE__)."/class.IConexao.php");
require_once(dirname(__FILE__)."/class.Conexao.MySql.php");
require_once(dirname(__FILE__)."/class.Conexao.SqlServer.php");
//require_once(dirname(__FILE__)."/class.Conexao.PDO.php");
require_once(dirname(__FILE__)."/class.DiretorioXML.php");
require_once(dirname(__FILE__)."/class.Geracao.php");
require_once(dirname(__FILE__)."/class.Util.php");
/**
 * Classe de interface funcional da ferramenta no formato web
 * @author Luiz Leão <luizleao@gmail.com>
 */
class ControleWeb{
    public $msg;
    
    function __construct() {
        
    }

    public function getMsg() {
        return $this->msg;
    }

    public function setMsg($msg) {
        $this->msg = $msg;
    }
    
    function conexao($sgbd, $host, $usuario, $senha, $bd=NULL){
        switch($sgbd){
            case "mysql": 
               $oConexao = new ConexaoMySql('Vazia');
            break;
            case "sqlserver": 
                $oConexao = new ConexaoSqlServer('Vazia');
            break;
        }
        
        $oConexao->set_conexao($host, $usuario, $senha, $bd);
        
        if($oConexao->get_conexao()){
            return $oConexao;
        }
        else{
            $this->msg = "Ocorreu o seguinte erro: ".$oConexao->msg;
            return false;
        }
    }
    
    function gerarXML($sgbd, $host, $login, $senha, $db){
        //die("$sgbd, localhost, $login, $senha");
        $oConexao = $this->conexao($sgbd, $host, $login, $senha, $db);

        if($oConexao){
            $oXML = simplexml_load_string("<?xml version=\"1.0\" encoding=\"UTF-8\"?> <DATABASE NOME=\"$db\" SGBD=\"$sgbd\"></DATABASE>");
            $aTabela = $oConexao->carregarColecaoTabelas();
            
            foreach($aTabela as $sTabela){
                $oTabela = $oXML->addChild("TABELA");
                $oTabela->addAttribute("NOME", $sTabela[0]);
                
                switch($sgbd){
                    case "mysql":     $oTabela->addAttribute("SCHEMA", ""); break;
                    case "sqlserver": $oTabela->addAttribute("SCHEMA", $sTabela[1]); break;
                }
                
                $aColuna = $oConexao->carregarColecaoColunasTabela($sTabela[0]);
                //print "<pre>"; print_r($aColuna); print "</pre>"; exit;
                
                $qtd_pk_sem_incremento = 0;
                $qtd_pk_com_incremento = 0;

                foreach($aColuna as $sColuna){
                    if($sColuna[3] == 'PRI'){
                        if($sColuna[5] == 'auto_increment'){
                            $qtd_pk_com_incremento++;
                        } else {
                            $qtd_pk_sem_incremento++;
                        }
                    }
                                        
                    $oCampo = $oTabela->addChild("CAMPO");
                    $oCampo->addChild("NOME", $sColuna[0]);
                    $oCampo->addChild("TIPO", $sColuna[1]);
                    $oCampo->addChild("CHAVE", (($sColuna[3] == 'PRI') ? 1 : 0));

                    $oFK = $oConexao->dadosForeignKeyColuna($db, $sTabela[0], $sColuna[0]);
                    
                    if($oFK[0] != ''){
                        $oFkTabela = $oCampo->addChild("FKTABELA", $oFK[0]);
                        $oFkCampo  = $oCampo->addChild("FKCAMPO",  $oFK[1]);
                    } else {
                        $oFkTabela = $oCampo->addChild("FKTABELA", "");
                        $oFkCampo  = $oCampo->addChild("FKCAMPO",  "");
                    }
                }

                //print "Tabela: {$reg[0]}\n qtd_pk_com_incremento: $qtd_pk_com_incremento \n qtd_pk_sem_incremento: $qtd_pk_sem_incremento\n\n";
                // ========== Verificar tipo da tabela ============
                if($qtd_pk_com_incremento == 1){
                    $oTabela->addAttribute("TIPO_TABELA", 'NORMAL');
                } else {
                    if($qtd_pk_sem_incremento == 2){
                        $oTabela->addAttribute("TIPO_TABELA", 'N:M');
                    } elseif($qtd_pk_sem_incremento == 1) {
                        $oTabela->addAttribute("TIPO_TABELA", '1:1');
                    } else {
                        $oTabela->addAttribute("TIPO_TABELA", 'NORMAL');
                    }
                }
            }

            $fp = fopen(dirname(dirname(__FILE__))."/xml/$db.xml","w");
            fputs($fp, $oXML->asXML());
            fclose($fp);
            $this->msg = ""; //Arquivo XML gerado com sucesso
            return true;
        }
        else{
            $this->msg = "Falha na geração do XML";
            return false;
        }
    }
    
    public function gerarArtefatos($xml, $moduloSeguranca){
        $oGeracao = new Geracao(dirname(dirname(__FILE__))."/xml/$xml.xml", $xml);
        $msg = "Log de Geração de Artefatos - Projeto <strong>$xml</strong>: <br /><hr /><pre>";
        $msg .= str_pad("Geracao geraClassesBasicas ",50,".").           ((!$oGeracao->geraClassesBasicas())            ? "Falha" : "Ok")."\n";
        $msg .= str_pad("Pacote adicional ",50,".").((!Util::copydir(dirname(dirname(__FILE__))."/dir", dirname(dirname(__FILE__))."/geradas/$xml")) ? "Falha" : "Ok")."\n";
        $msg .= str_pad("Geracao geraClassesBDBase ",50,".").            ((!$oGeracao->geraClassesBDBase())             ? "Falha" : "Ok")."\n";
        $msg .= str_pad("Geracao geraClasseControle ",50,".").           ((!$oGeracao->geraClasseControle())            ? "Falha" : "Ok")."\n";
        $msg .= str_pad("Geracao geraClassesBD ",50,".").                ((!$oGeracao->geraClassesBD())                 ? "Falha" : "Ok")."\n";
        $msg .= str_pad("Geracao geraClasseValidadorFormulario ",50,".").((!$oGeracao->geraClasseValidadorFormulario()) ? "Falha" : "Ok")."\n";
        $msg .= str_pad("Geracao geraClasseDadosFormulario ",50,".").    ((!$oGeracao->geraClasseDadosFormulario())     ? "Falha" : "Ok")."\n";
        $msg .= str_pad("Geracao geraClassesMapeamento ",50,".").        ((!$oGeracao->geraClassesMapeamento())         ? "Falha" : "Ok")."\n";
        $msg .= str_pad("Geracao geraInterface ",50,".").                ((!$oGeracao->geraInterface())                 ? "Falha" : "Ok")."\n";
        
        if(!$moduloSeguranca){
            $msg .= str_pad("Geracao Menu Estático ",51,".").            ((!$oGeracao->geraMenuEstatico())              ? "Falha" : "Ok")."</pre>";
        }
        return $msg;
    }
    
    public function excluirXML($xml){
    	try{
            unlink(dirname(dirname(__FILE__))."/xml/$xml.xml");
            $this->msg = "";
            return true;
    	} 
    	
    	catch(Exception $e){
            $this->msg = $e->getMessage();
            return false;
    	}
    } 
}
