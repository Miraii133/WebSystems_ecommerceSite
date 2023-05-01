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
