<?php

class CookieList
{

    public static function add_item($list_name, $item)
    {
        $items = self::get_items($list_name);

        $items[] = $item;

        wc_setcookie($list_name, implode('|', $items));
    }

    public static function get_items($list_name): array
    {
        if (empty($_COOKIE[$list_name])) {
            $items = array();
        } else {
            $items = wp_parse_id_list((array)explode('|', wp_unslash($_COOKIE[$list_name])));
        }

        return $items;
    }

    public static function add_items($list_name, $items_to_add)
    {

        $items = self::get_items($list_name);

        foreach ($items_to_add as $item) {
            $items[] = $item;
        }

        wc_setcookie($list_name, implode('|', $items));
    }

    /**
     * @param $list_name (string) name of the cookie that contains the list
     * @param $items_to_remove (array) list of items to be removed
     */
    public static function remove_items($list_name, $items_to_remove)
    {
        $items = self::get_items($list_name);

        $items = array_diff($items, $items_to_remove);

        wc_setcookie($list_name, implode('|', $items));

    }

}