<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="utf-8">
    <title>PET SHOP - Pet Shop Website Template</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free HTML Templates" name="keywords">
    <meta content="Free HTML Templates" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&family=Roboto:wght@700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="lib/flaticon/font/flaticon.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
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
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow-sm py-3 py-lg-0 px-3 px-lg-0">
        <a href="index.php" class="navbar-brand ms-lg-5">
            <h1 class="m-0 text-uppercase text-dark"><i class="bi bi-shop fs-1 text-primary me-3"></i>Pet Shop</h1>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto py-0">
                <?php

                if (isset($_COOKIE['email'])) {
                    $email = $_COOKIE['email'];
                    $userType = $_COOKIE['userType'];
                    echo "<h4 class='py-4 ' >Welcome ${userType}, ${email} </h4>";
                }
                ?>

                <a href="index.php" class="nav-item nav-link">Home</a>
                <?php
                if (
                    isset($_COOKIE['userType']) &&
                    $_COOKIE['userType'] == 'admin'
                ) {
                    echo "<a href='calendar.php' class='nav-item nav-link active'> Calendar </a>";
                    echo "<a href='admin_products_dashboard.php' class='nav-item nav-link'>Products</a>";
                    //<!-- goes back to login page when logged out -->
                    echo "<a href='logout_script.php' class='nav-item nav-link'>Logout</a>";
                }

                ?>
                <a href="contact.html" class="nav-item nav-link nav-contact bg-primary text-white px-5 ms-lg-5">Contact
                    <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->
    <?php
    function connectToDB()
    {
        $hostname = "localhost";
        $database = "Shopee";
        $db_login = "root";
        $db_pass = "";

        /*
        MYSQLI_REPORT_ERROR	    Report errors from mysqli function calls
        MYSQLI_REPORT_STRICT	Throw mysqli_sql_exception for errors instead of warnings
        */
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $dlink = mysqli_connect($hostname, $db_login, $db_pass, $database);
        mysqli_select_db($dlink, $database);
        // returns $dlink so other functions can use the same connection
        // should probably terminate it at some point
        // to avoid cpu leak 
        return $dlink;
    }


    function displayCalendar($dlink)
    {


        // Get the current year and month
        $year = date('Y');
        $month = date('m');


        $get_currentDate_sql = "SELECT DATE_FORMAT( DATE, '%d' ) AS date_only, COUNT( * ) AS count FROM purchase WHERE DATE_FORMAT( DATE, '%m' ) = MONTH( NOW( ) ) GROUP BY date_only HAVING COUNT( * ) >=1";
        $month_date_result = mysqli_query($dlink, $get_currentDate_sql);

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
        echo "<table width=100% border=1><caption>$month_name $year</caption>";

        // Print the table headers (days of the week)
        echo "<tr>";
        $tableHeadersHTML = <<<HTML
        <th style="text-align: center;">SUN</th>
        <th style="text-align: center;">MON</th>
        <th style="text-align: center;">TUE</th>
        <th style="text-align: center;">WED</th>
        <th style="text-align: center;">THUR</th>
        <th style="text-align: center;">FRI</th>
        <th style="text-align: center;">SAT</th>
    HTML;
        echo $tableHeadersHTML;
        echo "</tr>";

        // Start a new row for the first week
        echo "<tr>";

        // Print blank cells for the days before the first day of the month
        for ($i = 0; $i < $first_day_index; $i++) {
            echo "<td></td>";
        }

        $date_with_orders = array();
        $count_of_orders_in_a_date = array();
        while ($row2 = mysqli_fetch_array($month_date_result)) {
            $date_with_orders[] = $row2['date_only'];
            $count_of_orders_in_a_date[] = $row2['count'];
        }
        $count_of_days_with_orders = sizeof($date_with_orders);
        // starts loop with 0 to follow loop standards
        for ($day = 0; $day < $num_days; $day++) {

            // skips day 0 and proceeds to day 1
            // this is needed otherwise 0 is added to calendar
            // which is not desirable
            if ($day == 0)
                continue;
            // Start a new row at the beginning of each week
            if ($day > 1 && ($day - 1 + $first_day_index) % 7 == 0) {
                echo "</tr><tr>";
            }

            $dayHasOrders = false;
            // loops through all the days with orders
            // and adds a clickable anchor tag
            for ($i = 0; $i < $count_of_days_with_orders; $i++) {
                if ($date_with_orders[$i] == $day) {
                    $dayHasOrders = true;
                    //print_r($count_of_orders_in_a_date[$i]);
                    echo "<td align=center> <a href=admin_orders_dashboard.php?date_selected=${day}&quantity=${count_of_orders_in_a_date[$i]}> $day($count_of_orders_in_a_date[$i]) </a></td>";
                    break;
                }
            }
            // if a day has no orders
            // just print it as a normal text
            if (!$dayHasOrders) {
                echo "<td align=center> $day</td>";
            }

        }

        // Print blank cells for the days after the last day of the month
        for ($i = $num_days + $first_day_index; $i < 35; $i++) {
            echo "<td></td>";
        }

        // End the last row and the table
        echo "</tr></table>";
    }

    $dlink = connectToDB();
    displayCalendar($dlink);
    ?>


    <!-- Footer Start -->
    <div class="container-fluid bg-light mt-5 py-5">
        <div class="container pt-5">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-uppercase border-start border-5 border-primary ps-3 mb-4">Get In Touch</h5>
                    <p class="mb-4">No dolore ipsum accusam no lorem. Invidunt sed clita kasd clita et et dolor sed
                        dolor</p>
                    <p class="mb-2"><i class="bi bi-geo-alt text-primary me-2"></i>123 Street, New York, USA</p>
                    <p class="mb-2"><i class="bi bi-envelope-open text-primary me-2"></i>info@example.com</p>
                    <p class="mb-0"><i class="bi bi-telephone text-primary me-2"></i>+012 345 67890</p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-uppercase border-start border-5 border-primary ps-3 mb-4">Quick Links</h5>
                    <div class="d-flex flex-column justify-content-start">
                        <a class="text-body mb-2" href="#"><i class="bi bi-arrow-right text-primary me-2"></i>Home</a>
                        <a class="text-body mb-2" href="#"><i class="bi bi-arrow-right text-primary me-2"></i>About
                            Us</a>
                        <a class="text-body mb-2" href="#"><i class="bi bi-arrow-right text-primary me-2"></i>Our
                            Services</a>
                        <a class="text-body mb-2" href="#"><i class="bi bi-arrow-right text-primary me-2"></i>Meet The
                            Team</a>
                        <a class="text-body mb-2" href="#"><i class="bi bi-arrow-right text-primary me-2"></i>Latest
                            Blog</a>
                        <a class="text-body" href="#"><i class="bi bi-arrow-right text-primary me-2"></i>Contact Us</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-uppercase border-start border-5 border-primary ps-3 mb-4">Popular Links</h5>
                    <div class="d-flex flex-column justify-content-start">
                        <a class="text-body mb-2" href="#"><i class="bi bi-arrow-right text-primary me-2"></i>Home</a>
                        <a class="text-body mb-2" href="#"><i class="bi bi-arrow-right text-primary me-2"></i>About
                            Us</a>
                        <a class="text-body mb-2" href="#"><i class="bi bi-arrow-right text-primary me-2"></i>Our
                            Services</a>
                        <a class="text-body mb-2" href="#"><i class="bi bi-arrow-right text-primary me-2"></i>Meet The
                            Team</a>
                        <a class="text-body mb-2" href="#"><i class="bi bi-arrow-right text-primary me-2"></i>Latest
                            Blog</a>
                        <a class="text-body" href="#"><i class="bi bi-arrow-right text-primary me-2"></i>Contact Us</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-uppercase border-start border-5 border-primary ps-3 mb-4">Newsletter</h5>
                    <form action="">
                        <div class="input-group">
                            <input type="text" class="form-control p-3" placeholder="Your Email">
                            <button class="btn btn-primary">Sign Up</button>
                        </div>
                    </form>
                    <h6 class="text-uppercase mt-4 mb-3">Follow Us</h6>
                    <div class="d-flex">
                        <a class="btn btn-outline-primary btn-square me-2" href="#"><i class="bi bi-twitter"></i></a>
                        <a class="btn btn-outline-primary btn-square me-2" href="#"><i class="bi bi-facebook"></i></a>
                        <a class="btn btn-outline-primary btn-square me-2" href="#"><i class="bi bi-linkedin"></i></a>
                        <a class="btn btn-outline-primary btn-square" href="#"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>
                <div class="col-12 text-center text-body">
                    <a class="text-body" href="">Terms & Conditions</a>
                    <span class="mx-1">|</span>
                    <a class="text-body" href="">Privacy Policy</a>
                    <span class="mx-1">|</span>
                    <a class="text-body" href="">Customer Support</a>
                    <span class="mx-1">|</span>
                    <a class="text-body" href="">Payments</a>
                    <span class="mx-1">|</span>
                    <a class="text-body" href="">Help</a>
                    <span class="mx-1">|</span>
                    <a class="text-body" href="">FAQs</a>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid bg-dark text-white-50 py-4">
        <div class="container">
            <div class="row g-5">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-md-0">&copy; <a class="text-white" href="#">Your Site Name</a>. All Rights Reserved.
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0">Designed by <a class="text-white" href="https://htmlcodex.com">HTML Codex</a></p>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-primary py-3 fs-4 back-to-top"><i class="bi bi-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></scrip >
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>