<?php

class plugins_actionData extends \classes\Model\DataModel{
    public $dados  = array(
        'plugins_action_cod' => array(
	    'name'     => 'Código',
	    'type'     => 'int',
	    'size'     => '11',
	    'pkey'    => true,
	    'ai'      => true,
	    'grid'    => true,
	    'display' => true,
	    'private' => true
        ),
        'plugins_model_cod' => array(
	    'name'     => 'Modelo',
	    'type'     => 'int',
	    'size'     => '11',
	    'grid'    => true,
	    'display' => true,
            'notnull' => true,
	    'fkey' => array(
	        'model' => 'plugins/model',
	        'cardinalidade' => '1n',
	        'keys' => array('plugins_model_cod', 'plugins_model_label'),
	    ),
        ),
         'plugins_action_nome' => array(
	    'name'     => 'Nome',
	    'type'     => 'varchar',
	    'size'     => '64',
            'unique'   => array('model' => 'plugins/action'),
	    'index'   => true,
	    'grid'    => true,
	    'display' => true,
        ),
         'plugins_action_label' => array(
	    'name'     => 'Label',
	    'type'     => 'varchar',
	    'size'     => '32',
	    'grid'    => true,
	    'display' => true,
        ),
        'plugins_permissao_cod' => array(
	    'name'     => 'Permissão',
	    'type'     => 'int',
	    'size'     => '11',
	    'grid'    => true,
	    'display' => true,
	    'fkey' => array(
	        'model' => 'plugins/permissao',
	        'cardinalidade' => '1n',
	        'keys' => array('plugins_permissao_cod', 'plugins_permissao_label'),
	    ),
        ),
         'plugins_action_groupyes' => array(
	    'name'     => 'Padrão Sim',
	    'type'     => 'enum',
	    'default' => 's',
	    'options' => array(
	    	's' => 's',
	    	'n' => 'n',
	    	'p' => 'p',
	    ),
	    'notnull' => true,
	    'grid'    => true,
	    'display' => true,
            'description' => "Usuários que possuem a permissão especificada terão que tipo de acesso ao plugin?"
        ),
         'plugins_action_groupno' => array(
	    'name'     => 'Padrão Não',
	    'type'     => 'enum',
	    'default' => 'n',
	    'options' => array(
	    	's' => 's',
	    	'n' => 'n',
	    	'p' => 'p',
	    ),
	    'grid'    => true,
	    'display' => true,
            'description' => "Usuários que não possuem a permissão especificada terão que tipo de acesso ao plugin?"
        ),
        'plugins_action_privacidade' => array(
	    'name'     => 'Privacidade',
	    'type'     => 'enum',
	    'default' => 'publico',
	    'options' => array(
	    	'publico' => 'publico',
	    	'privado' => 'privado',
	    ),
	    'notnull' => true,
	    'grid'    => true,
	    'display' => true,
        ),
        
        'plugins_action_needcod' => array(
	    'name'     => 'Necessita código',
	    'type'     => 'enum',
	    'default' => 's',
	    'options' => array(
	    	's' => 'Sim',
	    	'n' => 'Não',
	    ),
	    'notnull' => true,
	    'grid'    => true,
	    'display' => true,
            'description' => 'Algumas ações como visualizar detalhes de um dado só fazem sentido quando
                o código deste dado é fornecido.'
        ),
        
        'plugins_action_sendnotify' => array(
	    'name'     => 'Envia Notificação',
	    'type'     => 'enum',
	    'default' => 'n',
	    'options' => array(
	    	's' => 'Sim',
	    	'n' => 'Não',
	    ),
	    'notnull' => true,
	    'grid'    => true,
	    'display' => true,
            'description' => "Marque sim caso este plugin envia alguma notificação para um usuário.
                Esta opção será utilizada no plugin de notificação para que o usuário escolha como 
                prefere ser notificado.",
        ),
        
        'plugins_action_title' => array(
	    'name'     => 'Título da página',
	    'type'     => 'varchar',
	    'size'     => '64',
	    'index'   => true,
	    'grid'    => true,
	    'display' => true,
        ),
        'plugins_action_description' => array(
	    'name'     => 'Descrição da página',
	    'type'     => 'varchar',
	    'size'     => '256',
	    'index'   => true,
	    'grid'    => true,
	    'display' => true,
        ),
        'plugins_action_tags' => array(
	    'name'     => 'Tags da página',
            'description' => 'Separe as tags por vírgulas',
	    'type'     => 'varchar',
	    'size'     => '120',
	    'index'   => true,
	    'grid'    => true,
	    'display' => true,
        ),
        'button' => array(
            'button' => 'Salvar Ação'
        )
    );
}