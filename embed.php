<?php
date_default_timezone_set('America/Los_Angeles');
$secret = "fd156360b05c96d39f7c06bf7a63fb7410e8cd41fa2e3f98b66c1161fe038a80";
$embedpath= "/embed/looks/2";
$host = "localhost:9999";
$path = "/login/embed/" . urlencode($embedpath);
$json_nonce = json_encode(md5(uniqid()));
$json_current_time = json_encode(time());
$json_session_length = json_encode(3600);
$json_external_user_id = json_encode("<db_user_id>");
$json_first_name = json_encode("<first_name>");
$json_last_name = json_encode("<last_name>");
$json_permissions = json_encode( array ( "see_user_dashboards", "see_lookml_dashboards", "access_data", "see_looks" ) );
$json_models = json_encode( array ( "<your_model_name>" ) );
$json_group_ids = json_encode( array ( 4, 2 ) );  // just some example group ids
$json_external_group_id = json_encode("awesome_engineers");
$json_user_attributes = json_encode( array ( "an_attribute_name" => "my_value", "my_number_attribute" => "0.231" ) );  // just some example attributes
// NOTE: accessfilters must be present and be a json hash. If you don't need access filters then the php
// way to make an empty json hash as an alternative to the below seems to be:
// $accessfilters = new stdClass()
$accessfilters = array (
  "<your_model_name>"  =>  array ( "view_name.dimension_name" => "<value>" )
);
$json_accessfilters = json_encode($accessfilters);
$stringtosign = "";
$stringtosign .= $host . "\n";
$stringtosign .= $path . "\n";
$stringtosign .= $json_nonce . "\n";
$stringtosign .= $json_current_time . "\n";
$stringtosign .= $json_session_length . "\n";
$stringtosign .= $json_external_user_id . "\n";
$stringtosign .= $json_permissions . "\n";
$stringtosign .= $json_models . "\n";
$stringtosign .= $json_group_ids . "\n";
$stringtosign .= $json_external_group_id . "\n";
$stringtosign .= $json_user_attributes . "\n";
$stringtosign .= $json_accessfilters;
$signature = trim(base64_encode(hash_hmac("sha1", utf8_encode($stringtosign), $secret, $raw_output = true)));
// , $raw_output = true
$queryparams = array (
    'nonce' =>  $json_nonce,
    'time'  =>  $json_current_time,
    'session_length'  =>  $json_session_length,
    'external_user_id'  =>  $json_external_user_id,
    'permissions' =>  $json_permissions,
    'models'  =>  $json_models,
    'group_ids' => $json_group_ids,
    'external_group_id' => $json_external_group_id,
    'user_attributes' => $json_user_attributes,
    'access_filters'  =>  $json_accessfilters,
    'first_name'  =>  $json_first_name,
    'last_name' =>  $json_last_name,
    'force_logout_login'  =>  false,
    'signature' =>  $signature
);
$querystring = "";
foreach ($queryparams as $key => $value) {
  if (strlen($querystring) > 0) {
    $querystring .= "&";
  }
  if ($key == "force_logout_login") {
    $value = "true";
  }
  $querystring .= "$key=" . urlencode($value);
}
$final = "https://" . $host . $path . "?" . $querystring;
?>
<textarea rows="15" cols="120">
<?php
echo $final;
echo "\n";
?>
</textarea>