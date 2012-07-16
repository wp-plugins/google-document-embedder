<?php

// access wp functions externally
require_once('bootstrap.php');
include_once(ABSPATH . 'wp-includes/pluggable.php');

if (!$_POST || !$_POST['email']) {
	echo "fail";
	exit;
} else {
	
	function gde_change_mail( $mail ) {
		return $_POST['email'];
    }
    
    function gde_change_sender ( $sendername ) {
		if ($_POST['name']) {
			return $_POST['name'];
		} else {
			return "GDE User";
		}
    }
	
    add_filter( 'wp_mail_from', 'gde_change_mail', 1 );
    add_filter( 'wp_mail_from_name', 'gde_change_sender', 1 );
	
	/* 
	 * Note to self: wp_mail doesn't deliver to Google Apps (at least in this config).
	 * It does deliver to regular Gmail, if necessary. Why? Hours wasted.
	 * Instead, deliver to POP account and let GA pick it up. Boo.
	 */
	$to = "wpp@dev.davismetro.com";
	
	$subject = "GDE Support Request";
	
	$headers = "";
	if ($_POST['cc'] == "yes") {
		$headers .= "CC: " . $_POST['email'] . "\n";
	}
	$headers .= "Reply-To: <" . $_POST['email'] . ">\n";
	
	$message = "A request was sent from the GDE Support Form.\n\n";
	if ($_POST['msg']) {
		$message .= stripslashes($_POST['msg']) . "\n\n";
	} else {
		$message .= "No message was included.\n\n";
	}
	
	if ($_POST['sc']) {
		$message .= "Shortcode: " . stripslashes($_POST['sc']) . "\n\n";
	} else {
		$message .= "No shortcode was included.\n\n";
	}
	
	if ($_POST['senddb']) {
		$message .= $_POST['senddb'];
	} else {
		$message .= "No debug info was included.";
	}
	
	if (wp_mail( $to, $subject, $message, $headers )) {
		echo "success";
	} else {
		echo "fail";
	}
}

?>