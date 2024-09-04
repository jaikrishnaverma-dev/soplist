<?php
// Fetch subscription details
$result = $conn->query("SELECT * FROM subscriptions WHERE id = 1");
$row = $result->fetch_assoc();

$current_date = new DateTime();
$start_date = new DateTime($row['start_date']);
$expiry_date = new DateTime($row['expiry_date']);

// Determine the subscription status and alert message
if ($current_date > $expiry_date) {
    $status_message = 'Subscription has expired. Please renew.';
    $alert_class = 'text-danger'; // Use Bootstrap's alert-danger class for expired status
} elseif ($current_date < $start_date) {
    $remaining_days = $start_date->diff($current_date)->days;
    $status_message = 'Subscription will start in ' . $remaining_days . ' day(s).';
    $alert_class = 'text-warning'; // Use Bootstrap's alert-warning class for not yet started status
}elseif($row['status']!=="active"){
    $status_message = 'Subscription '.ucfirst($row['status']).' by Admin.';
    $alert_class = 'text-warning'; // Use Bootstrap's alert-warning class for not yet started status
}

// Prevent further processing if subscription status is handled
if ($current_date > $expiry_date || $current_date < $start_date||$row['status']!=="active") {
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Subscription Status</title>
        <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <style>
            .container {
                margin-top: 20px;
            }
        </style>
    </head>

    <body></body>
    <section class="py-3 py-md-5 min-vh-100 d-flex justify-content-center align-items-center">
        <div class="container" style="max-width:800px">
            <div class="row">
                <div class="col-12">
                    <div class="text-center">
                        <h2 class="d-flex justify-content-center align-items-center gap-2 mb-4">
                            <i class="bi bi-exclamation-circle-fill text-danger display-4 mt-2"></i>
                            <span class="display-1 fw-bold bsb-flip-h">Notice</span>
                        </h2>
                        <div class="alert <?php echo $alert_class; ?>" role="alert">
                            <h3 class="h2 mb-2"><?php echo $status_message; ?></h3>
                        </div>

                        <!-- <p class="mb-5">It seems there was an issue with your subscription. Please check the following details:</p> -->
                        <div class=" mt-5 " role="alert">
                            <!-- <h3><?php // echo $status_message; 
                                        ?></h3> -->
                            <!-- <hr> -->
                            <p class="mb-0">For assistance, you can contact us via email at: <br> <a href="mailto:jaikrishnavermaofficial@gmail.com">jaikrishnavermaofficial@gmail.com</a> or call us at <strong>+91 8756706608</strong>.</p>
                        </div>
                        <!-- <a class="btn btn-dark rounded-pill px-5 fs-6 m-0" href="renew_subscription_page.html" role="button">Renew Subscription</a> -->
                        <!-- Optionally, include a "Back to Home" button -->
                        <!-- <a class="btn btn-light rounded-pill px-5 fs-6 m-0 mt-3" href="index.html" role="button">Back to Home</a> -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://unpkg.com/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>

    </html>
<?php
    die;
}
?>