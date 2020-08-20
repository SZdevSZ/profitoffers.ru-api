 <?php
    function connectDB() {
        return new mysqli ("10.18.123.49", "pbxtool", "swordfish","asterisk" );
    }

    function closeDB ($mysqli) {
        $mysqli->close();
     }

     function regUser ($login, $password) {
        $mysqli = connectDB();
        $mysqli->query("INSERT INTO ampusers ('username', 'password_sha1') VALUES ('$login','$password')");
        closeDB($mysqli);
     }

    function checkUser ($login, $password) {
        if (($login == "") || ($password == "")) return false;
        $mysqli = connectDB();
        $result_set = $mysqli->query("SELECT password_sha1 FROM ampusers WHERE username = '$login'");
        $user = $result_set->fetch_assoc();
        $real_password = $user['password_sha1'];
        closeDB($mysqli);
        return $real_password == $password;
    }
 ?>

