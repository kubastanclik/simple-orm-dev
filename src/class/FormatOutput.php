<?php

/**
 * Hellper class to color raw output.
 */
class FormatOutput
{
    /**
     * @param $result
     * @return void
     */
    public static function format($result):void
    {
        echo "<pre style='background:#000;color:#fff;font-weight:600;padding:5px;'>";

        if ( count($result) ) {
            foreach ($result as $key => $val) {
                echo "<p>";
                echo "<span style='color:#00ff00;'> $key </span><span style='color:#00ff00'> => </span><br />";
                if (is_array($val)) {
                    foreach ($val as $k => $v) {
                        echo "<span style='color:lightblue'>  $k </span><span style='color:#00ff00'> => </span><span style='color:indianred'>  $v  </span><br />";
                    }
                }
                echo "</p>";
            }
        }else{
            echo "<span style='color:indianred'>NOTHING TO DISPLAY</span>";
        }

        echo "</pre>";
    }
}