

<?php 
$errros = false;
if (!$currentSavedSearch) { 
    $errors = true;
    echo '<h3> Token received is invalid</h3>';
} else {
    require_once('leagueTableComparison_view.php');
} 