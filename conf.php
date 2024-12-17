<?php
error_reporting(E_ERROR | E_PARSE);
if(file_exists(__DIR__.'/db.php') || file_exists(__DIR__.'/db.json')) {
	$http 		= 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 's' : '') . '://';
	$newurl 	= str_replace(['index.php','conf.php'],'', $_SERVER['SCRIPT_NAME']);
	$base_url	= $http . $_SERVER['SERVER_NAME'] .  $newurl;
	header('Location:'.$base_url);
} else {
	if(isset($_POST['db_host'])) {
		if($_POST['db_host'] && $_POST['db_user'] && $_POST['db_name']) {

			$create_conf = false;

			$conn = new mysqli($_POST['db_host'], $_POST['db_user'], $_POST['db_pass'], $_POST['db_name']);
			if ($conn->connect_error) {
				$error = $conn->connect_error;
				$conn->close();
				if(file_exists(__DIR__.'/assets/app/db.sql')) {
					$conn = new mysqli($_POST['db_host'], $_POST['db_user'], $_POST['db_pass']);
					if ($conn->connect_error) {
						$error = $conn->connect_error;
						$conn->close();
					} else {
						$sql = "CREATE DATABASE ".$_POST['db_name'];
						if ($conn->query($sql) === TRUE) {
							$conn->close();

							$conn = new mysqli($_POST['db_host'], $_POST['db_user'], $_POST['db_pass'], $_POST['db_name']);
							$query = '';

							$sqlScript = file(__DIR__.'/assets/app/db.sql');
							$error 	= '';
							foreach ($sqlScript as $line) {
								$startWith = substr(trim($line), 0 ,2);
								$endWith = substr(trim($line), -1 ,1);
								if (empty($line) || $startWith == '--' || $startWith == '/*' || $startWith == '//') {continue;
								}
								$query = $query . $line;
								if ($endWith == ';') {
									if($conn->query($query) !== TRUE) {
										$error .= 'Gagal Query : <em>'.$query.'</em><br />';
									}
									$query= '';
								}
							}
							$conn->close();

							if($error) {
								$conn = new mysqli($_POST['db_host'], $_POST['db_user'], $_POST['db_pass']);
								if (!$conn->connect_error) {
									$sql = "DROP DATABASE ".$_POST['db_name'];
									$conn->query($sql);
								}
								$conn->close();
							} else {
								$create_conf = true;
							}
						} else {
							$error = $conn->error;
							$conn->close();
						}
					}
				}
			} else $create_conf = true;

			if($create_conf) {
				$data 		= [
					'hostname'	=> $_POST['db_host'],
					'username'	=> $_POST['db_user'],
					'password'	=> $_POST['db_pass'],
					'database'	=> $_POST['db_name']
				];
				$konten = json_encode($data);
				$filename = __DIR__.'/db.json';
				$handle = fopen ($filename, "wb");
				if($handle) {
					fwrite ( $handle, $konten );
				}
				fclose($handle);
				$oldmask = umask(0);
				chmod($filename, 0777);
				umask($oldmask);
				$http 		= 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 's' : '') . '://';
				$newurl 	= str_replace(['index.php','conf.php'],'', $_SERVER['SCRIPT_NAME']);
				$base_url	= $http . $_SERVER['SERVER_NAME'] .  $newurl;
				header('Location:'.$base_url);
				die;
			}
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Konfigurasi Database</title>

	<style type="text/css">

	body {
		background-color: #fff;
		margin: 40px;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
	}

	a {
		color: #003399;
		background-color: transparent;
		font-weight: normal;
	}

	h1 {
		color: #444;
		background-color: #f9f9f9;
		border-top-left-radius: 10px;
		border-top-right-radius: 10px;
		border-bottom: 1px solid #D0D0D0;
		font-size: 19px;
		font-weight: normal;
		margin: 0 0 14px 0;
		padding: 18px 20px 15px 20px;
	}

	code {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 12px;
		background-color: #f9f9f9;
		border: 1px solid #D0D0D0;
		color: #002166;
		display: block;
		margin: 14px 0 14px 0;
		padding: 12px 10px 12px 10px;
		border-radius: 4px;
	}

	code strong {
		color: #900;
	}

	#body {
		margin: 0 20px 0 20px;
	}

	p.footer {
		text-align: right;
		font-size: 11px;
		border-top: 1px solid #D0D0D0;
		line-height: 32px;
		padding: 0 10px 0 10px;
		margin: 20px 0 0 0;
	}

	#container {
		margin: 10px auto;
		border: 1px solid #f0f0f0;
		box-shadow: 0 0 8px #e0e0e0;
		border-radius: 10px;
		width: 100%;
		max-width: 450px;
	}

	.form-group {
		margin-bottom: 7px;
	}

	.form-group label {
		display: block;
		margin-bottom: 3px;
	}

	.form-group input {
		padding: 7px 11px;
		border: 1px solid #ccc;
		border-radius: 5px;
	}

	.form-group input:focus {
		border-color: #aaa;
	}

	button {
		margin-top: 5px;
		margin-bottom: 10px;
		padding: 7px 11px;
		border: 1px solid #ccc;
		border-radius: 5px;
		cursor: pointer;
	}

	.error {
		border: 1px solid #933;
		border-radius: 4px;
		color: #933;
		padding: 10px;
		background: #fcc;
	}
	</style>
</head>
<body>

<div id="container">
	<h1>Konfigurasi Database</h1>

	<div id="body">
		<?php if(isset($error) && $error) { ?> 
		<p class="error"><?php echo $error; ?></p>
		<?php } ?> 
		<code>Database otomatis ter-generate jika setelah pengecekan tidak ditemukan database yg ter-input.<br />Setelah berhasil ter-generate, untuk login silahkan gunakan username : <strong>admin</strong>, password : <strong>admin123</strong></code>
		<form method="post" action="">
			<div class="form-group">
				<label for="db_host">Host</label>
				<input type="text" name="db_host" id="db_host" autocomplete="off" required="" value="127.0.0.1" />
			</div>
			<div class="form-group">
				<label for="db_user">Username</label>
				<input type="text" name="db_user" id="db_user" autocomplete="off" required="" />
			</div>
			<div class="form-group">
				<label for="db_pass">Password</label>
				<input type="text" name="db_pass" id="db_pass" autocomplete="off" />
			</div>
			<div class="form-group">
				<label for="db_name">Database</label>
				<input type="text" name="db_name" id="db_name" autocomplete="off" required="" />
			</div>
			<div class="form-group">
				<button type="submit">Simpan</button>
			</div>
		</form>
	</div>
</div>

</body>
</html>
<?php } ?>