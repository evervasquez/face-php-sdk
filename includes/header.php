<?php
// Remember to copy files from the SDK's src/ directory to a
// directory in your application on the server, such as php-sdk/
require_once('face-sdk/facebook.php');
require_once 'config.php';
$config = array(
    'appId' => APP_ID,
    'secret' => APP_SECRET,
    'cookie' => true
);

$facebook = new Facebook($config);
$user_id = $facebook->getUser();
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
        <script type="text/javascript" src="http://cloud.github.com/downloads/malsup/cycle/jquery.cycle.all.latest.js"></script>
        <script type="text/javascript"> 
            $(document).ready(function() {
                $('.slideshow').cycle({
                    fx:    'fade'
                });
            });
        </script> 
    </head>
    <body>

        <?php
        if ($user_id) {

            // We have a user ID, so probably a logged in user.
            // If not, we'll get an exception, which we handle below.
            try {
                //472148372819412 = la jungla
                $user_profile = $facebook->api('/me', 'GET');
                $fotoPerfil = "<img src='http://graph.facebook.com/" . $user_profile['id'] . "/picture?type=small'/>";
                echo $fotoPerfil . "My Name: " . $user_profile['name'];

                $token = $facebook->getAccessToken();

                //recuperar tus amigos de facebook
                $amigos = $facebook->api('/me/friends');
                foreach ($amigos['data'] as $amigo) {
                    echo "<br>Amigos : <br>Nombre: " . $amigo['name'] . " ID: " . $amigo['id'];
                }
                
                $likes =$facebook->api('/me/likes');
                foreach ($likes['data'] as $likes){
                    if($likes['category']=='Hotel'){
                        $fan_pageid=$likes['id'];
                    }
                }
                 
                //recuperar albunes
                $album = $facebook->api('/'.$fan_pageid.'/albums');
//                echo '<pre>';
//                print_r($album);
                echo '<ul>';
                foreach ($album['data'] as $album) {
                    echo '<li>NAME: ' . $album['name'] . ' ID: ' . $album['id'] . '</li>';

                    echo "<div class='slideshow' id='albums'> ";

                    //recuperar fotos por albums
                    $photo = $facebook->api("/{$album['id']}/photos");
//                    echo '<pre>';
//                    print_r($photo);
                    foreach ($photo['data'] as $photo) {
                        echo "<img src='{$photo['picture']}' />";
                    }
                    echo '</div>';
                }
                echo '</ul>';
            } catch (FacebookApiException $e) {
                // If the user is logged out, you can have a 
                // user ID even though the access token is invalid.
                // In this case, we'll get an exception, so we'll
                // just ask the user to login again here.
                $login_url = $facebook->getLoginUrl(array(
                    'scope' => 'user_status,publish_stream,user_photos,user_photo_video_tags'
                        ));

                echo 'Please <a href="' . $login_url . '">login.</a>';
                error_log($e->getType());
                error_log($e->getMessage());
            }
        } else {

            // No user, print a link for the user to login
            $login_url = $facebook->getLoginUrl();
            echo 'Please <a href="' . $login_url . '">login.</a>';
        }
        ?>

    </body>
</html>