<?php

define('DEBUG', true);

if (defined('DEBUG')) {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}
?><html>
	<head></head>
	<body>
		<a href='?do=menu'>menu</a> - <a href='?do=content'>content</a><br><br>
		<?php
			if( isset( $_GET['do']) ){
				$do = $_GET['do'];
				try{
					$db = new PDO('sqlite:data.s3db'); // new SQLite3('data.s3db');
					switch($do){
						// echo each menu entry
						case "menu": echo "lets work on the menu<br>";
							$result = $db->query('SELECT * FROM menu ORDER BY "order"');
							$row = array();
							$i = 0;
							echo "<form action='admin.php?do=changemenu' method='post'><input type='submit' value='Submit'><br>";
							while ($res = $result->fetch()) {
								$num = $res['order']*10;
								echo "<input type='text' name='".$num."name' value='".$res['name']."'>";
								echo "<input type='text' name='".$num."nameold' value='".$res['name']."' style='display:none;'>";
								echo "<input type='text' name='".$num."order' value='".$res['order']."'>";
								echo "<input type='text' name='".$num."orderold' value='".$res['name']."' style='display:none;'><br>";
								$i++;
							}
							echo "<input type='text' name='new'></form>";
							break;
					// change the menues
						case "changemenu": $i = 0;
							$loop = 1;
							if (defined('DEBUG')) 
								echo "enter loop<br>";
								
							while($loop){
								$pname = $i."name";
								$pnameold = $i."nameold";
								$porder = $i."order";
								$porderold = $i."orderold"; 
								if( isset($_POST[$pname]) ){
									// check if old != new
									$name = $_POST[$pname];
									$nameold = $_POST[$pnameold];
									if($name != $nameold) {
										// make change in DB
										$order = $i/10;
										if (defined('DEBUG')) {
											$qrymenu = 'UPDATE menu SET name="'.$name.'" WHERE [order]='.$order;
											$qrycont = 'UPDATE content SET name="'.$name.'" WHERE name="'.$nameold.'"';
											echo $qrymenu."<br>";
											echo $qrycont."<br>";
										}
										$irwas = $db->prepare('UPDATE [menu] SET name=? WHERE [order]=?');
										//$irwas = $db->prepare('UPDATE [menu] SET name=? WHERE name=?');
										$irwas->execute(array($name, $order));
										$nowas = $db->prepare('UPDATE content SET name=? WHERE name=?');
										$nowas->execute(array($name, $nameold));
									}
									$i = $i+1;
								}
								else{
									//if submenu
									for($j=$i;$j>=0;$j=$j-10){
										//do nothing
									}
									$j = $j+10;
									//if not submenu
									if($j == 0) $loop = 0;	
									else $i = $i - $j + 10;
								}
							}
							if( isset($_POST['new']) ){
								// add the new menu
							}
							break;
						case "content": echo "lets work on the content";break;
						default: echo"sorry some error accured";break;
					}
					echo "all done";
					//$db->close();
					$db = null;
				}
				catch(Exception $err){
					echo $err;	
				}
			}
			
			else{
				echo "<h1>What do you want to do?</h1><br>";
			}
		?>
	</body>
</html>