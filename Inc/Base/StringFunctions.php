<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 3/27/2019
 * Time: 1:35 PM
 */

namespace Fajr\CustomWeeklyClass\Base;


class StringFunctions
{
    function after ($mark, $str)
    {
        if (!is_bool(strpos($str, $mark)))
            return substr($str, strpos($str,$mark)+strlen($mark));
    }

    function after_last ($mark, $str)
    {
        if (!is_bool(strrevpos($str, $mark)))
            return substr($str, StringFunctions::strrevpos($str, $mark)+strlen($mark));
    }

    function before ($mark, $str)
    {
        return substr($str, 0, strpos($str, $mark));
    }

    function before_last ($mark, $str)
    {
        return substr($str, 0, $this->strrevpos($str, $mark));
    }

    function between ($start, $end, $str)
    {
        return StringFunctions::before ($end, StringFunctions::after($start, $str));
    }

    function between_last ($start, $end, $str)
    {
        return StringFunctions::after_last($start, StringFunctions::before_last($end, $str));
    }

// use strrevpos function in case your php version does not include it
    function strrevpos($instr, $needle)
    {
        $rev_pos = strpos (strrev($instr), strrev($needle));
        if ($rev_pos===false) return false;
        else return strlen($instr) - $rev_pos - strlen($needle);
    }

}