<?php
function renderView($viewName, $data = [], $title = 'POS System') {
    // Extract data variables to be used in the view
    extract($data);
    
    // Start output buffering to capture the view content
    ob_start();
    
    // Include the view file
    include 'views/' . $viewName . '.php';
    
    // Get the captured content
    $content = ob_get_clean();
    
    // Include the layout and pass the content
    include 'views/layout.php';
}
?>