<?php
if (!defined('_PS_VERSION_')) {
  exit;
}
 
class fbchat extends Module
{
  public function __construct()
  {
    $this->name = 'fbchat';
    $this->tab = 'front_office_features';
    $this->version = '1.0.0';
    $this->author = 'Paweł Ługowski';
    $this->need_instance = 0;
    $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6'); 
    $this->bootstrap = true;
 
    parent::__construct();
 
    $this->displayName = $this->l('Facebook Chat');
    $this->description = $this->l('Widget czatu dla Prestashop 1.6');
 
    $this->confirmUninstall = $this->l('Jesteś pewien, że chcesz odinstalować ten moduł?');
 
    if (!Configuration::get('fbchat')) {
      $this->warning = $this->l('Brak pliku konfiguracyjnego');
    }
  }
    public function install()
{
  if (Shop::isFeatureActive()) {
    Shop::setContext(Shop::CONTEXT_ALL);
      
      return parent::install() &&
          $this->registerHook('leftColumn') &&
    $this->registerHook('header') &&
    $this->registerHook('displayFooter') &&
    Configuration::updateValue('fbchat', 'fbczat') &&
    Configuration::updateValue('fbpageid1', 'Id Twojej strony Facebook') &&
    Configuration::updateValue('fbwiadomzal1', 'Wiadomość powitalna dla klientów zalogowanych') &&
    Configuration::updateValue('fbwiadomwyl1', 'Wiadomość powitalna dla klientów niezalogowanych');
          
  }
 
  if (!parent::install()
    
  ) {
    return false;
  }
 
    //    DB::getInstance()->execute(
 //  "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "fbchat` (
 //     `fb_id_page` varchar(64) NOT NULL,
 //     `fb_wiad_zal` varchar(128) NOT NULL,
 //     `fb_wiad_wyl` varchar(128) NOT NULL,
 //     PRIMARY KEY (`fb_id_page`)
 //   ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;"
//);
        
        
        
  return true;
}
    public function uninstall()
{
  if (!parent::uninstall() ||
    !Configuration::deleteByName('fbchat')
  ) {
    return false;
  }
 
  return true;
}
    public function hookDisplayFooter($params)
{
  $this->context->smarty->assign(
      array(
          'fbpageide' => Configuration::get('fbpageid1'),
          'fbwiadomzale' => Configuration::get('fbwiadomzal1'),
          'fbwiadomwyle' => Configuration::get('fbwiadomwyl1')
      )
  );
  return $this->display(__FILE__, 'fbchat.tpl');
}
   
    public function getContent()
{
    $output = null;
 
    if (Tools::isSubmit('submit'.$this->name))
    {
       
            Configuration::updateValue('fbpageid1', Tools::getValue('fbpageid'));
            Configuration::updateValue('fbwiadomzal1', Tools::getValue('fbwiadomzal'));
            Configuration::updateValue('fbwiadomwyl1', Tools::getValue('fbwiadomwyl'));
            $output .= $this->displayConfirmation($this->l('Zapisano ustawienia'));
        }
    
    return $output.$this->displayForm();
}

   public function displayForm()
{
    // Get default language
    $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
     
    // Init Fields form array
    $fields_form[0]['form'] = array(
        'legend' => array(
            'title' => $this->l('Ustawienia'),
        )
         );
      $fields_form[1]['form']['input'][] = array( 
        
           
                'type' => 'text',
                'label' => $this->l('FB page ID'),
                'name' => 'fbpageid',
                'size' => 20,
                'required' => true,
               'class' => 'col-md-6'
            
            );
          $fields_form[2]['form']['input'][] = array( 
        
                'type' => 'text',
                'label' => $this->l('Wiadomość dla zalogowanych użytkowników'),
                'name' => 'fbwiadomzal',
                'size' => 20,
                'required' => true,
               'class' => 'col-md-6'
           
              );
          $fields_form[3]['form']['input'][] = array( 
           
                'type' => 'text',
                'label' => $this->l('wiadomość dla wylogowanych użytkowników'),
                'name' => 'fbwiadomwyl',
                'size' => 20,
                'required' => true,
               'class' => 'col-md-6'
           
              );
        $fields_form[4]['form']['submit'] = array(
				'title' => $this->l('Zapisz')
			);
     
    $helper = new HelperForm();
     
    // Module, token and currentIndex
    $helper->module = $this;
    $helper->name_controller = $this->name;
    $helper->token = Tools::getAdminTokenLite('AdminModules');
    $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
     
    // Language
    $helper->default_form_language = $default_lang;
    $helper->allow_employee_form_lang = $default_lang;
     
    // Title and toolbar
    $helper->title = $this->displayName;
    $helper->show_toolbar = true;        // false -> remove toolbar
    $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
    $helper->submit_action = 'submit'.$this->name;
    $helper->toolbar_btn = array(
        'save' =>
        array(
            'desc' => $this->l('Save'),
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
            '&token='.Tools::getAdminTokenLite('AdminModules'),
        ),
        'back' => array(
            'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Back to list')
        )
    );
     
    // Load current value
    $helper->fields_value['fbpageid'] = Configuration::get('fbpageid1');
    $helper->fields_value['fbwiadomzal'] = Configuration::get('fbwiadomzal1');
    $helper->fields_value['fbwiadomwyl'] = Configuration::get('fbwiadomwyl1');
     
    return $helper->generateForm($fields_form);
}
  
    
}
