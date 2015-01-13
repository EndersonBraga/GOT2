<?php
/** 
* Classe de conexão nativa com o SGBD PostgreSQL
* 
* @author Luiz Leão
* @filesource
*/
class ConexaoPostgre {
    public $conexao;
    public $consulta;  
    public $msg;
	
    function __construct($servidor = 'Producao'){
        switch ($servidor){
            case 'Local':
                    $this->conexaoBD("192.168.200.25","apsuser","seduc@control","seduc_desenv");
            break;
            case 'Producao':				
                    $this->conexaoBD("192.168.200.42","apsuser","seduc@control","seduc_oficial");
            break;
            case 'Vazia':
            break;
            default:
                    die("Servidor $servidor inexistente");
            break;
        }
    }
	
	function conexaoBD($host,$user,$senha,$bd){
            $conexao = @pg_connect("host=$host dbname=$bd user=$user password=$senha") or die (pg_last_error());
            $this->set_conexao($conexao);
	}
		
	function execute($sql){
		$consulta = @pg_query($this->get_conexao(),$sql);
		if($consulta){
			$this->set_consulta($consulta);
			return true;
		} else{
			$this->set_msg(pg_last_error());
			return false;
		}
	}
	
	function numRows($consulta = NULL){
		if(!$consulta) $consulta = $this->get_consulta();
		return (int) @pg_num_rows($consulta);
	}
	
	function fetchReg($consulta = NULL){
		if(!$consulta) $consulta = $this->get_consulta();
		return pg_fetch_array($consulta);
	}
	
	function fetchRow($consulta = NULL){
		if(!$consulta) 
			$consulta = $this->get_consulta();
		return pg_fetch_row($consulta);
	}
	
	function lastID(){
		return pg_last_oid($this->get_consulta());
	}
	
	function close(){
		pg_close($this->get_conexao());
	}
	
	// NOVO: MYSQL 4 COM SUPORTE A TRANSACOES
	
	function beginTrans(){
		$this->execute("BEGIN");	
	}

	function commitTrans(){
		$this->execute("COMMIT");		
	}

	function rollBackTrans(){
		$this->execute("ROLLBACK");
	}
	
	// METADADOS
	
	function databases(){
		$this->execute("SHOW DATABASES");
		$aDatabases = array();
		while ($aReg = $this->fetchRow()){
			$aDatabases[] = $aReg[0];
		}
		return $aDatabases;
	}
	
	// ========= GET E SET =========
	
	function get_conexao(){
		return $this->conexao;
	}
	
	function set_conexao($conexao){
		$this->conexao = $conexao;
	}
	
	function get_msg(){
		return $this->msg;
	}
	
	function set_msg($msg){
		$this->msg = $msg;
	}
	
	function get_consulta(){
		return $this->consulta;
	}
	
	function set_consulta($consulta){
		$this->consulta = $consulta;
	}

}
?>
