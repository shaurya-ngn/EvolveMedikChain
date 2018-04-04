<?php
namespace app\controllers;
use app\models\Blocks;
use app\models\Txs;
use app\extensions\action\EvolveChain;
class BlockchainController extends \lithium\action\Controller {

	public function index() {
  $EVOLVECHAIN = new EVOLVECHAIN('http://'.EVOLVECHAIN_WALLET_SERVER.':'.EVOLVECHAIN_WALLET_PORT,EVOLVECHAIN_WALLET_USERNAME,EVOLVECHAIN_WALLET_PASSWORD);
  $getinfo = $EVOLVECHAIN->getinfo();
  $getblockcount = $EVOLVECHAIN->getblockcount();
  $getconnectioncount = $EVOLVECHAIN->getconnectioncount();
	 $getblockhash = $EVOLVECHAIN->getblockhash($getblockcount);
	 $getblock = $EVOLVECHAIN->getblock($getblockhash);
 	$title = "Network connectivity ";		
	 return compact('getinfo','getblockcount','getconnectioncount','getblock','title');
	}

	public function blocks($blockcount = null){
		$EVOLVECHAIN = new EVOLVECHAIN('http://'.EVOLVECHAIN_WALLET_SERVER.':'.EVOLVECHAIN_WALLET_PORT,EVOLVECHAIN_WALLET_USERNAME,EVOLVECHAIN_WALLET_PASSWORD);
		
	  if (!isset($blockcount)){
	  	  $blockcount = $EVOLVECHAIN->getblockcount();
	  }else{
	  	$blockcount = intval($blockcount);
	  }
	  if($blockcount<10){$blockcount = 10;}
	  $getblock = array();
	  $getblockhash = array();
	  $j = 0;
	  for($i=$blockcount;$i>$blockcount-10;$i--){
		$getblockhash[$j] = $EVOLVECHAIN->getblockhash($i);
		$getblock[$j] = $EVOLVECHAIN->getblock($getblockhash[$j]);
		$j++;
	  }
  		$title = "Blocks: ". $blockcount;		
		return compact('getblock','blockcount','title');
	}
	public function peer(){
		$EVOLVECHAIN = new EVOLVECHAIN('http://'.EVOLVECHAIN_WALLET_SERVER.':'.EVOLVECHAIN_WALLET_PORT,EVOLVECHAIN_WALLET_USERNAME,EVOLVECHAIN_WALLET_PASSWORD);
		$title = "Peer connection infomration";
		$getpeerinfo = $EVOLVECHAIN->getpeerinfo();
		$getconnectioncount = $EVOLVECHAIN->getconnectioncount();
		return compact('title','getpeerinfo','getconnectioncount');
	
	}

	public function blockhash($blockhash = null){
		$EVOLVECHAIN = new EVOLVECHAIN('http://'.EVOLVECHAIN_WALLET_SERVER.':'.EVOLVECHAIN_WALLET_PORT,EVOLVECHAIN_WALLET_USERNAME,EVOLVECHAIN_WALLET_PASSWORD);
		$blockcount = $EVOLVECHAIN->getblockcount();
	if (!isset($blockhash)){
		$blockhash = $EVOLVECHAIN->getblockhash($blockcount);		
		$prevblock = $blockcount - 1;
		$prevblockhash = $EVOLVECHAIN->getblockhash($prevblock);		
	}else{
		$getblock = $EVOLVECHAIN->getblock($blockhash);
		$prevblock = $getblock['height'] - 1;
		$prevblockhash = $EVOLVECHAIN->getblockhash($prevblock);		
		if($getblock['height']<>$blockcount ){
			$nextblock = $getblock['height'] + 1;
			$nextblockhash = $EVOLVECHAIN->getblockhash($nextblock);		
		
		}
		
	}
	
		$getblock = $EVOLVECHAIN->getblock($blockhash);
		$title = "Block hash: ". $blockhash;		
		return compact('getblock','prevblockhash','nextblockhash','prevblock','nextblock','title');
	}

		
	public function transactionhash($transactionhash = null){
		$EVOLVECHAIN = new EVOLVECHAIN('http://'.EVOLVECHAIN_WALLET_SERVER.':'.EVOLVECHAIN_WALLET_PORT,EVOLVECHAIN_WALLET_USERNAME,EVOLVECHAIN_WALLET_PASSWORD);
		$getrawtransaction = $EVOLVECHAIN->getrawtransaction($transactionhash);
//		print_r($getrawtransaction);
		$decoderawtransaction = $EVOLVECHAIN->decoderawtransaction($getrawtransaction);
//		print_r($decoderawtransaction);
		$listsinceblock = $EVOLVECHAIN->listsinceblock($transactionhash);
		$title = "Transactions hash: ". $transactionhash;		
		return compact('decoderawtransaction','listsinceblock','title');
	}
	
	public function address($address = null,$json = null){
		$EVOLVECHAIN = new EVOLVECHAIN('http://'.EVOLVECHAIN_WALLET_SERVER.':'.EVOLVECHAIN_WALLET_PORT,EVOLVECHAIN_WALLET_USERNAME,EVOLVECHAIN_WALLET_PASSWORD);
		$title = "Transactions done by ". $address;
		$txout = Txs::find('all',array(
			'conditions'=>array('txid.address'=>$address),
		));
		print_r(count($txout));
		$txin = Txs::find('all',array(
			'conditions'=>array('txid.vin.address'=>$address),
		));
		print_r(count($txin));
		if($json == 'json'){
			return $this->render(array('json' =>  
				compact('n_tx','total_received','total_sent','final_balance','txs','address'),
			'status'=> 200));
		}
		return compact('txout','txin','address');
	}


}

?>