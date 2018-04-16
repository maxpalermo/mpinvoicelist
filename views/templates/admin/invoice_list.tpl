{*
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
*  @copyright 2007-2018 Digital Slutions®
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<form id="invoice_list_form" class="defaultForm form-horizontal AdminInvoices" action="index.php?controller=AdminInvoices&amp;token=5b5b711c5fe139a934a53dee1d2512c1" method="post" enctype="multipart/form-data" novalidate="novalidate">
    <input type="hidden" name="submitAddinvoice_list" value="1">
</form>
<script type="text/javascript">
    $(document).ready(function(){
        var form = $('#invoice_list_form').detach();
        $('#invoice_date_form').closest('.row').prepend(form);
        $('#invoice_list_panel').show();
        $('#invoice_list_form').validate();
        $('button[name="submitResetconfiguration"]').on('click', function(event){
            //event.preventDefault();
            console.log('reset');
            $('#local_configurationFilter_invoice_date_0').val('');
            $('#local_configurationFilter_invoice_date_1').val('');
            //$('#configurationFilter_invoice_date_0').val('');
            //$('#configurationFilter_invoice_date_1').val('');
        });
        $('#configuration-pagination-items-page').on('change', function(){
            $('#invoice_list_form').submit();
        });
    });
</script>