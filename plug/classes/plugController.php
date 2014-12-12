<?php 

use classes\Classes\EventTube;
class plugController extends classes\Controller\CController{
    public $model_name = "plugins/plug";
    
    public function __construct($vars) {
        $this->addToFreeCod(array("updateall", 'export', "reimport", "setactions"));
        parent::__construct($vars);
    }
    
    public function AfterLoad() {
        parent::AfterLoad();
        $this->LoadModel('admin/install', 'inst');
    }
    
    public function index($display = true, $link = "") {
        $this->model->updateHatUrl();
        $out = $this->model->getOutdated();
        if(!empty($out)) {
            $this->LoadResource('html', 'tml');
            $v = $virg= "";
            foreach($out as $o) {$v .= $virg .$o['pluglabel']; $virg = ", ";}
            $var = $this->html->getActionLinkIfHasPermission('plugins/plug/updateall', 'Clique aqui');
            if($var != ""){
                $this->registerVar ('alert', "Os seguintes plugins precisam ser atualizados: $v <hr/> 
                    $var para atualizar todos os plugins");
            }
        }
        
        $this->model->registerFoundedPlugins();
        $this->model->unregisterDropedPlugins();
        $this->setVars($this->model->getMessages());
        parent::index($display, $link);
    }
    
    public function acesso(){
        if(empty($this->item)) Redirect (LINK);
        $url = $this->item['plugnome']."/index/index";
        Redirect($url);
    }
    
    public function advanced($display = true, $link = ""){
        $this->changeMenu();
         $link = ($link == "")? "admin/auto/areacliente/page":$link;
        $this->registerVar("comp_action" , 'advanced');
        $this->registerVar('title',ucfirst($this->item['pluglabel']));
    	if($display) $this->display($link);
    }
    
    public function updateall(){
        $this->LoadClassFromPlugin('config/form/formDetector', 'fd')->importData();
        $bool  = $this->model->updateall();
        $arr   = $this->model->getMessages();
        $arr['status'] = ($bool === false)?"0":"1";
        $this->setVars($arr);
        $this->LoadModel('site/sitemap', 'smap')->createMap();
        $this->model->mountPerfilPermissions();
        $this->display("");
    }
    
    public function setactions(){
        $action = isset($this->vars[0])?$this->vars[0]:'';
        $this->LoadClassFromPlugin('plugins/plug/update/actionFinder', 'af');
        if($action == ""){
            $action = classes\Classes\Registered::getAllPluginsLocation();
            foreach($action as $act => $location){
                $var = $this->af->find($act);
                if($var == '') continue;
                echo ucfirst($act)."<hr/> $var";
            }
        }
        else{echo $this->af->find($action);} 
    }
    
    private function changeMenu(){
        $this->LoadModel('usuario/login', 'uobj');
        $is_webmaster = $this->uobj->UserIsWebmaster();
        if($this->item['versao'] != $this->item['lastversao']) {
            $this->LoadResource('html', 'html');
            $var = $this->html->getActionLinkIfHasPermission('plugins/plug/update', 'Clique aqui');
            $this->registerVar('alert', 'Você não está usando a vesão mais recente deste plugin. '.$var.' para atualizar o plugin');
        }elseif(!$is_webmaster) EventTube::removeItemFromMenu ('body-top', 'Atualizar');
        switch ($this->item['__status']){
            case 'instalado':
                EventTube::removeItemFromMenu ('body-top', 'Instalar Aplicativo');
                EventTube::removeItemFromMenu ('body-top', 'Desbloquear');
                EventTube::removeItemFromMenu ('body-top', 'Ativar');
                EventTube::removeItemFromMenu ('body-top', 'Desativar');
                
                break;
            case 'desinstalado':
                EventTube::removeItemFromMenu ('body-top','Desinstalar Aplicativo');
                EventTube::removeItemFromMenu ('body-top','Desbloquear');
                EventTube::removeItemFromMenu ('body-top','Atualizar');
                EventTube::removeItemFromMenu ('body-top', 'Ativar');
                EventTube::removeItemFromMenu ('body-top','Desativar');
                break;
            case 'desativado':
                EventTube::removeItemFromMenu ('body-top','Desinstalar Aplicativo');
                EventTube::removeItemFromMenu ('body-top','Desativar');
                EventTube::removeItemFromMenu ('body-top','Atualizar');
                EventTube::removeItemFromMenu ('body-top','Instalar Aplicativo');
                EventTube::removeItemFromMenu ('body-top','Popular');
                break;
        }
        
        if($this->item['__system'] == 's'){
            EventTube::removeItemFromMenu ('body-top','Desinstalar Aplicativo');
            EventTube::removeItemFromMenu ('body-top','Desativar');
            EventTube::removeItemFromMenu ('body-top','Ativar');
        }
    }
    
    public function show($display = true, $link = "") {
        
        $this->changeMenu();
        //$this->display('plugins/plug/show');
        parent::show($display, 'plugins/plug/show');
    }
    
    public function setdefault(){
        $this->model->setDefault($this->cod);
        $this->redirect(LINK ."/show/$this->cod");
    }
    
    public function apagar() {
        $this->redirect(LINK ."/index");
    }
    
    public function install(){
        $modulo = $this->item['plugnome'];
        if(!defined("LOG_INSTALACAO")) define ("LOG_INSTALACAO", "plugins/instalacao/".  GetPlainName($modulo));
        $bool = $this->LoadClassFromPlugin('plugins/plug/plugSetup', 'ps')->setup($modulo);
        $this->setVars($this->ps->getMessages());
        $this->registerVar('status', ($bool === false)?'0':'1');
        $this->model->mountPerfilPermissions();
        $this->redirect(LINK ."/show/$this->cod");
    }
    
    public function unstall(){
        if($this->item['__isdefault'] === 's'){
            $this->registerVar('erro',"Você não pode desinstalar um plugin padrão do sistema!");
            $this->redirect(LINK);
        }
        $this->action();
    }
    
    public function disable(){
        if(INSTALL_DB_ENABLE){
            if($this->item['__isdefault'] === 's'){
                $this->registerVar('erro',"Você não pode desativar um plugin padrão do sistema!");
                $this->redirect(LINK."show/$this->cod");
            }
            $this->action();
        }else Redirect(PAGE);
    }
    
    public function enable(){
        if(INSTALL_DB_ENABLE){
            $this->action();
        }else $this->redirect(LINK ."/show/$this->cod");
    }
    
    public function populate(){
        if(INSTALL_DB_POPULATE){
            $this->action();
        }else $this->redirect(LINK ."/show/$this->cod");
    }

    public function update(){
        if(INSTALL_DB_UPDATE){
            $this->action();
        }else $this->redirect(LINK ."/show/$this->cod");
    }
    
    public function api_update(){
        if(INSTALL_DB_UPDATE){
            $this->action('update');
        }else $this->redirect(LINK ."/show/$this->cod");
    }

    private function action($action_name = ""){
        $action = ($action_name === "")?CURRENT_ACTION:$action_name;
        $modulo = $this->item['plugnome'];
        if(!defined("LOG_INSTALACAO")) define ("LOG_INSTALACAO", "plugins/instalacao/".  GetPlainName($modulo));
        
        \classes\Utils\Log::save(LOG_INSTALACAO, "<h2>Executando a action " . CURRENT_ACTION . "</h2>");
        if(!method_exists($this->inst, $action)){
            $action = ucfirst($action);
            if(!method_exists($this->inst, $action)){
                \classes\Utils\Log::save(LOG_INSTALACAO, "$action abortada! A ação $action não existe!");
                 $this->redirect(LINK ."/show/$this->cod");
            }
        }
        $bool = $this->inst->$action($modulo);
        $this->setVars($this->inst->getMessages());
        \classes\Utils\Log::save(LOG_INSTALACAO, $this->inst->getMessages());
        \classes\Utils\Log::save(LOG_INSTALACAO, "$action concluída");
        $this->registerVar('status', ($bool === false)?'0':'1');
        $this->LoadModel('site/sitemap', 'smap')->createMap();
        $this->model->mountPerfilPermissions();
        $this->redirect(LINK ."/show/$this->cod");
    }
    
    public function inclasses(){
        $this->LoadClassFromPlugin('plugins/plug/inclasses/registerConfigurations', 'rconf')
                ->register($this->item['plugnome'], $this->item['cod_plugin']);
        print_r($this->rconf->getMessages());
    }
    
    public function log(){
        $url = URL."index.php?url=site/index/log&file=/plugins/instalacao/{$this->item['plugnome']}.html";
        SRedirect($url);
    }
    
    public function export(){
        $plugname = $this->vars[0];
        $e        = explode(":", $this->vars[1]);
        $coduser  = array_shift($e);
        $passwd   = array_shift($e);
        $user     = $this->LoadModel('usuario/login', 'uobj')->getAutenticatedUser($coduser, $passwd);
        if(empty($user)){
            $this->registerVar('erro', 'Erro ao autenticar usuário');
            return $this->display('');
        }
        
        if(false === $this->uobj->UserIsAdmin($coduser)){
            $this->registerVar('erro', 'Seu usuário não possui permissão para baixar os dados deste site!');
            return $this->display('');
        }
        $this->LoadResource('database/export', 'exp')->enableDownload()
                ->exportDataFromPlugin($plugname);
    }
    
    public function reimport(){
        $plugname = $this->vars[0];
        $item     = $this->LoadModel('plugins/hatapp', 'hurl')->getItem($this->vars[1]);
        if(empty($item)){die('API não registrada!');}
        $url      = "{$item['url']}index.php?url=plugins/plug/export/$plugname/{$item['user']}:{$item['passwd']}&ajax=true";
        $status   = $this->LoadResource('database/reimport', 'reimp')->reimportDataFromPlugin($plugname, $url);
        $arr      = $this->reimp->getMessages();
        $arr['status'] = ($status === true)?"1":"0";
        $this->setVars($arr);
        $this->display("");
    }
    
    public function mountperms(){
        die($this->LoadModel('usuario/perfil', 'md')->hasPermissionByName('usuario_FL') === true?"s":"n");
        $this->model->mountPerfilPermissions();
    }
}
