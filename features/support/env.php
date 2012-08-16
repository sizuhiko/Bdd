<?php
$world->getPathTo = function($path) use($world) {
    switch ($path) {
//  case 'TopPage': return '/';
    default: return $path;
    }
};
?>