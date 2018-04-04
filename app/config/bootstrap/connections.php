<?php
use lithium\data\Connections;
 
 Connections::add('default', array(
 	'type' => CONNECTION_TYPE,
 	'host' => array(CONNECTION,
		),
//	'replicaSet' => true,
	'database' => CONNECTION_DB,
	'login' => CONNECTION_USER,
	'password' => CONNECTION_PASS,	
//	'setSlaveOkay' => true,
//	'readPreference' => Mongo::RP_NEAREST 
 ));

 Connections::add('default_kyc', array(
 	'type' => CONNECTION_TYPE_KYC,
 	'host' => array(CONNECTION_KYC,
		),
//	'replicaSet' => true,
	'database' => CONNECTION_DB_KYC,
	'login' => CONNECTION_USER_KYC,
	'password' => CONNECTION_PASS_KYC,	
//	'setSlaveOkay' => true,
//	'readPreference' => Mongo::RP_NEAREST 
 ) );

 Connections::add('default_evolve', array(
 	'type' => CONNECTION_TYPE_EVOLVE,
 	'host' => array(CONNECTION_EVOLVE,
		),
//	'replicaSet' => true,
	'database' => CONNECTION_DB_EVOLVE,
	'login' => CONNECTION_USER_EVOLVE,
	'password' => CONNECTION_PASS_EVOLVE,	
//	'setSlaveOkay' => true,
//	'readPreference' => Mongo::RP_NEAREST 
) );
 
?>