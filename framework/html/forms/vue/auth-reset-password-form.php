<?php
if( isset( $_GET['action'] ) && $_GET['action'] == 'rp' && isset( $_GET['key'] ) && $_GET['key'] != '' ) {
	include 'parts/change-password.php';
} else {
	include 'parts/reset-password.php';
}