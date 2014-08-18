<?php
class plugComponent extends classes\Component\Component{
     //public $list_in_table = true;
    
    protected $show_item_class = 'span3 box-content';
     protected $listActions = array(
         'Detalhes'      => 'show',
     );
     
     public function advanced($model, $item) {
        $this->drawTitle($item);
         $this->gui->opendiv('', 'span12');
         $this->gui->closediv();
         $opt = array('plugin' => $item['cod_plugin']);
         $this->permissionWidget($opt);
         $this->gui->separator();
         $this->modelWidget($opt);
     }
     
     
     private function modelWidget($opt,$span = 'span6'){
         $this->gui->opendiv('', $span);
            \classes\Component\widget::executeWidgets(array(
                'plugins/model/widgets/modelWidget' => $opt
            ));
         $this->gui->closediv();
     }
     
     private function permissionWidget($opt){
         $this->gui->opendiv('', 'span6');
            \classes\Component\widget::executeWidgets(array(
                'plugins/permissao/widgets/permissionsWidget' => $opt
            ));
         $this->gui->closediv();
     }
     
     public function listar($model, $itens, $title = "", $class = '') {
         $this->Html->LoadCss('plugins');
         $temp = array();
         foreach($itens as $c => $i) $temp[$i['status']][$c] = $i;
         
         foreach($temp as $status => $all){
             echo "<div class='plugitem'>";
                $this->gui->Title(ucfirst($status));
                echo "<hr/>";
                parent::listar($model, $all, $title, $class);
             echo "</div>";
         }
         
     }
     
     public function DrawItem($model, $pkey, $item, $dados){
         $versao  = $item['versao'];
         $lversao = $item['lastversao'];
         $preco   = $item['preco'];
         unset($item['preco']);
         unset($item['versao']);
         $url = $this->getUrlImage($item['plugnome']);
         $item['image']      = "<img src='$url'/>";
         $item['versao']     = $versao;
         $item['lastversao'] = $lversao;
         $item['preco']      = $preco;
         parent::DrawItem($model, $pkey, $item, $dados);
     }
     
     private function getUrlImage($plugin){
         $alternative_path = \classes\Classes\Registered::getTemplateLocation(CURRENT_TEMPLATE) . "/img/plugins_icon/{$plugin}_120.png";
         $relative_path = "/Config/img/img_120.png";
         if(file_exists(\classes\Classes\Registered::getTemplateLocation(CURRENT_TEMPLATE, true) .$alternative_path)) {$url = URL_TEMPLATES.$alternative_path;}
         elseif(file_exists(\classes\Classes\Registered::getPluginLocation($plugin, true).$relative_path)){
             $url = URL.\classes\Classes\Registered::getPluginLocation ($plugin).$relative_path;
         }
         else {$url = $this->Html->getUrlImage('plugins_icon/plugins_120.png', false);}
         return $url;
     }
     
     public function format_preco($valor){
         $texto = (CURRENT_ACTION == "show")?"":"Valor Mensal: ";
         $preco = ($valor == '0.00')?'Gratuito':"R$ ".number_format($valor, 2, ",", ".");
         $var = "$texto $preco" ;
         if($texto != "") $var .= "<hr/>";
         return $var;
     }
     
     public function format_versao($valor, $arr, $item){
         $status = isset($item['__status'])?$item['__status']:$item['status'];
         if($status == 'desinstalado'){
             if(CURRENT_ACTION == "show") return "Aplicativo não instalado";
             return;
         }
         if(isset($item['lastversao']) && (int)$item['lastversao'] > (int)$valor){
             return (CURRENT_ACTION == "show")?"$valor":"Versão: $valor <br/> ";
         }
         return (CURRENT_ACTION == "show")?"$valor":"Versão: Última <br/> ";
     }
     
     public function format_lastversao($valor){
         return (CURRENT_ACTION == "show")?$valor:"";
     }
     
}