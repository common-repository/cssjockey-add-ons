<?php
$tab = (isset( $_GET['show'] ) && $_GET['show'] != '') ? $_GET['show'] : 'installed';
?>
<div id="cjwpbldr-cloud-app" v-cloak>
    <cjwpbldr-cloud show_tab="<?php echo $tab; ?>"></cjwpbldr-cloud>
</div>