<?php

/* 
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from ... in writing.
 */
function nz($source,$result) {
    return empty($source)?$result:$source;
}

if (!function_exists('array_column')) {

    /**
     * Returns the values from a single column of the input array, identified by
     * the $columnKey.
     *
     * Optionally, you may provide an $indexKey to index the values in the returned
     * array by the values from the $indexKey column in the input array.
     *
     * @param array $input A multi-dimensional array (record set) from which to pull
     *                     a column of values.
     * @param mixed $columnKey The column of values to return. This value may be the
     *                         integer key of the column you wish to retrieve, or it
     *                         may be the string key name for an associative array.
     * @param mixed $indexKey (Optional.) The column to use as the index/keys for
     *                        the returned array. This value may be the integer key
     *                        of the column, or it may be the string key name.
     * @return array
     */
    function array_column($input = null, $columnKey = null, $indexKey = null)
    {
        // Using func_get_args() in order to check for proper number of
        // parameters and trigger errors exactly as the built-in array_column()
        // does in PHP 5.5.
        $argc = func_num_args();
        $params = func_get_args();

        if ($argc < 2) {
            trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
            return null;
        }

        if (!is_array($params[0])) {
            trigger_error('array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given', E_USER_WARNING);
            return null;
        }

        if (!is_int($params[1])
            && !is_float($params[1])
            && !is_string($params[1])
            && $params[1] !== null
            && !(is_object($params[1]) && method_exists($params[1], '__toString'))
        ) {
            trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
            return false;
        }

        if (isset($params[2])
            && !is_int($params[2])
            && !is_float($params[2])
            && !is_string($params[2])
            && !(is_object($params[2]) && method_exists($params[2], '__toString'))
        ) {
            trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
            return false;
        }

        $paramsInput = $params[0];
        $paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;

        $paramsIndexKey = null;
        if (isset($params[2])) {
            if (is_float($params[2]) || is_int($params[2])) {
                $paramsIndexKey = (int) $params[2];
            } else {
                $paramsIndexKey = (string) $params[2];
            }
        }

        $resultArray = [];

        foreach ($paramsInput as $row) {

            $key = $value = null;
            $keySet = $valueSet = false;

            if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
                $keySet = true;
                $key = (string) $row[$paramsIndexKey];
            }

            if ($paramsColumnKey === null) {
                $valueSet = true;
                $value = $row;
            } elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
                $valueSet = true;
                $value = $row[$paramsColumnKey];
            }

            if ($valueSet) {
                if ($keySet) {
                    $resultArray[$key] = $value;
                } else {
                    $resultArray[] = $value;
                }
            }

        }

        return $resultArray;
    }

}
/**
 * Sorts a multi dimensional array
 *
 * $arr1 = array(
 *    array('id'=>1,'name'=>'aA','cat'=>'cc'),
 *    array('id'=>2,'name'=>'aa','cat'=>'dd'),
 *    array('id'=>3,'name'=>'bb','cat'=>'cc'),
 *    array('id'=>4,'name'=>'bb','cat'=>'dd')
 * );
 *
 * $arr2 = array_msort($arr1, array('name'=>SORT_DESC, 'cat'=>SORT_ASC));
 *
 *
 * @param $array
 * @param $cols
 *
 * @return array
 */
function array_msort($array, $cols)
{
  $colarr = array();
  foreach ($cols as $col => $order) {
    $colarr[$col] = array();
    foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
  }
  $eval = 'array_multisort(';
  foreach ($cols as $col => $order) {
    $eval .= '$colarr[\''.$col.'\'],'.$order.',';
  }
  $eval = substr($eval,0,-1).');';
  eval($eval);
  $ret = array();
  foreach ($colarr as $col => $arr) {
    foreach ($arr as $k => $v) {
      $k = substr($k,1);
      if (!isset($ret[$k])) $ret[$k] = $array[$k];
      $ret[$k][$col] = $array[$k][$col];
    }
  }
  return $ret;

}

/**
 * Tell whether all members of $array validate the $predicate.
 *
 * all(array(1, 2, 3),   'is_int'); -> true
 * all(array(1, 2, 'a'), 'is_int'); -> false
 */
function all($array, $predicate) {
    return array_filter($array, $predicate) === $array;
}

function qor78safa($asfa97){
  return base64_decode($asfa97);
}


/**
 * Tell whether any member of $array validates the $predicate.
 *
 * any(array(1, 'a', 'b'),   'is_int'); -> true
 * any(array('a', 'b', 'c'), 'is_int'); -> false
 */
function any($array, $predicate) {
    return array_filter($array, $predicate) !== [];
}

/**
 * Convert Array to Object recursively
 * @param $array
 *
 * @return \stdClass
 */
function array_to_object($array) {
  $obj = new stdClass;
  foreach ($array as $k => $v) {
    if (strlen($k)) {
      if (is_array($v)) {
        $obj->{$k} = array_to_object($v); //RECURSION
      }
      else {
        $obj->{$k} = $v;
      }
    }
  }

  return $obj;
}
