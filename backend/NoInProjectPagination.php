<?php
    // echo 'hey';
    // return;
    ini_set('display_errors','On'); 
    error_reporting(E_ALL);
    require_once 'dbconf.php';
    require_once 'cli-functions.php';
    //connect to db
    $connection = connectDB($dbhost, $dbname, $dblogin, $dbpassword, $charset);
    //result array to limit
    $items = array();
    //html
    $html = NULL;
    //count items on page
    $limit = 7;
    //count page
    $pageCount = 0;
    //first page set GET[start] to 0
    $start = isset($_GET['start']) ? intval($_GET['start']) :0;
    //sql querry
    // $sql = 'SELECT '.'*'.' FROM '.' usersCall'.' LIMIT '.$start.','.$limit.''.' ORDER BY '.'id'.' DESC';
    $sql = 'SELECT * FROM usersCall LIMIT '.$start.','.$limit.'';
    // $stmt = $connection->querry($sql);
    // $items = $stmt->fetchAll(PDO::FETCH_OBJ);
    $query = $connection->prepare($sql);
    $query->execute( array( 
        ':login'=>$login, 
        ':dialerName'=>$dialerName, 
        ':numberPhone'=>$numberPhone,
        ':agent'=>$agent, 
        ':context'=>$context, 
        ':userField'=>$userField ) );
    // echo "User registered successfully."; 
    // $conn = null;
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
        $sth = $connection->query($sql)->fetchAll(PDO::FETCH_ASSOC);
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

    $allItems = $connection->query('select count(*) from usersCall')->fetchColumn(); 
    $pageCount = ceil($allItems / $limit);
    for( $i = 0; $i < $pageCount; $i++ ) {    
        // Здесь ($i * $limit) - вычисляет нужное для каждой страницы  смещение, 
        // а ($i + 1) - для того что бы нумерация страниц начиналась с 1, а не с 0
        $html .= '<li><a href="pagination.php?category='.$critery.'&start='.($i*$limit).'">'.($i+1).'</a></li>';
    }
    // Собственно выводим на экран:
    echo '<ul class="pagination">' . $html . '</ul>';
    $conn = null;
?>
