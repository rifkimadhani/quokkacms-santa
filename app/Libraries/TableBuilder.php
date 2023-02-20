<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 2/17/2023
 * Time: 1:43 PM
 */

namespace App\Libraries;

class TableBuilder
{
    /**
     * @param $data
     * @param array $extraCol
     * @param $callback = function callback
     * @return string
     */
    static public function renderRow($data, $extraCol=[], $callback='')
    {
        $html = '';
        foreach ($data as $row){
            $html .= '<tr>';

            //render data pada col
            foreach ($row as $col){
                $html .= "<td>{$col}</td>";
            }

            //render extra col
            foreach ($extraCol as $col){
                if (empty($callback)==false){
                    $value = $callback($row);
                } else {
                    $value = '';
                }
                $html .= "<td>{$value}</td>";
            }

            $html .= '</tr>';
        }

        return $html;
    }
}