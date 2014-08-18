<?php

use classes\Classes\cookie;
class plugins_plugModel extends \classes\Model\Model{

    protected $tabela = "plugin_plug";
    protected $pkey   = "cod_plugin";

    public function listPlugins($all = false){

        $this->LoadResource("files/dir", "dir");
        $plugins = classes\Classes\Registered::getAllPluginsLocation();
        $exclude = array('Config', ".DS_Store");
        $out = array();

        $marr = $this->selecionar(array('plugnome'), "`status` != 'desinstalado'");
        //se não tem plugins
        if(empty($marr)) return array();
        $permitidos = array();
        foreach($marr as $temp){
            $permitidos[] = $temp['plugnome'];
        }

        $this->LoadModel('usuario/login', 'user');
        if(!$this->user->UserIsWebmaster()) $exclude[] = 'admin';
        foreach($plugins as $plugin => $diretorio){
            
            //se é proibido, continua
            if(in_array($plugin, $exclude)) continue;

            //se não é permitido, continua
            if(!in_array($plugin, $permitidos) && $all == false) continue;
            
            $out[$plugin]  = array($plugin => $plugin);
            $subplugins    = $this->dir->getPastas($diretorio);
            asort($subplugins);
            $total = 0;

            foreach($subplugins as $splugin){
                if(in_array($splugin, $exclude)) continue;
                $file = $diretorio . "/$splugin/classes/{$splugin}Admin.php";
                if(file_exists($file)) {
                    $this->LoadConfigFromPlugin($plugin);
                    $total++;
                    $name = ($splugin == "index")?$plugin:$splugin;
                    $out[$plugin][$name] = "$plugin/$splugin";
                }
            }
            
            if($total == 0 && !$all) {unset($out[$plugin]);}
        }

        return $out;
    }
    
    public function findPlugins(){
        $this->LoadResource("files/dir", "dir");
        $files = array_keys(classes\Classes\Registered::getAllPluginsLocation(true));
        
        $out = array();
        foreach($files as $nfile){
            if($nfile == ".DS_Store") continue;
            $out[$nfile] = $nfile;
        }
        return $out;
    }
    
    public function IsAvaible($plugnome){
        $total = $this->getCount("plugnome = '$plugnome' AND status = 'instalado'");
        if($total > 0) return true;
        $tt = $this->getCount("plugnome = '$plugnome'");
        if($tt == 0){
            $var = $this->listPlugins();
            if(empty ($var) ||!array_key_exists($plugnome, $var)){
                $var = $this->findPlugins();
                if(empty ($var) ||!array_key_exists($plugnome, $var)){
                    throw new \classes\Exceptions\UnexistentItemException("O plugin que você está procurando não existe!");
                }
                else throw new \classes\Exceptions\PageBuildingException();
            }

            $this->LoadClassFromPlugin('plugins/plug/plugSetup', 'psetup');
            if(!$this->psetup->install($plugnome)) throw new \classes\Exceptions\modelException("pluginModel", $this->iobj->getErrorMessage());
            return true;
        }
        
        $plugin = $this->selecionar(array('status'), "plugnome = '$plugnome'");
        if(!empty($plugin)) $plugin = array_shift ($plugin);
        else $plugin['status'] = 'desinstalado';
        if($plugin['status'] == "desinstalado") throw new \classes\Exceptions\PageBlockedException();
        //if($plugin['status'] == "desativado")   
        throw new \classes\Exceptions\PageBuildingException();
    }

    public function IsRegistered($plugnome){
        return ($this->getCount("plugnome = '$plugnome'") > 0);
    }
    
    public function setSubplugins(){
        $this->LoadModel('plugins/subplugin', 'sub');
        $list    = $this->listPlugins(true);
        $keys    = array_keys($list);
        $var     = implode("','", $keys);
        $subs    = $this->sub->selecionar();
        
        $temp    = array();
        if(!empty($subs)) 
            foreach($subs as $sub) 
                $temp[$sub['model']] = $sb;
        
        $plugins = $this->selecionar(array($this->pkey, 'plugnome'), "plugnome IN('$var')");
        foreach($plugins as $plugin){
            if(!array_key_exists($plugin['plugnome'], $list)) continue;
            
            foreach($list[$plugin['plugnome']] as $model){
                
                if(array_key_exists($model, $temp)) continue;
                $arr['model']      = $model;
                $arr['cod_plugin'] = $plugin[$this->pkey];
                $pname = explode("/", $model);
                $file = classes\Classes\Registered::getPluginLocation($pname[0], true).$pname[1].'/description.php';
                if(file_exists($file)){
                    require_once $file;
                    $md = str_replace('/', '_', $model) . "Desc";
                    $obj = new $md();
                    $arr['splabel']   = $obj->getLabel();
                    $arr['descricao'] = $obj->getDescription();
                }
                
                if(!$this->sub->inserir($arr))
                    echo $this->sub->getErrorMessage() . "<br/>";
                $arr = array();
            }
            
        }
    }
    
    public function editar($id, $post, $camp = "") {
        
        if(array_key_exists('status', $post) && $post['status'] == 'desinstalado'){
            $camp  = ($camp == "")?$this->pkey:$camp;
            $dados = $this->selecionar(array(), "$camp = '$id'");
            if(empty($dados)) {
                $this->setErrorMessage("O plugin que você está tentando desinstalar não existe");
                return false;
            }
            $dados = array_shift($dados);
            $observers = array('plugins/permissao', 'plugins/model');
            foreach($observers as $observername){
                $this->LoadModel($observername, 'md');
                if(!$this->md->unstall($dados['cod_plugin'])){
                    $erro = "Não foi possível desinstalar o plugin. <br/><br/>";
                    $this->setErrorMessage($erro . $this->md->getErrorMessage());
                    return false;
                }
            }
        }
        return parent::editar($id, $post, $camp);
    }
    
    public function getDefault(){
        if(!cookie::cookieExists('MODULE_DEFAULT')){
            $var = $this->selecionar(array('plugnome'), "default = 's'", 1);
            if(empty($var)) $var[]['plugnome'] = "usuario";
            cookie::setVar('MODULE_DEFAULT', $var);
        }
        else $var = cookie::getVar('MODULE_DEFAULT');
        $v = array_shift($var);
        return $v['plugnome'];
    }
    
    public function setDefault($cod_item){
         $this->db->ExecuteQuery("
            UPDATE $this->tabela SET isdefault = 'n'; 
            UPDATE $this->tabela SET isdefault = 's' WHERE $this->pkey = '$cod_item'; 
        ");
    }
    
    public function paginate($page, $link = "", $cod_item = "", $campo = "", $qtd = 200, $campos = array(), $adwhere = "", $order = "") {
        $order = ($order == "")?"status DESC, pluglabel ASC":$order;
        return parent::paginate($page, $link, $cod_item, $campo, $qtd, $campos, $adwhere, $order);
    }
    
    public function getSystemPlugins(){
        $all = $this->findPlugins();
        $default = array();
        foreach($all as $a){
            $class = "{$a}Install";
            $file  = classes\Classes\Registered::getPluginLocation($a, true) . "/Config/$class.php";
            if(!file_exists($file)) continue;
            require_once $file;
            if(!class_exists($class)) continue;
            $obj = new $class();
            if(!$obj->isSystem()) continue;
            $default[$a] = $a;
        }
        //print_r($default); die();
        return $default;
    }
    
    public function unregisterDropedPlugins(){
        $dir_plugins = $this->findPlugins();
        $wh          = "'".implode("','", $dir_plugins)."'";
        $db_plugins  = $this->selecionar(array('plugnome'), "plugnome NOT IN($wh)");
        foreach($db_plugins as $pl){
            if(in_array($pl['plugnome'], $dir_plugins))continue;
            if(!$this->apagar($pl['plugnome'], "plugnome")){
                $this->setErrorMessage("Erro ao registrar o plugin {$pl['plugnome']} no banco de dados: <br/>".
                        $this->plug->getErrorMessage());
                return false;
            }
        }
        return true;
    }
    
    public function registerFoundedPlugins(){
        $err  = array();
        $plugins = $this->findPlugins();
        foreach($plugins as $plugnome){
            if($this->IsRegistered($plugnome)) continue;
            $obj = $this->getPluginInstaller($plugnome);
            if(is_object($obj)){
                $dados = $obj->getDados();
            }
            $dados['plugnome'] = $plugnome;
            if(!$this->inserir($dados)){
                $err[] = "Erro ao registrar o plugin $plugnome no banco de dados: <br/>".$this->getErrorMessage();
                //return false;
            }
        }
        if(!empty($err)){
            $this->setErrorMessage($err);
            return false;
        }
        
        return true;
    }
    
    public function getPluginInstaller($plugin){
        $class = "{$plugin}Install";
        $file  = classes\Classes\Registered::getPluginLocation($plugin, true)."/Config/$class.php";
        getTrueDir($file);
        if(!file_exists($file)) return null;
        require_once $file;
        if(!class_exists($class)) return null;
        $obj = new $class();
        if(!($obj instanceof classes\Classes\InstallPlugin)) {
            $this->setErrorMessage("A classe $class não é uma instância de InstallPlugin. Instalação do plugin abortada!");
            return null;
        }
        return $obj;
    }
    
    public function getOutdated(){
        $var = $this->selecionar(array('plugnome', 'pluglabel'), "versao != lastversao AND status != 'desinstalado'");
        //die($this->db->getSentenca());
        return $var;
    }
    
    public function updateOutdated(){
        $all = $this->getOutdated();
        return $this->updateall_fn($all);
    }
    
    public function updateall(){
        $var = $this->selecionar(array('plugnome', 'pluglabel'), "status != 'desinstalado'");
        return $this->updateall_fn($var);
    }
    
    private function updateall_fn($all){
        $this->LoadModel('admin/install', 'inst');
        $bool = false;
        foreach($all as $a){
            if($a['plugnome'] === 'admin'){continue;}
            if(!$this->inst->update($a['plugnome'])){
                $erro = $this->inst->getErrorMessage();
                if(trim($erro) == "") {continue;}
                $bool = false;
                $this->appendErrorMessage($erro);
            }
        }
        if(false === $bool){return false;}
        return $this->setSuccessMessage('Plugins atualizados com sucesso!');
    }
    
    public function getPluginByName($name, $dados = array()){
        return $this->getItem($name, "plugnome", true, $dados);
    }
    
}