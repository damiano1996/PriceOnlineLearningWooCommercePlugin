<?php

/**
 * Class containing static functions to create a cookie containing a list of items.
 * It is used to save a list of product ids.
 */
class CookieList
{

    /**
     * Method to add an item to the cookie list.
     *
     * @param $list_name
     * @param $item
     */
    public static function add_item($list_name, $item)
    {
        $items = self::get_items($list_name);

        $items[] = $item;

        wc_setcookie($list_name, implode('|', $items));
    }

    /**
     * Method to add a list of items to the cookie list.
     *
     * @param $list_name
     * @param $items_to_add
     */
    public static function add_items($list_name, $items_to_add)
    {

        $items = self::get_items($list_name);

        foreach ($items_to_add as $item) {
            $items[] = $item;
        }

        wc_setcookie($list_name, implode('|', $items));
    }

    /**
     * Method to get the array of items from the cookie list.
     *
     * @param $list_name
     * @return array
     */
    public static function get_items($list_name): array
    {
        if (empty($_COOKIE[$list_name])) {
            $items = array();
        } else {
            $items = wp_parse_id_list((array)explode('|', wp_unslash($_COOKIE[$list_name])));
        }

        return $items;
    }

    /**
     * Method to remove items from the cookie list.
     *
     * @param $list_name (string) name of the cookie that contains the list
     * @param $items_to_remove (array) list of items to be removed
     */
    public static function remove_items($list_name, $items_to_remove)
    {
        $items = self::get_items($list_name);

        $items = array_diff($items, $items_to_remove);

        wc_setcookie($list_name, implode('|', $items));

    }

    /**
     * Method to clean the cookie list.
     *
     * @param $list_name
     */
    public static function clean($list_name)
    {
        $items = self::get_items($list_name);
        self::remove_items($list_name, $items);
    }

}