<?php 
 use classes\Controller\CController;
class actionController extends CController{
    public $model_name = 'plugins/action';
    
    public function show($display = true, $link = "plugins/action/show") {
        $exp = explode('/',$this->item['plugins_action_nome']);
        $this->registerVar('plugin', $exp[0]);
        $this->registerVar('subPlugin', $exp[1]);
        $this->registerVar('action', $exp[2]);
        parent::show($display, $link);
    }
    
    public function index($display = true, $link = "plugins/action/index") {
        parent::index($display, $link);
    }
    
    
}