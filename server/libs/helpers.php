<?php

function ArrSqlInsertInto($array = [], $cutter = false, $dbconnection = 0){

  if(count($array)) {
      foreach ($array as $key => $item) {
          if ($cutter) {
              foreach ($cutter as $cut) {
                  unset($item[$cut]);
                  unset($array[$key][$cut]);
              }
          }
          $escaped_values = array_map(array($dbconnection, 'real_escape_string'), array_values($item));
          $item = implode("','", $escaped_values);
          $values[] = "('{$item}')";
      }

      $values = implode(',', $values);

      $result['columns'] = implode(",", array_keys($array[0]));
      $result['values'] = $values;
  }
    return $result;
}