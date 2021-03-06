<?php

class startHelper extends \classes\Classes\Object{
    
    public function __construct() {
        $this->LoadClassFromPlugin('plugins/plug/inclasses/registerModels', 'rmds');
        $this->LoadResource("database/creator"  , 'iobj')->setLogName(LOG_INSTALACAO);
        $this->LoadResource('files/dir'         , 'dobj');
        $this->LoadModel("plugins/plug"         , "plug");
        $this->LoadResource("database"          , "db");
    }
    
    //carrega o modulo de instalacao
    public function start($plugin, $type){
        \classes\Utils\Log::save(LOG_INSTALACAO, "Realizando as mudanças dos models no banco de dados ($type)");
        $bool = true;
        if($type === "update"){
            //método install é chamado para que as dependências do plugin sejam instaladas
            $bool = $bool and $this->iobj->install($plugin);
        }
        $b = $bool and $this->iobj->$type($plugin);
        $this->setMessages($this->iobj->getMessages());
        $subplugins = $this->iobj->getPlugin($plugin);
        return $b and ($type == "unstall")? $this->deleteModels($plugin):$this->registerModels($plugin, $subplugins);
    }
    
            private function deleteModels($plugin){
                \classes\Utils\Log::save(LOG_INSTALACAO, "Apagando os modelos do sistema");
                $item = $this->getPluginItem($plugin);
                if($item === false){return false;}

                $this->LoadModel('plugins/model', 'smd')->apagar($item['cod_plugin'], "cod_plugin");
                $tb1 = $this->LoadModel('plugins/action', 'act')->getTable();
                $tb2 = $this->smd->getTable();
                $this->db->ExecuteQuery(
                    "ALTER TABLE $tb2 AUTO_INCREMENT = 1; ".
                    "ALTER TABLE $tb1 AUTO_INCREMENT = 1;"
                );
                return true;
            }

                    private function getPluginItem($plugin){
                        $item = $this->plug->getItem($plugin, 'plugnome');
                        if(empty($item)){
                            $erro = "Erro ao registrar subplugins: O plugin '$plugin' não foi registrado no banco de dados!";
                            \classes\Utils\Log::save(LOG_INSTALACAO, "Abortado! $erro");
                            return $this->setErrorMessage($erro);
                        }
                        return $item;
                    }

            private function registerModels($plugin, $subplugins){
                \classes\Utils\Log::save(LOG_INSTALACAO, "Iniciando o registro de modelos do plugin $plugin");
                //registra o plugin atual e os subplugins no sistema
                if(false == $this->runRegisterModel($plugin, $subplugins)){return false;}
                return $this->runInstallClasses($plugin);

            }
            
                    public function runRegisterModel($plugin, $subplugins = array()){
                        if(empty($subplugins)){$subplugins = $this->iobj->getPlugin($plugin);}
                        if(false === $this->rmds->register($plugin, $subplugins)){     
                            $messages = $this->rmds->getMessages();
                            \classes\Utils\Log::save(LOG_INSTALACAO, "Falha no registerModels!");
                            \classes\Utils\Log::save(LOG_INSTALACAO, $messages);
                            $this->setMessages($messages);
                            return false;
                        }
                        return true;
                    }

                    private function runInstallClasses($plugin){
                        $bool            = true;
                        $cod             = $this->rmds->getCodPlugin();
                        $install_classes = $this->FindInstallClasses();
                        if(false == $install_classes){return false;}
                        foreach($install_classes as $class){
                            $bool = $bool && $this->runInstallClass($plugin, $class, $cod);
                        }
                        return $bool;
                    }
                            private function FindInstallClasses(){
                                $dir = realpath(dirname(__FILE__) ."/../inclasses");
                                getTrueDir($dir);
                                $install_classes = $this->dobj->getArquivos("$dir");
                                foreach($install_classes as $cod => &$iclass){
                                    $iclass = str_replace('.php', '', $iclass);
                                    if($iclass === 'registerModels'){unset($install_classes[$cod]);}
                                }
                                
                                if(empty($install_classes)){
                                    \classes\Utils\Log::save(LOG_INSTALACAO, "Falha ao encontrar arquivos de instalação na pasta $dir!");
                                    return false;
                                }
                                return $install_classes;
                            }
                            
                            public function runInstallClass($plugin, $class, $cod_plugin){
                                $this->LoadClassFromPlugin("plugins/plug/inclasses/$class", 'r');
                                if(!($this->r instanceof install_subsystem)) {
                                    $msg = "$class Não é uma instância de install_subsystem ";
                                    \classes\Utils\Log::save(LOG_INSTALACAO, $msg);
                                    $this->setAlertMessage("$msg e não foi executado!");
                                    return true;
                                }

                                \classes\Utils\Log::save(LOG_INSTALACAO, "Executando $class");
                                if(!$this->r->register($plugin, $cod_plugin)){
                                    $msg = "Erro ao executar a classe $class!";
                                    $this->setAlertMessage($msg);
                                    \classes\Utils\Log::save(LOG_INSTALACAO, $msg);
                                    \classes\Utils\Log::save(LOG_INSTALACAO, $this->r->getMessages());
                                    $this->setMessages($this->r->getMessages());
                                    return false;
                                }
                                return true;
                            }
}

