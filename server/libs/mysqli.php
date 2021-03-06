<?php

class MySQLii {
    public $connection;
    private $connected;

    public function __construct($hostname, $username, $password, $database, $port = '3306') {
        try {

            $this->connection = new mysqli($hostname, $username, $password, $database, $port);
        } catch (\mysqli_sql_exception $e) {
            throw new \Exception('Error: Could not make a database link using ' . $username . '@' . $hostname . '!');
        }

        $this->connection->set_charset("utf8");
        $this->connection->query("SET SQL_MODE = ''");
    }

    public function query($sql, $count = false, $show_error = true) {
        $query = $this->connection->query($sql);

        if ($this->connection->errno) {
            $this->display_error($this->connection->error, $this->connection->errno, $sql);
        } else {


                if ($query instanceof \mysqli_result) {
                    $data = array();

                    while ($row = $query->fetch_assoc()) {
                        $data[] = $row;
                    }

                    $result = new \stdClass();
                    $result->num_rows = $query->num_rows;
                    $result->row = isset($data[0]) ? $data[0] : array();
                    $result->rows = $data;
                    $count = ($count) ? $this->connection->query('SELECT FOUND_ROWS()')->fetch_row()[0] : 0;
                    $result->count = ($count) ? $count : 0;

                    $query->close();

                    return $result;
                } else {
                    return true;
                }
            }

    }

    public function display_error($error, $error_num, $query = '') {

        $query = htmlspecialchars($query, ENT_QUOTES, 'utf-8');
        $error = htmlspecialchars($error, ENT_QUOTES, 'utf-8');

        $trace = debug_backtrace();

        $level = 0;
        if ($trace[1]['function'] == "query") $level = 1;
        if ($trace[2]['function'] == "super_query") $level = 2;

        $trace[$level]['file'] = str_replace(ROOT_DIR, "", $trace[$level]['file']);

        echo <<<HTML
<?xml version="1.0" encoding="iso-8859-1"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>MySQL Fatal Error</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
<!--
body {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	font-style: normal;
	color: #000000;
}
.top {
  color: #ffffff;
  font-size: 15px;
  font-weight: bold;
  padding-left: 20px;
  padding-top: 10px;
  padding-bottom: 10px;
  text-shadow: 0 1px 1px rgba(0, 0, 0, 0.75);
  background-color: #AB2B2D;
  background-image: -moz-linear-gradient(top, #CC3C3F, #982628);
  background-image: -ms-linear-gradient(top, #CC3C3F, #982628);
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#CC3C3F), to(#982628));
  background-image: -webkit-linear-gradient(top, #CC3C3F, #982628);
  background-image: -o-linear-gradient(top, #CC3C3F, #982628);
  background-image: linear-gradient(top, #CC3C3F, #982628);
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#CC3C3F', endColorstr='#982628',GradientType=0 ); 
  background-repeat: repeat-x;
  border-bottom: 1px solid #ffffff;
}
.box {
	margin: 10px;
	padding: 4px;
	background-color: #EFEDED;
	border: 1px solid #DEDCDC;

}
-->
</style>
</head>
<body>
	<div style="width: 700px;margin: 20px; border: 1px solid #D9D9D9; background-color: #F1EFEF; -moz-border-radius: 5px; -webkit-border-radius: 5px; border-radius: 5px; -moz-box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3); -webkit-box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3); box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3);" >
		<div class="top" >MySQL Error!</div>
		<div class="box" ><b>MySQL error</b> in file: <b>{$trace[$level]['file']}</b> at line <b>{$trace[$level]['line']}</b></div>
		<div class="box" >Error Number: <b>{$error_num}</b></div>
		<div class="box" >The Error returned was:<br /> <b>{$error}</b></div>
		<div class="box" ><b>SQL query:</b><br /><br />{$query}</div>
		</div>		
</body>
</html>
HTML;

        die();
    }
}