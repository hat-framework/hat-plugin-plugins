<?php

use classes\Classes\Object;
class fileSetup extends classes\Classes\Object{
    
    private $client_dir = "";
    private $chmod      = 0777;
    public function __construct() {
        $this->LoadResource('files/dir', 'dobj');
        $this->LoadResource('files/file', "fobj");
        $this->client_dir = DIR_BASIC . DIR_SUB_DOMAIN;
    }
    
    public function setupSystem(){
        $this->createClientFolder();
        return $this->createBasicFolders();
    }
    
            private function createClientFolder(){
                if(!$this->dobj->create($this->client_dir, '', $this->chmod, true)){
                    $this->setMessages($this->dobj->getMessages());
                    return false;
                }

                if(!file_exists($this->client_dir)){
                    $this->setErrorMessage("Não foi possível criar o diretório ". DIR_SUB_DOMAIN);
                    return false;
                }
                return true;
            }

            private function createBasicFolders(){
                $folders = array('templates', 'static/files','config');
                foreach($folders as $f){
                    if(!$this->dobj->create($this->client_dir, $f, $this->chmod, true)){
                        $this->setMessages($this->dobj->getMessages());
                        return false;
                    }

                    if(!file_exists($this->client_dir.$f)){
                        $this->setErrorMessage("Não foi possível criar o diretório ". $this->client_dir.$f);
                        return false;
                    }

                }
                return true;
            }
    
}