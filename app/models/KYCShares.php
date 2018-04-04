<?php
namespace app\models;

class KYCShares extends \lithium\data\Model {
 protected $_meta = array(
   'connection' => 'default_kyc',
   'source'=>'shares'
 );
}
?>