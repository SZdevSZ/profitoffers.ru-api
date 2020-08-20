<?php
//
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
namespace predicted\api;
class Notification {
	public function __construct()
	{
		$strong = '';
		$text = '';
		$link = '';
		$textLink ='';
	    echo '<style>
	    	    .closeT {
           	    	position: absolute;
           	    	top: 0;
           	    	right: 0;
           	    	padding: $alert-padding-y $alert-padding-x;
           	    	color: inherit;
	    	    }
	          </style> ';
	}
	public function success($strong,$text,$link,$textLink)
	{
		$result = '<div class="alert alert-success alert-dismissible fade in show" role="alert">
			    <strong>'.$strong.'</strong>'.$text.'<a href="'.$link.'" class="alert-link">'.$textLink.' </a>
			    <button type="button" class="btn closeT" data-dismiss="alert" aria-label="Close">
				  <span aria-hidden="true">&times;</span>
			    </button>
			  </div>';
		 return $result;
	}
	public function danger($strong,$text,$link,$textLink)
	{
		$result = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
			    <strong>'.$strong.'</strong>'.$text.'<a href="'.$link.'" class="alert-link">'.$textLink.'</a>
			    <button type="button" class="btn closeT" data-dismiss="alert" aria-label="Close">
			      <span aria-hidden="true">&times;</span>
			    </button>
			  </div>';
		 return $result;
	}
	public function warning($strong,$text,$link,$textLink)
	{
		$result = '<div class="alert alert-warning alert-dismissible fade in" role="alert">
			    <button type="button" class="btn closeT" data-dismiss="alert" aria-label="Close">
			      <span aria-hidden="true">&times;</span>
			    </button>
			    <strong>'.$strong.'</strong>'.$text.'<a href="'.$link.'" class="alert-link">'.$textLink.'</a>
			  </div>';
		 return $result;
	}
		public function info($strong,$text,$link,$textLink)
	{
		$result = '<div class="alert alert-info alert-dismissible fade in" role="alert">
			    <button type="button" class="btn closeT" data-dismiss="alert" aria-label="Close">
			      <span aria-hidden="true">&times;</span>
			    </button>
			    <strong>'.$strong.'</strong>'.$text.'<a href="'.$link.'" class="alert-link">'.$textLink.'</a>
			  </div>';
		 return $result;
	}
}
/**
*This is code!!
*/ 

//use Noti;

// $strong = ' Strong Danger'; 
// $text = ' Text Danger'; 
// $link = 'http://phone.octotrade.ru/dialer/login.php'; 
// $textLink =' GO TO HOMEPAGE';

//$success = new notification();
//$res = $success->success($strong,$text,'',''); 
//print_r($res);
//$danger = new notification();
//$res = $success->danger($strong,$text,$link,$textLink); 
//print_r($res);



// $warning = new notification();
// $res = $warning->warning($strong,$text,$link,$textLink); 
// print_r($res);
// $info = new notification();
// $res = $info->info($strong,$text,$link,$textLink); 
// print_r($res);
//echo 'hello';	
//echo ''.$strong.','.$text.','.$link.','.$textLink.'';
?>