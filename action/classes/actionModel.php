<?php 

use classes\Classes\session;
use classes\Classes\EventTube;
class plugins_actionModel extends \classes\Model\Model{
    public $tabela = "plugins_action";
    public $pkey   = 'plugins_action_cod';
    public function isPublic($action){
        if(!is_array($action)) return true;
        if(empty($action)) return true;
        if(!array_key_exists("plugins_action_privacidade", $action)) return true;
        return ($action['plugins_action_privacidade'] == 'publico');
    }
    
    public function needCode($action){
        $data = $this->getField($action, 'plugins_action_needcod', 'plugins_action_nome');
        return ($data === 's' || $data === '');
    }
    
    private static $cookie = 'plugins_action_menu_cookie';
    public function geraMenu($plugin, $action_name, $addEvent = true){
        if(!session::exists(self::$cookie))session::setVar (self::$cookie, array());
        $var = session::getVar(self::$cookie);
        $this->prepare_action($action_name);
        $prepared = array();
        if(!array_key_exists($action_name, $var) || \usuario_loginModel::IsWebmaster()){
              $prepared = $this->genMenu($var, $plugin, $action_name);
        }else $prepared = $var[$action_name];
        if($addEvent){
            EventTube::addMenu('body-top', $prepared, 'menu/menu');
        }
        return $prepared;
    }
    
    private function genMenu($var, $plugin, $action_name){
        $obj = $this->LoadActionObject($plugin);
        if($obj == NULL) return array();

        $menu = $this->getPermitedMenu($obj, $action_name);
        if(empty($menu)) return array();
        $prepared = $this->prepareMenu($menu);
        if(empty($prepared)) return array();

        $var[$action_name] = $prepared;
        session::setVar(self::$cookie, $var);
        return $prepared;
    }
    
    private function LoadActionObject($plugin){
        $class = "{$plugin}Actions";
        $file = classes\Classes\Registered::getPluginLocation($plugin, true) ."/Config/$class.php";
        if(!file_exists($file))return NULL;
        require_once $file;
        if(!class_exists($class)) return NULL;
        $obj = new $class();
        return $obj;
    }
    
    
     private function getPermitedMenu($obj, $action_name){
         //será utilizado na função mountPermMenu
        $this->LoadModel("usuario/perfil", 'perf'); 
        $menu = ($obj->getMenu($action_name));
        $out  = array();
        $this->mountPermMenu($menu, $out);
        return $out;
    }
    
    private function mountPermMenu($menu, &$out){
        foreach($menu as $name => $link){
            if(!is_array($link)){
                $lk = $link;
                if(!$this->perf->hasPermission($lk)) continue;
                $out[$name] = $link;
            }else $this->mountPermMenu ($link, $out[$name]);
        }
    }
    
    private function prepareMenu($menu){
        $out  = $act = $act_temp = array();
        $this->getMenuActions($menu, $act);
        foreach($act as $var){
            $this->prepare_action($var);
            $act_temp[] = $var; 
        }
        $string = "'".implode("','", $act_temp) . "'";
        $menu2 = $this->selecionar(array('plugins_action_nome','plugins_action_label'), "plugins_action_nome IN($string)");
        foreach($menu2 as $m){
            $out[$m['plugins_action_nome']] = $m['plugins_action_label'];
        }
        $actions = array();
        $this->mountActions($menu, $out, $actions);
        return $actions;
    }
    
    private function getMenuActions($menu, &$actions){
        foreach($menu as $m){            
            if(!is_array($m)) $actions[] = $m;
            else $this->getMenuActions ($m, $actions);
        }
    }
    
    private function mountActions($menu, $menu2, &$actions){
        foreach($menu as $cod => $m){
            if(!is_array($m)){
                if(!is_numeric($cod)) $actions[$cod] = $m;
                elseif(isset($menu2[$m])) $actions[$menu2[$m]]  = $m;
                else{
                    $mtemp = $m; 
                    $this->prepare_action($mtemp);
                    if(isset($menu2[$mtemp])) $actions[$menu2[$mtemp]]  = $m;
                    else $actions[$m]  = $m;
                }
            }
            else {
                $actions[$cod] = array();
                $this->mountActions($m, $menu2, $actions[$cod]);
            }
        }
    }
    
    //usado em loadPermissions para corrigir o nome da ação
    public function prepare_action(&$action_name){
        //a ação deve conter o caminho completo
        $exp = explode('/', $action_name);
        foreach($exp as $cod => $e){
            $exp[$cod] = str_replace('/', '', trim($e));
            if(trim($e) == "") unset($exp[$cod]);
        }
        
        $count = count($exp);
        if($count < 3){
            if($count == 0) return "s";
            $action_name = ($count == 1)? "$action_name/index/index":"$action_name/index";
        }elseif($count > 3) $action_name = @$exp[0]."/".@$exp[1]."/".@$exp[2]." ";
        $action_name = str_replace(array('//', "/ "), array('/', ""), trim($action_name));
        if($action_name[strlen($action_name)-1] == "/") {
            $action_name[strlen($action_name)-1] = "";
            $action_name = trim($action_name);
        }
    }
    
    public function getCodActionByName($action_name){
        $this->prepare_action($action_name);
        $var =$this->selecionar(array('plugins_action_cod'), "plugins_action_nome = '$action_name'");
        if(empty($var)) return '';
        $v = array_shift($var);
        return $v['plugins_action_cod'];
    }
    
    public function updateAction($action_name, $modelname, $arr){
        $this->LoadModel('plugins/model', 'md');
        $this->LoadModel('plugins/permissao', 'perm');
        $cod = $this->getCodActionByName($action_name);
        if($cod == "") {return;}
        $post['plugins_model_cod']     = $this->md->getCodModelByName($modelname);
        if(isset($arr['permission']))  {$post['plugins_permissao_cod']      = $this->perm->getCodPermissionByName($arr['permission']);}
        if(isset($arr['label']))       {$post['plugins_action_label']       = $arr['label']; }
        if(isset($arr['publico']))     {$post['plugins_action_privacidade'] = ($arr['publico'] == "s")?'publico':'privado';}
        if(isset($arr['needcod']))     {$post['plugins_action_needcod']     = $arr['needcod']; }
        $post['plugins_action_groupyes']    = 's';
        $post['plugins_action_groupno']     = 'n';
        //print_r($post); die('aa');
        return $this->editar($cod, $post);
    }
    
    public function getAllActions(){
        $public  = $this->getPublicActions();
        $private = $this->getPrivateActions();
        foreach($private as $codp => $arr){
            $out[$codp] = array_merge($arr, $public);
        }
        $out[Admin] = $this->getAdminActions();
        $out[0]     = $public;
        return $out;
    }
    
    public function getPrivateActions(){
        //$tbperm = $this->LoadModel('plugins/permissao', 'perm')->getTable();
        $tbacc  = $this->LoadModel('plugins/acesso'   , 'acc' )->getTable();
        $tbperf = $this->LoadModel('usuario/perfil'   , 'perf')->getTable();
        
        $this->join('plugins/permissao', 'plugins_permissao_cod', 'plugins_permissao_cod', "LEFT");
        $this->join('plugins/acesso'   , 'plugins_permissao_cod', 'plugins_permissao_cod', "LEFT", 'plugins/permissao');
        $this->join('usuario/perfil'   , 'usuario_perfil_cod'   , 'usuario_perfil_cod'   , "LEFT", 'plugins/acesso');
        
        $var = $this->selecionar(
            array('plugins_action_nome' ,"$tbperf.usuario_perfil_cod"),
            "plugins_action_privacidade='privado' AND $tbacc.plugins_acesso_permitir='s'"
        );
        
        $out = array();
        foreach($var as $v){
            $out[$v['usuario_perfil_cod']][] = $v['plugins_action_nome'];
        }
        return $out;
    }
    
    public function getPublicActions(){
        $var = $this->selecionar(
            array('plugins_action_nome'),
            "plugins_action_privacidade='publico'"
        );
        $out = array();
        foreach($var as $v){
            $out[] = $v['plugins_action_nome'];
        }
        return $out;
    }
    
    private function getAdminActions(){
        $var = $this->selecionar(array('plugins_action_nome'));
        $out = array();
        foreach($var as $v){
            $out[] = $v['plugins_action_nome'];
        }
        return $out;
    }
}