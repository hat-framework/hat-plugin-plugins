<?php 
 use classes\Controller\CController;
use classes\Classes\EventTube;
class hatappController extends CController{
    public $model_name = "plugins/hatapp";
    
    public function import(){
        $this->display(LINK . "/import");
    }
}
