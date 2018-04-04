<?php
namespace app\controllers;
use app\models\Countries;
use app\models\Templates;
use app\models\Apps;
use app\models\Wallets;


use app\extensions\action\Uuid;
use app\models\KYCDetails;
use app\models\KYCCompanies;
use app\models\KYCDocuments;
use app\models\KYCCountries;
use app\models\KYCFiles;
use app\models\KYCQuestions;
use app\models\KYCSubmits;
use app\models\KYCShares;

use app\extensions\action\GoogleAuthenticator;
use app\extensions\action\OP_Return;
use lithium\util\Validator;
use app\extensions\action\Functions;
use app\extensions\action\EvolveChain;

class VerifyController extends \lithium\action\Controller {

	public function index($id=null){
    
  $questions = KYCQuestions::find('first');
    $conditions = array('key' => $id);
    $appRecord = Apps::find('first',array(
    'conditions'=>$conditions
   ));
   
   $conditions = array('hash' => $appRecord['hash']);
   
		$document = KYCDocuments::find('first',array(
			'conditions'=>$conditions
		));
  
  
		if(count($document)==0){return $this->redirect('Pages::index');exit;}
		if($document['verify']['verified']=="Yes"){return $this->redirect('kyc::verified');}
		
		$image_hold_img = KYCFiles::find('first',array(
			'conditions'=>array('details_hold_img_id'=>(string)$document['_id'])
		));
  
		if($image_hold_img['filename']!=""){
				$imagename_hold_img = $image_hold_img['_id'].'_'.$image_hold_img['filename'];
    
				$path = LITHIUM_APP_PATH . '/webroot/documents/'.$imagename_hold_img;
				file_put_contents($path, $image_hold_img->file->getBytes());
		}

  
				$image_address = KYCFiles::find('first',array(
			'conditions'=>array('details_address_proof_id'=>(string)$document['_id'])
		));
		if($image_address['filename']!=""){
				$imagename_address = $image_address['_id'].'_'.$image_address['filename'];
					$path = LITHIUM_APP_PATH . '/webroot/documents/'.$imagename_address;
				file_put_contents($path, $image_address->file->getBytes());
		}

		$image_passport1 = KYCFiles::find('first',array(
			'conditions'=>array('details_passport1_id'=>(string)$document['_id'])
		));
		if($image_passport1['filename']!=""){
				$imagename_passport1 = $image_passport1['_id'].'_'.$image_passport1['filename'];
					$path = LITHIUM_APP_PATH . '/webroot/documents/'.$imagename_passport1;
				file_put_contents($path, $image_passport1->file->getBytes());
		}

		$image_passport2 = KYCFiles::find('first',array(
			'conditions'=>array('details_passport2_id'=>(string)$document['_id'])
		));
		if($image_passport2['filename']!=""){
				$imagename_passport2 = $image_passport1['_id'].'_'.$image_passport2['filename'];
					$path = LITHIUM_APP_PATH . '/webroot/documents/'.$imagename_passport2;
				file_put_contents($path, $image_passport2->file->getBytes());
		}

		$image_identity1 = KYCFiles::find('first',array(
			'conditions'=>array('details_identity1_id'=>(string)$document['_id'])
		));
		if($image_identity1['filename']!=""){
				$imagename_identity1 = $image_identity1['_id'].'_'.$image_identity1['filename'];
					$path = LITHIUM_APP_PATH . '/webroot/documents/'.$imagename_identity1;
				file_put_contents($path, $image_identity1->file->getBytes());
		}
		$image_identity2 = KYCFiles::find('first',array(
			'conditions'=>array('details_identity2_id'=>(string)$document['_id'])
		));
		if($image_identity2['filename']!=""){
				$imagename_identity2 = $image_identity2['_id'].'_'.$image_identity2['filename'];
					$path = LITHIUM_APP_PATH . '/webroot/documents/'.$imagename_identity2;
				file_put_contents($path, $image_identity2->file->getBytes());
		}
		

		$image_driving = KYCFiles::find('first',array(
			'conditions'=>array('details_drivinglicense1_id'=>(string)$document['_id'])
		));
		if($image_driving['filename']!=""){
				$imagename_driving = $image_driving['_id'].'_'.$image_driving['filename'];
					$path = LITHIUM_APP_PATH . '/webroot/documents/'.$imagename_driving;
				file_put_contents($path, $image_driving->file->getBytes());
		}
		
		$image_tax1 = KYCFiles::find('first',array(
			'conditions'=>array('details_tax1_id'=>(string)$document['_id'])
		));
		if($image_tax1['filename']!=""){
				$imagename_tax1 = $image_tax1['_id'].'_'.$image_tax1['filename'];
					$path = LITHIUM_APP_PATH . '/webroot/documents/'.$imagename_tax1;
				file_put_contents($path, $image_tax1->file->getBytes());
		}
		
		$image_profile_img = KYCFiles::find('first',array(
			'conditions'=>array('details_profile_img_id'=>(string)$document['_id'])
		));
		if($image_profile_img['filename']!=""){
				$imagename_profile_img = $image_profile_img['_id'].'_'.$image_profile_img['filename'];
					$path = LITHIUM_APP_PATH . '/webroot/documents/'.$imagename_profile_img;
				file_put_contents($path, $image_profile_img->file->getBytes());
		}
		
		
		if ($this->request->data) {
   $Score = 0;
   
   $Score = $Score + $this->request->data[Verify][Basic][Uploaded];
   $Score = $Score + $this->request->data[Verify][Basic][Correct];
   $Score = $Score + $this->request->data[Verify][Address][Uploaded];
   $Score = $Score + $this->request->data[Verify][Address][Correct];
   $Score = $Score + $this->request->data[Verify][Passport][Uploaded];
   $Score = $Score + $this->request->data[Verify][Passport][Correct];
   $Score = $Score + $this->request->data[Verify][Tax][Uploaded];
   $Score = $Score + $this->request->data[Verify][Tax][Correct];
   $Score = $Score + $this->request->data[Verify][Identity][Uploaded];
   $Score = $Score + $this->request->data[Verify][Identity][Correct];
   $Score = $Score + $this->request->data[Verify][Driving][Uploaded];
   $Score = $Score + $this->request->data[Verify][Driving][Correct];
   $Score = $Score + $this->request->data[Verify][Face][Uploaded];
   $Score = $Score + $this->request->data[Verify][Face][Correct];


   
			$data = array(
				"Verify.Basic.Uploaded"=>$this->request->data[Verify][Basic][Uploaded],
				"Verify.Basic.Correct"=>$this->request->data[Verify][Basic][Correct],
				"Verify.Address.Uploaded"=>$this->request->data[Verify][Address][Uploaded],
				"Verify.Address.Correct"=>$this->request->data[Verify][Address][Correct],
				"Verify.Passport.Uploaded"=>$this->request->data[Verify][Passport][Uploaded],
				"Verify.Passport.Correct"=>$this->request->data[Verify][Passport][Correct],
				"Verify.Tax.Uploaded"=>$this->request->data[Verify][Tax][Uploaded],
				"Verify.Tax.Correct"=>$this->request->data[Verify][Tax][Correct],
				"Verify.Identity.Uploaded"=>$this->request->data[Verify][Identity][Uploaded],
				"Verify.Identity.Correct"=>$this->request->data[Verify][Identity][Correct],
				"Verify.Driving.Uploaded"=>$this->request->data[Verify][Driving][Uploaded],
				"Verify.Driving.Correct"=>$this->request->data[Verify][Driving][Correct],
				"Verify.Face.Uploaded"=>$this->request->data[Verify][Face][Uploaded],
				"Verify.Face.Correct"=>$this->request->data[Verify][Face][Correct],

				"Verify.DateTime"=> new \MongoDate(),
				"Verify.Checked"=>"Yes",
				"Verify.Score" => $Score,
				"Verify.Percent" => round($Score/100*100,0)
			);
			$conditions = array('hash'=>$appRecord['hash']);
   
			$save = KYCDocuments::update($data, $conditions);
		}

  if($Score>=70){
    $coin = new EvolveChain('http://'.EVOLVECHAIN_WALLET_SERVER.':'.EVOLVECHAIN_WALLET_PORT,EVOLVECHAIN_WALLET_USERNAME,EVOLVECHAIN_WALLET_PASSWORD);
    $address = $coin->getnewaddress($document['kyc_id']);
    print_r($document['kyc_id']);
    print_r($address);
    exit;
   
   
  }



  $conditions = array('hash' => $appRecord['hash']);

		$document = KYCDocuments::find('first',array(
			'conditions'=>$conditions
		));

	return compact('document','imagename_address','imagename_identity1','imagename_identity2','imagename_passport1','imagename_passport2','imagename_driving','imagename_tax1','imagename_hold_img','imagename_profile_img','questions');
		
  }
  
  public function addaddress($secret=null,$address=null){
		if($address==null || $address==""){
			return $this->render(array('json' => array('success'=>0,
			'now'=>time(),
			'error'=>'EvolveChain address missing!'
			)));
		}
		$coin = new EvolveChain('http://'.EVOLVECHAIN_WALLET_SERVER.':'.EVOLVECHAIN_WALLET_PORT,EVOLVECHAIN_WALLET_USERNAME,EVOLVECHAIN_WALLET_PASSWORD);
			$validateAddress = $coin->validateaddress($address);
			if($validateAddress['isvalid']==0 || $validateAddress==null || $validateAddress==''){
				return $this->render(array('json' => array('success'=>0,
				'now'=>time(),
				'error'=>'Address incorrect!'
				)));
			}

		if($secret==null || $secret==""){
			return $this->render(array('json' => array('success'=>0,
			'now'=>time(),
			'error'=>'Something is wrong!'
			)));
		}
			$data = array(
     'secret'=>$secret,
					'address' => $address
				);

$txid = $coin->sendopreturn((string)$address,$secret)		;

	// if(strlen($txid)==64){
				Wallets::create()->save($data);
				return $this->render(array('json' => array('success'=>"1",
					'now'=>time(),
					'msg'=>'Client verified',
					'secret'=>$secret,
					'txid'=>$txid,
					'server'=>md5($_SERVER['SERVER_ADDR'])
				)));
		// }else{
				// return $this->render(array('json' => array('success'=>0,
				// 'now'=>time(),
				// 'error'=>'Not valid txid!'
				// )));
		// }
 }
}
?>