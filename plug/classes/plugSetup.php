<?php

use classes\Classes\Object;
class plugSetup extends classes\Classes\Object{
    
    public function __construct() {
        if(!defined("LOG_INSTALACAO")) {define ("LOG_INSTALACAO", "plugins/instalacao");}
        \classes\Utils\Log::delete(LOG_INSTALACAO);
        $this->LoadModel('plugins/plug', 'plug');
        $this->LoadResource("database", 'db');
        $this->LoadResource("database/creator", 'db_creator');
        $this->install_classes = $this->FindInstallClasses();
    }
    
    public function setup($module){
        \classes\Utils\Log::save(LOG_INSTALACAO, "<h2>Iniciando instalação do Módulo $module</h2>");
        $this->setPluginName($module);
        if($this->isInstaled()) {return true;}
        $methods = array('installPluginInDatabase', 'registerModels', 'registerOthers', 'setPluginInstaled', 'executePopulateSql');
        $bool    = true;
        foreach($methods as $method){
            //echo "Executando $method <br/>";
            \classes\Utils\Log::save(LOG_INSTALACAO, "Executando $method");
            if(false === $this->$method()){ 
                $bool = false;
                \classes\Utils\Log::save(LOG_INSTALACAO, "Falha ao executar o $method");
            }
        }
        
        if(true === $bool){
            \classes\Utils\Log::save(LOG_INSTALACAO, "Instalação concluída com sucesso!");
            return $this->setSuccessMessage("O plugin $this->plugin foi instalado Corretamente!");
        }
        return false;
    }
    
    public function setPluginName($module){
        $this->plugin = @array_shift(explode("/", $module));
    }
    
    private function isInstaled(){
        //echo __METHOD__."<br/>";
        $var = $this->plug->getItem($this->plugin, "plugnome");
        //se o plugin já está instalado, então retorna
        if(!empty ($var) && $var['status'] == 'instalado'){
            $this->setSuccessMessage("Plugin $this->plugin já está instalado");
            return true;
        }
        return false;
    }
    
     private function installPluginInDatabase(){
        if(!$this->db_creator->install($this->plugin)){
            $this->setMessages($this->db_creator->getMessages());
            return false;
        }
        return true;
    }
    
    public function registerModels(){
        //echo __METHOD__."<br/>";
        //registra o plugin atual e os subplugins no sistema
        $subplugins = $this->db_creator->getPlugin($this->plugin);
        $this->LoadClassFromPlugin('plugins/plug/inclasses/registerModels', 'rmds');
        if(!$this->rmds->register($this->plugin, $subplugins)){
            $this->setMessages($this->rmds->getMessages());
            return false;
        }
        $this->cod_plugin = $this->rmds->getCodPlugin();
        return true;
    }
    
     private function registerOthers(){
        // echo __METHOD__."<br/>";
        \classes\Utils\Log::save(LOG_INSTALACAO, "<hr/>");
        foreach($this->install_classes as $class){
            $this->LoadClassFromPlugin("plugins/plug/inclasses/$class", 'r');
            
            
            \classes\Utils\Log::save(LOG_INSTALACAO, "Executando plugins/plug/inclasses/$class");
            if(!($this->r instanceof \install_subsystem)) {continue;}
            if($this->r->register($this->plugin, $this->cod_plugin) === false){
                $this->setMessages($this->r->getMessages());
                $erro = "";
                
                $last = false;
                $err = debugarray($this->r->getMessages(), '', $last, false);
                classes\Utils\Log::save(LOG_INSTALACAO, $err);
                if($this->LoadModel('usuario/login', 'uobj')->UserIsWebmaster()){$erro.= "($err)";}
                $this->setErrorMessage("$class: Erro ao registrar dados de instalação! $erro");
                return false;
            }
        }
        return true;
    }
    
    //marca o plugin como instalado
    private function setPluginInstaled(){
        //echo __METHOD__."<br/>";
        $post = array('status' => 'instalado');
        if(!$this->plug->editar($this->plugin, $post, "plugnome")){
            $this->setAlertMessage("Não foi possível atualizar o status do plugin no banco de dados,
                porém ele foi instalado.");
            $this->setErrorMessage($this->plug->getErrorMessage());
            return false;
        }
        return true;
    }
    
    private function executePopulateSql(){
        //echo __METHOD__."<br/>";
        $file = classes\Classes\Registered::getPluginLocation($this->plugin, true) . "/Config/populate.sql";
        if(!file_exists($file)) return true;

        $counteudo = file_get_contents($file);
        if(!$this->db->ExecuteInsertionQuery($counteudo)){
            $this->setErrorMessage($this->db->getErrorMessage());
            return false;
        }
        return true;
    }
    
    private function FindInstallClasses(){
        //echo __METHOD__."<br/>";
        $dir = realpath(dirname(__FILE__));
        $this->LoadResource('files/dir', 'dobj');
        $install_classes = $this->dobj->getArquivos("$dir/inclasses");
        foreach($install_classes as &$iclass){
            $iclass = str_replace('.php', '', $iclass);
        }
        return $install_classes;
    }
}