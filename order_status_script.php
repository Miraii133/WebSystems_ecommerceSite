<?php

function displayStatusOptions()
{
    $status_menu_HTML = <<<HTML
    <div class="fashion_section" style="display: flex; justify-content: center; align-items: center; flex-direction: column;">
<div style="display: flex; align-items: center; justify-content: center; columns: 100px 3;">
    
<a href="?status=all">
        <p style="text-align: center; font-size: large; font-weight: 100; padding-left: 120px; padding-right: 120px; padding-bottom: 25px;">
        All
       </p>
    </a>
<a href="?status=pending">
        <p style="text-align: center; font-size: large; font-weight: 100; padding-left: 120px; padding-right: 120px; padding-bottom: 25px;">
        Pending
       </p>
    </a>
    <a href="?status=accepted">
        <p style="text-align: center; font-size: large; font-weight: 100; padding-left: 120px; padding-right: 120px; padding-bottom: 25px;">
        Accepted
       </p>
    </a>
    <a href="?status=Completed">
        <p style="text-align: center; font-size: large; font-weight: 100; padding-left: 120px; padding-right: 120px; padding-bottom: 25px;">
        Completed
       </p>
    </a>
    <a href="?status=Refunded">
        <p style="text-align: center; font-size: large; font-weight: 100; padding-left: 120px; padding-right: 120px; padding-bottom: 25px;">
        Refunded
       </p>
    </a>
</div>
</div>
HTML;
    echo $status_menu_HTML;
}

echo displayStatusOptions();

?>