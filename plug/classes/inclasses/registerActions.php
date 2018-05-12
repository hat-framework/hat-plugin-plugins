<?php

class registerActions extends classes\Classes\Object implements install_subsystem{
    
    //objetos a serem carregados
    protected $perf       = NULL;
    protected $act        = NULL;
    protected $model_obj  = NULL;
    protected $perm       = NULL;
    private   $action_obj = NULL;
    
    //variaveis de estado
    private   $actions    = array();
    private   $permissoes = array();
    private   $perfis     = array();
    private   $erro       = array();
    private   $cod_plugin = "";
    
    public function __construct() {
        
        if(!defined("LOG_INSTALACAO")) {define ("LOG_INSTALACAO", "plugins/instalacao");}
        //inicializa os objetos a serem usados pela classe
        $this->LoadModel('plugins/model'    , 'model_obj');
        $this->LoadModel('usuario/perfil'   , 'user_perfil');
        $this->LoadModel('plugins/action'   , 'act');
        $this->LoadModel('plugins/permissao', 'perm');
    }
    
    public function register($plugin, $cod_plugin){
        
        //inicializa as variaveis de estado
        \classes\Utils\Log::save(LOG_INSTALACAO, "Iniciando registerAction do plugin $plugin");
        $this->init($plugin, $cod_plugin);
        
        //registra os perfis de usuário
        \classes\Utils\Log::save(LOG_INSTALACAO, "Iniciando registro de perfis");
        $this->registerPerfis($plugin);


        //registra as acoes encontradas
        \classes\Utils\Log::save(LOG_INSTALACAO, "Iniciando registro de actions");
        $this->registerActions();
        
        //registra as permissoes
        \classes\Utils\Log::save(LOG_INSTALACAO, "Iniciando registro de permissões");
        $this->registerPermission();
        
        //verifica se ocorreu algum erro
        return $this->hasError();
    }
    
            private function init($plugin, $cod_plugin){
                $this->cod_plugin = $cod_plugin;
                $this->action_obj = $this->LoadActionClass($plugin);
                $this->actions    = $this->action_obj->getActions();
                $this->permissoes = $this->action_obj->getPermissions();
                $this->perfis     = $this->action_obj->getPerfis();
            }
            
                    private function LoadActionClass($plugin){

                        //carrega o arquivo
                        $file = classes\Classes\Registered::getPluginLocation($plugin, true) . "/Config/{$plugin}Actions.php";
                        if(!file_exists($file)) {
                            throw new classes\Exceptions\modelException(__CLASS__, 
                                "AILAC01 - Arquivo de configuração de ações <br/>$file<br/> não encontrado no plugin $plugin."
                            );
                        }
                        require_once $file;

                        //carrega a classe
                        $class  = "{$plugin}Actions";
                        if(!class_exists($class)) {
                            throw new classes\Exceptions\modelException(__CLASS__, 
                                "AILAC02 - Classe de ação $class não encontrada no plugin $plugin"
                            );
                        }
                        return new $class();
                    }
            
            private function registerPermission(){
                $this->LoadClassFromPlugin('plugins/plug/inclasses/registerPermissions', 'rp')
                        ->register($this->action_obj, $this->cod_plugin , $this->permissoes, $this->perfis);
            }
    
            private function registerActions(){
                foreach($this->actions as $url => $arr){
                    $this->registerAction($url, $arr);
                }
            }
    
                    private function registerAction($url, $arr){

                        //verifica se a ação já está registrada
                        if($this->act->getCount("plugins_action_nome = '$url'") > 0) return;

                        //inicializa as variaveis
                        $act   = explode('/', $url);
                        array_pop($act);
                        $model = implode("/", $act);

                        //registra o modelo (caso ainda não tenha sido registrado)
                        $cod_model = $this->registerModel($model);
                        if($cod_model == "") return;

                        $perm = $this->perm->getItem($arr['permission'], 'plugins_permissao_nome');
                        if(empty($perm)) {$this->erro[] = "A permissão \"".$arr['permission']."\" não foi registrado no banco de dados!<br/>"; return;}
                        $this->insertAction($cod_model, $perm['plugins_permissao_cod'], $url, $arr);
                    }
    
                            private function registerModel($model){
                                $item = $this->model_obj->getItem($model, 'plugins_model_name', true);
                                if(!empty($item)) return $item['plugins_model_cod'];

                                $insert = array(
                                    'plugins_model_name' => $model, 'plugins_model_label' => "", 
                                    "plugins_model_description" => "", 'cod_plugin' => $this->cod_plugin);
                                if(!$this->model_obj->inserir($insert)){
                                    $this->erro[] = "O model $model Não foi registrado no banco de dados!<br/>";
                                    $this->erro[] = implode("<br/>", $this->model_obj->getMessages());
                                    return '';
                                }

                                return $this->model_obj->getLastId();
                            }

                            private function insertAction($cod_model, $cod_perm, $url, $arr){

                                //verifica se uma ação possui tutorial ou algum tipo de notificação
                                if(array_key_exists('notificar', $arr))$post['notificar'] = $arr['notificar'];
                                if(array_key_exists('tutorial', $arr)) $post['tutorial']  = $arr['tutorial'];

                                //seta as variaveis a serem inseridas
                                $post['plugins_action_cod']         = "";
                                $post['plugins_model_cod']          = $cod_model;
                                $post['plugins_action_nome']        = $url;
                                $post['plugins_action_label']       = $arr['label'];
                                $post['plugins_permissao_cod']      = $cod_perm;
                                $post['plugins_action_privacidade'] = (isset($arr['publico']) &&   $arr['publico'] == "s")?'publico':'privado';
                                $post['plugins_action_groupyes']    = "s";
                                $post['plugins_action_groupno']     = "n";
                                $post['plugins_action_needcod']     = "n";
                                if(isset($arr['needcod'])){ $post['plugins_action_needcod'] = ($arr['needcod'])?'s':'n';}

                                if(!$this->act->inserir($post)){
                                   $this->erro[] = implode("<br/>", $this->act->getMessages());
                                }
                            }
    
            private function registerPerfis($plugin){
                if(false === $this->canCreateProfile($plugin)){return;}
                $this->LoadModel('usuario/perfil', 'up');
                $this->LoadModel('plugins/acesso', 'acc' );
                foreach($this->perfis as $perf){
                    $insert = $this->getArray($perf);
                    $this->updatePerfil($perf, $insert);
                }
            }

                    private function canCreateProfile($plugin){
                        if(in_array($plugin, array('plugins', 'admin'))){
                            $msg = "Você não pode criar um perfil de usuário no plugin $plugin";
                            \classes\Utils\Log::save(LOG_INSTALACAO, $msg);
                            $this->setAlertMessage($msg);
                            return false;
                        }
                        return true;
                    }

                    private function getArray($perf){
                        $insert = array();
                        foreach($perf as $key => $val){
                            if(is_array($val)){continue;}
                            $insert["usuario_perfil_$key"] = $val;
                        }
                        return $insert;
                    }

                    private function updatePerfil($perf, $insert){
                        $where      = "usuario_perfil_nome = '".$perf['nome']."' OR usuario_perfil_cod = '{$perf['cod']}'";
                        $var        = $this->up->selecionar(array('usuario_perfil_nome'), $where, 1);
                        if(empty($var) && false === $this->up->inserir($insert)){
                            $this->erro[] = implode("<br/>", $this->up->getMessages());
                        }
                    }
    
            private function hasError(){
                if(empty($this->erro)) {
                    \classes\Utils\Log::save(LOG_INSTALACAO, "registerAction concluído sem erros!");
                    return true;
                }

                \classes\Utils\Log::save(LOG_INSTALACAO, $this->erro);
                $erro = implode ("<hr/>", $this->erro);
                $this->setAlertMessage($erro);

                \classes\Utils\Log::save(LOG_INSTALACAO, "Fim do register Actions");
                return false;
            }
}
