<?php

class menuChanger{
    public function change($item){
        $this->item = $item;
        $this->checkUpdate();
        $method_name = $this->item['__status'];
        if(method_exists($this, $method_name)){
            $this->$method_name();
        }
        $this->isSystem();
    }
    
            private function checkUpdate(){
                $is_webmaster = usuario_loginModel::IsWebmaster();
                if($this->item['versao'] != $this->item['lastversao']) {
                    $this->LoadResource('html', 'html');
                    $var = $this->html->getActionLinkIfHasPermission('plugins/plug/update', 'Clique aqui');
                    $this->registerVar('alert', 'Você não está usando a vesão mais recente deste plugin. '.$var.' para atualizar o plugin');
                }elseif(!$is_webmaster) {classes\Classes\EventTube::removeItemFromMenu ('body-top', 'Atualizar');}
            }
            
            private function instalado(){
                classes\Classes\EventTube::removeItemFromMenu ('body-top', 'Instalar Aplicativo');
                classes\Classes\EventTube::removeItemFromMenu ('body-top', 'Desbloquear');
                classes\Classes\EventTube::removeItemFromMenu ('body-top', 'Ativar');
                classes\Classes\EventTube::removeItemFromMenu ('body-top', 'Desativar');
            }
            
            private function desinstalado(){
                classes\Classes\EventTube::removeItemFromMenu ('body-top', 'Acessar Aplicativo');
                classes\Classes\EventTube::removeItemFromMenu ('body-top', 'Atualizar Tudo');
                classes\Classes\EventTube::removeItemFromMenu ('body-top', 'Update Specific');
                classes\Classes\EventTube::removeItemFromMenu ('body-top', 'Marcar como Padrão');
                classes\Classes\EventTube::removeItemFromMenu ('body-top', 'Desativar Plugin');
                classes\Classes\EventTube::removeItemFromMenu ('body-top','Desinstalar Aplicativo');
                classes\Classes\EventTube::removeItemFromMenu ('body-top','Popular Banco de dados');
                classes\Classes\EventTube::removeItemFromMenu ('body-top','Desbloquear');
                classes\Classes\EventTube::removeItemFromMenu ('body-top','Atualizar');
                classes\Classes\EventTube::removeItemFromMenu ('body-top', 'Ativar');
                classes\Classes\EventTube::removeItemFromMenu ('body-top','Desativar');
            }
            
            private function desativado(){
                classes\Classes\EventTube::removeItemFromMenu ('body-top','Desinstalar Aplicativo');
                classes\Classes\EventTube::removeItemFromMenu ('body-top','Desativar');
                classes\Classes\EventTube::removeItemFromMenu ('body-top','Atualizar');
                classes\Classes\EventTube::removeItemFromMenu ('body-top','Instalar Aplicativo');
                classes\Classes\EventTube::removeItemFromMenu ('body-top','Popular');
            }
            
            private function isSystem(){
                if($this->item['__system'] != 's'){return;}
                classes\Classes\EventTube::removeItemFromMenu ('body-top','Desinstalar Aplicativo');
                classes\Classes\EventTube::removeItemFromMenu ('body-top','Desativar');
                classes\Classes\EventTube::removeItemFromMenu ('body-top','Ativar');
            }
}