<?php
/**
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    Massimiliano Palermo <info@mpsoft.it>
*  @copyright 2007-2018 Digital Solutions®
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}
if (!class_exists('MpInstallClass')) {
    require_once dirname(__file__).'/classes/MpInstallClass.php';
}
if (!class_exists('MpInvoiceListHelperList')) {
    require_once dirname(__file__).'/classes/MpInvoiceListHelperList.php';
}

class MpInvoiceList extends Module
{
    protected $install;
    
    public function __construct()
    {
        $this->name = 'mpinvoicelist';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Digital Solutions®';
        $this->need_instance = 0;
        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('MP Invoice list display');
        $this->description = $this->l('This module shows invoice list in Admin Invoices tab');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->install = new MpInstallClass($this);
        $this->smarty = Context::getContext()->smarty;
    }
    
    public function getPath()
    {
        return $this->local_path;
    }
    
    public function getUrl()
    {
        return $this->path;
    }
    
    public function setError($error)
    {
        $this->_errors[] = $error;
    }
    
    public function setConfirmation($message)
    {
        $this->_confirmations[] = $message;
    }
    
    public function setWarning($warning)
    {
        $this->_warnings[] = $warning;
    }
    
    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayAdminForm');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('controller', '') == 'AdminInvoices') {
            $this->context->controller->addJqueryPlugin('validate');
            $list = new MpInvoiceListHelperList($this);
            $content = $list->initList();
            //print "<pre><xmp>" . $content . "</xmp></pre>";
            $this->strReplace(
                '<div class="panel col-lg-12">',
                '<div class="panel col-lg-12" ' 
                .'id="invoice_list_panel" '
                .'style="display: none"'
                .'>',
                $content
            );
            
            $script = $this->smarty->fetch($this->getPath().'views/templates/admin/invoice_list.tpl');
            $this->strReplace('</form>', $content."</form>", $script);
            return $script;
        }
    }
    
    public function strReplace($search, $replace, &$subject)
    {
        $subject = str_replace($search, $replace, $subject); 
        return $subject;
    }
    
    public function hookDisplayAdminForm()
    {
        
    }
}
