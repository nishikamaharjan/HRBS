<?php
// khalti_config.php

// In a real production environment, load this from an actual environment variable
// e.g., $secret_key = getenv('KHALTI_SECRET_KEY');

// For this project, we define it here as requested
define('KHALTI_SECRET_KEY', 'd9c45c8084fd409b9882f4bec8fbf3d1');
define('KHALTI_BASE_URL', 'https://a.khalti.com/api/v2/epayment/');

// Helper to format currency if needed, though Khalti expects Paisa for some endpoints, 
// the v2 initiate endpoint documentation usually asks for specific formats. 
// However, the user payload example showed "amount": 1300. 
// Khalti v2 usually expects unit in Paisa (1 unit = 1 Paisa), so 1300 would be Rs 13.
// BUT, the user's payload shows "amount": 1300 for a room price of 1000 + vat 300.
// Let's assume the user means Rs 1300. 
// Khalti v2 initiate API expects amount in PAISA. 
// So Rs 1300 should be 1300 * 100 = 130000.
// I will convert the amount to Paisa in the logic.

?>
