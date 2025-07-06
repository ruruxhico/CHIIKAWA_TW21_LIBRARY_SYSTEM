<?php
function generateBookID($title, $monthAbbreviations, $dayAdded, $yearPub, $category, $count) {
    $prefix = strtoupper(substr($title, 0, 2));
    $categoryAbbr = strtoupper(substr($category, 0, 5));
    return $prefix . $monthAbbreviations . $dayAdded . $yearPub . '-' . $categoryAbbr . str_pad($count, 5, '0', STR_PAD_LEFT);
}
?>
