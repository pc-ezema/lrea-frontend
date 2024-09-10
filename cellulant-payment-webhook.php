<?php
	$webhook_response = json_decode(file_get_contents('php://input'));	

	// Return a response back to cellulant to acknowledge the payment.
	if ($webhook_response->requestStatusCode == 178) { // 178 is the status code for "request fully paid". So we'll send back a response with a status code of 183 (Payment was accepted by merchant).
		$webhook_request_response = array(
			"checkoutRequestID" => $webhook_response->checkoutRequestID,
			"merchantTransactionID" => $webhook_response->merchantTransactionID,
			"statusCode" => 183,
			"statusDescription" => "Payment processed successfully",
			"receiptNumber" => "",
		);
	}
	else { // Return a response code of 180 to tell Cellulant it was a failed payment.
		$webhook_request_response = array(
			"checkoutRequestID" => $webhook_response->checkoutRequestID,
			"merchantTransactionID" => $webhook_response->merchantTransactionID,
			"statusCode" => 180,
			"statusDescription" => "Payment was not processed successfully",
			"receiptNumber" => "",
		);
	}

	echo json_encode($webhook_request_response);
    exit();