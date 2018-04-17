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
*  @author    Massimiliano Palermo <info@mpsoft.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class MpInvoiceListHelperList extends HelperListCore
{
    public $context;
    public $values;
    public $id_lang;
    public $module;
    public $link;
    protected $cookie;
    
    public function __construct($module)
    {
        $this->module = $module;
        $this->context = Context::getContext();
        $this->link = new LinkCore();
        $this->values = array();
        $this->id_lang = (int)$this->context->language->id;
        parent::__construct();
        $this->cookie = Context::getContext()->cookie;
    }
    
    public function initList()
    {
        $this->bootstrap = true;
        $this->actions = array('export');
        $this->currentIndex = $this->context->link->getAdminLink('AdminInvoices', false);
        $this->identifier = 'id_order_invoice';
        $this->no_link = true;
        $this->page = Tools::getValue('submitFilterconfiguration', 1);
        $this->_default_pagination = Tools::getValue('configuration_pagination', 20);
        $this->show_toolbar = true;
        $this->toolbar_btn = array(
            '' => array(
                'desc' => '',
                'href' => 'javascript:void(0);',
            )
        );
        $this->shopLinkType='';
        $this->simple_header = false;
        $this->token = Tools::getAdminTokenLite('AdminInvoices');
        $this->title = $this->module->l('Invoices found', get_class($this));
        $list = $this->getList();
        $fields_display = $this->getFields();
        
        return $this->generateList($list, $fields_display);
    }
    
    protected function getFields()
    {
        $list = array();
        $this->addText(
            $list,
            $this->module->l('Id invoice', get_class($this)),
            'id_order_invoice',
            32,
            'text-right'
        );
        $this->addHtml(
            $list,
            $this->module->l('Id order', get_class($this)),
            'id_order',
            32,
            'text-right'
        );
        $this->addText(
            $list,
            $this->module->l('Invoice number', get_class($this)),
            'invoice_number',
            32,
            'text-right'
        );
        $this->addDate(
            $list,
            $this->module->l('invoice date', get_class($this)),
            'invoice_date',
            'auto',
            'text-center',
            true
        );
        $this->addText(
            $list,
            $this->module->l('Customer', get_class($this)),
            'customer',
            'auto',
            'text-left'
        );
        $this->addPrice(
            $list,
            $this->module->l('Invoice amount', get_class($this)),
            'invoice_amount',
            'auto',
            'text-right'
        );
        $this->addHtml(
            $list,
            $this->module->l('Print invoice', get_class($this)),
            'invoice_print',
            32,
            'text-center'
        );
        
        return $list;
    }
    
    protected function addText(&$list, $title, $key, $width, $alignment, $search = false)
    {
        $item = array(
            'title' => $title,
            'width' => $width,
            'align' => $alignment,
            'type' => 'text',
            'search' => $search,
        );
        
        $list[$key] = $item;
    }
    
    protected function addDate(&$list, $title, $key, $width, $alignment, $search = false)
    {
        $item = array(
            'title' => $title,
            'width' => $width,
            'align' => $alignment,
            'type' => 'date',
            'search' => $search,
        );
        
        $list[$key] = $item;
    }
    
    protected function addPrice(&$list, $title, $key, $width, $alignment, $search = false)
    {
        $item = array(
            'title' => $title,
            'width' => $width,
            'align' => $alignment,
            'type' => 'price',
            'search' => $search,
        );
        
        $list[$key] = $item;
    }
    
    protected function addHtml(&$list, $title, $key, $width, $alignment, $search = false)
    {
        $item = array(
            'title' => $title,
            'width' => $width,
            'align' => $alignment,
            'type' => 'bool',
            'float' => true,
            'search' => $search,
        );
        
        $list[$key] = $item;
    }

    protected function addIcon($icon, $color, $title = '')
    {
        return "<i class='icon $icon' style='color: $color;'></i> ".$title;
    }
    
    protected function getList()
    {
        if (Tools::isSubmit('page') && !Tools::isSubmit('submitResetconfiguration')) {
            $dates = Tools::getValue('configurationFilter_invoice_date', array());
            if (isset($dates[0])) {
                $date_start = $dates[0];
            }
            if (isset($dates[1])) {
                $date_end = $dates[1];
            }
        } else {
            $date_start = '';
            $date_end = '';
        }
        
        $db = Db::getInstance();
        
        $sql = new DbQueryCore();
        $sql->select('oi.id_order_invoice')
            ->select('o.id_order')
            ->select('oi.number as invoice_number')
            ->select('oi.date_add as invoice_date')
            ->select('o.total_paid as invoice_amount')
            ->select('o.module')
            ->select('CONCAT(c.firstname, \' \', c.lastname) as customer')
            ->from('order_invoice', 'oi')
            ->leftJoin('orders', 'o', 'oi.id_order_invoice=o.invoice_number')
            ->innerJoin('customer', 'c', 'c.id_customer=o.id_customer')
            ->orderBy('oi.date_add DESC')
            ->orderBy('oi.number DESC');
        
        $sql_count = new DbQueryCore();
        $sql_count->select('count(*)')
            ->from('order_invoice', 'oi')
            ->leftJoin('orders', 'o', 'oi.id_order_invoice=o.invoice_number')
            ->innerJoin('customer', 'c', 'c.id_customer=o.id_customer')
            ->orderBy('oi.date_add DESC')
            ->orderBy('oi.number DESC');
        
        
        if ($date_start) {
            $date_start .= ' 00:00:00';
            $sql->where('oi.date_add >= \''.pSQL($date_start).'\'');
            $sql_count->where('oi.date_add >= \''.pSQL($date_start).'\'');
        }
        if ($date_end) {
            $date_end .= ' 23:59:59';
            $sql->where('oi.date_add <= \''.pSQL($date_end).'\'');
            $sql_count->where('oi.date_add <= \''.pSQL($date_end).'\'');
        }
        
        
        $this->listTotal = $db->getValue($sql_count);
        
        //Save query in cookies
        Context::getContext()->cookie->export_query = $sql->build();
        
        //Set Pagination
        $sql->limit($this->_default_pagination, ($this->page-1)*$this->_default_pagination);
        
        $result = $db->executeS($sql);
        
        if ($result) {
            foreach ($result as &$row) {
                $id_order_link = $this->link->getAdminLink('AdminOrders')
                    .'&id_order='.(int)$row['id_order']
                    .'&vieworder';
                $button_link = $this->link->getAdminLink('AdminPdf')
                    .'&submitAction=generateInvoicePDF'
                    .'&id_order='.(int)$row['id_order'];
                $row['customer'] = $this->ucFirst($row['customer']);
                $row['id_order'] = $this->addLink($id_order_link, $row['id_order']);
                $row['invoice_print'] = $this->addButton($button_link, 'icon-file-text');
            }
        }
        
        return $result;
    }
    
    public function addButton($link, $icon, $color = '#797979', $title = '', $newpage = true)
    {
        if ($newpage) {
            $newpage = '_blank';
        } else {
            $newpage = '';
        }
        $i = $this->addIcon($icon, $color, $title);
        $link = "<a class='btn btn-default $newpage' href='$link'>".$i."</a>";
        return $link;
    }
    
    public function addLink($link, $content)
    {
        $link = "<a href='$link'>".$content."</a>";
        return $link;
    }
    
    public function ucFirst($str)
    {
        $str_lower = Tools::strtolower($str);
        $parts = explode(' ', $str_lower);
        foreach ($parts as &$part) {
            $part = Tools::ucfirst($part);
        }
        return implode(' ', $parts);
    }
}
