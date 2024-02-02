<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\Factory;

// Product name and error(s) text data
$productErrorData = [
    'productName' => '',
    'errorMessages' => [],
];

// Include product name if available
if (isset($this->product_name) && trim($this->product_name) !== '') {
    $productErrorData['productName'] = trim($this->product_name);
}

// Also include any existing error messages
$messageQueue = Factory::getApplication()->getMessageQueue();

foreach ($messageQueue as $message) {
    $productErrorData['errorMessages'][] = $message['message'];
}

// Output resulting data as JSON
echo json_encode($productErrorData);