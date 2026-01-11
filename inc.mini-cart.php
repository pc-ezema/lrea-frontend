<?php require('./components/inc.mini-cart-data.php'); ?>

<?php
if (!isset($cart_programmes) || !is_array($cart_programmes)) {
    $cart_programmes = [];
}

$total_courses_in_cart = count($cart_programmes);

?>

<div class="mini-cart">
    <?php if ($total_courses_in_cart > 0): ?>
        <ul class="mini-cart-items">
            <?php foreach ($cart_programmes as $prog): ?>
                <li class="mini-cart-item">
                    <div class="cart-thumb">
                        <img src="<?= htmlspecialchars($prog['thumbnail']) ?>" alt="<?= htmlspecialchars($prog['name']) ?>">
                    </div>
                    <div class="cart-info">
                        <span class="cart-name"><?= htmlspecialchars($prog['name']) ?></span>
                        <span class="cart-fee"><?= htmlspecialchars($prog['currency']) ?><?= $prog['discount_fee'] ?></span>
                        <div>
                            <span class="cart-original-fee"><?= htmlspecialchars($prog['currency']) ?><?= $prog['original_fee'] ?></span>
                            <?php if(!empty($prog['discount_note'])): ?>
                                <span class="cart-discount-note"><?= htmlspecialchars($prog['discount_note']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="cart-remove">
                        <button id="<?php echo $prog['id']; ?>" class="btn btn-custom-size btn-light remove-from-page" title="Remove"><i data-feather="trash"></i></button>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="mini-cart-summary">
            <div class="summary-row">
                <span>Subtotal:</span>
                <span><?= $cart_programmes[0]['currency'] ?><?= number_format($subtotal_cart_fee) ?></span>
            </div>
            <div class="summary-row">
                <span>VAT:</span>
                <span><?= $cart_programmes[0]['currency'] ?><?= $vat ?></span>
            </div>
            <div class="summary-row total">
                <span>Total:</span>
                <span><?= $cart_programmes[0]['currency'] ?><?= number_format($total_cart_fee) ?></span>
            </div>
        </div>

        <div class="mini-cart-payments">
            <img src="<?php echo MEDIA_ROOT_DIR; ?>images/payment-logos.svg" alt="Mastercard">
        </div>

        <div class="mini-cart-buttons">
            <a href="<?php echo $link_root_path; ?>checkout" class="btn btn-checkout btn-custom-color-1">
                <span>Proceed to checkout</span>
                <span class="total-in-checkout" data-progtotal="<?php echo round($total_cart_fee); ?>" data-fbCur="<?php echo $dis_programme_currency; ?>" data-fbVal="<?php echo round($total_cart_fee, 2); ?>" data-fbNum="<?php echo $totalRows_DisProg; ?>">
                    <?php echo $symbol_for_programme_currency.''.number_format($total_cart_fee); ?>
                </span>
            </a>
            <a href="<?php echo $link_root_path; ?>cart" class="btn btn-view-cart">View Cart</a>
        </div>

    <?php else: ?>
        <div class="mini-cart-empty">
            <p>Your cart is empty.</p>
            <a href="<?php echo $link_root_path; ?>" class="btn btn-outline-primary btn-sm">Browse Courses</a>
        </div>
    <?php endif; ?>
</div>

<style>
/* Container */
.mini-cart {
    width: 320px;
}

.mini-cart {
    width: 90%;              /* use 90% of screen on mobile */
    max-width: 320px;        /* never exceed 320px */
    padding: 15px;
    background: #fff;
    border: 1px solid #ddd;
    box-sizing: border-box;
    position: fixed;         /* make it float */
    right: 20px;             /* distance from right on desktop */
    z-index: 9999;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Mobile adjustments */
@media (max-width: 576px) {
    .mini-cart {
        width: 95%;          /* almost full width on small screens */
        right: 2.5%;         /* small gap from screen edge */
        top: 80px;
        padding: 10px;
    }

    .mini-cart-items {
        max-height: 200px;   /* smaller height for mobile */
    }
}

/* Cart Items Container */
.mini-cart-items {
    list-style: none;
    padding: 0;
    margin: 0;
    max-height: 220px;       /* limit the height */
    overflow-y: auto;        /* enable vertical scroll */
    scrollbar-width: thin;   /* Firefox */
    scrollbar-color: #ccc #f9f9f9; /* Firefox */
}

/* Scrollbar Styling for Webkit Browsers */
.mini-cart-items::-webkit-scrollbar {
    width: 6px;
}

.mini-cart-items::-webkit-scrollbar-track {
    background: #f9f9f9;
    border-radius: 3px;
}

.mini-cart-items::-webkit-scrollbar-thumb {
    background-color: #ccc;
    border-radius: 3px;
}

.mini-cart-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 12px;
    padding-right: 5px; /* make room for scrollbar */
    position: relative;
}

.cart-thumb img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 5px;
    margin-right: 10px;
}

.cart-info {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.cart-name {
    font-weight: bold;
    font-size: 14px;
    margin-bottom: 4px;
    color: #333;
}

.cart-original-fee {
    font-size: 13px;
    color: #c00;
    text-decoration: line-through;
    margin-right: 5px;
}

.cart-fee {
    font-size: 14px;
    color: #333;
    font-weight: bold;
}

.cart-discount-note {
    font-size: 12px;
    color: #666;
}

.cart-remove {
    margin-left: 8px;
    font-size: 18px;
    color: red;
}

.cart-remove a {
    text-decoration: none;
}

/* Summary */
.mini-cart-summary {
    border-top: 1px solid #eee;
    padding-top: 10px;
    margin-top: 10px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
    font-size: 14px;
}

.summary-row.total {
    font-weight: bold;
    font-size: 16px;
}

/* Payment Icons */
.mini-cart-payments {
    margin-top: 10px;
    display: flex;
    justify-content: center;
}

.mini-cart-payments img {
    height: 24px;
}

/* Buttons */
.mini-cart-buttons {
    margin-top: 15px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.btn-checkout {
    /* background-color: #007bff; */
    color: #fff;
    text-align: center;
    padding: 10px 0;
    font-weight: bold;
    text-decoration: none;
    border-radius: 5px;
}

.btn-view-cart {
    border: 1px solid #c00;
    color: #c00;
    text-align: center;
    padding: 10px 0;
    font-weight: bold;
    text-decoration: none;
    border-radius: 5px;
}

/* Empty */
.mini-cart-empty {
    text-align: center;
    padding: 40px 0;
}
</style>
