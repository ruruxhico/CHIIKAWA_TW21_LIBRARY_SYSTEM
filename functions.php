<?php
function generateBookID($title, $pubMonth, $dayAdded, $yearPub, $category, $count) {
    $prefix = strtoupper(substr($title, 0, 2));
    return $prefix . strtoupper($pubMonth) . $dayAdded . $yearPub . '-' . strtoupper($category) . str_pad($count, 5, '0', STR_PAD_LEFT);
}
?>
