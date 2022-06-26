<?php

/**
 * Hellper class to color raw sql queries.
 */
class SyntaxPainter
{

    /**
     * SYNTAX to color
     */
    public const COLORS = array(
        'RED'   => 'indianred',
        'GREEN' => '#00ff00',
        'BLUE'  => 'lightblue'
    );

    public const SYNTAX_STANDARD = array(
        'select '   => self::COLORS['GREEN'],
        'insert '   => self::COLORS['GREEN'],
        'into'      => self::COLORS['GREEN'],
        ' from '    => self::COLORS['GREEN'],
        ' where '   => self::COLORS['GREEN'],
        ' and '     => self::COLORS['GREEN'],
        ' or '      => self::COLORS['GREEN'],
        'update '   => self::COLORS['GREEN'],
        'left join' => self::COLORS['BLUE'],
        'inner join' => self::COLORS['BLUE'],
        ' on '      => self::COLORS['BLUE']
    );

    public const SYNTAX_EXTRA = array(

    );

    public const CHARS = array(
        '>' => array('/\ \>\ /i',self::COLORS['RED']),
        '<' => array('/\ \<\ /i',self::COLORS['RED']),
        '=' => array('/\ \=\ /i',self::COLORS['RED']),
    );

    /**
     * @param string $string
     * @return void
     */
    public static function paint(string $string):void
    {
        $order = 0;
        foreach(self::SYNTAX_STANDARD as $syntax => $color) {
            $string = str_replace($syntax,'<span class="' . 'value' . $order . '" style="color:' . $color . ';">' . $syntax . '</span>',$string);
        }

        $string = SyntaxPainter::colorChars($string);

        echo "<pre style='background:#000;color:#fff;text-transform:uppercase;font-weight:600;padding:5px;'>";
        echo $string;
        echo "</pre>";
    }

    private static function colorChars(string $string):string
    {

        foreach(self::CHARS as $char => $pattern) {
            $string = preg_replace($pattern[0],'<span style="color:' . $pattern[1] .';"> ' . $char . ' </span>',$string);
        }

        $string = str_replace('(','<span style="color' . self::COLORS['RED'] . ';">(',$string);
        $string = str_replace(')',')</span>',$string);

        return $string;
    }
}