<?php
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
  if(isset($_POST["secret"])){
    $secret = $_POST["secret"];
  }
  if(isset($_POST["embedpath"])){
    $embedpath = $_POST["embedpath"];
  }
  if(isset($_POST["host"])){
    $host = $_POST["host"];
  }
  if(isset($_POST["path"])){
    $path = $_POST["path"];
  }
  if(isset($_POST["fname"])){
    $fname = $_POST["fname"];
  }
  if(isset($_POST["lname"])){
    $lname = $_POST["lname"];
  }
}
?>
<form action="embed.php" method="post">
<label>secret</label>
fd156360b05c96d39f7c06bf7a63fb7410e8cd41fa2e3f98b66c1161fe038a80
<input type="text" value="<?php echo !empty($secret) ? $secret : ''; ?>" name="secret" size="100">
<br />
<label>embedpath</label>
/embed/looks/2
<input type="text" value="<?php echo !empty($embedpath) ? $embedpath : ''; ?>" name="embedpath" size="100">
<br />
<label>host</label>
localhost:9999
<input type="text" value="<?php echo !empty($host) ? $host : ''; ?>" name="host" size="100">
<br />
<label>path</label>
/login/embed/
<input type="text" value="<?php echo !empty($path) ? $path : ''; ?>" name="path" size="100">
<label>fname</label>
<input type="text" value="<?php echo !empty($fname) ? $fname : ''; ?>" name="fname" size="100">
<br />
<label>lname</label>
<input type="text" value="<?php echo !empty($lname) ? $lname : ''; ?>" name="lname" size="100">
<br />
<br />
<input type="submit">
</form>

<?php
if($_SERVER["REQUEST_METHOD"] == "POST"){
date_default_timezone_set('America/Los_Angeles');
// $secret = "fd156360b05c96d39f7c06bf7a63fb7410e8cd41fa2e3f98b66c1161fe038a80";
// $embedpath= "/embed/looks/2";
// $host = "localhost:9999";
// $path = "/login/embed/" . urlencode($embedpath);
$path = $path . urlencode($embedpath);
$json_nonce = json_encode(md5(uniqid()));
$json_current_time = json_encode(time());
$json_session_length = json_encode(3600);
$json_external_user_id = json_encode("<db_user_id>");
$json_first_name = json_encode("$fname");
$json_last_name = json_encode("$lname");
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
}
?>
<textarea rows="15" cols="120">
<?php
echo $final;
echo "\n";
?>
</textarea>

<a href="<?php echo $final; ?>" target="_blank">Open Embed in separate tab</a>
