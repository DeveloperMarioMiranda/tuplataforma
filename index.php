<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
 
sec_session_start();
 
if (login_check($mysqli) == true) {
    $logged = 'in';
} 
else {
    $logged = 'out';
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Teste de Acesso ao BD</title>
	<script type="text/JavaScript" src="js/sha512.js"></script> 
    <script type="text/JavaScript" src="js/form.js"></script>
</head>

<body>

	<div style="
				position: fixed;
				top: 50%; left: 50%;
				transform: translate(-50%, -50%);
				width: 400px; 
				height: 500px;
				border:10px; 
				border-color: black;
				border-style: solid;
				border-width: 5px;
				 ">
		<?php
          if (isset($_GET['error'])) {
              echo '<p class="error">Error Logging In!</p>';
          }
        ?>

        <div style="margin-top: 50px;"></div>
		<form action="includes/process_login.php" method="post" >

			<label for="usuario">Usuario</label>	
			<input type="text" name="usuario" style="display: block;margin:0 auto;">
			<label for="senha">Senha </label>
			<input type="password" name="senha" style="display: block;margin:0 auto;">
			<div style="margin-top: 10px;"></div>
			<button type="button" style="display: block;margin:0 auto;" onclick="formhash(this.form, this.form.senha);">Entrar</button>
		</form>

		<?php
	        if (login_check($mysqli) == true) {
	            echo '<p>Currently logged ' . $logged . ' as ' . htmlentities($_SESSION['usuario']) . '.</p>';                 
	            echo '<p>Do you want to change user? <a href="includes/logout.php">Log out</a>.</p>';
	        } 
	        else{
	          echo '<p>Currently logged ' . $logged . '.</p>';
	          echo "<p>If you don't have a login, please <a href='register.php'>register</a></p>";
	        }
      	?> 

	</div>
</body>
</html>