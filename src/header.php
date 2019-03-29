<?php namespace proxy

{

function canonicalHeaderKey($k) :string {

    $ws = explode('-', $k);
    foreach ($ws as $i => $w) {
        $ws[$i] = ucfirst(strtolower($w));
    }

    return join($ws, '-');
}

}
