const bookingForm = document.querySelector(".booking-form_container");
const enquireForm = document.querySelector(".enquire-form_container");
const scrollBtn = document.querySelector(".scrollup_btn");
const headerNavBar = document.querySelector(".nav-links");
const headerNavIcon = document.querySelector(".menubar");
const firstName = document.querySelector(".firstname");
const lastName = document.querySelector(".lastname");
const email = document.querySelector(".email");
const phoneNumber = document.querySelector(".phone");
const address = document.querySelector(".address");
const date = document.querySelector(".date");
const duration = document.querySelector(".duration");
const message = document.querySelector(".message");
const errorMsg = document.querySelector(".error-mssg");
const fullName = document.querySelector(".fullname");
const enquireEmail = document.querySelector(".enquire-email");
const enquireNumber = document.querySelector(".enquire-number");
const enquireMessage = document.querySelector(".enquire-textarea");
const hamburger = document.querySelector(".hamburger");
const dashSidebar = document.querySelector(".dash_sidebar");




if (headerNavIcon && headerNavBar) {
    headerNavIcon.addEventListener("click", () => {
        headerNavBar.classList.toggle("headervisible");
    })
}

if (hamburger && dashSidebar) {
    hamburger.addEventListener("click", () => {
        dashSidebar.classList.toggle("hamburgerActive")

    });

}

window.onscroll = () => {
    if (!scrollBtn) return;
    if (window.scrollY > 100) {
        scrollBtn.style.display = "flex"
    }
    else {
        scrollBtn.style.display = "none"

    }
}


// Show or hide error message
function showError() {
    if (errorMsg) errorMsg.style.display = "flex";
}

function clearError() {
    if (errorMsg) errorMsg.style.display = "none";
}


// Function to hide error when user types
function clearErrorOnInput(inputElement) {
    inputElement.addEventListener("input", () => {
        errorMsg.style.display = "none";
    });
}

// User Signup Logic
$("#signupForm").on("submit", function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    $.ajax({
        url: "./api/auth.php",
        cache: false,
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",

        success: function (res) {
            if (res.status === "success") {
                // ✅ RESET FORM HERE
                document.getElementById("signupForm").reset();
                swal({
                    title: "Success",
                    text: res.message,
                    icon: "success",
                    button: "Login Now"
                }).then(() => {
                    setTimeout(() => {
                        window.location.replace("./signin.php");
                    }, 1500);
                });
            } else {
                swal("Error", res.message, "error");
            }
        },

        // error: function (xhr) {
        //     console.log(xhr.responseText);
        //     swal("Error", "Server error. Check console.", "error");
        // }

    });
});
// User Login Logic
$("#signinForm").on("submit", function (e) {
    e.preventDefault();

    $.ajax({
        url: "./api/auth.php",
        method: "POST",
        data: $(this).serialize(),
        dataType: "json",

        success: function (res) {
            if (res.status === "success") {
                swal({
                    title: "Success",
                    text: res.message, // ✅ FROM PHP (fullname included)
                    icon: "success",
                    timer: 2000,
                    buttons: false
                }
                ).then(() => window.location.replace(res.redirect));
            }
            else {
                swal("Error", res.message, "error");
            }
        }
    });
});

// Admin Login Logic
$("#admin_SigninForm").on("submit", function (e) {
    e.preventDefault();
    $.ajax({
        url: "../api/auth.php",
        method: "POST",
        data: $(this).serialize(),
        dataType: "json",
        success: function (res) {
            if (res.status === "success") {
                swal({
                    title: "Success",
                    text: res.message, // ✅ FROM PHP (fullname included)
                    icon: "success",
                    timer: 2000,
                    buttons: false
                }).then(() => window.location.replace(res.redirect));

            } else {
                swal("Error", res.message, "error");
            }
        },

        error: function () {
            swal("Error", "Server error. Try again.", "error");
        }
    });
});

// Open file dialog when button is clicked in admin profile edit page
$("#changePhotoBtn").on("click", function () {
    $("#adminImageUpload").click();
});

// Preview selected image in admin profile edit page
$("#adminImageUpload").on("change", function (event) {
    let file = event.target.files[0];

    if (file) {
        let reader = new FileReader();
        reader.onload = function (e) {
            $("#adminPreview").attr("src", e.target.result);
        };
        reader.readAsDataURL(file);
    }
});
// Add previw Image in add product page
const imgUpload = document.querySelector(".imgUpload");
if (imgUpload) {
    imgUpload.addEventListener("change", function (e) {
        const file = e.target.files[0];
        if (file) {
            document.getElementById("previewImg").src =
                URL.createObjectURL(file);
        }
    });
}
// Product image preview (Edit/Add product pages)
const imgInput = $(".imgUpload");
const preview = $("#previewImg");

if (imgInput && preview) {
    imgInput.on("change", function (e) {
        const file = e.target.files[0];
        if (file) {
            preview.src = URL.createObjectURL(file);
        }
    });
}
// Logo upload + preview (Admin settings/profile pages)
const changeBtn = $("#changeLogoBtn");
const fileInput = $("#logoUpload");
const previewImg = $("#logoPreview");

// open file dialog
if (changeBtn && fileInput && previewImg) {
    changeBtn.on("click", function () {
        fileInput.click();
    });

    // preview  site logo
    fileInput.on("change", function (e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (event) {
                previewImg.attr("src", event.target.result);
            };
            reader.readAsDataURL(file);
        }
    });
}

// Admin setting for User profile image preview
const uploadInput = $("#upload");
const userPreview = $("#userPreview");

if (uploadInput && userPreview) {
    uploadInput.on("change", function (e) {
        const file = e.target.files[0];
        if (file) {
            userPreview.attr("src", URL.createObjectURL(file));
        }
    });
}
//  user setting profile image open file dialog
$("#changeUserPhotoBtn").click(function () {
    $("#userAvatarInput").click();
});

// preview image,  user setting profile image
$("#userAvatarInput").change(function (event) {
    let file = event.target.files[0];
    if (file) {
        let reader = new FileReader();
        reader.onload = function (e) {
            $("#userAvatarPreview").attr("src", e.target.result);
        };
        reader.readAsDataURL(file);
    }
});


// Add to cart logic
$(".cart-btn").click(function () {
    let productId = $(this).data("id");

    $.ajax({
        url: "./api/add_cart.php",
        type: "POST",
        data: { product_id: productId },
        dataType: "json",
        success: function (res) {
            if (res.status === "success") {
                $(".count").text(res.cart_count);
                swal("Added!", res.message, "success");
            } else {
                swal("Duplicate Order", res.message, "error");
            }
        }
    });
});

// Delete from cart
$(".cart-trash").click(function () {
    let productId = $(this).data("id");
    let parentItem = $(this).closest(".cart-item"); // target UI card

    $.ajax({
        url: "./api/delete_cart.php",
        type: "POST",
        data: { product_id: productId },
        dataType: "json",
        success: function (res) {
            if (res.status === "success") {
                parentItem.remove(); // remove item from UI instantly
                $(".count, .cartTotal").text(res.cart_count); // update header count only

                // Recalculate subtotal/total in UI
                let newSubtotal = 0;
                $(".cart-subtotal").each(function () {
                    newSubtotal += parseFloat($(this).text().replace(/₦|,/g, ""));
                });

                $(".subtotal_price, .summary_price").text("₦" + newSubtotal.toLocaleString());

            } else {
                swal("Error", res.message, "error");
            }
        }
    });
});

// Update cart qty -/+
$(".qty-plus, .qty-minus").click(function () {
    let id = $(this).data("id");
    let type = $(this).hasClass("qty-plus") ? "plus" : "minus";

    $.ajax({
        url: "./api/update_cart.php",
        type: "POST",
        data: { product_id: id, type },
        dataType: "json",
        success: function (res) {
            if (res.status === "success") {

                // Update qty number between buttons
                $(`[data-id='${id}']`).siblings(".qty-input").text(res.qty);

                // Update individual item subtotal
                $(`[data-id='${id}']`).closest(".cart-item").find(".cart-subtotal").text("₦" + res.item_subtotal);

                // Update Order Summary subtotal & total
                $(".subtotal_price, .summary_price").text("₦" + res.total);
            }
        }
    });
});

// checkout  logic
$(".cart-checkout-btn").click(function (e) {
    e.preventDefault();

    let address = $("#address").val().trim();

    if (!address) {
        swal("Error", "Delivery address is required", "error");
        return;
    }

    $.ajax({
        url: "./api/checkout.php",
        type: "POST",
        data: { delivery_address: address },
        dataType: "json",
        success: function (res) {
            if (res.status === "error") {
                swal("Error", res.message, "error");
                return;
            }

            if (res.status === "Login required") {
                swal({
                    title: "Login Required",
                    text: res.message,
                    icon: "warning",
                    buttons: false,
                    timer: 1500
                }).then(() => {
                    window.location.replace(res.redirect);
                });
                return;
            }

            if (res.status === "success") {
                swal({
                    title: "Order Saved!",
                    text: res.message,
                    icon: "success",
                    timer: 1500,
                    buttons: false
                }).then(() => window.location.replace(res.redirect));
            }
        },
        error: function () {
            swal("Error", "Server error. Try again.", "error");
        }
    });
});


function updateCheckoutState() {
    let count = parseInt($(".cartTotal").text()) || 0;
    $("#checkoutBtn").prop("disabled", count === 0);
}

// Run on page load
updateCheckoutState();

// After deleting an item
$(".cart-trash").click(function () {
    setTimeout(updateCheckoutState, 200);
});

// After adding a product to cart
$(".cart-btn").click(function () {
    setTimeout(updateCheckoutState, 200);
});

// After +/- qty change
$(".qty-minus, .qty-plus").click(function () {
    setTimeout(updateCheckoutState, 200);
});
// Paystack payment logic 
const PAYSTACK_KEY = window.PAYSTACK_PUBLIC_KEY;
$("#paystackBtn").click(function () {
    let email = $(this).data("email");
    let amount = parseFloat($(this).data("amount"));

    let handler = PaystackPop.setup({
        key: PAYSTACK_KEY, // replace with your Paystack public key
        email: email,
        amount: amount * 100,
        currency: "NGN",
        ref: "VF-" + Math.floor((Math.random() * 1000000000) + 1),

        callback: function (res) {
            $.ajax({
                url: "/veefashion/api/verify_payment.php",
                type: "POST",
                data: {
                    reference: res.reference,
                    paystack_response: JSON.stringify(res)
                },
                dataType: "json",
                success: function (res) {
                    if (res.status === "success") {
                        swal("Payment Successful!", res.message, "success").then(() => window.location.replace(res.redirect));

                    } else {
                        swal("Verification Error!", res.message, "error");
                    }
                }
            });
        },

        onClose: function () {
            swal("Cancelled", "Payment was not completed", "warning");
        }
    });

    handler.openIframe();
});

// Enquiry logic
$("#enquiryForm").on("submit", function (e) {
    e.preventDefault();

    $.ajax({
        url: "./api/save_enquiry.php",
        type: "POST",
        data: $(this).serialize(),
        dataType: "json",
        success: function (res) {
            if (res.status === "success") {
                swal("Sent!", res.message, "success");
                $("#enquiryForm")[0].reset();
            } else {
                swal("Error", res.message, "error");
            }
        },
        error: function () {
            swal("Error", "Server error. Try again.", "error");
        }
    });
});

// Reply email (Admin)
$("#replyForm").submit(function (e) {
    e.preventDefault();

    $.ajax({
        url: "../api/reply_enquiry.php",
        type: "POST",
        data: $(this).serialize(),
        dataType: "json",
        success: function (res) {
            if (res.status === "success") {
                swal("Sent!", res.message, "success").then(() => location.reload());
            } else {
                swal("Error", res.message, "error");
            }
        }
    });
});


// Mark as Delivered
$(".mark_delivered").click(function () {
    let orderId = $(this).data("id");

    swal({
        title: "Mark as Delivered?",
        text: "This action cannot be undone",
        icon: "warning",
        buttons: true,
        dangerMode: false,
    }).then((confirm) => {
        if (!confirm) return;

        updateOrderStatus(orderId, "Delivered");
    });
});

// Cancel Order
$(".cancel_order").click(function () {
    let orderId = $(this).data("id");

    swal({
        title: "Cancel this Order?",
        text: "This action cannot be undone",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((confirm) => {
        if (!confirm) return;

        updateOrderStatus(orderId, "Cancelled");
    });
});
// function for Delivered and Cancelled order
function updateOrderStatus(orderId, status) {
    $.ajax({
        url: "../api/update_order_status.php",
        method: "POST",
        data: {
            order_id: orderId,
            status: status
        },
        dataType: "json",
        success: function (res) {
            if (res.status === "success") {
                swal("Success", res.message, "success")
                    .then(() => location.reload());
            } else {
                swal("Error", res.message, "error");
            }
        }
    });
}

// delete order items logic
$(".order_delete_btn").click(function () {
    console.log("Clicked");
    if ($(this).is(":disabled")) return;

    let itemId = $(this).data("id");

    swal({
        title: "Delete this item?",
        text: "This item will be permanently removed",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((confirm) => {
        if (!confirm) return;

        $.ajax({
            url: "../api/delete_order_item.php",
            method: "POST",
            data: { item_id: itemId },
            dataType: "json",
            success: function (res) {
                if (res.status === "success") {
                    swal("Deleted", res.message, "success")
                        .then(() => location.reload());
                } else {
                    swal("Error", res.message, "error");
                }
            }
        });
    });
});

// Delete product logic
$(".deleteProduct_btn").click(function () {
    let productId = $(this).data("id");

    swal({
        title: "Delete Product?",
        text: "This product will be hidden and cannot be sold again.",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((confirm) => {
        if (!confirm) return;

        $.ajax({
            url: "../api/delete_product.php",
            method: "POST",
            data: { product_id: productId },
            dataType: "json",
            success: function (res) {
                if (res.status === "success") {
                    swal("Deleted", res.message, "success")
                        .then(() => location.reload());
                } else {
                    swal("Error", res.message, "error");
                }
            }
        });
    });
});

// Update product
$(".add_product_form").submit(function (e) {
    e.preventDefault();
    let formData = new FormData(this);

    $.ajax({
        url: "../api/update_product.php",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (res) {
            if (res.status === "success") {
                swal("Updated!", res.message, "success")
                    .then(() => window.location.href = "products.php");
            } else {
                swal("Error", res.message, "error");
            }
        },
        error: function () {
            swal("Error", "Something went wrong", "error");
        }
    });
});
// Add product
$(".addProductForm").submit(function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    $.ajax({
        url: "../api/add-product.php",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",

        success: function (res) {
            if (res.status === "success") {
                swal("Success", res.message, "success")
                    .then(() => {
                        window.location.href = "products.php";
                    });
            } else {
                swal("Error", res.message, "error");
            }
        },

        error: function () {
            swal("Error", "Server error occurred", "error");
        }
    });
});
// User user Menu Toggle dropdown
$(document).on("click", "#userMenuToggle", function (e) {
    e.stopPropagation();
    $(".user-dropdown").toggle();
});

$(document).on("click", function () {
    $(".user-dropdown").hide();
});

// Update user
$(".edit_user_form").submit(function (e) {
    e.preventDefault();
    let formData = new FormData(this);
    $.ajax({
        url: "../api/update_user.php",
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",

        success: function (res) {
            if (res.status === "success") {
                swal("Updated", res.message, "success")
                    .then(() => window.location.href = "users.php");
            } else {
                swal("Error", res.message, "error");
            }
        }
    });
});
//  Suspend user logic
$(".user_suspend_btn").click(function () {
    const userId = $(this).data("id");

    swal({
        title: "Suspend user?",
        text: "This user will not be able to login.",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willSuspend) => {
        if (willSuspend) {
            $.post("../api/suspend_user.php", { user_id: userId }, function (res) {
                if (res.status === "success") {
                    swal("Done", res.message, "success")
                        .then(() => location.reload());
                } else {
                    swal("Error", res.message, "error");
                }
            }, "json");
        }
    });
});

// Delete user logic
$(".user_delete_btn").click(function () {
    const userId = $(this).data("id");

    swal({
        title: "Delete user?",
        text: "This action cannot be undone!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            $.post("../api/delete_user.php", { user_id: userId }, function (res) {
                if (res.status === "success") {
                    swal("Deleted", res.message, "success")
                        .then(() => window.location.href = "users.php");
                } else {
                    swal("Error", res.message, "error");
                }
            }, "json");
        }
    });
});

// User profile update
$("#userSettingsForm").on("submit", function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    $.ajax({
        url: "../api/update_profile.php",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (res) {
            if (res.status === "success") {
                swal("Success", res.message, "success")
                    .then(() => window.location.href = "dashboard.php");
            } else {
                swal("Error", res.message, "error");
            }
        }
    });
});


// User cancel order logic
$(document).on("click", ".cancelOrder_btn:not(:disabled)", function () {
    const orderId = $(this).data("id");

    swal({
        title: "Cancel this order?",
        text: "This action cannot be undone.",
        icon: "warning",
        buttons: true,
        dangerMode: true
    }).then((willCancel) => {
        if (willCancel) {
            $.ajax({
                url: "../api/cancel_order.php",
                type: "POST",
                data: { order_id: orderId },
                dataType: "json",
                success: function (res) {
                    if (res.status === "success") {
                        swal("Cancelled", res.message, "success")
                            .then(() => {
                                window.location.href = "orders.php";
                            });
                    } else {
                        swal("Error", res.message, "error");
                    }
                }
            });
        }
    });
});

//  Suspend admin logic
$(".suspend_btn").click(function () {
    const adminId = $(this).data("id");

    swal({
        title: "Suspend admin?",
        text: "This admin will not be able to login.",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willSuspend) => {
        if (willSuspend) {
            $.post("../api/suspend_admin.php", { admin_id: adminId }, function (res) {
                if (res.status === "success") {
                    swal("Done", res.message, "success")
                        .then(() => location.reload());
                } else {
                    swal("Error", res.message, "error");
                }
            }, "json");
        }
    });
});

// Delete admin logic
$(".delete_btn").click(function () {
    const adminId = $(this).data("id");

    swal({
        title: "Delete admin?",
        text: "This action cannot be undone!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            $.post("../api/delete_admin.php", { admin_id: adminId }, function (res) {
                if (res.status === "success") {
                    swal("Deleted", res.message, "success")
                        .then(() => window.location.href = "admins.php");
                } else {
                    swal("Error", res.message, "error");
                }
            }, "json");
        }
    });
});

// Update admin
$("#edit_admin_form").submit(function (e) {
    e.preventDefault();
    let formData = new FormData(this);
    $.ajax({
        url: "../api/update_admin.php",
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",

        success: function (res) {
            if (res.status === "success") {
                swal("Updated", res.message, "success")
                    .then(() => window.location.href = "admins.php");
            } else {
                swal("Error", res.message, "error");
            }
        }
    });
});

// Update site setting
$("#settingsForm").on("submit", function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    $.ajax({
        url: "../api/update_settings.php",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (res) {
            if (res.status === "success") {
                swal("Updated", res.message, "success")
                    .then(() => window.location.href = "admin.php");
            } else {
                swal("Error", res.message, "error");
            }
        }
    });
});
// Admin setting
$("#adminProfileForm").submit(function (e) {
    e.preventDefault();
    let formData = new FormData(this);
    $.ajax({
        url: "../api/update_admin_profile.php",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (res) {
            if (res.status === "success") {
                swal("Success", res.message, "success")
                    .then(() => window.location.href = "admin.php");
            } else {
                swal("Error", res.message, "error");
            }
        }
    });
});

// Admin, User suspension and  Unauthorized Access error message
document.addEventListener("DOMContentLoaded", function () {
    const params = new URLSearchParams(window.location.search);
    const error = params.get("error");

    if (!error) return;

    const isAdminPage = window.location.pathname.includes("/admin/");

    if (error === "suspended") {
        swal({
            title: isAdminPage ? "Admin Access Suspended" : "Account Suspended",
            text: isAdminPage
                ? "Your admin access has been suspended. Contact a Super Admin."
                : "Your account has been suspended. Please contact support.",
            icon: "warning",
            button: "OK"
        }).then(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
        });
    }

    if (error === "unauthorized") {
        swal({
            title: "Unauthorized Access",
            text: isAdminPage
                ? "You are not authorized to access this admin page."
                : "You are not authorized to access this page.",
            icon: "error",
            button: "OK"
        }).then(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
        });
    }
});
// Logout message
document.addEventListener("DOMContentLoaded", function () {
    const params = new URLSearchParams(window.location.search);
    const logout = params.get("logout");

    if (logout === "success") {
        swal({
            title: "Logged Out",
            text: "You have been logged out successfully.",
            icon: "success",
            timer: 2000,
            buttons: false
        }).then(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
        });
    }
});



// Reset cart session
$("#resetCartBtn").on("click", function () {
    $.ajax({
        url: "./api/reset_cart.php",       // sends request to PHP file
        method: "POST",
        data: { reset_cart: true }, // field only needs to EXIST, value doesn't matter
        dataType: "json",
        success: function (res) {
            if (res.status === "success") {
                const counter = document.querySelector(".count"); // find cart UI element
                if (counter) {
                    counter.textContent = 0; // update it instantly
                }
                swal("Done", res.message, "success"); // SweetAlert shows PHP message
            } else {
                swal("Error", res.message, "error"); // SweetAlert shows PHP message
            }
        }
    });
});


AOS.init();
