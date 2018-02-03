<?php
/**
 * Common Template - tpl_header.php
 *
 * this file can be copied to /templates/your_template_dir/pagename<br />
 * example: to override the privacy page<br />
 * make a directory /templates/my_template/privacy<br />
 * copy /templates/templates_defaults/common/tpl_footer.php to /templates/my_template/privacy/tpl_header.php<br />
 * to override the global settings and turn off the footer un-comment the following line:<br />
 * <br />
 * $flag_disable_header = true;<br />
 *
 * @package templateSystem
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_header.php 4813 2006-10-23 02:13:53Z drbyte $
 */

 $products = $_SESSION['cart']->get_products();
 for ($i=0, $n=sizeof($products); $i<$n; $i++) {
   if (($i/2) == floor($i/2)) {
     $rowClass="rowEven";
   } else {
     $rowClass="rowOdd";
   }
   switch (true) {
     case (SHOW_SHOPPING_CART_DELETE == 1):
     $buttonDelete = true;
     $checkBoxDelete = false;
     break;
     case (SHOW_SHOPPING_CART_DELETE == 2):
     $buttonDelete = false;
     $checkBoxDelete = true;
     break;
     default:
     $buttonDelete = true;
     $checkBoxDelete = true;
     break;
     $cur_row++;
   } // end switch
   $attributeHiddenField = "";
   $attrArray = false;
   $productsName = $products[$i]['name'];
   // Push all attributes information in an array
   if (isset($products[$i]['attributes']) && is_array($products[$i]['attributes'])) {
     if (PRODUCTS_OPTIONS_SORT_ORDER=='0') {
       $options_order_by= ' ORDER BY LPAD(popt.products_options_sort_order,11,"0")';
     } else {
       $options_order_by= ' ORDER BY popt.products_options_name';
     }
     foreach ($products[$i]['attributes'] as $option => $value) {
       $attributes = "SELECT popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix
                      FROM " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                      WHERE pa.products_id = :productsID
                      AND pa.options_id = :optionsID
                      AND pa.options_id = popt.products_options_id
                      AND pa.options_values_id = :optionsValuesID
                      AND pa.options_values_id = poval.products_options_values_id
                      AND popt.language_id = :languageID
                      AND poval.language_id = :languageID " . $options_order_by;
 
       $attributes = $db->bindVars($attributes, ':productsID', $products[$i]['id'], 'integer');
       $attributes = $db->bindVars($attributes, ':optionsID', $option, 'integer');
       $attributes = $db->bindVars($attributes, ':optionsValuesID', $value, 'integer');
       $attributes = $db->bindVars($attributes, ':languageID', $_SESSION['languages_id'], 'integer');
       $attributes_values = $db->Execute($attributes);
       //clr 030714 determine if attribute is a text attribute and assign to $attr_value temporarily
       if ($value == PRODUCTS_OPTIONS_VALUES_TEXT_ID) {
         $attributeHiddenField .= zen_draw_hidden_field('id[' . $products[$i]['id'] . '][' . TEXT_PREFIX . $option . ']',  $products[$i]['attributes_values'][$option]);
         $attr_value = htmlspecialchars($products[$i]['attributes_values'][$option], ENT_COMPAT, CHARSET, TRUE);
       } else {
         $attributeHiddenField .= zen_draw_hidden_field('id[' . $products[$i]['id'] . '][' . $option . ']', $value);
         $attr_value = $attributes_values->fields['products_options_values_name'];
       }
 
       $attrArray[$option]['products_options_name'] = $attributes_values->fields['products_options_name'];
       $attrArray[$option]['options_values_id'] = $value;
       $attrArray[$option]['products_options_values_name'] = $attr_value;
       $attrArray[$option]['options_values_price'] = $attributes_values->fields['options_values_price'];
       $attrArray[$option]['price_prefix'] = $attributes_values->fields['price_prefix'];
     }
   } //end foreach [attributes]
   if (STOCK_CHECK == 'true') {
     $flagStockCheck = zen_check_stock($products[$i]['id'], $products[$i]['quantity']);
     if ($flagStockCheck == true) {
       $flagAnyOutOfStock = true;
     }
   }
   $linkProductsImage = zen_href_link(zen_get_info_page($products[$i]['id']), 'products_id=' . $products[$i]['id']);
   $linkProductsName = zen_href_link(zen_get_info_page($products[$i]['id']), 'products_id=' . $products[$i]['id']);
   $productsImage = (IMAGE_SHOPPING_CART_STATUS == 1 ? zen_image(DIR_WS_IMAGES . $products[$i]['image'], $products[$i]['name'], IMAGE_SHOPPING_CART_WIDTH, IMAGE_SHOPPING_CART_HEIGHT) : '');
   $show_products_quantity_max = zen_get_products_quantity_order_max($products[$i]['id']);
   $showFixedQuantity = (($show_products_quantity_max == 1 or zen_get_products_qty_box_status($products[$i]['id']) == 0) ? true : false);
 //  $showFixedQuantityAmount = $products[$i]['quantity'] . zen_draw_hidden_field('products_id[]', $products[$i]['id']) . zen_draw_hidden_field('cart_quantity[]', 1);
 //  $showFixedQuantityAmount = $products[$i]['quantity'] . zen_draw_hidden_field('cart_quantity[]', 1);
   $showFixedQuantityAmount = $products[$i]['quantity'] . zen_draw_hidden_field('cart_quantity[]', $products[$i]['quantity']);
   $showMinUnits = zen_get_products_quantity_min_units_display($products[$i]['id']);
   $quantityField = zen_draw_input_field('cart_quantity[]', $products[$i]['quantity'], 'size="4"');
   $buttonUpdate = ((SHOW_SHOPPING_CART_UPDATE == 1 or SHOW_SHOPPING_CART_UPDATE == 3) ? zen_image_submit(ICON_IMAGE_UPDATE, ICON_UPDATE_ALT) : '') . zen_draw_hidden_field('products_id[]', $products[$i]['id']);
   $tmp =  zen_add_tax($products[$i]['final_price'],zen_get_tax_rate($products[$i]['tax_class_id']));
 //  $productsPriceEach = $currencies->rateAdjusted($tmp);
 //  $productsPriceTotal = $productsPriceEach * $products[$i]['quantity'];
   $productsPriceTotal = $currencies->display_price($products[$i]['final_price'], zen_get_tax_rate($products[$i]['tax_class_id']), $products[$i]['quantity']) . ($products[$i]['onetime_charges'] != 0 ? '<br />' . $currencies->display_price($products[$i]['onetime_charges'], zen_get_tax_rate($products[$i]['tax_class_id']), 1) : '');
   $productsPriceEach = $currencies->display_price($products[$i]['final_price'], zen_get_tax_rate($products[$i]['tax_class_id']), 1) . ($products[$i]['onetime_charges'] != 0 ? '<br />' . $currencies->display_price($products[$i]['onetime_charges'], zen_get_tax_rate($products[$i]['tax_class_id']), 1) : '');
 //  $productsPriceTotal = $currencies->display_price($products[$i]['final_price'], zen_get_tax_rate($products[$i]['tax_class_id']), $products[$i]['quantity']) . ($products[$i]['onetime_charges'] != 0 ? '<br />' . $currencies->display_price($products[$i]['onetime_charges'], zen_get_tax_rate($products[$i]['tax_class_id']), 1) : '');
 //  echo  $currencies->rateAdjusted($tmp);
   $productArray[$i] = array('attributeHiddenField'=>$attributeHiddenField,
                             'flagStockCheck'=>$flagStockCheck,
                             'flagShowFixedQuantity'=>$showFixedQuantity,
                             'linkProductsImage'=>$linkProductsImage,
                             'linkProductsName'=>$linkProductsName,
                             'productsImage'=>$productsImage,
                             'productsName'=>$productsName,
                             'showFixedQuantity'=>$showFixedQuantity,
                             'showFixedQuantityAmount'=>$showFixedQuantityAmount,
                             'showMinUnits'=>$showMinUnits,
                             'quantityField'=>$quantityField,
                             'buttonUpdate'=>$buttonUpdate,
                             'productsPrice'=>$productsPriceTotal,
                             'productsPriceEach'=>$productsPriceEach,
                             'rowClass'=>$rowClass,
                             'buttonDelete'=>$buttonDelete,
                             'checkBoxDelete'=>$checkBoxDelete,
                             'id'=>$products[$i]['id'],
                             'attributes'=>$attrArray);
 } // end FOR loop
 
?>

<?php
  // Display all header alerts via messageStack:
  if ($messageStack->size('header') > 0) {
    echo $messageStack->output('header');
  }
  if (isset($_GET['error_message']) && zen_not_null($_GET['error_message'])) {
  echo htmlspecialchars(urldecode($_GET['error_message']));
  }
  if (isset($_GET['info_message']) && zen_not_null($_GET['info_message'])) {
   echo htmlspecialchars($_GET['info_message']);
} else {

}
?>


<!--bof-header logo and navigation display-->
<?php

?><pre><?php
//print_r($_SESSION);
?></pre>

<!--<div id="facebook">
	<iframe src="http://www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2F&amp;width=960&amp;colorscheme= light&amp;connections=500&amp;stream=false&amp;header=false&amp;height=160" 
  	scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:1280px; height:160px;" allowTransparency="true"></iframe>
</div>-->

<?php
if (!isset($flag_disable_header) || !$flag_disable_header) {
?>

<div id="headerWrapper">
<!--bof-navigation display-->
<div id="navMainWrapper">
<div id="navMain">
    <ul class="back">
    <!-- <li><?php echo '<a href="' . HTTP_SERVER . DIR_WS_CATALOG . '">'; ?><?php echo HEADER_TITLE_CATALOG; ?></a></li> -->
    
<?php if ($_SESSION['customer_id']) { ?>
    <li><a href="<?php echo zen_href_link(FILENAME_LOGOFF, '', 'SSL'); ?>"><?php echo HEADER_TITLE_LOGOFF; ?></a></li>
    <li><a href="<?php echo zen_href_link(FILENAME_ACCOUNT, '', 'SSL'); ?>"><?php echo HEADER_TITLE_MY_ACCOUNT; ?></a></li>
    
<?php
      } else {
        if (STORE_STATUS == '0') {
?>
    <li><a href="<?php echo zen_href_link(FILENAME_LOGIN, '', 'SSL'); ?>"><?php echo HEADER_TITLE_LOGIN; ?></a></li>
<?php } } ?>

<?php if ($_SESSION['cart']->count_contents() != 0) { ?>
    <li><a href="<?php echo zen_href_link(FILENAME_SHOPPING_CART, '', 'NONSSL'); ?>"><?php echo HEADER_TITLE_CART_CONTENTS; ?></a></li>
    <li><a href="<?php echo zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'); ?>"><?php echo HEADER_TITLE_CHECKOUT; ?></a></li>
<?php }?>
</ul>
</div>

<div id="navMainSearch"><?php require(DIR_WS_MODULES . 'sideboxes/search_header.php'); ?></div>
<br class="clearBoth" />

</div>
<!--eof-navigation display-->
<?php
if ($current_page_base == 'index' and $cPath == '2') {
	 echo '<div style="margin-top:20px;margin-left:200px;width:700px;height:-10px;font-size:12px;"><div style="float:left;color:#0000ff;font-size:11px;"></div></div>';
 }
 ?>
<!--bof-branding display-->
<div id="logoWrapper">

    <div id="logo">
			<?php echo '<a href="' . HTTP_SERVER . DIR_WS_CATALOG . '">' . zen_image($template->get_template_dir(HEADER_LOGO_IMAGE, DIR_WS_TEMPLATE, $current_page_base,'images'). '/' . HEADER_LOGO_IMAGE, HEADER_ALT_TEXT) . '</a>'; ?>&nbsp;&nbsp;
    	<!--<a href="https://www.facebook.com/pages/AdvanceTec-Safe-Deposit-Services-Inc/195883210458412?fref=ts" target="_blank"><img src="http://www.advancetecsafedeposit.com/images/buttons/FacebookButton.png" width="63" height="63" alt="Visit Advance Tec Safe Deposit Services on Facebook" border="0" /></a>-->
    </div> 
<?php if (HEADER_SALES_TEXT != '' || (SHOW_BANNERS_GROUP_SET2 != '' && $banner = zen_banner_exists('dynamic', SHOW_BANNERS_GROUP_SET2))) { ?>
    <div id="taglineWrapper">
    <pre><?php
    //print_r($productArray);
    ?></pre>

<?php
              if (HEADER_SALES_TEXT != '') {
?>
<div align="right" style="text-align:right;color: #ff0000;font-size:18px;margin-right:22px;">
	Place your order online or call us toll free: 888.231.0745
</div>
      <div id="tagline"><?php echo HEADER_SALES_TEXT;?></div>
<?php
              }
?>
<?php
              if (SHOW_BANNERS_GROUP_SET2 != '' && $banner = zen_banner_exists('dynamic', SHOW_BANNERS_GROUP_SET2)) {
                if ($banner->RecordCount() > 0) {
?>
      <div id="bannerTwo" class="banners"><?php echo zen_display_banner('static', $banner);?></div>
<?php
                }
              }
?>
    </div>
<?php } // no HEADER_SALES_TEXT or SHOW_BANNERS_GROUP_SET2 ?>
</div>
<br class="clearBoth" />
<!--eof-branding display-->

<!--eof-header logo and navigation display-->

<!--bof-optional categories tabs navigation display-->
<?php require($template->get_template_dir('tpl_modules_categories_tabs.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_categories_tabs.php'); ?>
<!--eof-optional categories tabs navigation display-->

<!--bof-header ezpage links-->
<?php if (EZPAGES_STATUS_HEADER == '1' or (EZPAGES_STATUS_HEADER == '2' and (strstr(EXCLUDE_ADMIN_IP_FOR_MAINTENANCE, $_SERVER['REMOTE_ADDR'])))) { ?>
<?php require($template->get_template_dir('tpl_ezpages_bar_header.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_ezpages_bar_header.php'); ?>
<?php } ?>
<!--eof-header ezpage links-->
</div>
<?php } ?>