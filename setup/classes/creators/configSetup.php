<?php

use classes\Classes\Object;
class configSetup extends classes\Classes\Object{
    
    public function __construct() {
        $this->LoadModel('site/confgrupo', 'cg')->findNewGroups();
        $this->LoadModel('site/configuracao', 'conf');
    }

    public function getMenuInstall(){
        $arr = $this->cg->getWebmasterGroups();
        $this->firstConfigPage($arr);
        $grupo = $this->cg->getItem($_GET['grupo']);
        $files = $this->conf->LoadFiles($_GET['grupo']);
        $next   = $this->getLinkOfNextPage($arr, $files);
        $this->setSimpleMessage('next_page', $next);
        $prev = $this->getLinkOfPreviousPage($arr, $files);
        if($prev != "") $this->setSimpleMessage('previous_page', $prev);
        //die($prev . "- $next");
        $out = array();
        foreach($arr as $name => $k){
            $k = @end(explode("/", $k));
            $out[$name] = ($k == $_GET['grupo'])?'active':'';
        }
        
        $cod_file = $_GET['file'];
        $fout     = array();
        foreach($files as $f){
            if($f['cod_cfile'] != $cod_file) continue;
            $fout[] = $f;
            break;
        }
        $this->setSimpleMessage('grupo', $grupo);
        $this->setSimpleMessage('files', $fout);
        return $out;
    }
    
    public function setupSystem(){
        return (isset($_GET['last']));
    }
    
    
    private function firstConfigPage($groups){
        if(isset($_GET['grupo'])) return;
        $cod_grupo = @end(explode("/", array_shift($groups)));
        $files     = $this->conf->LoadFiles($cod_grupo);
        $first     = array_shift($files);
        $cod_file  = $first['cod_cfile'];
        SRedirect($this->getUrl($cod_grupo, $cod_file));
    }
    
    private function getLinkOfPreviousPage($groups, $files){
        $files = array_reverse($files);
        $groups = array_reverse($groups);
        $nfile = $this->getNextFile($files);
        if($nfile != "") return $nfile; 
        return $this->getNextGroup($groups, true);
    }
    
    private function getLinkOfNextPage($groups, $files){
        $nfile = $this->getNextFile($files);
        if($nfile != "") return $nfile; 
        return $this->getNextGroup($groups);
    }
    
    private function getNextGroup($groups, $reverse = false){
        $cod_grupo = "";
        $break     = false;
        foreach($groups as $gr){
            $cod_grupo = @end(explode("/", $gr));
            if($break) break;
            if($cod_grupo == $_GET['grupo']) $break = true;
        }
        $files = $this->conf->LoadFiles($cod_grupo);
        if($reverse) $files = array_reverse($files);
        $first     = array_shift($files);
        $cod_file  = $first['cod_cfile'];
        if($cod_grupo != $_GET['grupo']) return $this->getUrl($cod_grupo, $cod_file);
        if($reverse) return "";
        $url = $this->getUrl($cod_grupo, $cod_file);
        return $url . '&last=1';
    }
    
    private function getNextFile($files){
        $break = false;
        if(empty($files)){return "";}
        foreach ($files as $f){
            if($break) break;
            if($_GET['file'] == $f['cod_cfile'])$break = true;
        }
        if($f['cod_cfile'] != $_GET['file']) return $this->getUrl($_GET['grupo'], $f['cod_cfile']);
        return "";
    }
    
    private function getUrl($cod_grupo, $cod_file){
        return URL ."admin/install.php?url=".CURRENT_ACTION."&grupo=$cod_grupo&file=$cod_file";
    }
    
}