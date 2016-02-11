<?php

class listarPlugins extends \classes\Classes\Object{
    
    private $webmasterlist = array("gerador");
    private $blacklist     = array("usuario", "admin", ".DS_Store");
    public function __construct() {
        $this->LoadModel("usuario/login", 'uobj');
        $this->LoadModel("plugins/plug" , 'plug');
        if($this->uobj->UserIsWebmaster()){
            $this->blacklist = array(".DS_Store");
        }
    }
    
    public function getList(){
        $inseriu = false;
        $plugins = $this->getPluginsList();
        
        //atualiza a visibilidade dos plugins de acordo com a permissao de webmaster
        $arq = $this->getPluginsLocation($plugins, $inseriu);

        //remove os plugins cujas pastas foram removidas mas que permaneceram no banco de dados
        $this->filterPluginsRemovedFolders($plugins, $arq);
        
        //recupera os plugins novamente caso algum tenha sido inserido
        return $this->getPlugins($plugins, $inseriu);
    }
    
            private function getPluginsList(){
                $data    = $this->plug->selecionar();
                $plugins = array();
                foreach($data as $arr) {$plugins[$arr['plugnome']] = $arr;}
                return $plugins;
            }
    
            private function getPluginsLocation($plugins, &$inseriu){
                $arq  = array_keys(classes\Classes\Registered::getAllPluginsLocation());
                $webm = $this->uobj->UserIsWebmaster();
                foreach($arq as $plugname){
                    //insere os plugins que não existem
                    if(!array_key_exists($plugname, $plugins)) {
                        $inseriu = true;
                        $this->inserirPlugin($plugname);
                        $plugins[$plugname] = ucfirst($plugname);
                    }
                    if(in_array($plugname, $this->blacklist)){
                        unset($plugins[$plugname]);
                    }
                    if(!$webm && in_array($plugname, $this->webmasterlist)) {unset($plugins[$plugname]);}
                }
                return $arq;
            }
    
            private function filterPluginsRemovedFolders(&$plugins, $arq){
                foreach($plugins as $pname => $parr){
                    if(in_array($pname, $arq)) {continue;}
                    $this->unstall($pname);
                    unset($plugins[$pname]);
                }
            }
    
            private function getPlugins($plugins, $inseriu){
                if($inseriu){
                    $data    = $this->plug->selecionar();
                    $plugins = array();
                    foreach($data as $arr) {$plugins[$arr['plugnome']] = $arr;}
                }

                return $plugins;
            }
    
}