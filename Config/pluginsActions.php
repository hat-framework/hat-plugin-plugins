<?php 
use classes\Classes\Actions;
class pluginsActions extends Actions{
    protected $permissions = array(
        "AcessoPlugin" => array(
            "nome"      => "Plugins_ACC",
            "label"     => "Gerenciar Aplicativos",
            "descricao" => "Permite visualizar, instalar e desinstalar os aplicativos do sistema",
            'default'   => "s"
        ),
        
        "GerenciarSistema" => array(
            "nome"      => "Plugins_GER",
            "label"     => "Gerenciar Sistema",
            "descricao" => "Permite gerenciar (Inserir, editar, apagar e visualizar) os dados
                referentes aos plugins. <b>CUIDADO!</b> Esta permissão deve ser dada apenas para 
                usuários administrativos e treinados pois ela pode desconfigurar o site e causar 
                perda definitiva de dados!",
            'default'   => "n"
        ),
    
        "GerenciarTutorial" => array(
            "nome"      => "Plugins_TUTO",
            "label"     => "Modificar Tutorial",
            "descricao" => "Permite gerenciar os dados dos tutoriais",
            'default'   => "n"
        ),
        
        "Analytics" => array(
            "nome"      => "Plugins_ANA",
            "label"     => "Analisar dados",
            "descricao" => "Permite analisar dados das actions",
            'default'   => "n"
        ),
    
    );
    
    protected $actions = array( 
        
        "plugins/index/index" => array(
            "label" => "plugins", "publico" => "s", "default_yes" => "s","default_no" => "n",
            "permission" => "Plugins_ACC",
            "menu" => array('plugins/action/index','plugins/model/index','plugins/permissao/index','plugins/acesso/index','plugins/plug/index','plugins/tutorial/index','plugins/completou/index',)
        ),
        
        'plugins/action/index' => array(
            'label' => 'action', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_ANA',
            'menu' => array('plugins/index/index', 'plugins/action/formulario')
        ),
        
        'plugins/action/formulario' => array(
            'label' => 'Criar action', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_GER',
            'menu' => array('plugins/action/index')
        ),
        
        'plugins/action/show' => array(
            'label' => 'Visualizar action', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_ANA', 'needcod' => true,
            'menu' => array('plugins/action/index', 'Ações' => array('plugins/action/edit', 'plugins/action/apagar'))
        ),
        
        'plugins/action/edit' => array(
            'label' => 'Editar action', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n', 
            'permission' => 'Plugins_GER', 'needcod' => true,
            'menu' => array('plugins/action/index', 'plugins/action/show')
        ),

        'plugins/action/apagar' => array(
            'label' => 'Apagar action', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_GER','needcod' => true,
            'menu' => array()
        ),

    
        
        'plugins/model/index' => array(
            'label' => 'model', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_GER',
            'menu' => array('plugins/index/index', 'plugins/model/formulario')
        ),
        
        'plugins/model/formulario' => array(
            'label' => 'Criar model', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_GER',
            'menu' => array('plugins/model/index')
        ),
        
        'plugins/model/show' => array(
            'label' => 'Visualizar model', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_GER', 'needcod' => true,
            'menu' => array('plugins/model/index', 'Ações' => array('plugins/model/edit', 'plugins/model/apagar'))
        ),
        
        'plugins/model/edit' => array(
            'label' => 'Editar model', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n', 
            'permission' => 'Plugins_GER', 'needcod' => true,
            'menu' => array('plugins/model/index', 'plugins/model/show')
        ),

        'plugins/model/apagar' => array(
            'label' => 'Apagar model', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_GER', 'needcod' => true,
            'menu' => array()
        ),

    
        
        'plugins/permissao/index' => array(
            'label' => 'permissao', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_GER',
            'menu' => array('plugins/index/index', 'plugins/permissao/formulario')
        ),
        
        'plugins/permissao/formulario' => array(
            'label' => 'Criar permissao', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_GER',
            'menu' => array('plugins/permissao/index')
        ),
        
        'plugins/permissao/show' => array(
            'label' => 'Visualizar permissao', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_GER', 'needcod' => true,
            'menu' => array('plugins/permissao/index', 'Ações' => array('plugins/permissao/edit', 'plugins/permissao/apagar'))
        ),
        
        'plugins/permissao/edit' => array(
            'label' => 'Editar permissao', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n', 
            'permission' => 'Plugins_GER', 'needcod' => true,
            'menu' => array('plugins/permissao/index', 'plugins/permissao/show')
        ),

        'plugins/permissao/apagar' => array(
            'label' => 'Apagar permissao', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_GER', 'needcod' => true,
            'menu' => array()
        ),

    
        
        'plugins/acesso/index' => array(
            'label' => 'acesso', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_GER',
            'menu' => array('plugins/index/index', 'plugins/acesso/formulario')
        ),
        
        'plugins/acesso/formulario' => array(
            'label' => 'Criar acesso', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_GER',
            'menu' => array('plugins/acesso/index')
        ),
        
        'plugins/acesso/show' => array(
            'label' => 'Visualizar acesso', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_GER', 'needcod' => true,
            'menu' => array('plugins/acesso/index', 'Ações' => array('plugins/acesso/edit', 'plugins/acesso/apagar'))
        ),
        
        'plugins/acesso/edit' => array(
            'label' => 'Editar acesso', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n', 
            'permission' => 'Plugins_GER', 'needcod' => true,
            'menu' => array('plugins/acesso/index', 'plugins/acesso/show')
        ),

        'plugins/acesso/apagar' => array(
            'label' => 'Apagar acesso', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_GER', 'needcod' => true,
            'menu' => array()
        ),

    
        
        'plugins/plug/index' => array(
            'label' => 'Central de Aplicativos', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_ACC',
            'menu' => array(
                'Configurações' => 'site/configuracao',
                'Menu Superior' => 'site/menu/index',
                'Atualizar Ações'     => 'plugins/plug/setactions',
             ),
            'breadscrumb' => array('plugins/plug/index')
        ),
        
        'plugins/plug/formulario' => array(
            'label' => 'Criar Aplicativo', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_GER',
            'menu' => array('plugins/plug/index'),
            'breadscrumb' => array('plugins/plug/index', 'plugins/plug/formulario')
        ),
        
        'plugins/plug/show' => array(
            'label' => 'Visualizar Aplicativo', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_ACC', 'needcod' => true,
            'menu' => array(
                'Instalar Aplicativo' => 'plugins/plug/install',
                'Acessar Aplicativo'  => 'plugins/plug/acesso',
                'Opções' => array(
                    'Avançado'  => 'plugins/plug/advanced',
                    'Editar'    => 'plugins/plug/edit', 
                    'Desativar' => 'plugins/plug/disable',
                    'Ativar'    => 'plugins/plug/enable',
                    'Atualizar' => 'plugins/plug/update',
                 ),
             ),
            'breadscrumb' => array('plugins/plug/index', 'plugins/plug/show')
        ),
        
        'plugins/plug/acesso' => array(
            'label' => 'Acessar Aplicativo', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_ACC', 'needcod' => true,
        ),
        
        'plugins/plug/advanced' => array(
            'label' => 'Avançado', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_ACC', 'needcod' => true,
            'menu' => array(
                'Voltar' => 'plugins/plug/show',
                'Instalar Aplicativo' => 'plugins/plug/install',
                'Desinstalar Aplicativo' => 'plugins/plug/unstall',
                'Opções' => array(
                    'Apagar'    => 'plugins/plug/apagar',
                    'Popular'   => 'plugins/plug/populate',
                 ),
             ),
            'breadscrumb' => array('plugins/plug/index', 'plugins/plug/show', 'plugins/plug/advanced')
        ),
        
        'plugins/permissao/show' => array(
            'label' => 'Visualizar Permissão', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_ACC', 'needcod' => true,
            'menu' => array('Ações' => array('plugins/permissao/edit', 'plugins/permissao/apagar')),
            'breadscrumb' => array('plugins/plug/index', 'plugins/plug/show', 'plugins/permissao/show')
        ),
        
        
        
        'plugins/permissao/edit' => array(
            'label' => 'Editar Permissão', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_ACC', 'needcod' => true,
            'breadscrumb' => array('plugins/plug/index', 'plugins/plug/show', 'plugins/permissao/show', 'plugins/permissao/edit')
        ),
        
        'plugins/model/show' => array(
            'label' => 'Visualizar modelo', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_ACC', 'needcod' => true,
            'menu' => array('Ações' => array('plugins/model/edit', 'plugins/model/apagar')),
            'breadscrumb' => array('plugins/plug/index', 'plugins/plug/show', 'plugins/model/show')
        ),
        
        'plugins/model/edit' => array(
            'label' => 'Editar Modelo', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_ACC', 'needcod' => true,
            'breadscrumb' => array('plugins/plug/index', 'plugins/plug/show', 'plugins/model/show', 'plugins/model/edit')
        ),
        
        'plugins/plug/updateall' => array(
            'label' => 'Atualizar todos', 'publico' => 's', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_ACC', 'needcod' => false,
            'menu' => array()
        ),
        
        'plugins/plug/install' => array(
            'label' => 'Instalar Aplicativo', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_ACC', 'needcod' => true,
            'menu' => array()
        ),
        
        'plugins/plug/unstall' => array(
            'label' => 'Desinstalar Aplicativo', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_ACC', 'needcod' => true,
            'menu' => array()
        ),
        
        'plugins/plug/disable' => array(
            'label' => 'Desativar Aplicativo', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_GER', 'needcod' => true,
            'menu' => array()
        ),
        
        'plugins/plug/enable' => array(
            'label' => 'Ativar Aplicativo', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_GER', 'needcod' => true,
            'menu' => array()
        ),
        
        'plugins/plug/populate' => array(
            'label' => 'Popular Aplicativo', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_GER', 'needcod' => true,
            'menu' => array()
        ),
        
        'plugins/plug/update' => array(
            'label' => 'Atualizar Aplicativo', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_ACC', 'needcod' => true,
            'menu' => array()
        ),
        
        'plugins/plug/api_update' => array(
            'label' => 'Atualizar Aplicativo via API', 'publico' => 's', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_ACC', 'needcod' => true,
            'menu' => array()
        ),
        
        'plugins/plug/edit' => array(
            'label' => 'Editar Aplicativo', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n', 
            'permission' => 'Plugins_GER', 'needcod' => true,
            'breadscrumb' => array('plugins/plug/index', 'plugins/plug/show', 'plugins/plug/edit')
        ),

        'plugins/plug/apagar' => array(
            'label' => 'Apagar Aplicativo', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_GER', 'needcod' => true,
            'menu' => array()
        ),

    
        
        'plugins/tutorial/index' => array(
            'label' => 'tutorial', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_TUTO',
            'menu' => array('plugins/index/index', 'plugins/tutorial/formulario')
        ),
        
        'plugins/tutorial/formulario' => array(
            'label' => 'Criar tutorial', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_TUTO',
            'menu' => array('plugins/tutorial/index')
        ),
        
        'plugins/tutorial/show' => array(
            'label' => 'Visualizar tutorial', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_TUTO', 'needcod' => true,
            'menu' => array('plugins/tutorial/index', 'Ações' => array('plugins/tutorial/edit', 'plugins/tutorial/apagar'))
        ),
        
        'plugins/tutorial/edit' => array(
            'label' => 'Editar tutorial', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n', 
            'permission' => 'Plugins_TUTO', 'needcod' => true,
            'menu' => array('plugins/tutorial/index', 'plugins/tutorial/show')
        ),

        'plugins/tutorial/apagar' => array(
            'label' => 'Apagar tutorial', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_TUTO', 'needcod' => true,
            'menu' => array()
        ),

    
        
        'plugins/completou/index' => array(
            'label' => 'completou', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_TUTO',
            'menu' => array('plugins/index/index', 'plugins/completou/formulario')
        ),
        
        'plugins/completou/formulario' => array(
            'label' => 'Criar completou', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_TUTO',
            'menu' => array('plugins/completou/index')
        ),
        
        'plugins/completou/show' => array(
            'label' => 'Visualizar completou', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_TUTO', 'needcod' => true,
            'menu' => array('plugins/completou/index', 'Ações' => array('plugins/completou/edit', 'plugins/completou/apagar'))
        ),
        
        'plugins/completou/edit' => array(
            'label' => 'Editar completou', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n', 
            'permission' => 'Plugins_TUTO', 'needcod' => true,
            'menu' => array('plugins/completou/index', 'plugins/completou/show')
        ),

        'plugins/completou/apagar' => array(
            'label' => 'Apagar completou', 'publico' => 'n', 'default_yes' => 's','default_no' => 'n',
            'permission' => 'Plugins_TUTO', 'needcod' => true,
            'menu' => array()
        ),
    
    );
    
     protected $perfis = array(
        'Analista_Informacao' => array(
            'cod'       => Analista_Informacao,
            'pai'       => Admin,
            'nome'      => 'Analista Informação',
            'default'   => '0',
            'tipo'      => 'usuario',
            'descricao' => 'Perfil destinado para analistas que terão acesso as métricas e aos acessos premium',
            'permissions' => array('Plugins_ANA'=> 's')
        ),
    );
    
}
