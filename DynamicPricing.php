<?php
/**
 * Dynamic Pricing Algorithm for Hotel Room Booking System
 * 
 * This class implements a sophisticated dynamic pricing model that considers:
 * - Base room prices
 * - Day of week multipliers (weekend pricing)
 * - Seasonal/peak period multipliers
 * - Advance booking discounts (early bird)
 * - Length of stay discounts
 * - Occupancy-based pricing (demand-based)
 * - Holiday pricing
 */

class DynamicPricing {
    private $conn;
    
    // Base prices for each room type
    private $base_prices = [
        'normal' => 1500,
        'deluxe' => 3000,
        'suite' => 5000,
        'luxury' => 4000,
        'premium' => 4500,
        'executive' => 3800,
        'family' => 3500,
        'single' => 1200
    ];
    
    // Peak season months (1 = January, 12 = December)
    private $peak_seasons = [12, 1, 2, 6, 7, 8]; // Dec, Jan, Feb (winter), Jun, Jul, Aug (summer)
    
    // Major holidays (format: 'MM-DD')
    private $holidays = [
        '01-01', // New Year
        '02-14', // Valentine's Day
        '04-14', // New Year (Nepal)
        '05-01', // Labor Day
        '08-30', // Janai Purnima
        '10-02', // Dashain
        '10-20', // Tihar
        '12-25', // Christmas
        '12-31'  // New Year's Eve
    ];
    
    public function __construct($database_connection) {
        $this->conn = $database_connection;
    }
    
    /**
     * Calculate dynamic price for a room booking
     * 
     * @param string $room_type Room type identifier
     * @param string $check_in Check-in date (Y-m-d)
     * @param string $check_out Check-out date (Y-m-d)
     * @return array Array containing price breakdown and total
     */
    public function calculatePrice($room_type, $check_in, $check_out, $db_base_price = null) {
        if ($db_base_price !== null) {
            $base_price = $db_base_price;
        } elseif (isset($this->base_prices[$room_type])) {
            $base_price = $this->base_prices[$room_type];
        } else {
            throw new Exception("Invalid room type: $room_type");
        }
        $check_in_date = new DateTime($check_in);
        $check_out_date = new DateTime($check_out);
        $days = $check_in_date->diff($check_out_date)->days;
        
        if ($days <= 0) {
            throw new Exception("Invalid date range");
        }
        
        $price_breakdown = [];
        $total_price = 0;
        $daily_prices = [];
        
        // Calculate price for each day
        $current_date = clone $check_in_date;
        for ($i = 0; $i < $days; $i++) {
            $day_price = $base_price;
            $factors = [];
            
            // 1. Day of week multiplier (Friday, Saturday are weekends)
            $day_of_week = (int)$current_date->format('w'); // 0 = Sunday, 6 = Saturday
            if ($day_of_week == 5 || $day_of_week == 6) { // Friday or Saturday
                $weekend_multiplier = 1.25; // 25% increase on weekends
                $day_price *= $weekend_multiplier;
                $factors[] = ['name' => 'Weekend', 'multiplier' => $weekend_multiplier];
            }
            
            // 2. Holiday multiplier
            $date_str = $current_date->format('m-d');
            if (in_array($date_str, $this->holidays)) {
                $holiday_multiplier = 1.50; // 50% increase on holidays
                $day_price *= $holiday_multiplier;
                $factors[] = ['name' => 'Holiday', 'multiplier' => $holiday_multiplier];
            }
            
            // 3. Peak season multiplier
            $month = (int)$current_date->format('n');
            if (in_array($month, $this->peak_seasons)) {
                $peak_multiplier = 1.30; // 30% increase during peak season
                $day_price *= $peak_multiplier;
                $factors[] = ['name' => 'Peak Season', 'multiplier' => $peak_multiplier];
            }
            
            // 4. Advance booking discount
            $today = new DateTime();
            $days_until_checkin = $today->diff($current_date)->days;
            
            if ($days_until_checkin >= 30) {
                $early_bird_discount = 0.15; // 15% discount for 30+ days advance booking
                $day_price *= (1 - $early_bird_discount);
                $factors[] = ['name' => 'Early Bird Discount', 'multiplier' => (1 - $early_bird_discount), 'type' => 'discount'];
            } elseif ($days_until_checkin >= 14) {
                $early_bird_discount = 0.10; // 10% discount for 14-29 days advance booking
                $day_price *= (1 - $early_bird_discount);
                $factors[] = ['name' => 'Early Bird Discount', 'multiplier' => (1 - $early_bird_discount), 'type' => 'discount'];
            } elseif ($days_until_checkin >= 7) {
                $early_bird_discount = 0.05; // 5% discount for 7-13 days advance booking
                $day_price *= (1 - $early_bird_discount);
                $factors[] = ['name' => 'Early Bird Discount', 'multiplier' => (1 - $early_bird_discount), 'type' => 'discount'];
            }
            
            // 5. Last-minute booking surcharge
            if ($days_until_checkin <= 2 && $days_until_checkin >= 0) {
                $last_minute_surcharge = 1.20; // 20% surcharge for last-minute bookings
                $day_price *= $last_minute_surcharge;
                $factors[] = ['name' => 'Last-Minute Booking', 'multiplier' => $last_minute_surcharge];
            }
            
            // 6. Occupancy-based pricing (demand-based)
            $occupancy_rate = $this->getOccupancyRate($current_date->format('Y-m-d'), $room_type);
            if ($occupancy_rate >= 0.80) { // High demand (80%+ booked)
                $demand_multiplier = 1.20; // 20% increase
                $day_price *= $demand_multiplier;
                $factors[] = ['name' => 'High Demand', 'multiplier' => $demand_multiplier];
            } elseif ($occupancy_rate >= 0.60) { // Medium-high demand (60-80% booked)
                $demand_multiplier = 1.10; // 10% increase
                $day_price *= $demand_multiplier;
                $factors[] = ['name' => 'Medium Demand', 'multiplier' => $demand_multiplier];
            } elseif ($occupancy_rate <= 0.30) { // Low demand (30% or less booked)
                $demand_discount = 0.10; // 10% discount
                $day_price *= (1 - $demand_discount);
                $factors[] = ['name' => 'Low Demand Discount', 'multiplier' => (1 - $demand_discount), 'type' => 'discount'];
            }
            
            // Round to 2 decimal places
            $day_price = round($day_price, 2);
            $daily_prices[] = [
                'date' => $current_date->format('Y-m-d'),
                'day_name' => $current_date->format('l'),
                'base_price' => $base_price,
                'final_price' => $day_price,
                'factors' => $factors
            ];
            
            $total_price += $day_price;
            $current_date->modify('+1 day');
        }
        
        // 7. Length of stay discount
        $length_discount = 0;
        if ($days >= 7) {
            $length_discount = 0.15; // 15% discount for 7+ nights
        } elseif ($days >= 4) {
            $length_discount = 0.10; // 10% discount for 4-6 nights
        } elseif ($days >= 3) {
            $length_discount = 0.05; // 5% discount for 3 nights
        }
        
        $subtotal = $total_price;
        if ($length_discount > 0) {
            $discount_amount = $total_price * $length_discount;
            $total_price = $total_price - $discount_amount;
            $price_breakdown['length_discount'] = [
                'days' => $days,
                'discount_percent' => $length_discount * 100,
                'discount_amount' => round($discount_amount, 2)
            ];
        }
        
        // Ensure minimum price is at least 80% of base price
        $minimum_price = ($base_price * $days) * 0.80;
        if ($total_price < $minimum_price) {
            $total_price = $minimum_price;
        }
        
        return [
            'base_price' => $base_price,
            'nights' => $days,
            'daily_prices' => $daily_prices,
            'subtotal' => round($subtotal, 2),
            'length_discount' => isset($price_breakdown['length_discount']) ? $price_breakdown['length_discount'] : null,
            'total_price' => round($total_price, 2),
            'average_price_per_night' => round($total_price / $days, 2)
        ];
    }
    
    /**
     * Get occupancy rate for a specific date and room type
     * This simulates demand-based pricing by checking existing bookings
     * 
     * @param string $date Date to check (Y-m-d)
     * @param string $room_type Room type
     * @return float Occupancy rate (0.0 to 1.0)
     */
    private function getOccupancyRate($date, $room_type) {
        // For now, we'll use a simulated occupancy rate
        // In a production system, you would query actual bookings from database
        
        // Assuming total rooms per type (can be configured)
        $total_rooms = [
            'normal' => 10,
            'deluxe' => 8,
            'suite' => 5,
            'luxury' => 6,
            'premium' => 7,
            'executive' => 8,
            'family' => 6,
            'single' => 12
        ];
        
        if (!isset($total_rooms[$room_type])) {
            return 0.5; // Default 50% occupancy
        }
        
        // Try to get actual booking count from database
        try {
            // Note: This would need check_in and check_out columns in bookings table
            // For now, we'll use a simulated approach based on booking_date
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) as booked_count 
                FROM bookings 
                WHERE room_type LIKE ? 
                AND status != 'cancelled'
                AND booking_date = ?
            ");
            
            $room_type_name = '%' . ucfirst($room_type) . '%';
            $stmt->bind_param("ss", $room_type_name, $date);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $booked_count = $row['booked_count'] ?? 0;
            
            // Simulate occupancy based on date (add some randomness based on day of week)
            $day_of_week = (new DateTime($date))->format('w');
            $base_occupancy = 0.4; // Base 40% occupancy
            
            // Weekend bookings are typically higher
            if ($day_of_week == 5 || $day_of_week == 6) {
                $base_occupancy = 0.6;
            }
            
            // Add actual bookings (assuming each booking is 1 room)
            $occupancy_from_bookings = min($booked_count / $total_rooms[$room_type], 1.0);
            
            // Combine simulated and actual occupancy
            $final_occupancy = min($base_occupancy + ($occupancy_from_bookings * 0.5), 1.0);
            
            return $final_occupancy;
        } catch (Exception $e) {
            // Fallback to simulated occupancy
            $day_of_week = (new DateTime($date))->format('w');
            if ($day_of_week == 5 || $day_of_week == 6) {
                return 0.65; // 65% on weekends
            }
            return 0.45; // 45% on weekdays
        }
    }
    
    /**
     * Get base price for a room type
     * 
     * @param string $room_type Room type identifier
     * @return float Base price
     */
    public function getBasePrice($room_type) {
        return isset($this->base_prices[$room_type]) ? $this->base_prices[$room_type] : 0;
    }
}

?>