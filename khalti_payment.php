<?php
// khalti_payment.php
session_start();
if (!isset($_SESSION['booking_data'])) {
    header('Location: rooms.php');
    exit();
}
$booking = $_SESSION['booking_data'];
$amount = $booking['total_price'];
$booking_id = $booking['booking_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay with Khalti</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .container { max-width: 450px; width: 100%; background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); padding: 2rem; text-align: center; }
        h2 { color: #2C3333; margin-bottom: 0.5rem; }
        .amount { font-size: 2rem; color: #5C2D91; font-weight: bold; margin: 1rem 0; }
        .details { text-align: left; margin: 1.5rem 0; background: #f1f1f1; padding: 1rem; border-radius: 8px; }
        .detail-row { display: flex; justify-content: space-between; margin-bottom: 0.5rem; }
        .label { color: #666; }
        .value { font-weight: 600; color: #333; }
        .pay-btn { background: #5C2D91; color: #fff; border: none; padding: 1rem 2rem; border-radius: 8px; font-size: 1.2rem; cursor: pointer; width: 100%; transition: background 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .pay-btn:hover { background: #4a2475; }
        .pay-btn:disabled { background: #aaa; cursor: not-allowed; }
        .error { color: #dc3545; margin-top: 1rem; display: none; background: #ffe6e6; padding: 10px; border-radius: 5px; font-size: 0.9rem; }
        .back-link { display: block; margin-top: 1rem; color: #666; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Confirm Payment</h2>
        <p>Complete your booking with Khalti.</p>
        
        <div class="amount">Rs. <?php echo number_format($amount, 2); ?></div>
        
        <div class="details">
            <div class="detail-row">
                <span class="label">Booking ID</span>
                <span class="value">#<?php echo $booking_id; ?></span>
            </div>
            <div class="detail-row">
                <span class="label">Room Type</span>
                <span class="value"><?php echo htmlspecialchars($booking['room_type']); ?></span>
            </div>
        </div>

        <button id="khalti-btn" class="pay-btn">
            Pay with Khalti
        </button>
        <p id="error-msg" class="error"></p>
        
        <!-- Price Breakdown Section -->
        <?php
        $breakdown = $booking['price_breakdown'] ?? [];
        $base_price = $booking['base_price'] ?? 0;
        $nights = $booking['days'] ?? 0;
        
        // Aggregate adjustments
        $adjustments_map = [
            'weekend' => ['amount' => 0, 'label' => 'Weekend Adjustment'],
            'seasonal' => ['amount' => 0, 'label' => 'Seasonal Adjustment'],
            'discount' => ['amount' => 0, 'label' => 'Long Stay Discount'],
            'safetynet' => ['amount' => 0, 'label' => 'Safety Net']
        ];
        
        // Process daily adjustments
        if (isset($breakdown['adjustments']) && is_array($breakdown['adjustments'])) {
            foreach ($breakdown['adjustments'] as $key => $val) {
                if ($key === 'global') {
                    foreach ($val as $adj) {
                        if (isset($adj['type']) && isset($adjustments_map[$adj['type']])) {
                            $adjustments_map[$adj['type']]['amount'] += $adj['amount'];
                        }
                    }
                } else {
                    // Daily adjustments
                    if (isset($val['reasons'])) {
                        foreach ($val['reasons'] as $reason) {
                            if (is_array($reason) && isset($reason['type'])) {
                                if (isset($adjustments_map[$reason['type']])) {
                                    $adjustments_map[$reason['type']]['amount'] += $reason['amount'];
                                }
                            }
                        }
                    }
                }
            }
        } elseif (!empty($breakdown)) {
            // Breakdown exists but 'adjustments' key missing? 
            // This happens if using old session data with new code.
            echo '<div style="background-color: #fff3cd; color: #856404; padding: 10px; border: 1px solid #ffeeba; border-radius: 5px; margin-bottom: 10px; font-size: 0.9em;">
                    <strong>Notice:</strong> Detailed price breakdown is available for new bookings only. Please create a new booking to see details.
                  </div>';
        }
        ?>

        <style>
            .breakdown-toggle {
                background: none;
                border: none;
                color: #5C2D91;
                cursor: pointer;
                font-size: 0.95rem;
                text-decoration: underline;
                margin-bottom: 1rem;
                display: inline-block;
            }
            .breakdown-content {
                display: none;
                text-align: left;
                background: #f9f9f9;
                padding: 1rem;
                border-radius: 8px;
                border: 1px solid #eee;
                margin-bottom: 1.5rem;
                font-size: 0.9rem;
            }
            .breakdown-item {
                margin-bottom: 0.8rem;
                border-bottom: 1px solid #eee;
                padding-bottom: 0.5rem;
            }
            .breakdown-item:last-child {
                border-bottom: none;
                margin-bottom: 0;
            }
            .breakdown-header {
                font-weight: 600;
                color: #444;
                display: flex;
                justify-content: space-between;
            }
            .breakdown-detail {
                color: #666;
                font-size: 0.85rem;
                margin-top: 2px;
            }
            .amount-pos { color: #dc3545; }
            .amount-neg { color: #28a745; }
        </style>

        <button type="button" class="breakdown-toggle" onclick="toggleBreakdown()">View Price Breakdown ▾</button>
        
        <div id="price-breakdown" class="breakdown-content">
            <!-- Base Price -->
            <div class="breakdown-item">
                <div class="breakdown-header">
                    <span>Base Price (Rs.<?php echo number_format($base_price, 2); ?> × <?php echo $nights; ?> nights)</span>
                    <span>Rs. <?php echo number_format($base_price * $nights, 2); ?></span>
                </div>
            </div>

            <!-- Weekend -->
            <div class="breakdown-item">
                <div class="breakdown-header">
                    <span>Weekend Adjustment (+25%)</span>
                    <?php 
                    $wk_markup = isset($breakdown['weekend_markup']) ? $breakdown['weekend_markup'] : 0;
                    if ($wk_markup > 0): ?>
                        <span class="amount-pos">+Rs. <?php echo number_format($wk_markup, 2); ?></span>
                    <?php else: ?>
                        <span>-</span>
                    <?php endif; ?>
                </div>
                <div class="breakdown-detail">
                    <?php echo ($wk_markup > 0) ? '✔ Applied (Fri/Sat)' : '❌ Not applied'; ?>
                </div>
            </div>

            <!-- Seasonal -->
            <div class="breakdown-item">
                <div class="breakdown-header">
                    <span>Seasonal Adjustment (+30%)</span>
                    <?php 
                    $seas_markup = isset($breakdown['seasonal_markup']) ? $breakdown['seasonal_markup'] : 0;
                    if ($seas_markup > 0): ?>
                        <span class="amount-pos">+Rs. <?php echo number_format($seas_markup, 2); ?></span>
                    <?php else: ?>
                        <span>-</span>
                    <?php endif; ?>
                </div>
                <div class="breakdown-detail">
                    <?php echo ($seas_markup > 0) ? '✔ Applied (Peak Season)' : '❌ Not applied'; ?>
                </div>
            </div>

            <!-- Long Stay -->
            <div class="breakdown-item">
                <div class="breakdown-header">
                    <span>Long Stay Discount</span>
                    <?php 
                    $ls_disc = isset($breakdown['long_stay_discount']) ? $breakdown['long_stay_discount'] : 0;
                    if ($ls_disc < 0): ?>
                        <span class="amount-neg">-Rs. <?php echo number_format(abs($ls_disc), 2); ?></span>
                    <?php else: ?>
                        <span>-</span>
                    <?php endif; ?>
                </div>
                <div class="breakdown-detail">
                    <?php echo ($ls_disc < 0) ? '✔ Applied' : '❌ Not applied'; ?>
                </div>
            </div>

            <!-- Safety Net -->
            <div class="breakdown-item">
                <div class="breakdown-header">
                    <span>Safety Net</span>
                    <?php 
                    $safe_adj = isset($breakdown['safety_net_adjustment']) ? $breakdown['safety_net_adjustment'] : 0;
                    if ($safe_adj > 0): ?>
                        <span class="amount-pos">+Rs. <?php echo number_format($safe_adj, 2); ?></span>
                    <?php else: ?>
                        <span>-</span>
                    <?php endif; ?>
                </div>
                <div class="breakdown-detail">
                    <?php echo ($safe_adj > 0) ? '✔ Applied (Min. 80% base)' : '❌ Not applied'; ?>
                </div>
            </div>

            <div style="margin-top: 1rem; border-top: 2px solid #ddd; padding-top: 0.5rem; display: flex; justify-content: space-between; font-weight: bold; color: #333;">
                <span>Final Total</span>
                <span>Rs. <?php echo number_format($amount, 2); ?></span>
            </div>
            
            <p style="margin-top: 10px; font-size: 0.8rem; color: #777; font-style: italic;">
                Prices may vary based on dates, weekends, and seasonal demand.
            </p>
        </div>

        <script>
            function toggleBreakdown() {
                var content = document.getElementById('price-breakdown');
                var btn = document.querySelector('.breakdown-toggle');
                if (content.style.display === 'block') {
                    content.style.display = 'none';
                    btn.innerText = 'View Price Breakdown ▾';
                } else {
                    content.style.display = 'block';
                    btn.innerText = 'Hide Price Breakdown ▴';
                }
            }
        </script>

        <a href="rooms.php" class="back-link">Cancel Payment</a>
    </div>

    <script>
        document.getElementById('khalti-btn').addEventListener('click', function() {
            const btn = this;
            const errorMsg = document.getElementById('error-msg');
            const bookingId = <?php echo json_encode($booking_id); ?>;
            
            // UI Update
            btn.disabled = true;
            btn.innerText = 'Redirecting...';
            errorMsg.style.display = 'none';

            // Clean Fetch Call
            fetch('initiate_khalti_payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    booking_id: bookingId
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Redirect to Khalti
                    window.location.href = data.payment_url;
                } else {
                    throw new Error(data.message || 'Payment initiation failed.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorMsg.innerText = error.message;
                errorMsg.style.display = 'block';
                btn.disabled = false;
                btn.innerText = 'Pay with Khalti';
            });
        });
    </script>
</body>
</html>
