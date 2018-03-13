<?php
namespace App\Controller;


class test {

    public function getExtension($file) {

        $extension = end(explode('.', $file));
        return $extension ? $extension : false;
    }


}