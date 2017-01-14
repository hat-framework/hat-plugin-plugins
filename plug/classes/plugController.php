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
        $this->LoadModel('plugins/plug/install', 'inst');
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
		$this->LoadClassFromPlugin('plugins/plug/menuChanger','mc')->change($this->item);
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
    
    public function show($display = true, $link = "") {
		$this->LoadClassFromPlugin('plugins/plug/menuChanger','mc')->change($this->item);
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
        $this->redirect(LINK ."/show/$this->cod");
    }
    
    public function unstall(){
        if($this->item['__isdefault'] === 's'){
            $this->registerVar('erro',"Você não pode desinstalar um plugin padrão do sistema!");
            $this->redirect(LINK);
        }
        if(!usuario_loginModel::ConfirmPassword()){die("Password incorreto!");}
        $this->action();
    }
    
    public function disable(){
        if($this->item['__isdefault'] === 's'){
            $this->registerVar('erro',"Você não pode desativar um plugin padrão do sistema!");
            $this->redirect(LINK."show/$this->cod");
        }
        usuario_loginModel::ConfirmPassword();
        $this->action();
    }
    
    public function enable(){
        $this->action();
    }
    
    public function populate(){
        if(DEBUG === true){
            usuario_loginModel::ConfirmPassword();
            $this->action();
        }else $this->redirect(LINK ."/show/$this->cod");
    }

    public function update(){
        $this->action();
    }
    
    public function api_update(){
        $this->action('update');
    }
    
    public function updateSpecific(){
        if(isset($this->vars[1])){
            $class  = $this->vars[1];
            $plugin = $this->item['plugnome'];
            define('LOG_INSTALACAO', "plugins/$plugin/$class");
            $bool = $this->runClass($class, $plugin);
            $this->registerVar('status', ($bool == true)?'1':"0");
            $name = ($bool == true)?'success':'erro';
            $var  = ($bool == true)?"Dados da classe $class atualizados com sucesso!":'Erro ao atualizar dados!';
            $this->registerVar($name, $var);
            $this->setVars($this->obj->getMessages());
        }
        $this->registerVar('avaible', $this->getSpecificAvaible());
        $this->display(LINK.'/updateSpecific');
    }
    
            private function runClass($class, $plugin){
                if(in_array($class, array('executePopulateSql','setPluginInstaled','updateOtherSystems'))){
                    $this->LoadClassFromPlugin('plugins/plug/plugSetup', 'obj')->setPluginName($plugin);
                    $bool = $this->obj->$class();
                }else{
                    $this->LoadClassFromPlugin('plugins/plug/setupAux/startHelper', 'obj');
                    if($class == "registerModels"){$bool = $this->obj->runRegisterModel($plugin);}
                    else                          {$bool = $this->obj->runInstallClass($plugin, $class, $this->cod);}
                }
                return $bool;
            }
    
            private function getSpecificAvaible(){
                return array(
                    'registerActions'      ,'registerConfigurations',
                    'registerModels'       ,'registerPermissions',
                    'registerSetupPlugin'  , 'executePopulateSql',
                    'setPluginInstaled'    , 'updateOtherSystems'
                );
            }

    private function action($action_name = ""){
        $action = ($action_name === "")?CURRENT_ACTION:$action_name;
        $modulo = $this->item['plugnome'];
        if(!defined("LOG_INSTALACAO")) {define ("LOG_INSTALACAO", "plugins/instalacao/".  GetPlainName($modulo));}
        $this->getTrueMethod($action);
        $bool = $this->doAction($action, $modulo);
        $this->registerVar('status', ($bool === false)?'0':'1');
        $this->redirect(LINK ."/show/$this->cod");
    }
    
            private function getTrueMethod(&$action){
                \classes\Utils\Log::save(LOG_INSTALACAO, "<h2>Executando a action " . CURRENT_ACTION . "</h2>");
                if(method_exists($this->inst, $action)){return true;}
                $action = ucfirst($action);
                if(method_exists($this->inst, $action)){return true;}
                \classes\Utils\Log::save(LOG_INSTALACAO, "$action abortada! A ação $action não existe!");
                 $this->redirect(LINK ."/show/$this->cod");
            }
            
            private function doAction($action, $modulo){
                $bool = $this->inst->$action($modulo);
                $this->setVars($this->inst->getMessages());
                \classes\Utils\Log::save(LOG_INSTALACAO, $this->inst->getMessages());
                \classes\Utils\Log::save(LOG_INSTALACAO, "$action concluída");
                if($action == 'update'){
                    $class = 'plugins/plug/inclasses/registerSetupPlugin';
                    $this->LoadClassFromPlugin($class, 'rsp')->setMethod('update')->register($modulo, "");
                }
                
                $this->LoadModel('site/sitemap', 'smap')->createMap();
                $this->model->mountPerfilPermissions();
                $this->LoadClassFromPlugin('config/form/formDetector', 'fd')->importData();
                return $bool;
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