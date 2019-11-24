<?php

// Domain list accumulator
$count = [];

// Emulates database record set flatten into single dimention array
$rs = [ 'a@b.com', 'b@b.com, c@b.com', 'c@c.com, c@d.com, c@e.com', 'a@e.com', 'a@b.com' ];

// Collapse all the records into a single line separated with comma
$line_list = join(',', $rs);
// Sanitize the list by removing whitespaces and probably other characters
$line_list = str_replace(' ', '', $line_list);

// Split all the records into array of addresses
$email_list = explode(',', $line_list);

foreach ($email_list as $email) {
	// Split each record into account and domain array
	$ad_list = explode('@', $email);

	// Retrieve domain name itself
	$domain = $ad_list[1];

	// Checks whether there is a domain already in the array
	// array_push() is proven slower rather than direct access with []
	if (array_key_exists($domain, $count)) {
		$count[$domain] += 1;
	} else {
		$count[$domain] = 1;
	}
}

// Prints out resulting list with domain name and account quantity
print_r($count);
