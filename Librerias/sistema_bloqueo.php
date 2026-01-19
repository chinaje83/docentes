<?php
//--------------------------------------------------------------------------
// Para el manejo del BLOQUEO DEL SISTEMA.
//--------------------------------------------------------------------------
class SistemaBloqueo
{
	var $MostrarMSGBloqueo;

//-----------------------------------------------------------------------------------------
// Inicializa el objeto. Si no existe la variable de sesion, inicializa en SI

	function __construct()
	{
		//if(isset($_SESSION['mostrarmsgbloqueo']))
		//	$this->MostrarMSGBloqueo=$_SESSION['mostrarmsgbloqueo'];
		//else
			$this->MostrarMSGBloqueo="SI";
	}

//-----------------------------------------------------------------------------------------
// Setea la variable de mostrarmsgbloqueo

	function setMostrarMSG($texto)
	{
		$MostrarMSGBloqueo=$texto;
		$_SESSION['mostrarmsgbloqueo']=$texto;
	}

//-----------------------------------------------------------------------------------------
// Muestra el mensaje de Bloqueo

	function MostrarAviso($aviso,$fecha)
	{
		?>
		<!-- Para el mensaje de aviso que el sistema se bloquear� -->
		<style type="text/css">
		.avisobloqueo {
			font-family: Arial, Helvetica, sans-serif;
			font-size: 18px;
			font-weight: bold;
			color: #FFFFFF;
			background-color: #FF0000;
			background-position: center;
		}
		</style>
		<?php
		echo "<table width='100%' border='0' cellspacing='0' cellpadding='0' >
			 <tr> <td class='avisobloqueo'>";
			  echo 'El sistema se bloquear� el '  .substr($fecha,8,2)."/".substr($fecha,5,2)."/".substr($fecha,0,4).' a las '.substr($fecha,10,6).' hs. <br />';
			  echo $aviso."<br />";
		echo "</td></tr></table>";
	}

//--------------------------------------------------------------------------
//  Verifica si la base esta activa (tabla:sistema)

// 1) En caso de NO estar activa verifica si
//    *) Si esta fuera de linea : Termina con la session y muestra mensaje
//    *) Si todavia en linea: Muestra aviso de pr�ximamente ser� sacado de linea.

	function VerificarActivo($conexion,&$activo,&$mensaje,$muestromensaje)
	{
		$param=array();
		if(!$conexion->ejecutarStoredProcedure('sel_sistema',$param,$resultado,$numfilas,$errno))
			die("Se produjo un error al acceder al sistema - ".$errno);
		if($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($conexion,MSG_ERRGRAVE,"Error en la tabla sistema.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array('formato'=>FMT_TEXTO));
			$activo = "SI";
		}
		else
		{
			$fila = $conexion->ObtenerSiguienteRegistro($resultado);
			if ($fila['Activo'] == "SI")
				$activo = "SI";
			else
			{
				$ahora = strtotime(date("Y-m-d H:i:s"));
				$inicio = strtotime($fila['FechaInicio']);
				if ($ahora >= $inicio)
				{
					$activo  = "NO";
					$mensaje = $fila['Mensaje'];

					// Los roles diferentes a Admin se les destruye la session
					if (!isset($_SESSION['rolcod'][10]) || $_SESSION['rolcod'][10] != 10)
					{
						$_SESSION=array();
						session_destroy();
					}
				}
				else
				{
					$activo = "SI";
					if ($muestromensaje=="SI")
						$this->MostrarAviso($fila['Aviso'],$fila['FechaInicio']);
				}
			} // if activo
		} // if cant de filas de sistema
	} // fin funcion

//--------------------------------------------------------------------------
// Verifico si esta bloqueado. Si es asi, finaliza el script.

	function VerificarBloqueo($conexion)
	{
       if (defined("LOGERRORESBD") && LOGERRORESBD==1)
       {
            ErrorReporter::init([
                'channels' => ['email' => false, 'db' => true],
                'db' => [
                    'adapter' => 'accesoBD',
                    'instance' => $conexion,     // le pasás tu objeto accesoBDLocal
                    'table' => BASEDATOSERRORESPHP.'.error_reports',
                ],
                'show_friendly_page'=>false,
                'user' => $_SESSION['usuariocod'] ?? '',
                'rol'  =>  $_SESSION['ConstanteRol'] ?? '',

            ]);

        }

		unset($_SESSION['sistemaEnMantenimiento']);

		if($this->MostrarMSGBloqueo == "SI")
		{
			$this->VerificarActivo($conexion,$activo,$mensaje,"SI");

			if ($activo == "NO")
			{
				// Guardamos solo la bandera para el header
				$_SESSION['sistemaEnMantenimiento'] = 1;

				// Si NO es rol 10, bloquear
				if (!isset($_SESSION['rolcod'][10]) || $_SESSION['rolcod'][10] != 10)
				{
					$mensajeBloqueo = $mensaje;
					include("sistema_bloqueo_view.php");
					exit;
				}
			}

		}
	}

        // TODO: Implement __callStatic() me
} // fin clase
?>
