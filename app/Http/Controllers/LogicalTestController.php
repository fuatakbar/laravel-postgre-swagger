<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogicalTestController extends Controller
{
    public function index() {
        $arr = ['11','12','cii','001','2','1998','7','89','iia','fii'];
        $result = array();

        // filtering an array into letters only
        foreach ($arr as $key => $val) if (is_numeric($val)) unset($arr[$key]);
        $arr = array_values($arr);

        // convert text to lexicoGraphically and assign value into each letter variable
        for ($i = 0; $i < count($arr); $i++) {
            $result[$arr[$i]] = $this->lexicoGraphically($arr[$i]);
        }

        $result = (object) $result;

        return response()->json($result); // the result is an object that can accessed by $result->cii[index] or you can loop by key
    }

    public function lexicoGraphically($str) {
        $len = strlen($str);
        $iteration = ($len * 2) - 1;
        $result = array();

        for ($i = 0; $i < $iteration; $i++) {
            if ($i+1 <= $len) $result[$i] = substr($str, 0, $i+1);
            else $result[$i] = substr($str, $i-($len-1), $i+1);
        }

        return $result;
    }
}
