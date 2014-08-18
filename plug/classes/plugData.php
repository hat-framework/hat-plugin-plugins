<?php

class plugins_plugData extends \classes\Model\DataModel{
    
    protected $dados  = array(
       
        'cod_plugin'  => array(
            'name'    => "cod_plugin",
            'pkey'    => true,
            'ai'      => true,
            'grid'    => true,
            'type'    => 'int',
            'display' => true,
            'private' => true,
            'notnull' => true
        ),
        'plugnome' => array(
            'name' 	=> 'Nome',
            'type' 	=> 'varchar',
            'especial'  => 'hide',
            'size' 	=> '50',
            'unique'    => array('model' => 'plugins/plug'),
            'notnull'   => true,
            'display'   => true,
            'grid'      => true,
        ),
        'pluglabel' => array(
            'name' 	=> 'Label',
            'type' 	=> 'varchar',
            'grid'      => true,
            'title'     => true,
            'size' 	=> '32',
            'display'   => true,
            'notnull'   => true
        ),
        
        'versao' => array(
            'name' 	=> 'Versão Instalada',
            'type' 	=> 'varchar',
            'size' 	=> '8',
            'display'   => true,
            'notnull'   => true,
            'default'   => '1.0',
        ),
        
        'lastversao' => array(
            'name' 	=> 'Última Versão',
            'type' 	=> 'varchar',
            'size' 	=> '8',
            'display'   => true,
            'notnull'   => true,
            'default'   => '1.0',
        ),

        'status'    => array(
            'name'    => 'Status',
            'type'    => 'enum',
            'grid'    => true,
            'notnull' => true,
            'display' => true,
            'especial' => 'hide',
            'default' => 'desinstalado',
            'label'   => true,
            'options' => array(
                'desinstalado' => 'desinstalado',
                'instalado'    => 'instalado',
                'desativado'   => 'desativado'
            )
        ),
        'isdefault' => array(
	    'name'    => 'Padrão',
	    'type'    => 'enum',
            'default' => 'n',
            'especial' => 'hide',
            'options' => array(
                's' => 'Sim',
                'n' => 'Não',
             ),
	    'grid'    => true,
            'notnull' => true,
	    'display' => true,
        ),
        
        'system' => array(
	    'name'    => 'Plugin do Sistema',
	    'type'    => 'enum',
            'default' => 'n',
            'especial' => 'hide',
            'options' => array(
                's' => 'Sim',
                'n' => 'Não',
             ),
	    'grid'    => true,
            'notnull' => true,
	    'display' => true,
        ),
        
        'preco' => array(
            'name' 	=> 'Preço Mensal',
            'type' 	=> 'float',
            'size' 	=> '(20,2)',
            'especial'  => 'monetary',
            'grid'      => true,
            'notnull'   => true,
            'display'   => true,
        ),
        
        'periodo' => array(
            'name' 	=> 'Período de Testes',
            'type' 	=> 'int',
            'size' 	=> '4',
        ),
        
        'instaladoem' => array(
            'name' 	=> 'Data de Instalação',
            'type' 	=> 'date',
        ),
        
        'terminotestes' => array(
            'name' 	=> 'Término do Período de testes',
            'type' 	=> 'date',
        ),
        
        'detalhes' => array(
            'name' 	=> 'Detalhes do plugin',
            'type' 	=> 'text',
        ),
        
    );
    
}