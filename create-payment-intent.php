<?php
require_once('connections/config.php');
require_once('components/misc.inc.php');

require __DIR__ . '/vendor/autoload.php';

// Set your secret key. You can find it on your Stripe Dashboard.
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['payment_method_id'])) {
    echo json_encode(['error' => 'Payment method ID is required']);
    exit;
}

try {
    // Define fixed amount and currency
    $amount = $data['amount']; // Amount in pence (150 pounds)
    $currency = $data['currency']; // Currency in GBP

    // Create a PaymentIntent with the provided PaymentMethod ID
    $paymentIntent = \Stripe\PaymentIntent::create([
        'payment_method' => $data['payment_method_id'],
        'amount' => $amount,
        'currency' => $currency,
        'description' => 'Membership subscription paid by ' . $data['name'],
        'confirm' => true,
        'receipt_email' => $data['email'],
        'automatic_payment_methods[enabled]' => true,
        'automatic_payment_methods[allow_redirects]' => 'never'
    ]);

    // Log and display the PaymentIntent status for debugging
    $status = $paymentIntent->status;
    error_log("PaymentIntent status: $status");

    // Check if the payment requires further action (e.g., 3D Secure)
    if (
        $status === 'requires_action' &&
        $paymentIntent->next_action->type === 'use_stripe_sdk'
    ) {
        // Tell the client to handle the action (e.g., 3D Secure authentication)
        echo json_encode([
            'requires_action' => true,
            'payment_intent_client_secret' => $paymentIntent->client_secret,
        ]);
    } else if ($status === 'succeeded') {
        // Payment is successful
        echo json_encode(['success' => true, 'reference' => $paymentIntent->id]); // Return the reference
    } else {
        // Any other status
        echo json_encode([
            'error' => 'Unexpected PaymentIntent status',
            'status' => $status,  // Add the status in the response for debugging
        ]);
    }
} catch (\Stripe\Exception\ApiErrorException $e) {
    // Handle error
    echo json_encode(['error' => $e->getMessage()]);
}
