<?php

class plugins_hatappData extends \classes\Model\DataModel{
    
    protected $dados  = array(
       
        'cod'  => array(
            'name'    => "Código",
            'pkey'    => true,
            'ai'      => true,
            'grid'    => true,
            'type'    => 'int',
            'display' => true,
            'private' => true,
            'notnull' => true
        ),
        'url' => array(
            'name' 	=> 'Nome',
            'type' 	=> 'varchar',
//            'especial'  => 'url',
            'title'     => true,
            'size' 	=> '128',
            'notnull'   => true,
            'display'   => true,
            'grid'      => true,
        ),
        'passwd' => array(
            'name' 	=> 'Senha',
            'type' 	=> 'text',
        ),
        'user' => array(
            'name' 	=> 'Usuário',
            'type' 	=> 'varchar',
            'grid'      => true,
            'title'     => true,
            'size' 	=> '11',
            'display'   => true,
            'notnull'   => true
        ),
        
        
    );
    
}