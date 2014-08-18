<?php

use classes\Classes\Object;
class installSetup extends classes\Classes\Object{
    
    private $basic = array('plugins' => 'plugins', 'admin' => 'admin', 'usuario' => 'usuario', 'site' => 'site');
    public function __construct() {
        $this->LoadModel('plugins/plug', 'plug');
        $this->LoadResource("database/creator", 'db_creator');
    }
    
    public function setupSystem(){
        $instaled  = $this->getInstaledTables();
        $needed    = $this->getNeededPlugins();
        $ninstaled = $this->getNotInstaledPlugin($needed, $instaled);
        $bool      = $this->findUnstaledNeededPlugins($ninstaled);
        if(is_bool($bool) && $bool === false) die('falha catastófica!');//return $bool;
        
        if(!$this->setupPlugins($ninstaled)) return false;
        if(!$this->findErros()) return false;
        $this->setSuccessMessage("Banco de dados instalado corretamente!");
        return true;
    }

    private function getInstaledTables(){
        $this->LoadResource('database', 'db');
        $tables = $this->db->showTables();
        $out    = array();
        foreach($tables as $tb){
            if(is_array($tb)) $out[] = array_shift($tb);
            else              $out[] = $tb;
        }
        return $out;
    }
    
    private function getNeededPlugins(){
        $basic = $this->basic;
        $plugins = $this->plug->getSystemPlugins();
        foreach($plugins as $p){
            $basic[$p] = $p;
        }
        return $basic;
    }
    
    private function getNotInstaledPlugin($needed, $instaled){
        $needinstall = array();
        foreach($needed as $module){
            $temp = $this->db_creator->getPlugin($module);
            foreach($temp as $t){
                $load = array_shift($t);
                try {
                    $this->LoadModel($load, 'temp');
                    if(!method_exists($this->temp, "getTable")) continue;
                    $tbs = $this->temp->getTable();
                    if($tbs == "") continue;
                    if(in_array($tbs, $instaled))continue;
                    $needinstall[$module] = $module;
                }catch (\classes\Exceptions\DBException $db){$this->erros[] = $db->getMessage();}
            }
        }
        return $needinstall;
    }
    
    private function findUnstaledNeededPlugins(&$ninstaled){
        $list = implode("','", $ninstaled);
        $plugs = $this->plug->selecionar(array('plugnome'), "status = 'desinstalado' AND plugnome in('$list')");
        if(!empty($plugs)){
            foreach($plugs as $p) $ninstaled[$p['plugnome']] = $p['plugnome'];
        }
        if(empty($ninstaled)) return $this->findErros();
    }
    
    private function setupPlugins($needinstall){
        $this->LoadClassFromPlugin('plugins/plug/plugSetup', 'psetup');
        foreach($needinstall as $nd){
            if($this->psetup->setup($nd) === false){
                $err = $this->psetup->getErrorMessage();
                if($err === "")$err = $this->psetup->getAlertMessage();
                $erro = ("Falha ao instalar o plugin $nd<br/> Detalhes: $err <br/>Método: ".__METHOD__);
                if(trim($erro) != "") $this->erros[] = $erro;
            }
        }

        //pog feia: a tabela de ações pode não ter sido criada no momento que o sistema estava sendo criado,
        //para resolver o problema faço um update para instalar as ações, menu, segurança, etc
        foreach($needinstall as $nd){
            $this->psetup->setPluginName($nd);
            if($this->psetup->registerModels() === false){
                $err = $this->psetup->getErrorMessage();
                if($err == "")$err = $this->psetup->getAlertMessage();
                
                $erro = ("Falha ao registrar o modelo $nd<br/> Detalhes: $err <br/>Método: ".__METHOD__);
                if(trim($erro) != "") $this->erros[] = $erro;
            }
        }

        return true;
    }
    
    private function findErros(){
        if(isset($this->erros) && !empty($this->erros)){
            $this->setErrorMessage(implode("<br/>", $this->erros));
            return false;
        }
        return true;
    }
    
}

?>