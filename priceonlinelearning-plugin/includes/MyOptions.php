<?php

class MyOptions
{
    public static function get_min_prices_options(): array
    {
        return array(
            "regular_price - 50%" => -0.5,
            "regular_price - 20%" => -0.2,
            "regular_price - 10%" => -0.1,
            "regular_price (recommended)" => 0.0
        );
    }

    public static function get_max_prices_options(): array
    {
        return array(
            "regular_price" => 0.0,
            "regular_price + 10%" => 0.1,
            "regular_price + 20% (recommended)" => 0.2,
            "regular_price + 50%" => 0.5
        );
    }

    public static function get_days_consistency_options(): array
    {
        return array(
            "1" => 1,
            "2" => 2,
            "3 (recommended)" => 3,
            "7" => 7
        );
    }

    public static function reverse_option($option)
    {
        return array_combine(array_values($option), array_keys($option));
    }
}