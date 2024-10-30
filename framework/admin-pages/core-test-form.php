<?php
$options = [
	[
		'id' => 'test',
		'type' => 'text',
		'label' => 'label',
		'info' => 'info',
		'prefix' => 'prefix',
		'suffix' => 'suffix',
		'params' => [
			'placeholder' => 'placeholder'
		],
		'default' => 'one',
		'options' => [
			'one' => 'One',
			'two' => 'Two',
		],
	],
	[
		'id' => 'textarea',
		'type' => 'textarea',
		'label' => 'label',
		'info' => 'info',
		'prefix' => 'prefix',
		'suffix' => 'suffix',
		'params' => [
			'placeholder' => 'placeholder'
		],
		'default' => 'one',
		'options' => [
			'one' => 'One',
			'two' => 'Two',
		],
	]
];
$saved_values = [
	'test' => 'text value',
	'textarea' => 'textarea value',
];
$json_form = $this->helpers->jsonFormOptions( $options, $saved_values );
if( isset( $_POST['submit_form'] ) ) {
	echo '<pre>';
	print_r( $_POST );
	echo '</pre>';
}
?>

<form action="" method="post">
    <div class="render-vue-form">
        <vue-form options='<?php echo $json_form; ?>'></vue-form>
    </div>
    <button name="submit_form" type="submit" class="cj-button cj-is-primary">Submit</button>
</form>