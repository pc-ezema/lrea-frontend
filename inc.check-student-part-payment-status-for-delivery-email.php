<?php 

$first_time_payment_for_student = student_has_part_payment($order_id, 'status', true) === 'pending' ? true : false;

$prevent_multiple_delivery_email = false;

// if the student is not making a part payment for the first time,
// prevent the delivery email from being sent.
if (!$first_time_payment_for_student) {                        
    $prevent_multiple_delivery_email = true;
}