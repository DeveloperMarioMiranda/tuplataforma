<?php
	include_once "psl-config.php";

	function sec_session_start(){

		//$session_name = 'sec_session_id'; //nome da sessao

		//session_name($session_name);

		$secure = true;

		$httponly = true;

		if(ini_set('session.use_only_cookies', 1) === FALSE){
			header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
			exit();
		}

		$cookieparams = session_get_cookie_params();
		session_set_cookie_params($cookieparams["lifetime"], $cookieparams["path"], $cookieparams["domain"], true, true);

		session_start();            // Start the PHP session 
   	 	//session_regenerate_id(true);
	}

	function login($usuario, $senhaInput, $mysqli) {

	    // Using prepared statements means that SQL injection is not possible. 
	    if ($stmt = $mysqli->prepare("SELECT * FROM usuarios WHERE usuario = ? LIMIT 1")){
	        $stmt->bind_param('s', $usuario);  //s para strin, i para inteiro, d para double e b para dados enviados para blob
	        $stmt->execute();    // Execute the prepared query.
	        $stmt->store_result();
	 
	        // get variables from result.
	        $stmt->bind_result($id, $usuario, $senha, $email, $dateRegis);
	        $stmt->fetch();

	        if ($stmt->num_rows == 1) { 
	            // If the user exists we check if the account is locked
	            // from too many login attempts 
	 /*
	            if (checkbrute($id, $mysqli) == true) {
	                // Account is locked 
	                // Send an email to user saying their account is locked
	                return false;
	            }
	           */
	       //     else {
	                // Check if the password in the database matches
	                // the password the user submitted. We are using
	                // the password_verify function to avoid timing attacks.
	                
	                if (password_verify($senhaInput, $senha)){
	                    // Password is correct!
	                    // Get the user-agent string of the user.
	                   // echo "Senha Correta";
	                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
	                    // XSS protection as we might print this value
	                    $id = preg_replace("/[^0-9]+/", "", $id);
	                    $_SESSION['id'] = $id;
	                    // XSS protection as we might print this value
	                    $usuario = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $usuario);
	                    $_SESSION['usuario'] = $usuario;
	                    $_SESSION['login_string'] = hash('sha512', $senha . $user_browser);

	                    return true;
	                }

	                else {
	                    // Password is not correct
	                    // We record this attempt in the database
	                    echo "Senha incorreta";
	                    $now = time();
	                    $mysqli->query("INSERT INTO tentativas_login(id_usuario, tempo) VALUES ('$id', '$now')");
	                    return false;
	                }
	            }
	   //     }
	        else {
	            // No user exists.
	            return false;
	        }
	    }
	}


	function login_check($mysqli){
	/*	echo '<pre>';
		print_r($mysqli);
		echo '</pre>';
		*/
	    // Check if all session variables are set 
	    if (isset($_SESSION['id'], $_SESSION['usuario'], $_SESSION['login_string'])){	 
	        $user_id = $_SESSION['id'];
	        $login_string = $_SESSION['login_string'];
	        $username = $_SESSION['usuario'];

	        //echo "O nome é ". $username . " e id " . $user_id;

	        // Get the user-agent string of the user.
	        $user_browser = $_SERVER['HTTP_USER_AGENT'];	 
	        if ($stmt = $mysqli->prepare("SELECT senha FROM usuarios WHERE id = ? LIMIT 1")){
	            // Bind "$user_id" to parameter. 
	            $stmt->bind_param('i', $user_id);
	            $stmt->execute();   // busca o usuario dado o id
	            $stmt->store_result();
	 			

	            if($stmt->num_rows == 1){ //verifica se achou
	                // se achar o usuario  ve se a senha confere
	                $stmt->bind_result($senha);
	                $stmt->fetch();
	                $login_check = hash('sha512', $senha . $user_browser);

	                //echo "Aqui o check = ".$login_check ."<br>";
	                //echo "Aqui o String = ".$login_string;
	 
	                if (hash_equals($login_check, $login_string) ){
	                    // Logged In!!!! 
	                    return true;
	                }

	                else {
	                    // Not logged in 
	                 //  	echo "primeiro";
	                    return false;
	                }
	            }

	            else {
	            	echo "segundo";
	                // Not logged in 
	                return false;
	            }

	       	} 
	        
	        else{
	        	echo "terceiro";
	            // Not logged in 
	            return false;
	        }
	    }

	    else{
	        // Not logged in 
	        return false;
	    }
	}



	function checkbrute($user_id, $mysqli) {
	    // Get timestamp of current time 
	    $now = time();
	 
	    // All login attempts are counted from the past 2 hours. 
	    $valid_attempts = $now - (2 * 60 * 60);
	 
	    if ($stmt = $mysqli->prepare("SELECT tempo FROM tentativas_login WHERE id_usuario = ? AND tempo > '$valid_attempts'")){
	        $stmt->bind_param('i', $user_id);
	 
	        // Execute the prepared query. 
	        $stmt->execute();
	        $stmt->store_result();
	 
	 		//sempre que der erro no login vai ser armazenado na tabela tentativas_login o id do usuario e a hora
	        // a resultado da pesquisa no BD acima vai retornar todas as rows de tentativas feitas das duas ultimas horas
	        // correspondente ao id do usuario, se o retorno dessas pesquisas for maior que cinco retorna verdadeiro
	        if ($stmt->num_rows > 5) {
	            return true;
	        }
	        else {
	            return false;
	        }
	    }
	    //NOTA: acho que deveria tirar esse else dali de cima e colocar 'return false' aqui embaixo
	    //pois ele so entra no if se achar o usuario se nao achar ja da erro de cara,
	    //e caso entre no if e o numero de tentativas seje maior que 5 ele sai daquele
	    //if e continua o codigo e ja bate com o false aqui embaixo.
	}



	function esc_url($url) {
 
	    if ('' == $url) {
	        return $url;
	    }
	 
	    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
	 
	    $strip = array('%0d', '%0a', '%0D', '%0A');
	    $url = (string) $url;
	 
	    $count = 1;
	    
	    while ($count) {
	        $url = str_replace($strip, '', $url, $count);
	    }
	 
	    $url = str_replace(';//', '://', $url);
	 
	    $url = htmlentities($url);
	 
	    $url = str_replace('&amp;', '&#038;', $url);
	    $url = str_replace("'", '&#039;', $url);
	 
	    if ($url[0] !== '/') {
	        // We're only interested in relative links from $_SERVER['PHP_SELF']
	        return '';
	    } 

	    else {
	        return $url;
	    }
	}
?>