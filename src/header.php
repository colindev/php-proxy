<?php namespace proxy

{

function canonicalHeaderKey($k) {

    $ws = explode('-', $k);
    foreach ($ws as $i => $w) {
        $ws[$i] = ucfirst(strtolower($w));
    }

    return join($ws, '-');
}

}
