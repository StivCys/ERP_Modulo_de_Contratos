// resources/js/config/menuConfig.js -  fonte única da verdade para itens de menu, rotas, ícones e permissões
export const menuConfig = [
  {
    key: 'dashboard',
    label: 'Dashboard',
    route: 'dashboard',
    icon: 'LayoutDashboard',
    type: 'link', // link direto, sem sidebar
  },
  {
    key: 'cadastros',
    label: 'Cadastros',
    type: 'group', // abre sidebar
    icon: 'Database',
    children: [
      {
        label: 'Clientes',
        route: 'cliente.index',
        icon: 'Users',
        permission: 'cliente.view', // chave do Spatie
      },
      {
        label: 'Serviços',
        route: 'servico.index',
        icon: 'Truck',
        permission: 'servico.view',
      },
      {
        label: 'Contratos',
        route: 'contrato.index',
        icon: 'Package',
        permission: 'contrato.view',
        // children: [ // sub-itens opcionais
        //   { label: 'Categorias', route: 'products.categories', permission: 'products.view' },
        //   { label: 'Unidades', route: 'products.units', permission: 'products.view' },
        // ],
      },
      {
        label: 'Regras de Negócio',
        route: 'regra-adicional.index',
        icon: 'Percent',
        permission: 'regra-adicional.view', // User can fix Spatie mapping later
      },
      {
        label: 'Meus Relatórios',
        route: 'relatorios.index',
        icon: 'FileText',
        permission: 'relatorio.view', // User can fix Spatie mapping later
      },
    ],
  },
  // {
  //   key: 'fiscal',
  //   label: 'Fiscal',
  //   type: 'group',
  //   icon: 'FileText',
  //   permission: 'fiscal.access', // permissão no grupo inteiro
  //   children: [
  //     { label: 'NF-e', route: 'fiscal.nfe', icon: 'Receipt', permission: 'nfe.view' },
  //     { label: 'NFS-e', route: 'fiscal.nfse', icon: 'Receipt', permission: 'nfse.view' },
  //   ],
  // },
  {
    key: 'configuracoes',
    label: 'Configurações',
    type: 'group',
    icon: 'Settings',
    permission: 'users.view', // User can fix Spatie mapping later
    children: [
      { label: 'Usuários', route: 'users.index', permission: 'users.view' },
      { label: 'Perfis de Acesso', route: 'roles.index', permission: 'roles.view' },
    ],
  },
]