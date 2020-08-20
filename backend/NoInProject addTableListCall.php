<?php
    //ini_set('display_errors','On'); 
    //error_reporting(E_ALL);
    session_start();
    require_once 'dbconf.php';
    require_once "cli-functions.php";
    $login = $_POST['login'];
    $password = ($_POST['password']);
    $dialerName = ($_POST['dialerName']);
    $numberPhone = ($_POST['numberPhone']);
    $agent = ($_POST['agent']);
    $context = ($_POST['context']);
    $userField = ($_POST['userField']);
    $_SESSION['origPassword'] = ($_POST['password']);
    unset($_SESSION['error_auth']);
    if (checkUser($login, $password, $dbhost, $dbname, $dblogin, $dbpassword, $charset) == 'ok'){
        $_SESSION['login'] = $login;
        $_SESSION['password'] = $password;
    }
        else $_SESSION['error_auth'] = 1;
    if (isset($_SESSION['error_auth']) && !empty($_SESSION['error_auth']) && $_SESSION['error_auth'] == 1){
          header("Location: main.php");
    //      //header("Location:".$_SERVER['HTTP_REFERER']);
   }
?>
  <!-- <div class="col container-fluid testimonial-group text-center flex-nowrap"> -->
  <div class="row">
  <div class="col text-center">
<?php
    require_once 'cli-functions.php';
    require_once 'dbconf.php';
    // if (empty($dialerName){
    //      echo "Please fill in all input fields";
    //      return;
    //  };
    //     elseif (empty($numberPhone)) {
    //         echo "Please fill in all input fields";
    //         return;
    //     }
    //     elseif (empty($agent)) {
    //         echo "Please fill in all input fields";
    //         return;
    //     }
    //     elseif (empty($context)) {
    //         echo "Please fill in all input fields";
    //         return;
    //     }

    // if ($_POST){
    //     print_r($_POST);
    // }
		$connection = connectDB($dbhost, $dbname, $dblogin, $dbpassword, $charset);
    $sql = "INSERT INTO usersCall (login, dialerName, numberPhone, agent, context, userField) 
    VALUES (:login, :dialerName, :numberPhone, :agent, :context, :userField)";
    $query = $connection->prepare($sql);
    $query->execute( array( 
        ':login'=>$login, 
        ':dialerName'=>$dialerName, 
        ':numberPhone'=>$numberPhone,
        ':agent'=>$agent, 
        ':context'=>$context, 
        ':userField'=>$userField ) );
        // echo "User registered successfully."; 
    $conn = null;
    echo '<table>
            <thead>
              <tr class="d-flex">
                <th class="col-1">Num</th>
                <th class="col">Name list</th>
                <th class="col">Number phone</th>
                <th class="col-1">Agent</th>
                <th class="col-1">Context</th>
                <th class="col-1">Mark</th>
                <th class="col-1">Date</th>
              </tr> 
            </thead>
            <tbody class="table-striped">';
    $sth = $connection->query("SELECT * FROM usersCall ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    $i = 1;
    foreach ($sth as $k => $v){
        echo '<tr class="d-flex"> 
                <td class="col-1">'.$i.'</td>
                <td class="col">'.$v['dialerName'].'</td>
                <td class="col">'.$v['numberPhone'].'</td>
                <td class="col-1">'.$v['agent'].'</td>
                <td class="col-1">'.$v['context'].'</td>
                <td class="col-1">'.$v['userField'].'</td>
                <td>'.$v['datetime'].'</td>
              </tr>';
        $i++;
		}
    $conn = null;
?>
</tbody>
</table>
</div>
</div>


