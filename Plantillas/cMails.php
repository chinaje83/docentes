<?php  
class cMails
{
	
	protected $conexion;
	protected $formato;
	
	
	// Constructor de la clase
	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = $formato;
    } 
	
	// Destructor de la clase
	function __destruct() {	
		
    } 	


	function MailReenvioContrasenia($datos)
	{
		$mail = new PHPMailer ();

		
		
		$subject = utf8_decode("Reenviar Contraseña - ".TITLESISTEMA);
		$mail -> AddEmbeddedImage(DOCUMENT_ROOT.'/assets/images/logo.png', 'logo.png');

		$texto = " 
		<html> 
		<body style='margin:25px;'>
		<center>
		<table width='600' border='0' cellspacing='0' cellpadding='0'>
		  <tr>
			<td style='font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px; color:#333333; text-align:justify;'>
					  <div style='width:100%; border-bottom:7px solid #9aca3c;'>
					  	<img width='150' style='display: block; margin: 40px auto 40px auto;' src='cid:logo.png' />
					  </div>
				<b style='font-size:13px;'>Hola ".$datos['UsuarioAd'].".</b>
				<br/><br/><br/>
				<b>Soporte t&eacute;cnico, te reenv&iacute;a una nueva contrase&ntilde;a de acceso.</b>
				<br/>
				<p><hr><p/>
				<center>
				Para entrar en el sistema, utiliza el siguiente enlace:
				<p><a href='".DOMINIOADMIN."' target='_blank'>".DOMINIOADMIN."</a></p>
				</center>
				Encontrar&aacute;s, a continuaci&oacute;n, el usuario de acceso que te permitir&aacute;n acceder al panel de administraci&oacute;n: 
				<br/><br/>
				e_Mail: <b>".$datos['Email']."</b>
				<br/>
				Constrase&ntilde;a: <b>".$datos['Password']."</b>
				<br/><br/>
				Atentamente.<br/>Servicio de Atenci&oacute;n al Usuario.<br/><br/>
				<p><hr></p><br/>
				<span style='font-size:9px; color:#999;'>La informaci&oacute;n contenida tanto en este e-mail, es informaci&oacute;n confidencial y privilegiada para uso exclusivo de la persona o personas a las que va dirigido. No est&aacute; permitido el acceso a este mensaje a cualquier otra persona distinta de los indicados. Si no es el destinatario o ha recibido este mensaje por error, cualquier duplicaci&oacute;n, reproducci&oacute;n, distribuci&oacute;n, as&iacute; como cualquier uso de la informaci&oacute;n contenida o cualquiera otra acci&oacute;n tomada en relaci&oacute;n con el mismo, est&aacute; prohibida y puede ser ilegal. No se autoriza su utilizaci&oacute;n con fines comerciales o para su incorporaci&oacute;n a ficheros automatizados de las direcciones del emisor o del destinatario. En consecuencia, si recibe este correo sin ser el destinatario del mismo, le rogamos proceda a su eliminaci&oacute;n y lo ponga en conocimiento del emisor.</span>
			</td>
		  </tr>
		</table>
		</center>
		</body>
		</html> 
		"; 
		
		$mail -> From = CUENTASMTP;
		$mail -> FromName = PROJECTNAME;
		$mail -> AddAddress ($datos['Email']);
		$mail -> Subject = $subject;
		$mail -> Body = $texto;
		$mail -> IsHTML (true);

		if (ENVIAREMAIL)
		{
			if(!$mail->Send()) 
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al enviar el email de recuperación de contraseña.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}
		
		
		return true;	
	}
	
	
	
	function EnvioContrasenia($datos)
	{
		$mail = new PHPMailer ();

		
		
		$subject = utf8_decode("Contraseña - ".TITLESISTEMA);

		$texto = " 
		<html> 
		<body style='margin:25px;'>
		<center>
		<table width='600' border='0' cellspacing='0' cellpadding='0'>
		  <tr>
			<td>
				<b style='font-size:18px;'>Servicio T&eacute;cnico - ".TITLESISTEMA."</b>
				<p><hr><p/>
			</td>
		  </tr>
		  <tr>
			<td style='font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px; color:#333333; text-align:justify;'>
				<b style='font-size:13px;'>Hola ".$datos['usuarionombre']." ".$datos['usuarioapellido'].".</b>
				<br/><br/><br/>
				<b>Soporte t&eacute;cnico, te env&iacute;a la nueva contrase&ntilde;a de acceso.</b>
				<br/>
				<p><hr><p/>
				<center>
				Para entrar en el sistema, utiliza el siguiente enlace:
				<p><a href='".DOMINIOADMIN."' target='_blank'>".DOMINIOADMIN."</a></p>
				</center>
				Encontrar&aacute;s, a continuaci&oacute;n, el usuario de acceso que te permitir&aacute;n acceder al panel de administraci&oacute;n: 
				<br/><br/>
				e_Mail: <b>".$datos['usuarioemail']."</b>
				<br/>
				Constrase&ntilde;a: <b>".$datos['usuariopassword']."</b>
				<br/><br/>
				Atentamente.<br/>Servicio de Atenci&oacute;n al Usuario.<br/><br/>
				<p><hr></p><br/>
				<span style='font-size:9px; color:#999;'>La informaci&oacute;n contenida tanto en este e-mail, es informaci&oacute;n confidencial y privilegiada para uso exclusivo de la persona o personas a las que va dirigido. No est&aacute; permitido el acceso a este mensaje a cualquier otra persona distinta de los indicados. Si no es el destinatario o ha recibido este mensaje por error, cualquier duplicaci&oacute;n, reproducci&oacute;n, distribuci&oacute;n, as&iacute; como cualquier uso de la informaci&oacute;n contenida o cualquiera otra acci&oacute;n tomada en relaci&oacute;n con el mismo, est&aacute; prohibida y puede ser ilegal. No se autoriza su utilizaci&oacute;n con fines comerciales o para su incorporaci&oacute;n a ficheros automatizados de las direcciones del emisor o del destinatario. En consecuencia, si recibe este correo sin ser el destinatario del mismo, le rogamos proceda a su eliminaci&oacute;n y lo ponga en conocimiento del emisor.</span>
			</td>
		  </tr>
		</table>
		</center>
		</body>
		</html> 
		"; 
		
		$mail -> From = CUENTASMTP;
		$mail -> FromName = PROJECTNAME;
		$mail -> AddAddress ($datos['usuarioemail']);
		$mail -> Subject = $subject;
		$mail -> Body = $texto;
		$mail -> IsHTML (true);

		
		if (ENVIAREMAIL)
		{
			if(!$mail->Send()) 
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al enviar el email de alta.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}
		return true;	
	}
	
	
	
	
	
}


?>