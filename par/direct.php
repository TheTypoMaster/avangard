<?
function send( $site, $location, $data )
{
	$conn    = fsockopen( $site, 80 );
	$headers =	"POST $location HTTP/1.0\r\n" .
				"Host: $site\r\n" .
				"Connection: close\r\n" .
				"Content-Type: application/x-www-form-urlencoded\r\n" .
				"Content-Length: " . strlen($data) . "\r\n\r\n";
	
	fputs( $conn, "$headers" );
	fputs( $conn, $data) ;
	$response = "";
	while( !feof( $conn ) ) {
		$response .= fgets( $conn, 1024 );
	}
	fclose( $conn );
	
	$sep = strpos( $response, "\r\n\r\n" );
	return substr( $response, $sep + 4 );
}

if ( !$_POST[ "AddLink" ] && !isset( $_GET[ "sendcat" ] ) && !isset( $_POST[ "find" ] ) && !isset( $_GET[ "sn" ] ) ) Header("Location: ".$_GET["c"]."1.htm?subcatalog=".$_GET[ 'subcatalog' ]);
else 
{
	$_GET[ "addform" ] = true;
	require_once( "site.php" ); 
	$MainHost  = str_replace( "http://", "", $MainSite );
	$sep       = strpos( $MainHost, "/" );
	$site      = substr( $MainHost, 0, $sep );
	$location  = substr( $MainHost, $sep );
		
	if ( isset( $_GET[ "sendcat" ] ) )
	{
		$location .= "add_form.php";
		$_GET[ "sendcat" ] = intval( $_GET[ "sendcat" ] );
		$data  = "";
		$data .= "sendcat=".$_GET[ "sendcat" ];
		echo send( $site, $location, $data );
	}
	elseif ( isset( $_POST[ "find" ] ) )
	{
		$location .= "find_lnk.php";
		$data  = "";
		$data .= "find_link=".$_POST["find_link"];
		$data .= "&find_url=".$_POST["find_url"];
		header( "Location: ".send( $site, $location, $data ) );
	}
	elseif ( isset( $_GET[ "sn" ] ) )
	{
		$location .= "img.php";
		$data  = "";
		$data .= $_GET[ "sn" ]."=".$_GET[ "si" ];
		echo send( $site, $location, $data );
	}
	else
	{
		$location .= "add_form.php";
		$data  = "";
		$data .= "RetUrl=".$_POST[ "RetUrl" ];
		$data .= "&AddLink=".$_POST[ "AddLink" ];
		$data .= "&banner=".$_POST[ "banner" ];
		$data .= "&link=".$_POST[ "link" ];
		$data .= "&cat=".$_POST[ "cat" ];
		$data .= "&subcat=".$_POST[ "subcat" ];
		$data .= "&backlink=".$_POST[ "backlink" ];
		$data .= "&email=".$_POST[ "email" ];
		$data .= "&antispam=".$_POST[ "antispam" ];
		$data .= "&form=".$_POST[ "form" ];
		$data .= "&".$_POST[ "sn" ]."=".$_POST[ "si" ];
		
		header( "Location: add_link.htm?".send( $site, $location, $data ) );
	}
}
exit();
?>