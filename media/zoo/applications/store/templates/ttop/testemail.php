<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

//$this->app->document->addStylesheet($this->template->resource.'assets/css/uikit.css');

$class = $this->template->name.'';

if(!$this->oid) {
	echo 'Order ID not provided!';
	return;
}


?>
<div id="contentext"><span><i class="uk-icon-spinner uk-icon-spin"></i>Sending Email</span></div>
<script>

jQuery(document).ready(function($) {
	$("#contentext").load("http://ttop?option=com_zoo&controller=checkout&task=orderNotification&oid=<?php echo $this->oid; ?>&format=raw");
});

</script>