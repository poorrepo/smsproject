<?php
	require_once './libs/MDB2.php';
	
	class baza
	{
		var $mdb2;
		var $dbstate; // Stan bazy (polaczony true, niepolaczony false)
		
		function __construct()
		{
			// Globalizacja tablicy $_CONFIG
			global $_CONFIG; 
			//
			$dsn  = "mysql://".$_CONFIG['DB']['user'].":".$_CONFIG['DB']['pass']."@".$_CONFIG['DB']['host']."/".$_CONFIG['DB']['base'];
			$options = array(
				'debug' => 2,
				'result_buffering' => false,
				'use_transactions' => true,
				'portability' => MDB2_PORTABILITY_ALL,
			);
			
			
			$this->mdb2 =& MDB2::singleton($dsn, $options);

			if (PEAR::isError($this->mdb2))
			{
				echo 'B│╣d po│╣czenia z baz╣ danych!';
				die ($this->mdb2->getMessage());
			}
			$this->mdb2->query("SET NAMES 'utf8'");
			$this->dbstate = true;
			
			//return $this->$mdb2;
		}	
			

		function __destruct()
		{
			$this->mdb2->disconnect();
		}
		
		// Czy polaczony
		function is_connected()
		{
			return $this->dbstate;
		}
	}
?>
