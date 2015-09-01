<?php

use classes\Classes\EventTube;
class setupAdmin extends \classes\Controller\Controller{
    
    private $steps = array(
        'index'     => 'Iniciando Instalação',
        'file'      => 'Instalação dos diretórios',
        'conexao'   => 'Conectar ao Banco de dados', 
        'install'   => 'Instalando o banco de dados',
        'webmaster' => 'Criar Administrador', 
        'config'    => 'Configurações do Sistema',
        'plugins'   => 'Instalação dos plugins'
    );
    private $url = "";
    
    public function AfterLoad() {
        error_reporting(E_ALL ^ E_NOTICE);
        ini_set("display_errors", 1);
        
        $this->url = URL . "admin/install.php?url=";
        try{
            $this->checkWebmaster();
        }  catch (Exception $e){/*do notthing*/}

    }
    
            private function checkWebmaster(){
                //verifica se existe o arquivo de configuração do banco de dados
                $this->LoadClassFromPlugin('plugins/setup/creators/conexaoSetup', 'cst');
                if(false === $this->cst->hasDBFile()){return;}

                //verifica se já existe webmaster cadastrado
                $this->LoadModel('usuario/login', 'uobj');
                $this->LoadClassFromPlugin('plugins/setup/creators/webmasterSetup', 'wms');
                if(false === $this->wms->hasWebmaster()){return;}

                //se ja existir webmaster, requere que o mesmo faça login
                $this->uobj->needWebmasterLogin($this->url . CURRENT_ACTION);
            }
    
    public function index(){
        $this->execute();
    }
    
    public function file(){
        $this->execute();
    }
    
    public function conexao(){
        $this->execute();
    }
    
    public function install(){
        $this->execute();
    }
    
    public function webmaster(){
        $this->execute();
    }
    
    public function config(){
        $this->execute();
    }
    
    public function plugins(){
        classes\Utils\cache::delete("CRYPT_KEYS");
        SRedirect(URL ."plugins/plug");
    }
    
    private $step = false;
    private function execute(){
        $this->LoadClassFromPlugin('plugins/setup/creators/'.CURRENT_ACTION.'Setup', 'cfs');
        if(method_exists($this->cfs, 'needExecute'))if(!$this->cfs->needExecute()) $this->nextStep();
        $view = (method_exists($this->cfs, 'getView'))?$this->cfs->getView():LINK . "/creators/".CURRENT_ACTION;
        $this->desenhaMenu();
        $this->registerVar("url_action", $this->url.CURRENT_ACTION);
        if(!$this->cfs->setupSystem($_POST)){
            $var = $this->cfs->getMessages();
            $this->setVars($var);
            if(empty($var))  $this->registerVar('erro',"Erro desconhecido na instalação do subsistema  '".CURRENT_ACTION."'");
            $this->display($view);
            return;
        }
        
        if(array_key_exists(CURRENT_ACTION, $this->steps)){
            $this->registerVar('alert', $this->steps[CURRENT_ACTION]);
            $this->display($view);
        }
        $this->step = true;
    }
    
    public function BeforeExecute(){
        if($this->step){$this->nextStep();}
    }
    
    private function nextStep(){
        $action   = CURRENT_ACTION;
        $steps = array_keys($this->steps);
        foreach($steps as $step){
            if(!method_exists($this, $step)) die("O passo $step não existe!");
            if($step == $action) break;
        }
        $nextstep = current($steps);
        SRedirect($this->url . $nextstep);
    }
    
    private function desenhaMenu(){
        $extra_menu = method_exists($this->cfs, 'getMenuInstall')? $this->cfs->getMenuInstall():array();
        $var = "<ul>";
        foreach($this->steps as $action => $name){
            if(CURRENT_ACTION == $action) $name = "<span style='color:red;'>$name</span>";
            $var .= "<li>$name";
            if(CURRENT_ACTION == $action && !empty($extra_menu)){
                $var .= "<ul>";
                foreach($extra_menu as $key => $value)
                    $var .= ($value == 'active')?"<li><span style='color:red;'>$key</span></li>":"<li>$key</li>";
                $var .= "</ul>";
            }
            $var .= "</li>";
        }
        $var .= "</ul>";
        
        $title = 'Minhas Configurações';
        EventTube::addEvent('menu-lateral', "<h3>$title</h3>$var");
    }
    
}