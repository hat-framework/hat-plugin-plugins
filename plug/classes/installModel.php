<?php

class plugins_installModel extends \classes\Model\Model{

    private $observers = array(
        'notificacao/notifica', 
        'files/pasta', 
        'site/menu', 
        'usuario/gadget',
        'site/conffile'
    );
    public function  __construct() {
        $this->LoadResource("files/dir"         , "dir");
        $this->LoadResource("database"          , "db");
        $this->LoadResource("files/file"        , "file");
        $this->LoadResource("database/creator"  , 'iobj');
        $this->LoadModel("plugins/plug/features", 'fea')->SaveFeatures();
        $this->LoadModel("plugins/plug"         , "plug");
        $this->LoadModel("usuario/login"        , 'uobj');
        parent::__construct();
    }
    
    public function populate($module){
        $this->LoadResource("database/populator", "pop");
        $this->pop->populate($module);
    }
    
    public function disable($modulo){
        $var = $this->getPluginVar($modulo, "Iniciando a ativação do plugin $modulo");
        if($this->checkStatusEquals($var, $modulo, 'desativado')){return true;}
        $this->editPlugin($modulo, 'desativado');
        return $this->setSuccessMessage("O plugin $modulo foi desativado");
    }

    public function enable($module){
        $var = $this->getPluginVar($module, "Iniciando a ativação do plugin $module");
        if($var['status'] == 'instalado'){return $this->setSuccessMessage("O plugin $module já está ativo");}
        if($var['status'] == 'desinstalado'){return $this->setErrorMessage("O plugin $module não está instalado");}
        if(false === $this->editPlugin($module, 'instalado')){return false;}
        return $this->setSuccessMessage("O plugin $module foi reativado");
    }
    
    public function unstall($module){
        $var = $this->getPluginVar($module, "Iniciando a desinstalação do plugin $module");
        if($this->checkStatusEquals($var, $module, 'desinstalado')){return true;}
        
        if($this->Start($module, "unstall") === false){
            $plugin = $this->getPlugin($module);
            if(empty($plugin)) {return true;}
            if(false === $this->tryReinstall($module, $plugin)){return false;}
            return $this->tryDrop($plugin);
        }
        if(false === $this->doEditPlugin($module, 'desinstalado')){return false;}
        return $this->setStatusMessage($module);
    }
    
            private function tryReinstall($module, $plugin){
                $arq = array_keys(classes\Classes\Registered::getAllPluginsLocation());
                if(in_array($plugin['plugnome'], $arq)){
                    $this->install($module);
                    $this->setSuccessMessage("");
                    return false;
                }
                return true;
            }
            
            private function tryDrop($plugin){
                if(!$this->plug->apagar($plugin['plugnome'], 'plugnome')){
                    $this->setErrorMessage("Não foi possível desinstalar o plugin {$plugin['plugnome']}. 
                    Seu diretório foi removido, mas ele ainda consta no banco de dados e não pôde ser excluído do sistema. 
                    Detalhes do erro:" . $this->plugin->getErrorMessage());
                    return false;
                }
                return true;
            }
            
            private function setStatusMessage($module){
                if(!$this->notifyAllObservers($module)) {
                    $this->setAlertMessage("Falha ao notificar observers no plugin $module");
                    return true;
                }
                return $this->setSuccessMessage("O plugin $module foi desinstalado corretamente!");
            }
    
    public function update($module){
        
        //inicializa as variaveis
        $var = $this->getPluginVar($module, "<b>Iniciando atualização do plugin $module</b><br/>");
        if(false === $var){return false;}
  
        //verifica se o plugin está instalado
        if(false === $this->checkStatusEquals($var, $module, 'instalado')){return $this->setErrorMessage("O plugin $module não está instalado!");}
        
        //inicializa o update do banco de dados
        if($this->Start($module, 'update') === false){
            \classes\Utils\Log::save(LOG_INSTALACAO, "Erro ao executar o update!");
            return false;
        }
        
        //mensagem de sucesso!
        $msg = "O plugin $module foi atualizado corretamente!";
        \classes\Utils\Log::save(LOG_INSTALACAO, $msg);
        return $this->setSuccessMessage($msg);
    }
           
    public function install($module){
        
        //inicializa as variaveis
        $var = $this->getPluginVar($module, "Iniciando a instalação do plugin $module");
        if(false === $var){return false;}
        
        //se o plugin já está instalado, então retorna
        if($this->checkStatusEquals($var, $module, 'instalado')){return true;}
        
        //inicializa a instalação no banco de dados
        $this->Start($module, "install");
        
        //marca o plugin como instalado
        if(false == $this->doEditPlugin($module, 'instalado')){return false;}
        
        //executa o arquivo populate.sql
        if(false == $this->doSelfPopulate($module)){return false;}
        
        //plugin instalado corretamente!!
        return $this->setSuccessMessage("Plugin $module instalado corretamente!");
    }
    
            private function doSelfPopulate($module){
                if(!$this->selfpopulate($module)){
                    $this->setSuccessMessage("");
                    $this->setAlertMessage("Não foi possível popular o plugin automaticamente!");
                    return false;
                }
                return true;
            }
    
                    private function selfpopulate($module){
                        $file = classes\Classes\Registered::getPluginLocation($module, true)."/Config/populate.sql";
                        if(!file_exists($file)) {return true;}

                        $counteudo = file_get_contents($file);
                        if(!$this->db->ExecuteInsertionQuery($counteudo)){
                            $this->setErrorMessage($this->db->getErrorMessage());
                            return false;
                        }
                        return true;
                    }

    public function init($module){

        $plugin = $this->getModuleName($module);
        
        //instala os modulos basicos
        if(!$this->install($plugin)) {return false;}
        
        $dados['status'] = 'instalado';
        $dados['plugnome'] = $plugin;
        if(!$this->plug->inserir($dados)){
            return $this->setErrorMessage($this->plug->getErrorMessage());
        }
        return true;
    }

    public function updatePluginModels($plugin){
        $subplugins = $this->iobj->getPlugin($plugin);
        return $this->registerModels($plugin, $subplugins);
    }

    public function getPluginStatus(){
        return $this->plug->selecionar();
    }

    private function getPlugin($plugin){
        $var = array();
        if($plugin != "admin"){
            $var = $this->plug->selecionar(array(), "`plugnome` = '$plugin'");
            if(!empty ($var)) {$var = array_shift($var);}
        }
        return $var;
    }

    public function listarPlugins(){
        return $this->LoadClassFromPlugin('plugins/plug/setupAux/listarPlugins', 'slp')->getList();
    }
    
    private function Start($plugin, $type){
        $bool = $this->LoadClassFromPlugin('plugins/plug/setupAux/startHelper', 'sthelper')->start($plugin, $type);
        $this->setMessages($this->sthelper->getMessages());
        return $bool;
    }

    public function inserirPlugin($plugnome){
        $dados['plugnome'] = $plugnome;
        if(!$this->plug->inserir($dados)){
            $this->setErrorMessage($this->plug->getErrorMessage());
            return false;
        }
        return true;
    }
    
    private function editPlugin($module, $status){
        $post = array('status' => $status);
        $bool = $this->plug->editar($module, $post, "plugnome");
        $this->setErrorMessage($this->plug->getErrorMessage());
        return $bool;
    }
    
    private function checkStatusEquals($var, $module, $status){
        if(!empty ($var) && $var['status'] == $status){
            $this->setSuccessMessage("Plugin $module já está $status");
            return true;
        }
        return false;
    }
    
    private function doEditPlugin($module, $status){
        if(!$this->editPlugin($module, $status)){
            $this->setSuccessMessage("");
            $this->setAlertMessage("Não foi possível atualizar o status do plugin no banco de dados, porém ele foi instalado.");
            return false;
        }
        return true;
    }
    
    private function getPluginVar(&$module, $msg){
        $this->setLog($module);
        \classes\Utils\Log::save(LOG_INSTALACAO, $msg, 'noDate');
        $module = $this->getModuleName($module);
        $var = $this->getPlugin($module);
        if(empty ($var)){
            $erro = "O plugin $module não existe ou não pode ser modificado!";
            \classes\Utils\Log::save(LOG_INSTALACAO, $erro);
            return $this->setErrorMessage($erro);
        }
        return $var;
    }
    
            private function setLog($module){
                if(defined('CURRENT_ACTION') && CURRENT_ACTION === "updateall"){
                    if(!defined("LOG_INSTALACAO")){define("LOG_INSTALACAO", "plugins/updateall");}
                }
                if(!defined("LOG_INSTALACAO")){define("LOG_INSTALACAO", "plugins/instalacao/$module");}
            }
            
            
    private function getModuleName($module){
        $e = explode("/", $module);
        return array_shift($e);
    }

    private function notifyAllObservers($module){
        $alert = array();
        $bdname = (defined("cbd_name"))?cbd_name:bd_name;
        foreach($this->observers as $ob){
            try {
                $this->doNotify($ob, $bdname, $module, $alert);
            }catch (Exception $e){/*do nothing*/}
        }
        
        return $this->processAlert($alert);
    }
    
            private function doNotify($ob, $bdname, $module, &$alert){
                $this->LoadModel($ob, 'observer');
                $table = $this->observer->getTable();
                $count = $this->db->ExecuteQuery("
                    SELECT COUNT(*) as total
                    FROM information_schema.tables 
                    WHERE table_schema = '$bdname' 
                    AND table_name = '$table';"
                );
                if($count[0]['total'] <= 0){return;}
                if(false == $this->observer->unstall($module)){
                    $this->appendAlert($ob, $alert);
                }
            }
            
                    private function appendAlert($ob, &$alert){
                        $error   = $this->observer->getErrorMessage();
                        if(trim($error) == ""){
                            $error = "Falha no observer $ob. Porém nenhuma mensagem de erro foi especificada. Contacte o webmaster para mais detalhes!";
                        }
                        $alert[] = $error;
                    }
    
            private function processAlert($alert){
                if(!empty($alert)){
                    $msg = implode('<br/>', $alert);
                    return $this->setErrorMessage($msg);
                }
                return true;
            }

}