<?php

class registerSetupPlugin extends classes\Classes\Object implements \install_subsystem{
    
    public function register($plugin, $cod){
        $class = "\\{$plugin}Install";
        $file  = classes\Classes\Registered::getPluginLocation($plugin, true)."/Config/$class.php";
        getTrueDir($file);
        if(!file_exists($file)){die("Arquivo $file não existe!"); return true;}
        require_once $file;
        if(!class_exists($class, false)){
            return $this->setErrorMessage("Erro ao instalar plugin: Classe $class não existe! (embora o arquivo da classe exista!)");
        }
        $obj = new $class();
        return $this->propagateMessage($obj, 'install');
    }
    
}