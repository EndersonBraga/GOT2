<?php
/**
 * Gerencia a lista de arquivos XML gerados pela aplicação
 * 
 */
class DiretorioXML {
    /**
     * LIsta de arquivos XML
     * 
     * @var string[] 
     */
    public $arquivos;

    function __construct(){
        $arquivos = array();
        $dir      = dirname(__FILE__)."/../xml/";
        $dh       = opendir($dir);
        while(($file = readdir($dh)) !== false){
            if(filetype($dir.$file) == "file" && substr($file,-4) == ".xml"){
                $arquivos[] = substr($file,0,strrpos($file,".xml"));
            }
        }
        $this->arquivos = $arquivos;
    }
    
    /**
     * Método GET
     * 
     * @return string[]
     */
    function get_arquivos(){
        return $this->arquivos;
    }

    /**
     * Método SET
     * 
     * @param string[] $arquivos
     */
    function set_arquivos($arquivos){
        $this->arquivos = $arquivos;
    }
}