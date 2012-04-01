<?php
// Version 1.0 - 02-10-09

function get_percent($a) {
	return array_sum($a) / count($a) * 100; 
}

function get_mean($a) {
	return array_sum($a) / count($a);
}

function get_variance($a) {
  $the_array_sum = array_sum($a); 
  $number_elements = count($a); 
  $the_mean = $the_array_sum / $number_elements;
  for ($i = 0; $i < $number_elements; $i++) {
    $the_variance = $the_variance + ($a[$i] - $the_mean) * ($a[$i] - $the_mean); 
  }
  $the_variance = $the_variance / $number_elements;
  return $the_variance;
}

function get_standard_deviation($a) {
  $the_array_sum = array_sum($a); 
  $number_elements = count($a); 
  $the_mean = $the_array_sum / $number_elements;
  for ($i = 0; $i < $number_elements; $i++) {
    $the_variance = $the_variance + ($a[$i] - $the_mean) * ($a[$i] - $the_mean); //sum the array
  }
  $the_variance = $the_variance / $number_elements;
  $the_standard_deviation = pow( $the_variance, 0.5);  //calculate the standard deviation
  return $the_standard_deviation;
}

?>