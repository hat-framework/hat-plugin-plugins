<?php

class registerSetupPlugin extends classes\Classes\Object implements \install_subsystem{
    
    private $method = "install";
    public function setMethod($method){
        $this->method = $method;
        return $this;
    }
    public function register($plugin, $cod){
        $class = "\\{$plugin}Install";
        $file  = classes\Classes\Registered::getPluginLocation($plugin, true)."/Config/$class.php";
        getTrueDir($file);
        if(!file_exists($file)){die("Arquivo $file não existe!"); return true;}
        require_once $file;
        if(!class_exists($class, false)){
            return $this->setErrorMessage("Erro ao instalar plugin: Classe $class não existe! (embora o arquivo da classe exista!)");
        }
        $method       = $this->method;
        $this->method = 'install';
        $obj = new $class();
        if(!method_exists($obj, $method)){return true;}
        return $this->propagateMessage($obj, $method);
    }
    
}