<?php

function displayStatusOptions($date_selected)
{
    $status_menu_HTML = <<<HTML
    <div class="fashion_section" style="display: flex; justify-content: center; align-items: center; flex-direction: column;">
<div style="display: flex; align-items: center; justify-content: center; columns: 100px 3;">
    
<a href="?date_selected=${date_selected}&status=all">
        <p id="#th_all" style="text-align: center; font-size: large; font-weight: 100; padding-left: 120px; padding-right: 120px; padding-bottom: 25px;">
        All
       </p>
    </a>
<a href="?date_selected=${date_selected}&status=pending">
        <p id="#th_pending" style="text-align: center; font-size: large; font-weight: 100; padding-left: 120px; padding-right: 120px; padding-bottom: 25px;">
        Pending
       </p>
    </a>
    <a href="?date_selected=${date_selected}&status=accepted">
        <p id="#th_accepted" style="text-align: center; font-size: large; font-weight: 100; padding-left: 120px; padding-right: 120px; padding-bottom: 25px;">
        Accepted
       </p>
    </a>
    <a href="?date_selected=${date_selected}&status=completed">
        <p id="#th_completed" style="text-align: center; font-size: large; font-weight: 100; padding-left: 120px; padding-right: 120px; padding-bottom: 25px;">
        Completed
       </p>
    </a>
    <a href="?date_selected=${date_selected}&status=refunded">
        <p id="#th_refunded" style="text-align: center; font-size: large; font-weight: 100; padding-left: 120px; padding-right: 120px; padding-bottom: 25px;">
        Refunded
       </p>
    </a>
</div>
</div>
HTML;
    echo $status_menu_HTML;
}
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['date_selected'])) {
    displayStatusOptions($_GET['date_selected']);
}

?>