<?php

class DynamicPricing {

    /**
     * Calculate total price based on dynamic rules.
     * 
     * @param float $base_price Base price per night for the room
     * @param string $check_in Check-in date (Y-m-d)
     * @param string $check_out Check-out date (Y-m-d)
     * @return array Price breakdown
     */
    public function calculateTotal(float $base_price, string $check_in, string $check_out): array {
        $start = new DateTime($check_in);
        $end = new DateTime($check_out);
        
        // Interval is needed to iterate, but for total nights we use diff
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($start, $interval, $end);
        
        $nights = 0;
        $calculated_total = 0.0;
        $adjustments = [];

        // Peak Season Months: June(6)-August(8), December(12)-February(2)
        $peak_months = [6, 7, 8, 12, 1, 2];

        foreach ($period as $date) {
            $nights++;
            $night_price = $base_price;
            $date_str = $date->format('Y-m-d');
            $day_adjustments = [];

            // B. Weekend Pricing (Friday & Saturday -> +25%)
            // format('N') returns 1 (Mon) to 7 (Sun). Fri=5, Sat=6.
            $day_of_week = (int)$date->format('N');
            if ($day_of_week === 5 || $day_of_week === 6) {
                // Weekend
                $increase = $base_price * 0.25;
                $night_price += $increase;
                $day_adjustments[] = [
                    'type' => 'weekend',
                    'label' => 'Weekend (+25%)',
                    'amount' => $increase
                ];
            }

            // C. Seasonal Pricing (Peak Months -> +30%)
            $month = (int)$date->format('n');
            if (in_array($month, $peak_months)) {
                $increase = $base_price * 0.30;
                $night_price += $increase;
                $day_adjustments[] = [
                    'type' => 'seasonal',
                    'label' => 'Peak Season (+30%)',
                    'amount' => $increase
                ];
            }

            $calculated_total += $night_price;
            
            if (!empty($day_adjustments)) {
                $adjustments[$date_str] = [
                    'base' => $base_price,
                    'final' => $night_price,
                    'reasons' => $day_adjustments
                ];
            }
        }

        // D. Length of Stay Discount (AFTER total)
        // 3 nights -> 5% off
        // 4–6 nights -> 10% off
        // 7+ nights -> 15% off
        
        $discount_percent = 0.0;
        if ($nights >= 7) {
            $discount_percent = 0.15;
        } elseif ($nights >= 4) {
            $discount_percent = 0.10;
        } elseif ($nights == 3) {
            $discount_percent = 0.05;
        }

        $pre_discount_total = $calculated_total;
        if ($discount_percent > 0) {
            $discount_amount = $pre_discount_total * $discount_percent;
            $calculated_total -= $discount_amount;
            $adjustments['global'][] = [
                'type' => 'discount',
                'label' => "Length of Stay Discount ({$nights} nights, -" . ($discount_percent * 100) . "%)",
                'amount' => -$discount_amount
            ];
        }

        // E. Price Safety Net
        // Final total must NOT go below 80% of base total
        $base_total = $base_price * $nights;
        $min_allowed = $base_total * 0.80;

        if ($calculated_total < $min_allowed) {
            $diff = $min_allowed - $calculated_total;
            $adjustments['global'][] = [
                'type' => 'safetynet',
                'label' => "Price Safety Net Enforced (Reset to 80% of base)",
                'amount' => $diff
            ];
            $calculated_total = $min_allowed;
        }

        return [
            'nights' => $nights,
            'base_total' => $base_total,
            'adjustments' => $adjustments,
            'final_total' => (float)$calculated_total,
            
            // Aggregated Totals for UI
            'weekend_markup' => array_reduce($adjustments, function($carry, $day) {
                if (isset($day['reasons'])) {
                    foreach ($day['reasons'] as $r) {
                        if ($r['type'] === 'weekend') $carry += $r['amount'];
                    }
                }
                return $carry;
            }, 0.0),
            
            'seasonal_markup' => array_reduce($adjustments, function($carry, $day) {
                if (isset($day['reasons'])) {
                    foreach ($day['reasons'] as $r) {
                        if ($r['type'] === 'seasonal') $carry += $r['amount'];
                    }
                }
                return $carry;
            }, 0.0),
            
            'long_stay_discount' => isset($adjustments['global']) ? array_reduce($adjustments['global'], function($carry, $g) {
                if ($g['type'] === 'discount') $carry += $g['amount']; // negative value
                return $carry;
            }, 0.0) : 0.0,
            
            'safety_net_adjustment' => isset($adjustments['global']) ? array_reduce($adjustments['global'], function($carry, $g) {
                if ($g['type'] === 'safetynet') $carry += $g['amount'];
                return $carry;
            }, 0.0) : 0.0
        ];
    }
}
