<html>
<head>
    <style>
        a,p{
            font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
            font-size:1.4em;
            text-align:left;

        }
        #contacts
        {
            font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
            width:100%;
            border-collapse:collapse;
        }
        #contacts td, #contacts th
        {
            font-size:1.2em;
            border:1px solid #98bf21;
            padding:3px 7px 2px 7px;
        }
        #contacts th
        {
            font-size:1.4em;
            text-align:left;
            padding-top:5px;
            padding-bottom:4px;
            background-color:#A7C942;
            color:#fff;
        }
        #contacts tr.alt td
        {
            color:#000;
            background-color:#EAF2D3;
        }
        .example{
            width: 50%;
            border: 1px double;
            padding: 10px;
            background-color: #ccffcc;
        }
    </style>
    <style>
        #content div.example h3
        {
            text-align: center;
            font-size: xx-large;
            font-family: sans-serif;
        }

        #content div.example strong
        {
            font-size: large;
            color: #999999;
            font-family: monospace;
            text-decoration: underline;
            font-weight: bold;
        }




    </style>
    <!-- In the callback, you would hide the gSignInWrapper element on a
 successful sign in -->
    <link href="./css/bootstrap-social.css" rel="stylesheet" type="text/css" />
    <link href="./css/font-awesome.css" rel="stylesheet" type="text/css" />

</head>
<body>






<?php
/*
 * Copyright 2012 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require_once './src/Google_Client.php';
session_start();

$client = new Google_Client();
$client->setApplicationName('Contact Manager V.0.1 (By: Harini)');
$client->setScopes("http://www.google.com/m8/feeds/");
// Documentation: http://code.google.com/apis/gdata/docs/2.0/basics.html
// Visit https://code.google.com/apis/console?api=contacts to generate your
// oauth2_client_id, oauth2_client_secret, and register your oauth2_redirect_uri.
$client->setClientId('962044607441-o2bp6hao393rh0difl8palddforgbqj0.apps.googleusercontent.com');
$client->setClientSecret('dFDJ_Y0zp6RFLn9_8Oy_eRqO');
$client->setRedirectUri('https://contactApp-harini.rhcloud.com/');
$client->setDeveloperKey('AIzaSyB5KN3b-ukxvWBsdl0ttpjzkY076zleoJQ');

$emails = array();
$givenNames = array();

if (isset($_GET['code'])) {
    $client->authenticate();
    $_SESSION['token'] = $client->getAccessToken();
    $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
    header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['token'])) {
    $client->setAccessToken($_SESSION['token']);
}

if (isset($_REQUEST['logout'])) {
    unset($_SESSION['token']);
    $client->revokeToken();
}

if ($client->getAccessToken()) {
    $req = new Google_HttpRequest("https://www.google.com/m8/feeds/contacts/default/full?v=3.0");
    $val = $client->getIo()->authenticatedRequest($req);


    $xml = new SimpleXMLElement($val->getResponseBody());
    //echo 'Here:'.$xml->asXML();
    //$xmlData = simplexml_load_string($val->getResponseBody());
    //echo (string)$xmlData;
    //$output_array = array();

    print'
    <div class="example">

    <h3>Contact Manager</h3>


    <p div="welcome" style="font-size:1.0em;"> Your saved contacts in Gmail are as follows:</br> This is an initial version. I will add more features in future. :) </p>

</div>';




    echo '<table id="contacts">
    <tr>
<th>S.No.</th>
<th>Saved Name</th>
<th>Email Address</th>
</tr>';

    $c=0;
    foreach ($xml->entry as $entry) {
//For Alternate Style in table css
        if ($c%2!=0)
            echo '<tr class="alt">';
        else
            echo '<tr>';

        echo '<td>'.++$c.'</td>';
        echo '<td>'.(string)$entry->title.'</td>';
        foreach ($entry->xpath('gd:email') as $email) {

            echo '<td>'.(string)$email->attributes()->address.'</td>';
//            $output_array[] = array((string)$entry->title, (string)$email->attributes()->address);
        }
        foreach ($entry->xpath('gd:phoneNumber') as $phone) {
            echo '<td>'.(string)$phone->attributes()->t.'</td>';
//            $output_array[] = array((string)$entry->title, (string)$email->attributes()->address);
        }

        echo '</tr>';
    }
    echo '</table>';
//    print_r($output_array);
    /**
    //ANOTHER GOOD WAY TO DO IT. IF YOU KNOW xPath & THE STR

    $xml->registerXPathNamespace('gd', 'http://schemas.google.com/g/2005');
    $result = $xml->xpath('//email');

    //$result = $xml->xpath('//*');
    //print_r($result);

    foreach($result as $title)
    {
    print($title->attributes()->address.'</br>');
    //echo $title->parentNode->getElementsByTagName('title')->item(0)->textContent;
    $emails[] = $title->attributes()->address;
    //print_r($emails);

    }
     **/

    // The contacts api only returns XML responses.
    //$response = json_encode(simplexml_load_string($val->getResponseBody()));// THIS RETURNS PRIMITIVE ELEMENTS ONLY, NO USE
    // print "<pre>" . print_r(json_decode($response, true), true) . "</pre>";

    // The access token may have been updated lazily.
    $_SESSION['token'] = $client->getAccessToken();
} else {
    $auth = $client->createAuthUrl();
}

if (isset($auth)) {
    print'
    <div class="example">

    <h3>Contact Manager</h3>


    <p div="welcome" style="font-size:1.0em;"> Welcome to Contact Manager Application that uses google by Harini. Using this php based web application, you will be able to list your existing gmail contacts.</br></br> This is an initial version. I hope to add more features in future. :) </p>

</div>';

    print '
    <a class="btn btn-block btn-google-plus" class=login href='.$auth.'>
    <i class="fa fa-google-plus"></i>
    Sign in with Google</a></a>';
} else {
    print '
    <a class="btn btn-block btn-google-plus" class=logout href=\'?logout\'><i class="fa fa-google-plus"></i>Log out</a></a>';


    //print "<a class=logout href='?logout'>Logout</a>";
}
?>
</body>
</html>