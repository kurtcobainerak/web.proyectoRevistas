<?php  

require_once('db_abstract_model.php');
require_once('login_admin_model.php');

class Login extends DBAbstractModel
{
	// DEFINIENDO ATRIBUTOS
	private $name;
	private $user;
	private $password;
	private $state;
	private $account;

	public 	$status = FALSE;

	

	// METODOS MAGICOS
	function __construct( $user = '', $password = '' )
	{
		$this->db_name = 'mvc';

		$this->user = $user;
		$this->encrypting( $password );
	}

	function __destruct()
	{
		unset( $this );
	}
	
	// LOGIN
	public function login()
	{
		$this->date();
		return ( $this->status );		
	}

		
	// BROWSE USER
	public function browseUser()
	{    
		$this->query = "
			SELECT 		name, email, password, state, account
			FROM 		usuarios 
			WHERE 		email		= '$this->user' 
			AND 		password 	= '$this->password' ";
		$this->get_results_from_query();

		if( count($this->rows) == 1 ):

			foreach( $this->rows[0] as $valor):
				$propiedad[] = $valor;
			endforeach;

			$this->name = $propiedad[0];
			$this->state = $propiedad[3];
			$this->account = $propiedad[4];

			$this->newSession();
		endif;
	}

	// SESSION START
	private function varSession()
	{
		session_name("user");
		if(!isset($_SESSION)):
	        session_start(); 
	    endif; 
	}

	/// NEW SESSION
	public function newSession()
	{
		//$this->varSession();
		$_SESSION["authenticated"] = TRUE;
		$_SESSION["name"] = $this->name;
		$_SESSION["user"] = $this->user;
		$_SESSION["state"] = $this->state;
		$_SESSION["account"] = $this->account;
		$this->status = TRUE;
	}

	// GET STATUS SESSION
	public function getStatus()
	{
		$this->varSession();
		$this->status = $_SESSION["authenticated"];
		return $this->status;
	}

	// CLOSE SESSION
	public function closeSession()
	{
		$this->varSession();
		session_unset();
		session_destroy();
		unset($_SESSION["user"]);
		unset($_SESSION["authenticated"]);
		unset($_SESSION["state"]);
		unset($_SESSION["account"]);
		$this->status = FALSE;

	}

	private function date()
	{

		$login_date = new Login_admin();
		$login_date->browseDateCompany();
		
		$day = date("l");

		if ( $day=="Monday" ) 		$day 	=	"LUNES";
		if ( $day=="Tuesday" ) 		$day 	=	"MARTES";
		if ( $day=="Wednesday" ) 	$day 	=	"MIERCOLES";
		if ( $day=="Thursday" ) 	$day 	=	"JUEVES";
		if ( $day=="Friday" ) 		$day 	=	"VIERNES";
		if ( $day=="Saturday" ) 	$day 	=	"SABADO";
		if ( $day=="Sunday" ) 		$day 	=	"DOMINGO";

		$days_week = explode(",", $login_date->days);
		$hour = date("H:i:s");
		
		foreach ( $days_week as $days) :

			if ( $days == $day ) :
				if ( ( $login_date->start_time < $hour ) && ( $hour < $login_date->end_time ) ) :
					$this->browseUser();
				endif;
			endif;

		endforeach;
	}

	// ENCRYPTING PASSWORD
	private function encrypting( $password )
	{
		$this->password = hash('sha1', $password);
	}

}

?>