<?php
  session_name("relojes"); 
  session_start(); 
  session_unset(); 
  session_destroy(); 
  include("./zklib/util.php");
   date_default_timezone_set('America/Santiago');
  if (isset($_POST['entrar']))
   {
     $sqlusuario="Select count(cod_usuario) as U,nombre,usuario,clave  from usuario where usuario='".$_POST['usuario']."' and clave='".$_POST['clave']."'";
	 //echo $sqlusuario;
	 conectar();
     $datos=mysql_query($sqlusuario,$db);
     desconectar();
	 $empleado=mysql_fetch_array($datos);
	 if ($empleado['U']>0)
	    {
		   session_name("relojes"); 
     	   session_start();
           //session_register("nombreu");
		   $_SESSION["nombreu"]=$empleado["nombre"];
		   $_SESSION["ip"]=$_SERVER['REMOTE_ADDR'];
           $_SESSION["pc"]= gethostbyaddr($_SERVER['REMOTE_ADDR']);
           $_SESSION["fecha"]=date("Y-m-d");
           $_SESSION["hora"]=date("H:i:s");
           
           $ip =getIP();
		   $pc = gethostbyaddr($_SERVER['REMOTE_ADDR']); 
           $fecha = date("Y-m-d");
           $hora= date("H:i:s");
           $acs="Insert into accesos(nombre,fecha,hora,pc,ip)
                 values('".$empleado['nombre']."','".$fecha."','".$hora."','".$pc."','".$ip."')";
            conectar();
            mysql_query($acs,$db);
            desconectar();
		    header("Location: ./procesos/inicio.php");
		}
   }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Simple Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <meta name="layout" content="main"/>
    
    <script type="text/javascript" src="http://www.google.com/jsapi"></script>

    <script src="./js/jquery/jquery-1.8.2.min.js" type="text/javascript" ></script>
    <link href="./css/customize-template.css" type="text/css" media="screen, projection" rel="stylesheet" />

    <style>
    </style>
</head>
    <body>
        
        <div id="body-container">
     <div id="body-content">
           <div class='container'>
               <div class="signin-row row">
                   <div class="span4"></div>
                    <div class="span8">
                        <div class="container-signin">
                            <legend>Control de Asistencia 2017</legend>
                            <form  method='POST' id='loginForm' class='form-signin' autocomplete='off'>
                                <div class="form-inner">
                                    <div class="input-prepend">
                                        
                                        <span class="add-on" rel="tooltip" title="Username or E-Mail Address" data-placement="top"><i class="icon-user"></i></span>
                                        <input type='text' class='span4' id='username' name='usuario'/>
                                    </div>

                                    <div class="input-prepend">
                                        
                                        <span class="add-on"><i class="icon-key"></i></span>
                                        <input type='password' class='span4' id='password' name='clave'/>
                                    </div>
                                </div>
                                <footer class="signin-actions">
                                    <input class="btn btn-primary" type='submit' id="submit" name='entrar' value='Acceso'/>
                                </footer>
                            </form>
                        </div>
                    </div>
                    <div class="span3"></div>
                </div>

                <div class="signin-row row">
                    <div class="span4"></div>
                    <div class="span8">
                        <div class="well well-small well-shadow">
                            <legend class="lead">Información</legend>
                             Sistema creado a solicitud del Departamento de Recursos Humanos de American College.
							 En este sistema podrá revisar las marcacionde Entrada y Salida del Personal.
							 Como asi verificar carga horaria y cumplimiento de cada uno. El acceso a este sistema
							 es de carácter restringido.
                        </div>
                    </div>
                    <div class="span8"></div>
                </div>
            <!--<div class="span4">

                </div>-->
            </div>
    

            </div>
        </div>

        <div id="spinner" class="spinner" style="display:none;">
            Loading&hellip;
        </div>
		<?php
		require('./procesos/pie.php');
		?>
	</body>
</html>