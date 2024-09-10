<?php 
    $value = '{"id":"pay_gsdxsysouljujhxgbxectqhqyy","requested_on":"2021-05-24T17:13:35Z","source":{"id":"src_dmbh57vxqnxejglcqwp4rrq5ce","type":"card","billing_address":{"country":"GB"},"expiry_month":4,"expiry_year":2024,"name":"Asifat Kazeem Adeniyi","scheme":"Mastercard","last4":"4318","fingerprint":"38D203B742E6DAF2DA380088B46B658B1E2BECE71E6B17C489EA4CCB390DFD80","bin":"539983","card_type":"Debit","card_category":"Consumer","issuer":"GUARANTY TRUST BANK PLC","issuer_country":"NG","product_id":"MDU","product_type":"Debit Unembossed MasterCard® Card","cvv_check":"Y"},"amount":200,"currency":"USD","payment_type":"Regular","reference":"1621876346CH8242023","status":"Captured","approved":true,"3ds":{"downgraded":false,"enrolled":"Y","signature_valid":"Y","authentication_response":"Y","cryptogram":"jE7A/B/SvMFeCBApaha0AyIAAAA=","xid":"MDAwMDAwMDAwMDA2NzQyNjcyNzY=","version":"1.0.2"},"risk":{"flagged":false},"customer":{"id":"cus_zkwsgx3b736efoycsx4ckdvhze","email":"sample@gmail.com","name":"John Doe"},"billing_descriptor":{"name":"www.sample.com","city":"Dubai"},"metadata":{"mode":"payment","cancel_url":"https://sample.com/checkout-dot-com-transaction-complete.php?initpage=https%3A%2F%2Fsample.com%2Fcheckout%2F3dcfc1827f6b5843e7a856b1525952dfa1df3447&cid=8242023","correlationId":"765e5950-f7c4-4d9d-be3f-e4ebde62197c","correlation_id":"765e5950-f7c4-4d9d-be3f-e4ebde62197c"},"actions":[{"id":"act_zj7mrkmzjwgulnbjxs3tgsox6y","type":"Capture","response_code":"10000","response_summary":"Approved"},{"id":"act_gsdxsysouljujhxgbxectqhqyy","type":"Authorization","response_code":"10000","response_summary":"Approved"}],"_links":{"self":{"href":"https://api.checkout.com/payments/pay_gsdxsysouljujhxgbxectqhqyy"},"actions":{"href":"https://api.checkout.com/payments/pay_gsdxsysouljujhxgbxectqhqyy/actions"},"refund":{"href":"https://api.checkout.com/payments/pay_gsdxsysouljujhxgbxectqhqyy/refunds"}}}';
    $json_encoded = json_encode($value);
    $json_decoded = json_decode($json_encoded, true, JSON_PRETTY_PRINT);
    

    echo '<pre>'; var_dump($value, $json_encoded, $json_decoded); echo '</pre>';

    $value2 = '{
        "id": "pay_gsdxsysouljujhxgbxectqhqyy",
        "requested_on": "2021-05-24T17:13:35Z",
        "source": {
            "id": "src_dmbh57vxqnxejglcqwp4rrq5ce",
            "type": "card",
            "billing_address": {
                "country": "GB"
            },
            "expiry_month": 4,
            "expiry_year": 2024,
            "name": "Asifat Kazeem Adeniyi",
            "scheme": "Mastercard",
            "last4": "4318",
            "fingerprint": "38D203B742E6DAF2DA380088B46B658B1E2BECE71E6B17C489EA4CCB390DFD80",
            "bin": "539983",
            "card_type": "Debit",
            "card_category": "Consumer",
            "issuer": "GUARANTY TRUST BANK PLC",
            "issuer_country": "NG",
            "product_id": "MDU",
            "product_type": "Debit Unembossed MasterCard® Card",
            "cvv_check": "Y"
        },
        "amount": 200,
        "currency": "USD",
        "payment_type": "Regular",
        "reference": "1621876346CH8242023",
        "status": "Captured",
        "approved": true,
        "3ds": {
            "downgraded": false,
            "enrolled": "Y",
            "signature_valid": "Y",
            "authentication_response": "Y",
            "cryptogram": "jE7A/B/SvMFeCBApaha0AyIAAAA=",
            "xid": "MDAwMDAwMDAwMDA2NzQyNjcyNzY=",
            "version": "1.0.2"
        },
        "risk": {
            "flagged": false
        },
        "customer": {
            "id": "cus_zkwsgx3b736efoycsx4ckdvhze",
            "email": "sample@gmail.com",
            "name": "John Doe"
        },
        "billing_descriptor": {
            "name": "www.sample.com",
            "city": "Dubai"
        },
        "metadata": {
            "mode": "payment",
            "cancel_url": "https://sample.com/checkout-dot-com-transaction-complete.php?initpage=https%3A%2F%sample.com%2Fcheckout%2F3dcfc1827f6b5843e7a856b1525952dfa1df3447&cid=8242023",
            "correlationId": "765e5950-f7c4-4d9d-be3f-e4ebde62197c",
            "correlation_id": "765e5950-f7c4-4d9d-be3f-e4ebde62197c"
        },
        "actions": [
            {
                "id": "act_zj7mrkmzjwgulnbjxs3tgsox6y",
                "type": "Capture",
                "response_code": "10000",
                "response_summary": "Approved"
            },
            {
                "id": "act_gsdxsysouljujhxgbxectqhqyy",
                "type": "Authorization",
                "response_code": "10000",
                "response_summary": "Approved"
            }
        ],
        "_links": {
            "self": {
                "href": "https://api.checkout.com/payments/pay_gsdxsysouljujhxgbxectqhqyy"
            },
            "actions": {
                "href": "https://api.checkout.com/payments/pay_gsdxsysouljujhxgbxectqhqyy/actions"
            },
            "refund": {
                "href": "https://api.checkout.com/payments/pay_gsdxsysouljujhxgbxectqhqyy/refunds"
            }
        }
    }';

    echo '<pre>'; var_dump($value2); echo '</pre>';
 ?>