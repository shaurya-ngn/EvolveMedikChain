<?php
namespace app\controllers;
use app\models\Countries;
use app\models\Templates;
use app\models\Apps;

use app\extensions\action\Uuid;
use app\models\KYCDetails;
use app\models\KYCCompanies;
use app\models\KYCDocuments;
use app\models\KYCCountries;
use app\models\KYCFiles;
use app\models\KYCQuestions;
use app\models\KYCShares;

use MongoID;    
use app\extensions\action\GoogleAuthenticator;
use app\extensions\action\OP_Return;
use lithium\util\Validator;
use app\extensions\action\Functions;
use app\extensions\action\EvolveChain;

ini_set('memory_limit', '-1');

class IdController extends \lithium\action\Controller {

 public function index($email=null,$company=null,$kyc1=null,$kyc2=null,$kyc3=null){
  
  
  if($email != null && $company != null && $kyc1 != null && $kyc2 != null && $kyc3 != null){
    if(count($this->request->data)==0){
      return compact('email','company','kyc1','kyc2','kyc3');
    }
  }
  
  if(count($this->request->data)!=0){
  
  $conditions = array('email' => $this->request->data['email']);
   $appRecord = Apps::find('first',array(
    'conditions'=>$conditions
   ));
   $conditions = array('hash' => $appRecord['hash']);
   $documentRecord = KYCDocuments::find('first',array(
    'conditions'=>$conditions
   ));
   
   
   $ga = new GoogleAuthenticator();
   if($ga->verifyCode($documentRecord['secret'][0], $this->request->data['kyc1'], 120)==true 
     && $ga->verifyCode($documentRecord['secret'][1], $this->request->data['kyc2'], 120)==true
     && $ga->verifyCode($documentRecord['secret'][2], $this->request->data['kyc3'], 120)==true){
    
     $conditions = array(
      'hash'=> $appRecord['hash'],
      'new_KYC'=>$this->request->data['kyc1'].'-'.$this->request->data['kyc2'].'-'.$this->request->data['kyc3']
     );
     
     $record = KYCShares::find('first', array(
      'conditions' => $conditions
     ));
     if(count($record)>0){
      $data = array(
       'Company.IP'=>$_SERVER['REMOTE_ADDR'],
       'Company.DateTime' => new \MongoDate(), 
       'Company.Name'=>$this->request->data['name'],
       'Company.Email'=>$this->request->data['companyemail'],
       'Company.Phone'=>$this->request->data['companyphone'],
       'status'=>'used'
      );
      KYCShares::update($data,$conditions);
      $toShare = array();
      $shared = $record['sharedoc'];
      foreach($shared as $share){
       array_push($toShare,array($share=>($documentRecord['details'][$share])));
      }
      
      $document = array(
       'Name'=>$documentRecord['details']['Name']['first'].' '.$documentRecord['details']['Name']['middle'].' '.$documentRecord['details']['Name']['last'],
       'Mobile'=>$documentRecord['details']['Mobile'],
       'Email'=>$documentRecord['email'],
       'ValidTill'=>0
      );
      
      return compact('shared','toShare','document','email','company','kyc1','kyc2','kyc3');
     }      
 }
  }
 }
}?>
