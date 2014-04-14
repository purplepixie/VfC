<?php
// This defines functions getres(), getraw() and setraw(), which are functions used to communicate with the database

$database = 'visionsf_visions';
$db_username = 'root';
$db_password = '';
$host = 'localhost';
$connected=false;
getres("SELECT 1");//connect wherever this file is included

housekeeping();

function dbreadystr($str)
{
    $str=stripslashes($str);
    $str=htmlspecialchars($str);
    $str=str_replace("\n","<br />",$str);
    $str=str_replace("\r","",$str);
    $str=mysql_real_escape_string($str);
    return $str;
}

function dbreadystrhtmlnochange($str)
{
    $str=stripslashes($str);
    $str=mysql_real_escape_string($str);
    return $str;
}

function getres($query)
{
     global $database;
     global $host;
     global $connected;
	 global $db_username;
	 global $db_password;

     if (! $connected)
     {
          $link = mysql_connect($host, $db_username,$db_password) or die('Error:Could not connect: ');
          mysql_select_db($database);
          $connected=true;
      }

     // Performing SQL query
     //echo("hello2 $query");
     $result = mysql_query($query) or die("Error:Query {$query} failed: " . mysql_error());
     
     return($result);
}

function getraw($table,$field,$whereorid,&$fail)
{
  getres("SELECT 1");//ensures connection to db
  if($whereorid=='')
    $whereclause='';
  else
  {
     if($whereorid==(($whereorid-0).''))
          $whereclause=" WHERE id=$whereorid";
     else
          $whereclause=" WHERE $whereorid";
  }
     $res=getres("SELECT $field FROM $table$whereclause");
     if(mysql_num_rows($res)!=1)
          $fail=true;
     else
     {
          $valarr=mysql_fetch_array($res);
          $fail=false;
          return($valarr[0]);
     }
     return(0);
}

function setraw($table,$field,$whereorid,&$data,&$fail)
{
  //echo("whereorid=$whereorid\n");
  getres("SELECT 1");//ensures connection to db
     if(($whereorid.'')===(($whereorid-0).''))
          $whereclause=" WHERE id=$whereorid";
     else
     {
          $whereclause=" WHERE $whereorid";
     }
     $res=getres("SELECT $field FROM $table$whereclause");
	  if(mysql_num_rows($res)!=1)
               return(false);
     //echo("Dat$data");
     //echo("whereclause=$whereclause");
     $md=mysql_real_escape_string($data);
     //echo("UPDATE $table SET $field='".$md."'$whereclause");
     getres("UPDATE $table SET $field='".$md."'$whereclause");

     return(true);

}

?>