<?php

class pluginsInstall extends classes\Classes\InstallPlugin{
    
    protected $dados = array(
        'pluglabel' => 'Aplicativos',
        'isdefault' => 'n',
        'system'    => 's',
        'versao'    => '1.0',
    );
    
    public function install(){
        return true;
    }
    
    public function unstall(){
        return true;
    }
}