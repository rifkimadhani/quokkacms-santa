<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 2/23/2023
 * Time: 4:12 PM
 */

namespace App\Libraries;


class HtmlBuilder
{

    static public function renderOption($data, $select=''){

        //bentuk option
        $html = '';
        foreach ($data as $item){
            $id = $item['id'];
            $value = $item['value'];
            if ($select==$id) $selected='selected'; else $selected='';
            $html .= "<option value='$id' $selected>$value</option>\n";
        }

        return $html;
    }

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