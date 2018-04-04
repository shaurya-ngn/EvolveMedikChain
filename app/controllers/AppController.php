<?php
namespace app\controllers;
use app\models\Wallets;
use app\models\Apps;
use app\models\KYCDocuments;
use app\models\KYCFiles;
use app\models\KYCShares;

use app\models\Countries;
use app\models\Templates;
use MongoID;	
use app\extensions\action\GoogleAuthenticator;
use app\extensions\action\OP_Return;
use lithium\util\Validator;
use app\extensions\action\Functions;
use app\extensions\action\EvolveChain;

class AppController extends \lithium\action\Controller {
	
  public function index($ip = null){
   if($this->request->data){
   if($this->request->data['ip']==null || $this->request->data['ip']==""){
    return $this->render(array('json' => array('success'=>0,
     'now'=>time(),
     'error'=>'IP missing!'
    )));
   }
   if($this->request->data['device_type']==null || $this->request->data['device_type']==""){
    return $this->render(array('json' => array('success'=>0,
     'now'=>time(),
     'error'=>'Device Type missing!'
    )));
   }
   if($this->request->data['device_name']==null || $this->request->data['device_name']==""){
    return $this->render(array('json' => array('success'=>0,
     'now'=>time(),
     'error'=>'Device Name missing!'
    )));
   }
   if($this->request->data['os_version']==null || $this->request->data['os_version']==""){
    return $this->render(array('json' => array('success'=>0,
     'now'=>time(),
     'error'=>'OS Version missing!'
    )));
   }    
   if($this->request->data['vendor_uuid']==null || $this->request->data['vendor_uuid']==""){
    return $this->render(array('json' => array('success'=>0,
     'now'=>time(),
     'error'=>'Vendor uuid missing!'
    )));
   }    
   $key = md5(time());
   $data = array(
     'device_type'=>$this->request->data['device_type'],
     'os_version'=>$this->request->data['os_version'],
     'device_name'=>$this->request->data['device_name'],
     'vendor_uuid'=>$this->request->data['vendor_uuid'],
     'DateTime' => new \MongoDate(),
     'IP' => $this->request->data['ip'],
     'key' => $key,
   'Server'=>md5($_SERVER["SERVER_ADDR"]),
   'Refer'=>md5($_SERVER["REMOTE_ADDR"])
   );
   $App = Apps::create()->save($data);
   return $this->render(array('json' => array('success'=>1,
    'now'=>time(),
    'key'=>$key,
    'ip'=>$this->request->data['ip'],
    'Server'=>md5($_SERVER["SERVER_ADDR"]),
    'Refer'=>md5($_SERVER["REMOTE_ADDR"])
   )));
    }else{
     return $this->render(array('json' => array('success'=>0,
     'now'=>time(),
     'error'=>'IP missing!'
    )));
    }
	}

  public function initialize($ip = null,$device_type=null,$os_version=null,$device_name=null){
  
     if($this->request->data){
      if($this->request->data['ip']==null || $this->request->data['ip']==""){
       return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'IP missing!'
       )));
      }
      if($this->request->data['device_type']==null || $this->request->data['device_type']==""){
       return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Device Type missing!'
       )));
      }
      if($this->request->data['device_name']==null || $this->request->data['device_name']==""){
       return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Device Name missing!'
       )));
      }
      if($this->request->data['os_version']==null || $this->request->data['os_version']==""){
       return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'OS Version missing!'
       )));
      }    
      if($this->request->data['vendor_uuid']==null || $this->request->data['vendor_uuid']==""){
       return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Vendor uuid missing!'
       )));
      }    
      $key = md5(time());
      $data = array(
        'device_type'=>$this->request->data['device_type'],
        'os_version'=>$this->request->data['os_version'],
        'device_name'=>$this->request->data['device_name'],
        'vendor_uuid'=>$this->request->data['vendor_uuid'],
        'DateTime' => new \MongoDate(),
        'IP' => $this->request->data['ip'],
        'key' => $key,
        'isdelete' => '0',
    		'Server'=>md5($_SERVER["SERVER_ADDR"]),
    		'Refer'=>md5($_SERVER["REMOTE_ADDR"])
      );
      $App = Apps::create()->save($data);
      return $this->render(array('json' => array('success'=>1,
       'now'=>time(),
       'key'=>$key,
       'ip'=>$this->request->data['ip'],
       'Server'=>md5($_SERVER["SERVER_ADDR"]),
       'Refer'=>md5($_SERVER["REMOTE_ADDR"])
      )));
     }else{
      return $this->render(array('json' => array('success'=>0,
      'now'=>time(),
      'error'=>'IP missing!'
     )));
     }
  }

  public function setpin($key=null){
    if ($key==null || $key == ""){
      return $this->render(array('json' => array('success'=>0,
      'now'=>time(),
      'error'=>'Key missing!'
     )));
    }
    
    if($this->request->data){
      if($this->request->data['pin']==null || $this->request->data['pin']==""){
       return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Pin missing!'
       )));
      }
     
     $data = array(
       'pin' => $this->request->data['pin']
       );
     $conditions = array(
       'key' => $key 
     );
     
     Apps::update($data,$conditions);
      return $this->render(array('json' => array('success'=>1,
       'now'=>time(),
       'key'=>$key,
       'pin'=>$this->request->data['pin'],
       'Server'=>md5($_SERVER["SERVER_ADDR"]),
       'Refer'=>md5($_SERVER["REMOTE_ADDR"])
      )));
    }else{
      return $this->render(array('json' => array('success'=>0,
      'now'=>time(),
      'error'=>'Pin missing!'
     )));
    }
  }

  public function checkpin($key=null){
    if ($key==null || $key == ""){
      return $this->render(array('json' => array('success'=>0,
      'now'=>time(),
      'error'=>'Key missing!'
     )));
    }
     $conditions = array(
       'key' => $key 
     );
     
     $record = Apps::find('first',array(
      'conditions'=>$conditions
     ));
     if(count($record)!=0){
      return $this->render(array('json' => array('success'=>1,
       'now'=>time(),
       'key'=>$key,
       'pin'=>$record['pin'],
       'Server'=>md5($_SERVER["SERVER_ADDR"]),
       'Refer'=>md5($_SERVER["REMOTE_ADDR"])
      )));
     }else{
      return $this->render(array('json' => array('success'=>0,
      'now'=>time(),
      'error'=>'Invalid Key!'
     )));    
     }
  }

  public function changepin($key = null){
    if ($key==null || $key == ""){
      return $this->render(array('json' => array('success'=>0,
      'now'=>time(),
      'error'=>'Key missing!'
     )));
    }
    if($this->request->data){
        if($this->request->data['pin']==null || $this->request->data['pin']==""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Pin missing!'
          )));
        }
          if($this->request->data['new_pin']==null || $this->request->data['new_pin']==""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'New Pin missing!'
          )));
        }

        if(strlen($this->request->data['new_pin']) != 4){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'New pin must be 4 number!'
          )));
          }

        if($this->request->data['pin'] == $this->request->data['new_pin']){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'New Pin same as current pin'
          )));
          }
        
          $record = Apps::find('first',array(
            'conditions' => array(
              'key'=>$key,
              'pin'=>$this->request->data['pin'] )
          )
        );
        if(count($record) > 0){
          $data = array('pin' => $this->request->data['new_pin']);
          $conditions =  array(
            'key'=>$key,
            'pin'=>$this->request->data['pin'] 
          );
          Apps::update($data, $conditions);
          return $this->render(array('json' => array('success'=>1,
            'now'=>time(),
            'result'=>'New Pin updated',
            'pin'=> $this->request->data['new_pin']
          )));
        }
        else{ 
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Old Pin does not match',
          )));
        }
    }
  }

  public function login($key = null){
      extract($this->request->data);
      if($key==null || $key==""){
        return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'Key missing!'
        )));
      }

      if($kycid==null || $kycid==""){
        return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'Kyc id missing!'
        )));
      }

      if($number==null || $number==""){
        return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'Number id missing!'
        )));
      }

      if($Ip==null || $Ip==""){
        return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'Ip id missing!'
        )));
      }

      $record = Apps::find('first',array('conditions'=>array('key' => $key,'isdelete' =>'0')));
      if(count($record)!=0){
          
          $conditions = array( 'kyc_id'=>$kycid,
                               'or' => array(
                                  array('details.Identity.no' => $number),
                                  array('details.Tax.id' => $number),
                                ), 
                               );
          $document = KYCDocuments::find('first',array('conditions'=>$conditions));  
          // 'Verify.Score'=>['$gte' => 70]
          if(count($document)!=0){
            if($document['Verify']['Score'] >= 70){

                 /* Delete Old Key */ 
                 $data['isdelete'] = '1';
                 $conditions = array('hash' => $document['hash']);
                 Apps::update($data,$conditions);

                 /* Key Assign */ 
                 $data['email'] = $document['email'];
                 $data['hash']  = $document['hash'];
                 $data['phone'] = $document['phone'];
                 $data['IP']    = $Ip;
                 $data['isdelete'] = '0';
                 $conditions = array('key' => $key);
                 Apps::update($data,$conditions);
                 
                 if(empty($document['profile'])){
                    /* Profile Picture Move FtpCDN Server  */
                      $path  =  $this->getImage($document['hash'],'profile_img');
                      $movepath = LITHIUM_APP_PATH. '/webroot/documents/'.$document['_id'].'.jpg';
                      $this->smart_resize_image($path, null,300 , 0 , true , $movepath , false , false ,100 );  
                      $profile = $this->uploadProfileThumb($document['hash'],$document['_id']);
                      $basename = pathinfo($path,PATHINFO_BASENAME);
                      unlink(LITHIUM_APP_PATH. '/webroot/documents/'.$basename);
                      unlink($movepath);
                      
                      $kycdata['profile'] = $profile;
                      $conditions = array('hash' => $document['hash']);
                      KYCDocuments::update($kycdata, $conditions);

                    /* */
                 }



                 return $this->render(array('json' => array('success'=>1,
                    'now'=>time(),
                    'result' =>'Login success',
                    'kyc_id' => $document['kyc_id'],
                    'email' => $document['email'],
                    'name' => $document['details']['Name'],
                    'phone' => $document['phone'],
                    'address' => $document['details']['Address'],
                    'passport' => $document['details']['Passport'],
                    'tax' => $document['details']['Tax'],
                    'identity' => $document['details']['Identity'],
                    'driving' => $document['details']['Driving'],
                    'profile' => FTP_URL."/profiles/".$document['profile'],     
                    // 'profile' => $this->getImage($document['hash'],'profile_img')      
                  )));
            }else{
              return $this->render(array('json' => array('success'=>0,
                'now'=>time(),
                'error'=>'KYC not verified!'
                )));
            }     
          }else{
            return $this->render(array('json' => array('success'=>0,
              'now'=>time(),
              'error'=>'Something went to wrong!'
            )));
          }                     
      }else{
          return $this->render(array('json' => array('success'=>2,
            'now'=>time(),
            'error'=>'Invalid Key!'
          ))); 
        }
  }

  public function logout($key = null){
   if ($key==null || $key == ""){
     return $this->render(array('json' => array('success'=>0,
     'now'=>time(),
     'error'=>'Key missing!'
     )));
   }

   $record = Apps::find('first',array(
     'conditions' => array(
       'key'=>$key,'isdelete' =>'0'
       )
     )
   );
 
   if(count($record) > 0){
     return $this->render(array('json' => array('success'=>1,
       'now'=>time(),
       'result'=>'Logout success'
     ))); 
   }else{
     return $this->render(array('json' => array('success'=>0,
       'now'=>time(),
       'error'=>'is delete is not 0!',
     )));
   }
  }

  public function getprofile($key = null){
    if ($key==null || $key == ""){
      return $this->render(array('json' => array('success'=>0,
      'now'=>time(),
      'error'=>'Key missing!'
      )));
    }

    $record = Apps::find('first',array(
      'conditions' => array(
        'key'=>$key
        )
      )
    );
  
    if(count($record) > 0){
        $document = KYCDocuments::find('first',array(
          'conditions' => array(
            'hash'=>$record['hash']
            )
          )
        );
      
        if(count($document) > 0){
           $path = $this->getImage($record['hash'],'profile_img'); 
          return $this->render(array('json' => array('success'=>1,
            'now'=>time(),
            'result'=>'Profile getting',
            'kyc_id' => $document['kyc_id'],
            'email' => $document['email'],
            'name' => $document['details']['Name'],
            'phone' => $document['phone'],
            'address' => $document['details']['Address'],
            'passport' => $document['details']['Passport'],
            'tax' => $document['details']['Tax'],
            'identity' => $document['details']['Identity'],
            'driving' => $document['details']['Driving'],
            'profile' => FTP_URL."/profiles/".$document['profile'],  
            // 'profile' => $this->getImage($document['hash'],'profile_img') //FTP_HOST."/profiles/".$record['profile'],
          ))); 
        }else{
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Document not exits!',
          )));
        }  
    }else{
        return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'Key does not match',
        )));
    }
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

        file_put_contents($path, $image_name->file->getBytes());
        return $return_path;
    }
  }  
  
  private function uploadProfileThumb($hash,$id){
     $directory = substr($hash,0,1)."/".substr($hash,1,1)."/".substr($hash,2,1)."/".substr($hash,3,1);
     
     $file = $id.".jpg";   
   
     $ftp_host = FTP_HOST; /* host */
     $ftp_user_name = FTP_USER; /* username */
     $ftp_user_pass = FTP_PASS; /* password */
     $local_file = LITHIUM_APP_PATH.   "/webroot". FTP_LOCAL_PATH. $file;     
     $remote_file = "/profiles/" . $directory ."/".$file; 
     

     $connect_it = ftp_connect( $ftp_host );   
     /* Login to FTP */
     $login_result = ftp_login( $connect_it, $ftp_user_name, $ftp_user_pass );
 
     /* Send $local_file to FTP */
     if(ftp_put( $connect_it, $remote_file, $local_file, FTP_BINARY)){
        return $directory."/".$file;
     }else{
        return $directory."/".$file;
     }
  }

  /* Common Function */
  private function smart_resize_image($file,$string= null,$width= 0,$height= 0,$proportional= false,$output= 'file',$delete_original= true,$use_linux_commands = false,$quality = 100) {
      if ( $height <= 0 && $width <= 0 ) return false;
      if ( $file === null && $string === null ) return false;
          # Setting defaults and meta
      $info   = $file !== null ? getimagesize($file) : getimagesizefromstring($string);
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


}
?>