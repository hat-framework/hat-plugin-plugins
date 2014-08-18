<?php
class permissaoComponent extends classes\Component\Component{
    protected $show_item_class = 'span4 box-content';
    
    public function show($model, $item) {
        $this->gui->opendiv('', 'span12');
        parent::show($model, $item);
        $this->gui->closediv();
        
        $opt = array('permissao' => $item['plugins_permissao_cod']);
        $this->Widget($opt);
    }
    
    private function Widget($opt){
         $this->gui->opendiv('', 'span6');
            \classes\Component\widget::executeWidgets(array(
                'plugins/acesso/widgets/acessoWidget' => $opt
            ));
         $this->gui->closediv();
         
         $this->gui->opendiv('', 'span6');
            \classes\Component\widget::executeWidgets(array(
                'plugins/action/widgets/actionWidget' => $opt
            ));
         $this->gui->closediv();
     }
}
