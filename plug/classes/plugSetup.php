<?php

use classes\Classes\Object;
class plugSetup extends classes\Classes\Object{
    
    private $methods = array(
        'installPluginInDatabase', 
        'registerModels', 
        'registerOthers', 
        'updateOtherSystems', 
        'setPluginInstaled', 
        'executePopulateSql',
        'updateOtherSystems', 
    );
    
    public function __construct() {
        if(!defined("LOG_INSTALACAO")) {define ("LOG_INSTALACAO", "plugins/instalacao");}
        \classes\Utils\Log::delete(LOG_INSTALACAO);
        $this->LoadModel('plugins/plug', 'plug');
        $this->LoadResource("database", 'db');
        $this->LoadResource("database/creator", 'db_creator');
        $this->install_classes = $this->FindInstallClasses();
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
    
    public function setup($module){
        if(false === $this->initSetup($module)){return false;}
        $bool = $this->doSetup();
        return $this->processResult($bool);
    }
    
            private function initSetup($module){
                \classes\Utils\Log::save(LOG_INSTALACAO, "<h2>Iniciando instalação do Módulo $module</h2>");
                $this->setPluginName($module);
                if(false === $this->isInstaled()) {return true;}
                \classes\Utils\Log::save(LOG_INSTALACAO, "<div class='alert alert-success'>Plugin já está instalado</div>");
                return false;
            }
    
                    public function setPluginName($module){
                        $this->plugin = @array_shift(explode("/", $module));
                        return $this;
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
    
            
            private function doSetup() {
                $bool    = true;
                foreach($this->methods as $method){
                    \classes\Utils\Log::save(LOG_INSTALACAO, "Executando $method");
                    if(false === $this->$method()){ 
                        $bool = false;
                        \classes\Utils\Log::save(LOG_INSTALACAO, "<div class='alert alert-danger'>Falha ao executar o $method</div>");
                    }
                }
                return $bool;
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
                    public function setPluginInstaled(){
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

                    public function executePopulateSql(){
                        //echo __METHOD__."<br/>";
                        $file = classes\Classes\Registered::getPluginLocation($this->plugin, true) . "/Config/populate.sql";
                        if(!file_exists($file)){return true;}

                        $counteudo = file_get_contents($file);
                        if(false == $this->db->ExecuteInsertionQuery($counteudo)){
                            return $this->setErrorMessage($this->db->getErrorMessage());
                        }
                        return true;
                    }
                    
                    public function updateOtherSystems(){
                        $this->plug->mountPerfilPermissions();
                        $this->LoadModel('site/sitemap', 'smap')->createMap();
                        $this->LoadClassFromPlugin('config/form/formDetector', 'fd')->importData();
                        return true;
                    }
            
            private function processResult($bool){
                if(false === $bool){return false;}
                \classes\Utils\Log::save(LOG_INSTALACAO, "Instalação concluída com sucesso!");
                return $this->setSuccessMessage("O plugin $this->plugin foi instalado Corretamente!");
            }
}