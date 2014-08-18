<?php 
 use classes\Controller\CController;
class modelController extends CController{
    public $model_name = 'plugins/model';
    
    public function show($display = true, $link = ''){
        parent::show($display, 'plugins/model/model');
    }
    
}