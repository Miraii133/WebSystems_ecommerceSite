<!-- Topbar Start -->
<div class="container-fluid border-bottom d-none d-lg-block">
    <div class="row gx-0">
        <div class="col-lg-4 text-center py-2">
            <div class="d-inline-flex align-items-center">
                <i class="bi bi-geo-alt fs-1 text-primary me-3"></i>
                <div class="text-start">
                    <h6 class="text-uppercase mb-1">Our Office</h6>
                    <span>123 Street, New York, USA</span>
                </div>
            </div>
        </div>
        <div class="col-lg-4 text-center border-start border-end py-2">
            <div class="d-inline-flex align-items-center">
                <i class="bi bi-envelope-open fs-1 text-primary me-3"></i>
                <div class="text-start">
                    <h6 class="text-uppercase mb-1">Email Us</h6>
                    <span>info@example.com</span>
                </div>
            </div>
        </div>
        <div class="col-lg-4 text-center py-2">
            <div class="d-inline-flex align-items-center">
                <i class="bi bi-phone-vibrate fs-1 text-primary me-3"></i>
                <div class="text-start">
                    <h6 class="text-uppercase mb-1">Call Us</h6>
                    <span>+012 345 6789</span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Topbar End -->


<!-- Navbar Start -->
<nav class="navbar navbar-expand-lg bg-white navbar-light shadow-sm py-3 py-lg-0 px-3 px-lg-0 mb-5">
    <a href="index.php" class="navbar-brand ms-lg-5">
        <h1 class="m-0 text-uppercase text-dark"><i class="bi bi-shop fs-1 text-primary me-3"></i>Pet Shop</h1>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto py-0">
            <?php
            // adds Welcome message when user is logged in
            if (isset($_COOKIE['email'])) {
                $email = $_COOKIE['email'];
                $userType = $_COOKIE['userType'];
                echo "<h4 class='py-4 ' >Welcome ${userType}, ${email} </h4>";
            }
            ?>
            <a href="index.php" class="nav-item nav-link">Home</a>
            <a href="product.php" class="nav-item nav-link active">Product</a>

            <?php
            // shows register and login when not logged in,
            // shows cart and logout when logged in as only logged in users should
            // be able to see.
            if (!isset($_COOKIE['email'])) {
                echo "<a href='register.php' class='nav-item nav-link'>Register</a>";
                echo "<a href='login.php' class='nav-item nav-link'>Login</a>";
            } else {
                echo "<a href='cart.php' class='nav-item nav-link'>Cart</a>";
                echo "<a href='orders.php' class='nav-item nav-link'>My Orders</a>";
                echo "<a href='logout_script.php'  class='nav-item nav-link'>Logout</a>";
            }
            ?>
            <a href="contact.html" class="nav-item nav-link nav-contact bg-primary text-white px-5 ms-lg-5">
                Contact <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</nav>
<!-- Navbar End -->

<?php
// Get the current year and month
$year = date('Y');
$month = date('m');

// Get the number of days in the current month
$num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

// Get the name of the current month, F in format('F') means the full name of the month
$date = new DateTime("$year-$month-01");
$month_name = $date->format('F');

// Get the index of the first day of the month (0 = Sunday, 1 = Monday, etc.)
//The first argument, 'w', specifies that we want to retrieve the day of the week as a numeric value (0 for Sunday, 1 for Monday, and so on).
//strtotime function creates a timestamp representing the first day of the given month and year.
$first_day_index = (int) date('w', strtotime("$year-$month-01"));

// Start the table and print the month name
echo "<table width=80% border=1><caption>$month_name $year</caption>";

// Print the table headers (days of the week)
echo "<tr>";
echo "<th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th>";
echo "<th>Thu</th><th>Fri</th><th>Sat</th>";
echo "</tr>";

// Start a new row for the first week
echo "<tr>";

// Print blank cells for the days before the first day of the month
for ($i = 0; $i < $first_day_index; $i++) {
    echo "<td></td>";
}

// Print the cells for the days of the month
for ($day = 1; $day <= $num_days; $day++) {
    // Start a new row at the beginning of each week
    if ($day > 1 && ($day - 1 + $first_day_index) % 7 == 0) {
        echo "</tr><tr>";
    }

    // Print the cell for the current day
    echo "<td align=center>$day</td>";
}

// Print blank cells for the days after the last day of the month
for ($i = $num_days + $first_day_index; $i < 42; $i++) {
    echo "<td></td>";
}

// End the last row and the table
echo "</tr></table>";
?>