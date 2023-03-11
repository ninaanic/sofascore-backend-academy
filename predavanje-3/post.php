<?php // post.php

// 1. primjer
/*
if ('POST' === ($_SERVER['REQUEST_METHOD'] ?? '')) {
    print_r($_POST);
}
*/

// 2. primjer
if ('POST' === ($_SERVER['REQUEST_METHOD'] ?? '')) {
    print_r($_FILES);

    foreach ((array) $_FILES['files']['tmp_name'] as $i => $tmpName) {
        $name = ((array) $_FILES['files']['name'])[$i];
        move_uploaded_file($tmpName, __DIR__.'/uploads/'.$name);
    }
}