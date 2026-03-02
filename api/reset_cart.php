<?php
session_start(); // Start or resume session so we can access $_SESSION

// ---- CART RESET TEST LOGIC ----
if (isset($_POST['reset_cart'])) { // Check if the reset button triggered this request
    $_SESSION['cart'] = [];      // Completely reset the cart from the session
    echo json_encode([            // Send JSON response back to JavaScript
        "status"  => "success",
        "message" => "Cart has been reset successfully"
    ]);
    exit; // Stop script so nothing else runs after reset
}

