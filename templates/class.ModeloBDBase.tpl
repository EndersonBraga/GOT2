<?php
class %%NOME_CLASSE%%BDBase {
    public $oConexao;
    public $msg;

    function __construct(Conexao $oConexao){
        try{
            $this->oConexao = $oConexao;
        } 
        catch (PDOException $e){
            $this->msg = $e->getMessage();
        }
    }
	
    function inserir(%%OBJETO_CLASSE%%){
        $reg = %%NOME_CLASSE%%MAP::objToRs(%%OBJETO_CLASSE%%);
        $aCampo = array_keys($reg);
        $sql = "
                insert into %%TABELA%%(
                    ".implode(',', $aCampo)."
                ) 
                values(
                    :".implode(", :", $aCampo).")";

            foreach($reg as $cv=>$vl)
                $regTemp[":$cv"] = ($vl=='') ? NULL : $vl;

            try{
                $this->oConexao->executePrepare($sql, $regTemp);
                if($this->oConexao->msg != ""){
                    $this->msg = $this->oConexao->msg;
                    return false;
                }
                return $this->oConexao->lastID();
            }
            catch(PDOException $e){
                $this->msg = $e->getMessage();
                return false;
            }
	}
	
	function alterar(%%OBJETO_CLASSE%%){
            $reg = %%NOME_CLASSE%%MAP::objToRs(%%OBJETO_CLASSE%%);
            $sql = "
                    update 
                        %%TABELA%% 
                    set
                        ";
            foreach($reg as $cv=>$vl){
                %%CAMPOS_CHAVE_ALTERAR%%
                $a[] = "$cv = :$cv";
            }
            $sql .= implode(",\n", $a);
            $sql .= "
                    where
                        %%CHAVES_WHERE%%";

            foreach($reg as $cv=>$vl){
                %%CAMPOS_CHAVE_ALTERAR%%
                $regTemp[":$cv"] = ($vl=='') ? NULL : $vl;
            }
            try{
                $this->oConexao->executePrepare($sql, $regTemp);
                if($this->oConexao->msg != ""){
                    $this->msg = $this->oConexao->msg;
                    return false;
                }
                return true;
            }
            catch(PDOException $e){
                $this->msg = $e->getMessage();
                return false;
            }
	}
	
	function excluir(%%LISTA_CHAVES%%){
            $sql = "
                    delete from
                        %%TABELA%% 
                    where
                        %%CHAVES_WHERE_DEL%%";

            try{
                $this->oConexao->execute($sql);
                if($this->oConexao->msg != ""){
                    $this->msg = $this->oConexao->msg;
                    return false;
                }
                return true;
            }
            catch(PDOException $e){
                $this->msg = $e->getMessage();
                return false;
            }
	}
	
	function selecionar(%%LISTA_CHAVES%%){
            $sql = "
                    select 
                        %%COLUNAS%% 
                    from
                        %%TABELA_JOIN%% 
                    where
                        %%CHAVES_WHERE_SEL%%";
            try{
                $this->oConexao->execute($sql);
                if($this->oConexao->numRows() != 0){
                    $aReg = $this->oConexao->fetchReg();
                    return %%NOME_CLASSE%%MAP::rsToObj($aReg);
                } else {
                    $this->msg = "Nenhum registro encontrado!";
                    return false;
                }
            }
            catch(PDOException $e){
                $this->msg = $e->getMessage();
                return false;
            }
	}
	
    function carregarColecao($aFiltro = NULL, $aOrdenacao = NULL){
        $sql = "
                select
                    %%COLUNAS%% 
                from
                    %%TABELA_JOIN%%";
        
        if(count($aFiltro)>0){
            $sql .= " where ";
            $sql .= implode(" and ", $aFiltro);
        }
        
        if(count($aOrdenacao)>0){
            $sql .= " order by ";
            $sql .= implode(",", $aOrdenacao);
        }
        try{
            $this->oConexao->execute($sql);
            $aObj = array();
            if($this->oConexao->numRows() != 0){
                while ($aReg = $this->oConexao->fetchReg()){
                    $aObj[] = %%NOME_CLASSE%%MAP::rsToObj($aReg);
                }
                return $aObj;
            } else {
                return false;
            }
        }
        catch(PDOException $e){
            $this->msg = $e->getMessage();
            return false;
        }
    }

    function totalColecao(){
        $sql = "select count(*) from %%TABELA%%";
        try{
            $this->oConexao->execute($sql);
            $aReg = $this->oConexao->fetchReg();
            return (int) $aReg[0];
        }
        catch(PDOException $e){
            $this->msg = $e->getMessage();
            return false;
        }
    }
	
    function consultar($valor){
    	$valor = Util::formataConsultaLike($valor); 

        $sql = "
                select
                    %%COLUNAS%% 
                from
                    %%TABELA_JOIN%%
                where
                    %%CHAVES_WHERE_CONS%%";
        //print "<pre>$sql</pre>";
        try{
            $this->oConexao->execute($sql);
            $aObj = array();
            if($this->oConexao->numRows() != 0){
                while ($aReg = $this->oConexao->fetchReg()){
                    $aObj[] = %%NOME_CLASSE%%MAP::rsToObj($aReg);
                }
                return $aObj;
            } else {
                return false;
            }
        }
    	catch(PDOException $e){
            $this->msg = $e->getMessage();
            return false;
        }
    }
}