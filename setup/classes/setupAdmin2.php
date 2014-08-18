<?php

use classes\Classes\EventTube;
class setupAdmin extends \classes\Controller\Controller{
    
    private $steps = array(
        'Instalação do banco de dados' => 'dbinstall', 
        'Criar uma conta de Webmaster' => 'webmaster', 
        'Configurações básicas'        => 'configure',
        'Instalação dos plugins'       => ''
    );
    
    public function AfterLoad() {
        parent::AfterLoad();
        $this->LoadModel('site/confgrupo', 'gr');
        $this->LoadModel("plugins/setup", 'sobj');
        $this->url = URL . "admin/install.php?url=index";
        $var = "<ul>";
        foreach($this->steps as $name => $action){
            if(CURRENT_ACTION == $action) $name = "<span style='color:red;'>$name</span>";
            $var .= "<li>$name";
            if($action == 'configure'){
                $out = $this->gensession();
                if(!empty($out)){
                    $var .= "<ul>";
                    foreach($out as $cod => $o){
                        if(@$_GET['page'] == $cod) $o = "<span style='color:red;'>$o</span>";
                        $var .= "<li>$o</li>";
                    }
                    $var .= "</ul>";
                }
            }
            $var .= "</li>";
        }
        $var .= "</ul>";
        
        $title = 'Minhas Configurações';
        EventTube::addEvent('menu-lateral', "<h3>$title</h3>$var");
        try{
            $this->LoadModel('usuario/login', 'uobj');
        }catch (\classes\Exceptions\DBException $e){
            $this->registerVar('alert', $e->getMessage());
        }
        //die("oinc");
    }

    public function dbinstall(){
        
        if($this->sobj->hasDbConnection()) SRedirect($this->url);
        if(!$this->sobj->dbInstall($_POST)){
            $this->setVars($this->sobj->getMessages());
            $this->registerVar("dados", $this->sobj->getDados());
            $this->genTags("Configurar o banco de dados");
            $this->display('admin/install/forminstall');
        }
        else SRedirect($this->url);
    }
    
    public function tableinstall(){
        
        if($this->sobj->CheckBasicTables()) { SRedirect($this->url);}
        $this->setVars($this->sobj->getMessages());
        $this->display("");
    }
    
    public function webmaster(){
        
        if(!$this->gr->findNewGroups()){
            $this->setVars($this->gr->getMessages());
            $this->display('');
            return;
        }
        //die(CONFIG_SUBDOMAIN);
        $this->uobj->Logout();
        if(!$this->sobj->hasWebmaster()){
            $this->genTags("Criar um novo Webmaster");
            $bool = $this->sobj->setWebmaster($_POST);
            $this->setVars($this->sobj->getMessages());
            $this->registerVar('status', $bool?'1':"0");
            if(isset($_REQUEST['ajax']) && $bool){
                $this->registerVar('redirect', $this->url);
            }
            
            $this->registerVar("dados", $this->sobj->getWebmasterData());
            $this->registerVar('titulo', "Dados do Webmaster");
            $this->display('admin/auto/formulario');
            return;
            
        }else {SRedirect($this->url);}
            
    }
    
    private function gensession(){
        $g   = $this->gr->getWebmasterGroups();
        $gr  = array_flip($g);
        $out = array();
        foreach($gr as $link => $var){
            $e = @end(explode("/", $link));
            $out[$e] = $var;
        }
        $this->groups = $out;
        return $this->groups;
    }
    
    public function configure(){
        $this->createSubdomainFolder();
        $nextpage = $antpage = "";
        if(!isset($_GET['page'])){
            $this->groups = $this->gensession();
            $page = @array_shift(array_keys($this->groups));
            SRedirect(URL ."admin/install.php?url=configure&page=$page");
        }elseif(!empty($this->groups)){
            if(array_key_exists($_GET['page'], $this->groups)){
                $keys = array_reverse(array_keys($this->groups));
                foreach($keys as $cod){
                    if($cod == $_GET['page']) break;
                    $nextpage = $cod;
                }
                $antpage = current($keys);//$antpage = next($keys);
            }else SRedirect(URL ."admin/index.php?url=admin/install");
        }
        
        if($antpage!= "")  $arr['Anterior']                = URL ."admin/install.php?url=configure&page=$antpage";
        else               $arr['Pular configuração']      = URL ."admin/index.php?url=admin/install";
        if($nextpage!= "") $arr['Próxima']                 = URL ."admin/install.php?url=configure&page=$nextpage";
        else               $arr['Instalação dos Plugins']  = URL ."admin/index.php?url=admin/install";
        EventTube::addMenu('body-top', $arr, 'menu/multiple');
        
        $this->LoadModel('site/configuracao', 'scon');
        $files = $this->scon->LoadFiles($cod);
        $this->registerVar('grupo', $this->gr->getItem($cod));
        $this->registerVar('files', $files);
        $this->display('site/configuracao/group');
        //$this->gr->ge
    }
    
    private function createSubdomainFolder(){
        $this->LoadResource('files/dir', 'dobj');
        $this->dobj->create(DIR_SUB_DOMAIN, "", 0755);
    }
    
    public function index(){
        
        /*$this->setVars($this->sobj->getMessages());
        $erro  = $this->sobj->getErrorMessage(); if($erro != "") {$this->display (""); return; }
        $alert = $this->sobj->getAlertMessage(); if($alert != "") {$this->display (""); return; }
        */
        if(!$this->sobj->hasDbConnection()) SRedirect(URL ."admin/install.php?url=dbinstall");
        if(!$this->sobj->CheckBasicTables()) {$this->tableinstall(); return;}
        if(!$this->sobj->hasWebmaster()) SRedirect(URL ."admin/install.php?url=webmaster");
        $this->LoadModel('usuario/login', 'uobj');
        if(!$this->uobj->IsLoged()) $this->uobj->needLogin(URL ."admin/install.php?url=configure");
        SRedirect(URL ."admin/install.php?url=configure");
    }
    
}
?>