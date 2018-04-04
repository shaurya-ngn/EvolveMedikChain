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
use app\models\KYCShares;


use MongoID;    
use app\extensions\action\GoogleAuthenticator;
use app\extensions\action\OP_Return;
use lithium\util\Validator;
use app\extensions\action\Functions;
use app\extensions\action\EvolveChain;

ini_set('memory_limit', '-1');

class KycController extends \lithium\action\Controller {

  public function index(){
       echo "Index";
       exit();
  }
 
  public function verifyemail($key=null){
      if($key==null || $key==""){
        return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'Key missing!'
        ))); 
      }

      if(Validator::rule('email',$this->request->data['email'])==""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Email not correct!'
          )));
      }

      extract($this->request->data);

      $conditions = array('key' => $key);
      $record = Apps::find('first',array('conditions'=>$conditions));

      if(count($record)!=0){
        $ga = new GoogleAuthenticator();
        $secret = $ga->createSecret(64);
        $kyc_id = $ga->getCode($secret);  
        $email = strtolower($email);
        $email_code = substr($kyc_id,0,6);
          
        ////////////////////////////////////////Send Email
          $emaildata = array(
           'kyc_id'=>$email_code,
           'email'=>$email
          );
          $function = new Functions();
          $compact = array('data'=>$emaildata);
          $from = array(NOREPLY => "noreply@".COMPANY_URL);
          $email = $email;
          $function->sendEmailTo($email,$compact,'process','sendKYC',"KYCGlobal - Email Code",$from,'','','',null);
        //////////////////////////////////////////////////////////////////////
          
          $filed = ['chkemail'=>$email,'email_code'=>$email_code];
          $conditions = array('key' => $key);
          Apps::update($filed,$conditions);


       return $this->render(array('json' => array('success'=>1,
        'result' => 'Please check your email to get the code',
        'email_code'=>$email_code,
        'email' => $this->request->data['email']
       )));
      }else{
        return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'Invalid Key!'
        )));  
      } 
  }

  public function verifymobile($key=null){
    if($key==null || $key==""){
      return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Key missing!'
      ))); 
    }

    if($this->request->data['mobile']==null || $this->request->data['mobile']==""){
      return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Mobile missing!'
      )));
    }

    extract($this->request->data);
    $conditions = array('key' => $key);
    $record = Apps::find('first',array('conditions'=>$conditions));
    if(count($record)!=0){
      $ga = new GoogleAuthenticator();
      $secret = $ga->createSecret(64);
      $signinCode = $ga->getCode($secret);  
      $function = new Functions();
      $phone = $this->request->data['mobile'];
      if(substr($phone,0,1)=='+'){
        $phone = str_replace("+","",$phone);
      }
    
      $msg = 'Please enter EvolveChain mobile verification code: '.$signinCode.'.';
      $returnvalues = $function->twilio($phone,$msg,$signinCode);  // Testing if it works 
      
        $filed = ['chkphone'=>$mobile,'phone_code'=>$signinCode];
        $conditions = array('key' => $key);
        Apps::update($filed,$conditions);

      return $this->render(array('json' => array('success'=>1,
        'now'=>time(),
        'result' => 'Verification OTP sent',
        'phone_code'=>$signinCode,
        'phone'=>$phone
      )));    
    }else{
      return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Invalid Key!'
      )));  
    } 
  }

  public function createupdatekyc($key = null){
     if($key==null || $key==""){
       return $this->render(array('json' => array('success'=>0,
         'now'=>time(),
         'error'=>'Key missing!'
       )));
     }

     if($this->request->data['email']==null || $this->request->data['email']==""){
       return $this->render(array('json' => array('success'=>0,
         'now'=>time(),
         'error'=>'Email missing!'
       )));
     }

     if($this->request->data['email_code']==null || $this->request->data['email_code']==""){
       return $this->render(array('json' => array('success'=>0,
         'now'=>time(),
         'error'=>'email code missing!'
       )));
     }

     if($this->request->data['mobile']==null || $this->request->data['mobile']==""){
       return $this->render(array('json' => array('success'=>0,
         'now'=>time(),
         'error'=>'Mobile missing!'
       )));
     }

     if($this->request->data['mobile_code']==null || $this->request->data['mobile_code']==""){
       return $this->render(array('json' => array('success'=>0,
         'now'=>time(),
         'error'=>'Mobile Code missing!'
       )));
     }

     if($this->request->data['ip']==null || $this->request->data['ip']==""){
       return $this->render(array('json' => array('success'=>0,
         'now'=>time(),
         'error'=>'IP missing!'
       )));
     }
     
     extract($this->request->data);
     $email = strtolower($email);
     $conditions = array('key' => $key,'chkemail' => $email,'email_code' => $email_code,
                         'chkphone' => $mobile,'phone_code' => $mobile_code);
     $record = Apps::find('first',array('conditions'=>$conditions));
     if(count($record)!=0){
         $conditions = array('email' => $email,'phone' => $mobile,'isdelete' =>'0');
         $chk = Apps::find('first',array('conditions'=>$conditions));
           
         if(count($chk) != 0){

            if($chk['key'] != $key){

               $data['email'] = $chk['email'];
               $data['hash'] = $chk['hash'];
               $data['phone'] = $chk['phone'];
               $data['isdelete'] = '0';
               $data['IP'] = $this->request->data['ip'];
               $conditions = array('key' => $key);
               
               Apps::update($data,$conditions);

               $filed = ['isdelete' => '1'];
               $conditions = array('key' => $chk['key']);
               Apps::update($filed,$conditions);    
            }

             return $this->render(array('json' => array(
               'success'=>1,
               'result' => 'success',
               'Server'=>md5($_SERVER["SERVER_ADDR"]),
               'Refer'=>md5($_SERVER["REMOTE_ADDR"])
             )));


               // $conditions = array('hash' => $chk['hash'],'step.issubmit' => 'y');
               // $document = KYCDocuments::find('first',array('conditions'=>$conditions));               
           
               // if(count($document) == 0){
               //   return $this->render(array('json' => array('success'=>1,
               //     'now'=>time(),
               //     'result'=>'success'
               //   )));
               
               // }else{
               //   return $this->render(array('json' => array('success'=>1,
               //     'now'=>time(),
               //     'result'=>'Aleredy Your Email verify!'
               //   ))); 
               // }


         }else{
           // new Kyc
           /* **** */
               $emails = KYCDocuments::find('first',array(
                   'conditions'=>array('email'=>$email)
               ));
                 
                 $data = array(
                   'phone' => $mobile,
                   'email' => strtolower($email),
                   'hash'=>md5(strtolower($email)),
                   'isdelete'=>'0'
                 );
                 $conditions = array('key' => $key);
                 Apps::update($data,$conditions);

                 $ga = new GoogleAuthenticator();
                 ///////////////////////////////////Generate KYC ---------------------- and random KYC 
                 $secret1 = $ga->createSecret(64);
                 $secret2 = $ga->createSecret(64);
                 $secret3 = $ga->createSecret(64);
                 $kyc_id = $ga->getCode($secret1).'-'.$ga->getCode($secret2).'-'.$ga->getCode($secret3);  
                 
                 $email_code = substr($kyc_id,0,6);
                 $data = array(
                    'kyc_id'=>$kyc_id,
                    'secret.0' => $secret1,
                    'secret.1' => $secret2,
                    'secret.2' => $secret3,
                    'email'=>$email,
                    'phone' =>$mobile,
                    'details.Mobile' =>$mobile,
                    'hash'=>md5(strtolower($email)),
                    'IP'=>$_SERVER['REMOTE_ADDR']
                 );

                 $Documents = KYCDocuments::create($data);
                 $saved = $Documents->save();
                       
                 return $this->render(array('json' => array(
                   'success'=>1,
                   'result' => 'success',
                   'Server'=>md5($_SERVER["SERVER_ADDR"]),
                   'Refer'=>md5($_SERVER["REMOTE_ADDR"])
                 )));
           /* **** */
         }
     }else{
       return $this->render(array('json' => array('success'=>0,
         'now'=>time(),
         'error'=>'Invalid Email and Mobile!'
       ))); 
     }    
  }

  public function getStepStatus($key = null){
      if($key==null || $key==""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Key missing!'
          )));
      }else{  
      
        $record = Apps::find('first',array('conditions'=>array('key' => $key,'isdelete' =>'0')));
        if(count($record)!=0){
          // $status = ['basic'=>0,'address'=>0,'passport'=>0,'identity'=>0,'taxation'=>0,'drivinglicense'=>0,'holdimg'=>0];
          $kyc = KYCDocuments::find('first',array('conditions'=>array('hash' => $record['hash'])))->to('array'); 

            if(!array_key_exists("step",$kyc)) {
            
                $data = array(
                    'step.issubmit' =>'n'   
                );
                $conditions = array('hash' =>$record['hash']);
                KYCDocuments::update($data, $conditions);
                $step = ['issubmit' => 'n'];   
            }else{
                $step = $kyc['step'];
            }


          return $this->render(array('json' => array('success'=>1,
            'now'=>time(),
            'result'=>'Step Status',
            'step' => $step
          )));  
        }else{
          return $this->render(array('json' => array('success'=>2,
            'now'=>time(),
            'error'=>'Invalid Key!'
          ))); 
        } 
      }
  }

  public function getKycDocument($key=null){
     if($key==null || $key==""){
         return $this->render(array('json' => array('success'=>0,
           'now'=>time(),
           'error'=>'Key missing!'
         )));
     }
     else if($this->request->data['step']==null || $this->request->data['step']==""){
         return $this->render(array('json' => array('success'=>0,
           'now'=>time(),
           'error'=>'step name missing!'
         )));    
     }else{
        $conditions = array('key' => $key,'isdelete' =>'0');
        $record = Apps::find('first',array('conditions'=>$conditions));
      
        if(count($record)!=0){
             $kyc = KYCDocuments::find('first',array('conditions'=>array('hash' => $record['hash'])));
            
              if(count($kyc)!=0){
                 $step = $this->request->data['step'];
                 switch ($step) {
                    case 'basic':
                      $this->getKycBasic($key,$kyc);
                      break;
                    case 'address':
                      $this->getKycAddress($key,$kyc);
                      break;
                    case 'passport':
                      $this->getKycPassport($key,$kyc);
                      break;
                    case 'identity':
                      $this->getKycIdentity($key,$kyc);
                      break;
                    case 'taxation':
                      $this->getKycTaxation($key,$kyc);
                      break;
                    case 'drivinglicense':
                      $this->getKycDrivingLicense($key,$kyc);
                      break;
                    case 'holdimg':
                      $this->getHoldingImg($key,$kyc);
                      break;    
                    default:
                      return $this->render(array('json' => array('success'=>0,'now'=>time(),'error'=>'step name missing!'))); 
                      break;
                  }
              }else{
                 return $this->render(array('json' => array('success'=>0,
                 'now'=>time(),
                 'error'=>'not found kyc documents!'
                 ))); 
              } 
        }else{
           return $this->render(array('json' => array('success'=>2,
           'now'=>time(),
           'error'=>'Invalid Key!'
           ))); 
        }
     } 
  }

  private function getKycBasic($key,$kyc){
     $path = $this->getImage($kyc['hash'],'profile_img');
     return $this->render(array('json' => array('success'=>1,
       'now'=>time(),
       'result' => 'Basic Information',
       'Name'  => $kyc['details']['Name'],
       'Birth' => $kyc['details']['Birth'],
       'profile_img' =>$path,
       'key' => $key
     ))); 
  }

  private function getKycAddress($key,$kyc){
     $path = $this->getImage($kyc['hash'],'address_proof');
     return $this->render(array('json' => array('success'=>1,
       'now'=>time(),
       'result'  => 'Address Information',
       'Address' => $kyc['details']['Address'],
       'address_proof' => $path,
       'key'    => $key
     ))); 
  }

  private function getKycPassport($key,$kyc){
    $img = ['passport1' => '','passport2'=> '']; 
    foreach ($img as $img_key => $value) {
      $img[$img_key] = $this->getImage($kyc['hash'],$img_key);  
    }
  
    return $this->render(array('json' => array('success'=>1,
     'now'=>time(),
     'result'  => 'Passport Information',
     'Passport' => $kyc['details']['Passport'],
     'passport1' => $img['passport1'],
     'passport2' => $img['passport2'],
     'key'    => $key
    ))); 
  }

  private function getKycIdentity($key,$kyc){
   $img = ['identity1' => '','identity2'=> '']; 
   foreach ($img as $img_key => $value) {
      $img[$img_key] = $this->getImage($kyc['hash'],$img_key);  
   }
  
   return $this->render(array('json' => array('success'=>1,
     'now'=>time(),
     'result'  => 'Identity Information',
     'identity' => $kyc['details']['Identity'],
     'identity1' => $img['identity1'],
     'identity2' => $img['identity2'],
     'key'    => $key
   ))); 
  }

  private function getKycTaxation($key,$kyc){
    $path = $this->getImage($kyc['hash'],'tax1');  
    return $this->render(array('json' => array('success'=>1,
     'now'=>time(),
     'result'  => 'Tax Information',
     'Tax' => $kyc['details']['Tax'],
     'tax1' => $path,
     'key'    => $key
    ))); 
  }

  private function getKycDrivingLicense($key,$kyc){
   $path = $this->getImage($kyc['hash'],'drivinglicense1');  
   return $this->render(array('json' => array('success'=>1,
     'now'=>time(),
     'result'  => 'Driving Licence Information',
     'Driving' => $kyc['details']['Driving'],
     'drivinglicense1' => $path,
     'hash'    => $key
   ))); 
  }  

  private function getHoldingImg($key,$kyc){
    $path = $this->getImage($kyc['hash'],'hold_img');  
    return $this->render(array('json' => array('success'=>1,
     'now'=>time(),
     'result'  => 'Holding Image Information',
     'hold_img' => $path,
     'key'    => $key
    ))); 
  }

  private function getImage($id=null,$type=null){
    $document = KYCDocuments::find('first',array(
         'conditions'=>array(
             'hash'=>$id,
             )
    ));

    $image_name = KYCFiles::find('first',array(
        'conditions'=>array('details_'.$type.'_id'=>(string)$document['_id'])
    ));

    if($image_name['filename']!=""){
         $image_name_name = $image_name['_id'].'_'.$image_name['filename'];
         $path = LITHIUM_APP_PATH . '/webroot/documents/'.$image_name_name;
         $return_path = 'http://'.COMPANY_URL.'/documents/'.$image_name_name;
       //  $return_path = 'http://192.168.10.131:8888/hitarth.org/documents/'.$image_name_name;
         file_put_contents($path, $image_name->file->getBytes());
         return $return_path;
    }
  }

  // General Function Calling App Site
  public function saveKycDocument($key=null){
  
    if($key==null || $key==""){
        return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'Key missing!'
        )));
    }
    else if($this->request->data['step']==null || $this->request->data['step']==""){
        return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'step name missing!'
        )));    
    }
    else{
      $record = Apps::find('first',array(
          'conditions'=>array('key' => $key,'isdelete' =>'0')
        ));
       
      if(count($record)!=0){

         $kyc = KYCDocuments::find('first',array(
          'conditions'=>array('hash' => $record['hash'])
         ));

         $step = $this->request->data['step'];
         switch ($step) {
             case 'basic':
               $this->kycBasicInfo($key,$kyc);
               break;
             case 'address':
               $this->kycAddressInfo($key,$kyc);
               break;
             case 'passport':
               $this->kycPassportInfo($key,$kyc);
               break;
             case 'identity':
               $this->kycIdentityInfo($key,$kyc);
               break;
             case 'taxation':
               $this->kycTaxationInfo($key,$kyc);
               break;
             case 'drivinglicense':
               $this->kycDrivinglicenseInfo($key,$kyc);
               break;
             case 'holdimg':
               $this->kycHoldingImgInfo($key,$kyc);
               break;    
             default:
               return $this->render(array('json' => array('success'=>0,'now'=>time(),'error'=>'step name missing!'))); 
               break;
           }      
       }else{
          return $this->render(array('json' => array('success'=>2,
          'now'=>time(),
          'error'=>'Invalid Key!'
          ))); 
       }
    } 
  }

  // Step 1
  private function kycBasicInfo($key,$kyc){

      extract($this->request->data);  
      if($firstname==null || $firstname=="") {
         return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'First Name Required!'
         )));
      } 

      if($lastname==null || $lastname=="") {
         return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'Last Name Required!'
         )));
      } 

      if($dob==null || $dob=="") {
         return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'Date Of Birth Required!'
         )));
      }

      $arr = explode("-", $dob);
      $dd = $arr[2];
      $mm = $arr[1];
      $yyyy = $arr[0];

      if (!checkdate($mm, $dd, $yyyy)) {
          return $this->render(array('json' => array('success'=>0,
           'now'=>time(),
           'error'=>'Date of birth not valid yyyy-mm-dd'
          )));
      }

      $middlename = ($middlename == '') ? '' : $middlename;

      if(isset($this->request->data['file'])){
           
          foreach ($this->request->data['file'] as $img_key => $value) { 
          
              $status = $this->upload($kyc['hash'],$img_key,$type[$img_key]);

              if($status['upload'] == 0){
                 return $this->render(array('json' => array('success'=>0,
                  'now'=>time(),
                  'error'=>$status['msg']
                 ))); 
              }
          }
      }else{
        if(!$this->checkImageExits($kyc['hash'],'profile_img')){
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Profile Image Required!'
           )));
        }
      }
          $nameArray = array(
              'name' => $firstname.''.$lastname
            );

          $conditions = array('key' => $key);
          Apps::update($nameArray, $conditions);
      
          $basic = array(
              'first' => $firstname,
              'middle' =>$middlename,
              'last' =>$lastname
            );

          $birth = array(
              'date' => $dob
            );
          
          $data = array(
              'details.Name' => $basic,
              'details.Birth' => $birth,
              'step.basic.status' =>'completed'
          );

          $conditions = array('hash' => $kyc['hash']);
          KYCDocuments::update($data, $conditions);

          return $this->render(array('json' => array('success'=>1,
            'now'=>time(),
            'result'=>'Basic Information save success',
            'first' => $firstname,
            'middle' =>$middlename,
            'last' =>$lastname,
            'dob' => $dob,
            'key' => $key
          ))); 
  }

  //Step2
  private function kycAddressInfo($key,$kyc){
        
        if($this->request->data['address']==null || $this->request->data['address']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Address Required!'
           )));
        } 

        if($this->request->data['street']==null || $this->request->data['street']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Address Street Required!'
           )));
        } 

        if($this->request->data['city']==null || $this->request->data['city']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'City Required!'
           )));
        } 

        if($this->request->data['zip']==null || $this->request->data['zip']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Zip Required!'
           )));
        }

        if($this->request->data['state']==null || $this->request->data['state']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'State Required!'
           )));
        }

        if($this->request->data['country']==null || $this->request->data['country']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Country Required!'
           )));
        }
    
            extract($this->request->data);

            if(isset($this->request->data['file'])){
             
                foreach ($this->request->data['file'] as $img_key => $value) { 
                
                    $status = $this->upload($kyc['hash'],$img_key,$type[$img_key]);

                    if($status['upload'] == 0){
                       return $this->render(array('json' => array('success'=>0,
                        'now'=>time(),
                        'error'=>$status['msg']
                       ))); 
                    }
                }
            }else{
              if(!$this->checkImageExits($kyc['hash'],'address_proof')){
                 return $this->render(array('json' => array('success'=>0,
                  'now'=>time(),
                  'error'=>'Address Proof Required!'
                 )));
              }
            }

            $address_info = array(
                'address' => $address,
                'street' =>$street,
                'city' =>$city,
                'zip' =>$zip,
                'state' =>$state,
                'country' =>$country
              );

            $data = array(
                'details.Address' => $address_info,
                'step.address.status' =>'completed'
            );
            
            $conditions = array('hash' => $kyc['hash']);
            KYCDocuments::update($data, $conditions);

            return $this->render(array('json' => array('success'=>1,
              'now'=>time(),
              'result'=>'Address Information save success',
                'address' => $address,
                'street' =>$street,
                'city' =>$city,
                'zip' =>$zip,
                'state' =>$state,
                'country' =>$country,
                'key' => $key
            ))); 
  }

  //Step3
  private function kycPassportInfo($key,$kyc){
          
        if($this->request->data['firstname']==null || $this->request->data['firstname']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Passport First Name Required!'
           )));
        } 

        if($this->request->data['lastname']==null || $this->request->data['lastname']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Passport Last Name Required!'
           )));
        } 

        // if($this->request->data['middlename']==null || $this->request->data['middlename']=="") {
        //    return $this->render(array('json' => array('success'=>0,
        //     'now'=>time(),
        //     'error'=>'Passport Middel Name Required!'
        //    )));
        // }

        if($this->request->data['dob']==null || $this->request->data['dob']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Passport Date of Birth Required!'
           )));
        }

        $arr = explode("-", $this->request->data['dob']);
        $dd = $arr[2];
        $mm = $arr[1];
        $yyyy = $arr[0];

        if (!checkdate($mm, $dd, $yyyy)) {
            return $this->render(array('json' => array('success'=>0,
             'now'=>time(),
             'error'=>'Date of birth not valid yyyy-mm-dd'
            )));
        }

        if($this->request->data['address']==null || $this->request->data['address']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Address Required!'
           )));
        } 

        if($this->request->data['street']==null || $this->request->data['street']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Address Street Required!'
           )));
        } 

        if($this->request->data['city']==null || $this->request->data['city']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'City Required!'
           )));
        } 

        if($this->request->data['zip']==null || $this->request->data['zip']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Zip Required!'
           )));
        }

        if($this->request->data['state']==null || $this->request->data['state']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'State Required!'
           )));
        }

        if($this->request->data['country']==null || $this->request->data['country']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Country Required!'
           )));
        }

        if($this->request->data['no']==null || $this->request->data['no']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Passport Number Required!'
           )));
        }

        if($this->request->data['expiry']==null || $this->request->data['expiry']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Passport Expiry Required!'
           )));
        }

        $arr = explode("-", $this->request->data['expiry']);
        $dd = $arr[2];
        $mm = $arr[1];
        $yyyy = $arr[0];

        if (!checkdate($mm, $dd, $yyyy)) {
            return $this->render(array('json' => array('success'=>0,
             'now'=>time(),
             'error'=>'Passport expiry not valid yyyy-mm-dd'
            )));
        }

        if($this->request->data['pass_country']==null || $this->request->data['pass_country']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Passport Country Required!'
           )));
        }

    
        extract($this->request->data);
        $middlename = ($middlename == '') ? '' : $middlename;
            $img = ['passport1','passport2'];
            if(isset($this->request->data['file'])){
                
                $diff = array_values(array_diff($img, $type));
              
                if(count($diff) > 0){
                  if(!$this->checkImageExits($kyc['hash'],$diff[0])){
                     return $this->render(array('json' => array('success'=>0,
                      'now'=>time(),
                      'error'=>'Passport photos Required!'
                     )));
                  }
                } 

                foreach ($this->request->data['file'] as $img_key => $value) { 
                  if($this->request->data['file'][$img_key]['name'] != ''){
                        $status = $this->upload($kyc['hash'],$img_key,$type[$img_key]);
                        if($status['upload'] == 0){
                           return $this->render(array('json' => array('success'=>0,
                            'now'=>time(),
                            'error'=>$status['msg']
                           ))); 
                        }
                  }  
                }  
            }else{
              foreach ($img as $image) {
                if(!$this->checkImageExits($kyc['hash'],$image)){
                   return $this->render(array('json' => array('success'=>0,
                    'now'=>time(),
                    'error'=>'Passport photos Required!'
                   )));
                }
              }
            }



            $passport_info = array(
                'firstname' => $firstname,
                'middlename' => $middlename,
                'lastname' => $lastname,
                'dob' => $dob,
                'address' => $address,
                'street' =>$street,
                'city' =>$city,
                'zip' =>$zip,
                'state' =>$state,
                'country' =>$country,
                'no' =>$no,
                'expiry' =>$expiry,
                'pass_country' =>$pass_country
              );

           
            
            $data = array(
                'details.Passport' => $passport_info,
                'step.passport.status' =>'completed'
            );
            
            $conditions = array('hash' => $kyc['hash']);
            KYCDocuments::update($data, $conditions);

            return $this->render(array('json' => array('success'=>1,
              'now'=>time(),
              'result'=>'Passport Information save success',
                'firstname' => $firstname,
                'middlename' => $middlename,
                'lastname' => $lastname,
                'dob' => $dob,
                'address' => $address,
                'street' =>$street,
                'city' =>$city,
                'zip' =>$zip,
                'state' =>$state,
                'country' =>$country,
                'no' =>$no,
                'expiry' =>$expiry,
                'pass_country' =>$pass_country,
                'key' => $key
            ))); 
  }

  //Step4
  private function kycIdentityInfo($key,$kyc){
        if($this->request->data['firstname']==null || $this->request->data['firstname']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Identity First Name Required!'
           )));
        } 

        if($this->request->data['lastname']==null || $this->request->data['lastname']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Identity Last Name Required!'
           )));
        } 

        if($this->request->data['no']==null || $this->request->data['no']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Identity Number Required!'
           )));
        }

        if(!is_numeric($this->request->data['no'])) {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Identity Number Must Be Numeric!'
           )));
        }
          
        if(strlen($this->request->data['no']) != 12) {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Identity Number Length Must Be 12!'
           )));
        }
      

        extract($this->request->data);  
        $middlename = ($middlename == '') ? '' : $middlename;

            $img = ['identity1','identity2'];
            if(isset($this->request->data['file'])){
              
                $diff = array_values(array_diff($img, $type));
                if(count($diff) > 0){
                  if(!$this->checkImageExits($kyc['hash'],$diff[0])){
                    return $this->render(array('json' => array('success'=>0,
                      'now'=>time(),
                      'error'=>'Identity photos Required!'
                    )));  
                  }
                }  
              
                foreach ($this->request->data['file'] as $img_key => $value) { 
                    if($this->request->data['file'][$img_key]['name'] != ''){
                      $status = $this->upload($kyc['hash'],$img_key,$type[$img_key]);
                      if($status['upload'] == 0){
                         return $this->render(array('json' => array('success'=>0,
                          'now'=>time(),
                          'error'=>$status['msg']
                         ))); 
                      }
                    }  
                }
                
            }else{
              foreach ($img as $image) {
                if(!$this->checkImageExits($kyc['hash'],$image)){
                   return $this->render(array('json' => array('success'=>0,
                    'now'=>time(),
                    'error'=>'Identity photos Required!'
                   )));
                }
              }
            }



            $Identity_info = array(
                'firstname' => $firstname,
                'middlename' => $middlename,
                'lastname' => $lastname,
                'no' => $no,
            );

           
            
            $data = array(
                'details.Identity' => $Identity_info,
                'step.identity.status' =>'completed'
            );
            
            $conditions = array('hash' => $kyc['hash']);
            KYCDocuments::update($data, $conditions);

            return $this->render(array('json' => array('success'=>1,
              'now'=>time(),
              'result'=>'Identity Information save success',
                'firstname' => $firstname,
                'middlename' => $middlename,
                'lastname' => $lastname,
                'no' => $no,
                'key' => $key
            ))); 
  }

  //Step5
  private function kycTaxationInfo($key,$kyc){
        
        if($this->request->data['firstname']==null || $this->request->data['firstname']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Tax First Name Required!'
           )));
        } 

        // if($this->request->data['middlename']==null || $this->request->data['middlename']=="") {
        //    return $this->render(array('json' => array('success'=>0,
        //     'now'=>time(),
        //     'error'=>'Tax Middel Name Required!'
        //    )));
        // } 

        if($this->request->data['lastname']==null || $this->request->data['lastname']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Tax Last Name Required!'
           )));
        } 

        if($this->request->data['dateofbirth']==null || $this->request->data['dateofbirth']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Tax Date of Birth Required!'
           )));
        }

        $arr = explode("-", $this->request->data['dateofbirth']);
        $dd = $arr[2];
        $mm = $arr[1];
        $yyyy = $arr[0];

        if (!checkdate($mm, $dd, $yyyy)) {
            return $this->render(array('json' => array('success'=>0,
             'now'=>time(),
             'error'=>'Date of birth not valid yyyy-mm-dd'
            )));
        }

        if($this->request->data['id']==null || $this->request->data['id']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Tax Id Required!'
           )));
        }

        // if(strlen($this->request->data['id']) != 16) {
        //    return $this->render(array('json' => array('success'=>0,
        //     'now'=>time(),
        //     'error'=>'Tax id Length Must Be 16!'
        //    )));
        // }
      

        extract($this->request->data); 
        $middlename = ($middlename == '') ? '' : $middlename; 
        
            if(isset($this->request->data['file'])){
               
                foreach ($this->request->data['file'] as $img_key => $value) { 
                    if($this->request->data['file'][$img_key]['name'] != ''){
                      $status = $this->upload($kyc['hash'],$img_key,$type[$img_key]);
                      if($status['upload'] == 0){
                         return $this->render(array('json' => array('success'=>0,
                          'now'=>time(),
                          'error'=>$status['msg']
                         ))); 
                      }
                    }  
                }
            }else{
              if(!$this->checkImageExits($kyc['hash'],'tax1')){
                 return $this->render(array('json' => array('success'=>0,
                  'now'=>time(),
                  'error'=>'Tax Photos Required!'
                 )));
              }
            }

            $tax = array(
                'firstname' => $firstname,
                'middlename' => $middlename,
                'lastname' => $lastname,
                'dateofbirth'=> $dateofbirth,
                'id'=> $id
            );

          
            $data = array(
                'details.Tax' => $tax,
                'step.taxation.status' =>'completed'
            );
            
            $conditions = array('hash' => $kyc['hash']);
            KYCDocuments::update($data, $conditions);

            return $this->render(array('json' => array('success'=>1,
              'now'=>time(),
              'result'=>'Tax Information save success',
                'firstname' => $firstname,
                'middlename' => $middlename,
                'lastname' => $lastname,
                'dateofbirth'=> $dateofbirth,
                'id'=> $id,
                'key' => $key
            ))); 
  }

  //Step6
  private function kycDrivinglicenseInfo($key,$kyc){
          
        if($this->request->data['firstname']==null || $this->request->data['firstname']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'license First Name Required!'
           )));
        } 

        if($this->request->data['lastname']==null || $this->request->data['lastname']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'license Last Name Required!'
           )));
        } 

        // if($this->request->data['middlename']==null || $this->request->data['middlename']=="") {
        //    return $this->render(array('json' => array('success'=>0,
        //     'now'=>time(),
        //     'error'=>'license Middel Name Required!'
        //    )));
        // }

        if($this->request->data['dob']==null || $this->request->data['dob']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'license Date of Birth Required!'
           )));
        }

        $arr = explode("-", $this->request->data['dob']);
        $dd = $arr[2];
        $mm = $arr[1];
        $yyyy = $arr[0];

        if (!checkdate($mm, $dd, $yyyy)) {
            return $this->render(array('json' => array('success'=>0,
             'now'=>time(),
             'error'=>'Date of birth not valid yyyy-mm-dd'
            )));
        }


        if($this->request->data['address']==null || $this->request->data['address']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Address Required!'
           )));
        } 

        if($this->request->data['street']==null || $this->request->data['street']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Address Street Required!'
           )));
        } 

        if($this->request->data['city']==null || $this->request->data['city']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'City Required!'
           )));
        } 

        if($this->request->data['zip']==null || $this->request->data['zip']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Zip Required!'
           )));
        }

        if($this->request->data['state']==null || $this->request->data['state']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'State Required!'
           )));
        }

        if($this->request->data['country']==null || $this->request->data['country']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Country Required!'
           )));
        }

        if($this->request->data['no']==null || $this->request->data['no']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'license Number Required!'
           )));
        }

        if($this->request->data['expiry']==null || $this->request->data['expiry']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'license Expiry Required!'
           )));
        }

        $arr = explode("-", $this->request->data['expiry']);
        $dd = $arr[2];
        $mm = $arr[1];
        $yyyy = $arr[0];

        if (!checkdate($mm, $dd, $yyyy)) {
            return $this->render(array('json' => array('success'=>0,
             'now'=>time(),
             'error'=>'license expiry not valid yyyy-mm-dd'
            )));
        }

        if($this->request->data['license_country']==null || $this->request->data['license_country']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'license Country Required!'
           )));
        }

    
            extract($this->request->data);
             $middlename = ($middlename == '') ? '' : $middlename;

            if(isset($this->request->data['file'])){
             
                foreach ($this->request->data['file'] as $img_key => $value) { 
                    if($this->request->data['file'][$img_key]['name'] != ''){
                      $status = $this->upload($kyc['hash'],$img_key,$type[$img_key]);
                      if($status['upload'] == 0){
                         return $this->render(array('json' => array('success'=>0,
                          'now'=>time(),
                          'error'=>$status['msg']
                         ))); 
                      }
                    }  
                }
            }else{
              if(!$this->checkImageExits($kyc['hash'],'drivinglicense1')){
                 return $this->render(array('json' => array('success'=>0,
                  'now'=>time(),
                  'error'=>'Driving license Photos Required!'
                 )));
              }
            }



            $license = array(
                'firstname' => $firstname,
                'middlename' => $middlename,
                'lastname' => $lastname,
                'dob' => $dob,
                'address' => $address,
                'street' =>$street,
                'city' =>$city,
                'zip' =>$zip,
                'state' =>$state,
                'country' =>$country,
                'no' =>$no,
                'expiry' =>$expiry,
                'license_country' =>$license_country
              );

           
            
            $data = array(
                'details.Driving' => $license,
                'step.drivinglicense.status' =>'completed'
            );
            
            $conditions = array('hash' => $kyc['hash']);
            KYCDocuments::update($data, $conditions);

            return $this->render(array('json' => array('success'=>1,
              'now'=>time(),
              'result'=>'Driving license Information save success',
                'firstname' => $address,
                'middlename' => $address,
                'lastname' => $address,
                'dob' => $dob,
                'address' => $address,
                'street' =>$street,
                'city' =>$city,
                'zip' =>$zip,
                'state' =>$state,
                'country' =>$country,
                'no' =>$no,
                'expiry' =>$expiry,
                'license_country' =>$license_country,
                'key' => $key
            ))); 
  }

  //Step7
  private function kycHoldingImgInfo($key,$kyc){
      extract($this->request->data);

        if(isset($this->request->data['file'])){
         
          foreach ($this->request->data['file'] as $img_key => $value) { 
              if($this->request->data['file'][$img_key]['name'] != ''){
                $status = $this->upload($kyc['hash'],$img_key,$type[$img_key]);
                if($status['upload'] == 0){
                   return $this->render(array('json' => array('success'=>0,
                    'now'=>time(),
                    'error'=>$status['msg']
                   ))); 
                }else{
                  $data = array(
                      'step.holdimg.status' =>'completed'
                  );
                  
                  $conditions = array('hash' => $kyc['hash']);
                  KYCDocuments::update($data, $conditions);  

                }
              }  
          }
      
          return $this->render(array('json' => array('success'=>1,
            'now'=>time(),
            'result'=>'Holding Image save success',
            'key' => $key
          ))); 

        }
        else{
              if(!$this->checkImageExits($kyc['hash'],'hold_img')){
                 return $this->render(array('json' => array('success'=>0,
                  'now'=>time(),
                  'error'=>'Holding Photos Required!'
                 )));
              }else{
                 return $this->render(array('json' => array('success'=>1,
                  'now'=>time(),
                  'result'=>'Holding Photos Aleredy uploaded!'
                 )));
              }
        }  
  }

  // final kyc submit
  public function submitKycDocument($key=null){
        if($key==null || $key==""){
            return $this->render(array('json' => array('success'=>0,
                'now'=>time(),
                'error'=>'Key missing!'
            )));
        }
        else{
            $record = Apps::find('first',array('conditions'=>array('key' => $key,'isdelete' =>'0'))); 
            if(count($record)!=0){

              $kyc = KYCDocuments::find('first',array('conditions'=>array('hash' => $record['hash'])));
              if(count($kyc) !=0){

                $basic = $kyc['step']['basic']['status'];
                $address = $kyc['step']['address']['status'];
                $Identity = $kyc['step']['identity']['status'];
                $taxation = $kyc['step']['taxation']['status'];
                $holdimg = $kyc['step']['holdimg']['status'];
                
                $passport = $kyc['step']['passport']['status'];
                $drivinglicense  = $kyc['step']['drivinglicense']['status'];


                if($basic == ''|| $address == '' || $Identity == ''|| $taxation == '' || $holdimg == '')
                {
                    return $this->render(array('json' => array('success'=>0,
                      'now'=>time(),
                      'error'=>'Kyc Incompleted !'
                    )));

                }else if($basic == 'process' ||  $address == 'process' ||  $Identity == 'process' || $taxation == 'process' || $holdimg == 'process')
                {
                      return $this->render(array('json' => array('success'=>0,
                        'now'=>time(),
                        'error'=>'Your Kyc Verification Procces in EvolveChain.'
                      )));

                }else if($basic == 'reject'|| $address == 'reject' || $Identity == 'reject' || $taxation == 'reject' || $holdimg == 'reject')
                {
                    return $this->render(array('json' => array('success'=>0,
                      'now'=>time(),
                      'error'=>'Please Edit Your Rejected Document!'
                    )));

                }else if($basic == 'incompleted'|| $address == 'incompleted' || $Identity == 'incompleted' || $taxation == 'incompleted' || $holdimg == 'incompleted')
                {
                    return $this->render(array('json' => array('success'=>0,
                      'now'=>time(),
                      'error'=>'Please Edit Your Incompleted Document!'
                    )));

                }else if($basic == 'approved' && $address == 'approved' &&  $Identity == 'approved' && $taxation == 'approved' && $holdimg == 'approved'){

                    return $this->render(array('json' => array('success'=>0,
                      'now'=>time(),
                      'error'=>'Your Kyc Document Aleredy Approved.'
                    )));  

                }


                  ////////////////////////////////////////Send Email
                  
                  $emaildata = array(
                   'kyc_id'=>$kyc['kyc_id'],
                   'email'=>$record['email'],
                   'hash'=>$record['hash']
                  );
                  $function = new Functions();
                  $compact = array('data'=>$emaildata);
                  $from = array(NOREPLY => "noreply@".COMPANY_URL);
                  $email = MAIL_4;
                  $function->sendEmailTo($email,$compact,'process','submitKYC',"KYC - New From Submit",$from,MAIL_1,MAIL_2,MAIL_3,null);

                  /////////////////////////////////////////

                  $data = [];
                  foreach ($kyc['step'] as $k => $val){
                    if($k == 'issubmit')
                         $data['step.'.$k] = 'y';
                    else{
                      foreach ($val as $sk => $v) {
                        if($sk == 'status' && $v == 'completed'){
                            $data['step.'.$k.'.'.$sk] = 'process';
                            $data['step.'.$k.'.message'] = 'In process';
                        }    
                      }
                    }      
                  }

                  if(count($data) > 0){
                    // $data = array('step.issubmit' =>'y');
                    $conditions = array('hash' => $kyc['hash']);
                    KYCDocuments::update($data, $conditions); 
                  }

                  $data = array(
                    'kyc_id'=>$kyc_id,
                    'email'=>$email,
                    'phone' =>$mobile,
                    'details.Mobile' =>$mobile,
                    'hash'=>md5(strtolower($email)),
                    'IP'=>$_SERVER['REMOTE_ADDR']
                  );

                 $Documents = Wallets::create($data);
                 $saved = $Documents->save();









                  return $this->render(array('json' => array('success'=>1,
                     'now'=>time(),
                     'result'=>'Submit success!'
                   ))); 
                   
              }else{
                return $this->render(array('json' => array('success'=>0,
                  'now'=>time(),
                  'error'=>'Documents not found!'
                ))); 
              }
              

            }else{
              return $this->render(array('json' => array('success'=>2,
                'now'=>time(),
                'error'=>'Invalid Key!'
              ))); 
            }
        }
  }

  // Flushing Kyc Document Image
  public function removeImage($key=null){
      if($key==null || $key==""){
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Key missing!'
       )));
      }

      if($this->request->data['step']==null || $this->request->data['step']==""){
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'step key required!'
       )));
      } 


      if($this->request->data['type']==null || $this->request->data['type']==""){
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Image type required!'
       )));
      }  


      $record = Apps::find('first',array('conditions'=>array('key' => $key,'isdelete' =>'0')));
        if(count($record)!=0){
          
          $document = KYCDocuments::find('first',array('conditions'=>array('hash'=>$record['hash'])));
          if(count($document) != 0){ 
              
              $step = $this->request->data['step'];
              $type = $this->request->data['type'];
              
              $field = 'details_'.$type.'_id';
              $remove = KYCFiles::remove('all',array(
                  'conditions'=>array( $field => (string)$document['_id'])
              ));

              $data = array('step.'.$step.'.status' => 'incompleted');  
              $conditions = array('hash' => $record['hash']);
              KYCDocuments::update($data,$conditions); 
              
              return $this->render(array('json' => array('success'=>1,
                'now'=>time(),
                'result'=>'Delete success',
                'key' => $key
              )));

           }else{
              return $this->render(array('json' => array('success'=>0,
              'now'=>time(),
              'error'=>'Document not found!',
            )));
           }   

        }else{
          return $this->render(array('json' => array('success'=>2,
            'now'=>time(),
            'error'=>'Invalid Key!',
          )));
        }  



      // $document = KYCDocuments::find('first',array(
      //     'conditions'=>array(
      //         'hash'=>$id,
      //         )
      // ));  

      //  $image_name = KYCFiles::find('first',array(
      //     'conditions'=>array('details_'.$type.'_id'=>(string)$document['_id'])
      // ));

      // if($image_name['filename']!=""){
      //         $image_name_name = $image_name['_id'].'_'.$image_name['filename'];
      //            $path = LITHIUM_APP_PATH . '/webroot/documents/'.$image_name_name;
      //            unlink($path);
      //            return true;
      // }
  }

  // free storage Space
  public function unlinkKycImg($key=null){
      if($key==null || $key==""){
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Key missing!'
       )));
      }  


      $record = Apps::find('first',array('conditions'=>array('key' => $key,'isdelete' =>'0')));
        if(count($record)!=0){
          $kycImage = $this->request->data['type'];

          foreach ($kycImage as $type) {
            $document = KYCDocuments::find('first',array('conditions'=>array('hash'=>$record['hash'])));  
            $image = KYCFiles::find('first',array(
                'conditions'=>array('details_'.$type.'_id'=>(string)$document['_id'])
            ));

            if($image['filename']!=""){
                    $image_name = $image['_id'].'_'.$image['filename'];
                    $path = LITHIUM_APP_PATH . '/webroot/documents/'.$image_name;
                    unlink($path);
            }
          }

          return $this->render(array('json' => array('success'=>1,
            'now'=>time(),
            'result'=>'Unlink success',
            'key' => $key
          ))); 
        }else{
          return $this->render(array('json' => array('success'=>2,
            'now'=>time(),
            'error'=>'Invalid Key!',
          )));
        }   
  }
 
  /* Common Function */
  private function smart_resize_image($file,$string= null,$width= 0,$height= 0,$proportional= false,$output= 'file',$delete_original= true,$use_linux_commands = false,$quality = 100) {
          if ( $height <= 0 && $width <= 0 ) return false;
          if ( $file === null && $string === null ) return false;
              # Setting defaults and meta
          $info   = $file !== null ? getimagesize($file) : sendsizefromstring($string);
          $image  = '';
          $final_width = 0;
          $final_height = 0;
          // print_r($info);
          if($info==null){
            return false;
          }
            
          list($width_old, $height_old) = $info;
          $cropHeight = $cropWidth = 0;

            # Calculating proportionality
            if ($proportional) {
              if      ($width  == 0)  $factor = $height/$height_old;
              elseif  ($height == 0)  $factor = $width/$width_old;
              else                    $factor = min( $width / $width_old, $height / $height_old );

              $final_width  = round( $width_old * $factor );
              $final_height = round( $height_old * $factor );
            }
            else {
              $final_width = ( $width <= 0 ) ? $width_old : $width;
              $final_height = ( $height <= 0 ) ? $height_old : $height;
              $widthX = $width_old / $width;
              $heightX = $height_old / $height;
              
              $x = min($widthX, $heightX);
              $cropWidth = ($width_old - $width * $x) / 2;
              $cropHeight = ($height_old - $height * $x) / 2;
            }
            # Loading image to memory according to type
            switch ( $info[2] ) {
              case IMAGETYPE_JPEG:  $file !== null ? $image = imagecreatefromjpeg($file) : $image = imagecreatefromstring($string);  break;
              case IMAGETYPE_GIF:   $file !== null ? $image = imagecreatefromgif($file)  : $image = imagecreatefromstring($string);  break;
              case IMAGETYPE_PNG:   $file !== null ? $image = imagecreatefrompng($file)  : $image = imagecreatefromstring($string);  break;
              default: return false;
            }
                
            
            # This is the resizing/resampling/transparency-preserving magic
            $image_resized = imagecreatetruecolor( $final_width, $final_height );
            if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
              $transparency = imagecolortransparent($image);
              $palletsize = imagecolorstotal($image);

              if ($transparency >= 0 && $transparency < $palletsize) {
                $transparent_color  = imagecolorsforindex($image, $transparency);
                $transparency       = imagecolorallocate($image_resized, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                imagefill($image_resized, 0, 0, $transparency);
                imagecolortransparent($image_resized, $transparency);
              }
              elseif ($info[2] == IMAGETYPE_PNG) {
                imagealphablending($image_resized, false);
                $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
                imagefill($image_resized, 0, 0, $color);
                imagesavealpha($image_resized, true);
              }
            }
            imagecopyresampled($image_resized, $image, 0, 0, $cropWidth, $cropHeight, $final_width, $final_height, $width_old - 2 * $cropWidth, $height_old - 2 * $cropHeight);
            
            
            # Taking care of original, if needed
            if ( $delete_original ) {
              if ( $use_linux_commands ) exec('rm '.$file);
              else @unlink($file);
            }

            # Preparing a method of providing result
            switch ( strtolower($output) ) {
              case 'browser':
                $mime = image_type_to_mime_type($info[2]);
                header("Content-type: $mime");
                $output = NULL;
              break;
              case 'file':
                $output = $file;
              break;
              case 'return':
                return $image_resized;
              break;
              default:
              break;
            }
            
            # Writing image according to type to the output destination and image quality
            switch ( $info[2] ) {
              case IMAGETYPE_GIF:   imagegif($image_resized, $output);    break;
              case IMAGETYPE_JPEG:  imagejpeg($image_resized, $output, $quality);   break;
              case IMAGETYPE_PNG:
                $quality = 9 - (int)((0.9*$quality)/10.0);
                imagepng($image_resized, $output, $quality);
                break;
              default: return false;
            }

            return true;
  }

  /* Common Function */
  private function checkImageExits($id=null,$type=null){
       $document = KYCDocuments::find('first',array('conditions'=>array('hash'=>$id)));



       $image_name = KYCFiles::find('first',array('conditions'=>array('details_'.$type.'_id'=>(string)$document['_id'])));
       if($image_name['filename']!=""){
           return true;
       }else{
           return false;
       }    
  }

  /* Common Function  */
  private function upload($id=null,$img_key=0,$type="passport"){
          $response = [];
          $uploadOk = 1;

          $document = KYCDocuments::find('first',array(
              'conditions'=>array(
                  'hash'=>$id,
                  )
          ));

          // $countries = Countries::find('all',array(
          //     'order'=>array('order'=>1)
          // ));
          
          if(count($document)==0){
              $response['msg'] = "Documents not found";
              $response['upload'] = 0;
              $uploadOk = 0;
          }

          if ($this->request->data && $uploadOk == 1) {
              $extension = pathinfo($this->request->data['file'][$img_key]['name'],PATHINFO_EXTENSION);
            
              $allowed = array('jpg', 'jpeg', 'png', 'gif');
              if(!in_array(strtolower($extension), $allowed)){
                      $response['msg'] = "Sorry, only JPG, PNG, GIF file is allowed.";
                      $response['upload'] = 0;
                      $uploadOk = 0;
              }
              if(strtolower($extension)=='pdf'){
                      $response['msg'] = "Please do not upload PDF file.";
                      $response['upload'] = 0;
                      $uploadOk = 0;
              }
              $size = round($this->request->data['file'][$img_key]['size']/1024/1024,2)            ;
              if($size >= 10){
                      $response['msg'] = "Sorry, File too large, should be less than 10 MB. It is ".$size."MB!";
                      $response['upload'] = 0;
                      $uploadOk = 0;
              }
              if($uploadOk==1){ 

                   //$option = $this->request->data['option'];
                  $option = $type;
              
                  $data = array(
                      $option => $this->request->data['file'][$img_key],
                      $option.'.verified'=>'No',
                      $option.'.IP'=>$_SERVER['REMOTE_ADDR'],
                  );

                  $field = 'details_'.$type.'_id';
                  $remove = KYCFiles::remove('all',array(
                      'conditions'=>array( $field => (string)$document['_id'])
                  ));
                  $file = $this->request->data['file'];
                  
                  $path = LITHIUM_APP_PATH. '/webroot/documents/';
                  
                  $resizedFile = $path.$this->request->data['file'][$img_key]['name'];
                  
                  $resize = $this->smart_resize_image($this->request->data['file'][$img_key]['tmp_name'], null,1024 , 0 , true , $resizedFile , false , false ,100 );

                  if($resize == false){
                      $msg = "File format different, cannot verify.";
                      $uploadOk = 0;
                  }

                  
                  
                  $fileData = array(
                          'file' => file_get_contents($resizedFile),
                          'filename'=>$this->request->data['file'][$img_key]['name'],
                          'metadata'=>array('filename'=>$this->request->data['file'][$img_key]['name']),
                          'details_'.$type.'_id' => (string)$document['_id']
                  );
                  
                  KYCDocuments::find('first',
                       array('conditions'=>array('_id'=> (string)$document['_id']))
                   )->save($data);

                  $file = KYCFiles::create();
                  if ($file->save($fileData)) {
                      $response['msg'] = "Upload OK";
                      $response['upload'] = 1; 
                  }
                  unlink($resizedFile);
              }            
          }

          return $response;

          /* Use Image Upload */
          // $this->getImage($id,$type);
          // $this->removeImage($id,$type);
        


          // if(($document['details']['Address']['country']=='IND')){
          //     $image_identity = KYCFiles::find('first',array(
          //         'conditions'=>array('details_identity_id'=>(string)$document['_id'])
          //     ));
          //     if($image_identity['filename']!=""){
          //             $imagename_identity = $image_identity['_id'].'_'.$image_identity['filename'];
          //                 $path = LITHIUM_APP_PATH . '/webroot/documents/'.$imagename_identity;
          //             file_put_contents($path, $image_identity->file->getBytes());
          //     }
          // }else{
          //     $image_identity = "";
          //     $imagename_identity = "";
          // }
  }

  /*
    // This Api Only Use for App Developer Bcz Local server mail not get so Kyc id missing
  */
  public function getkycidDeveloperUse($key=null){

    if($key==null || $key==""){
        return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'Key missing!'
        )));
    }else{
       $conditions = array('key' => $key);
       $record = Apps::find('first',array('conditions'=>$conditions));
     
       if(count($record)!=0){
            $kyc = kycDocuments::find('first',array('conditions'=>array('hash' => $record['hash'])));
            
            return $this->render(array('json' => array('success'=>1,
                'now'=>time(),
                'result' =>'Kyc info',
                'kyc' => $kyc,
               )));
             
       }else{
          return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'Invalid Key!'
          ))); 
       }
    } 
  }

  public function generateRandomKYC($key=null){
      // echo "<pre>";
      // print_r($this->request->data);
      extract($this->request->data);
      if ($key==null || $key == ""){
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Key missing!'
        )));
      }

      if ($companyname==null || $companyname == ""){
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Company name missing!'
        )));
      }

      if ($comment==null || $comment == ""){
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Comment missing!'
        )));
      }

      if (count($sharedoc) == 0){
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Please select any document!'
        )));
      }

      $appRecord = Apps::find('first',array(
         'conditions' => array(
           'key'=>$key,'isdelete' =>'0'
           )
         )
       );
     
       if(count($appRecord) > 0){
          // $conditions = array('key' => $key);
          // $appRecord = Apps::find('first',array(
          //  'conditions'=>$conditions
          // ));
          $conditions = array('hash' => $appRecord['hash']);
          $documentRecord = KYCDocuments::find('first',array(
            'conditions'=>$conditions
          ));
           $ga = new GoogleAuthenticator();

           $new_KYC = $ga->getCode($documentRecord['secret'][0]).'-'.$ga->getCode($documentRecord['secret'][1]).'-'.$ga->getCode($documentRecord['secret'][2]);
           
           $code = $ga->createSecret(64); 
             $data = array(
                'code' => $code,
                'hash' => $documentRecord['hash'],
                'companyname' => $companyname,
                'comment' => $comment,
                'sharedoc' => $sharedoc,
                'new_KYC' => $new_KYC,
                'status' => 'shared',
                'DateTime' => new \MongoDate(), 
              );
             KYCShares::create()->save($data);

            return $this->render(array('json' => array('success'=>1,
              'now'=>time(),
//              'secret1'=>$documentRecord['secret'][0],
//              'secret2'=>$documentRecord['secret'][1],
//              'secret3'=>$documentRecord['secret'][2],
              'new_KYC'=> $new_KYC,
              'code' => $code,
            )));

       }else{
         return $this->render(array('json' => array('success'=>2,
            'now'=>time(),
            'error'=>'Invalid Key!'
          ))); 
       }
  }

  public function revokeDocument($key=null){
      // echo "<pre>";
      // print_r($this->request->data);
      extract($this->request->data);
      if ($key==null || $key == ""){
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Key missing!'
        )));
      }

      if ($code==null || $code == ""){
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Code missing!'
        )));
      }

      $appRecord = Apps::find('first',array(
         'conditions' => array(
           'key'=>$key,'isdelete' =>'0'
           )
         )
       );
     
       if(count($appRecord) > 0){
          $conditions = array('hash' => $appRecord['hash'],'code' => $code);
          $data = array(
            'status' => 'revoke',
            'DateTime' => new \MongoDate(), 
          );
          KYCShares::update($data,$conditions);

            return $this->render(array('json' => array('success'=>1,
              'now'=>time(),
              'result'=>'Success revoke document.',
            )));

       }else{
         return $this->render(array('json' => array('success'=>2,
            'now'=>time(),
            'error'=>'Invalid Key!'
          ))); 
       }
  }

  public function getShareHistory($key=null){
    extract($this->request->data);
      if($key==null || $key == ""){
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Key missing!'
        )));
      }

      $appRecord = Apps::find('first',array(
         'conditions' => array(
           'key'=>$key,'isdelete' =>'0'
           )
         )
       );
            
       if(count($appRecord) > 0){
            $history = KYCShares::find('all', [
                         'conditions' => array(
                              'hash' => $appRecord['hash'],
                              // 'status' => [
                              //   '$in' => ['shared', 'used']
                              // ]
                         ),
                         'fields' => ['companyname', 'comment', 'code', 'sharedoc', 'new_KYC', 'status', 'DateTime'],
                         'order' => ['DateTime' => 'DESC'],
                         // 'page' => 1,
                         // 'limit' => 10   
                       ])->data();

             return $this->render(array('json' => array('success'=>1,
               'now'=>time(),
               'result' =>'KYC Share history list',
               'history' => array_values($history)
             )));

       }else{
         return $this->render(array('json' => array('success'=>2,
            'now'=>time(),
            'error'=>'Invalid Key!'
          ))); 
       }
  }

}?>