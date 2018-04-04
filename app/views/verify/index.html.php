<form role="form" method="post">
<br>
<h3 class="padded">Know your customer (KYC)<br><small>Required on EvolveChain, MedikChain sites...</small></h3>
<hr>
<h5 class="alert alert-success">Basic Information <span class="float-right">Score: <?=($document['Verify']['Basic']['Correct']+$document['Verify']['Basic']['Uploaded'])?> Total: <?=$document['Verify']['Score']?></span></h5>
<div class="row">
	<div class="col-sm-5">
 	<table class="table">
		<tr>
			<td>Name: </td>
			<td><?=$document['details']['Name']['first']?> <?=$document['details']['Name']['middle']?> <?=$document['details']['Name']['last']?></td>
		</tr>
		<tr>
		<tr>
			<td>Mobile: </td>
			<td><?=$document['details']['Mobile']?></td>
		</tr>
  <tr>
			<td>Email: </td>
			<td><?=$document['email']?></td>
		</tr>
  <tr>
			<td>Date of Birth: </td>
			<td><?=$document['details']['Birth']['date']?></td>
		</tr>
		<tr>
  </table>
 </div>
 <div class="col-sm-4">
  <table class="table">
  <tr>
  <td>Profile<br>
			<?php if($imagename_profile_img==""){?>
				No Document Uploaded
			<?php }else{?>
				<a href="/documents/<?=$imagename_profile_img?>" target="_blank"><img src="/documents/<?=$imagename_profile_img?>" width="100%"></a>
			<?php } ?>
			</td>
   </tr>
  </table>
 </div>
 <div class="col-sm-3">
 <table class="table">
  <tr>
   <td>
    Document Uploaded?
   </td>
  </tr>
  <tr>
   <td>
   <input type="radio" name="Verify[Basic][Uploaded]" value="<?php print_r($questions['Basic']['Uploaded'][1])?>" <?php if($questions['Basic']['Uploaded'][1]==$document['Verify']['Basic']['Uploaded']){echo " checked ";}?>> Yes 
   <input type="radio" name="Verify[Basic][Uploaded]" value="<?php print_r($questions['Basic']['Uploaded'][0])?>" <?php if($questions['Basic']['Uploaded'][0]==$document['Verify']['Basic']['Uploaded']){echo " checked ";}?>> No 
   </td>
  </tr>
  <tr>
   <td>
   Information Correct?
   </td>
  </tr>
  <tr>
   <td>
   <input type="radio" name="Verify[Basic][Correct]" value="<?php print_r($questions['Basic']['Correct'][1])?>" <?php if($questions['Basic']['Correct'][1]==$document['Verify']['Basic']['Correct']){echo " checked ";}?>> Yes 
   <input type="radio" name="Verify[Basic][Correct]" value="<?php print_r($questions['Basic']['Correct'][0])?>" <?php if($questions['Basic']['Correct'][0]==$document['Verify']['Basic']['Correct']){echo " checked ";}?>> No 
   </td>
  </tr>
 </table>
 </div>
</div>
<hr>
<h5 class="alert alert-success">Address Information <span class="float-right">Score: <?=($document['Verify']['Address']['Correct']+$document['Verify']['Address']['Uploaded'])?> Total: <?=$document['Verify']['Score']?></span></h5>
<div class="row">
	<div class="col-sm-5">
  <table class="table">
		<tr>
			<td>Address: </td>
			<td><?=$document['details']['Address']['address']?> <?=$document['details']['Address']['street']?></td>
		</tr>
		<tr>
			<td>City: </td>
			<td><?=$document['details']['Address']['city']?></td>
		</tr>  
		<tr>
			<td>State: </td>
			<td><?=$document['details']['Address']['state']?></td>
		</tr>  
		<tr>
			<td>Pin: </td>
			<td><?=$document['details']['Address']['zip']?></td>
		</tr>  
		<tr>
			<td>Country: </td>
			<td><?=$document['details']['Address']['country']?></td>
		</tr>  
  </table>
 </div>
 <div class="col-sm-4">
  <table class="table">
  <tr>
  <td>Address<br>
			<?php if($imagename_address==""){?>
				No Document Uploaded
			<?php }else{?>
				<a href="/documents/<?=$imagename_address?>" target="_blank"><img src="/documents/<?=$imagename_address?>" width="100%"></a>
			<?php } ?>
			</td>
   </tr>
  </table>
 </div>
 <div class="col-sm-3">
 <table class="table">
  <tr>
   <td>
    Document Uploaded?
   </td>
  </tr>
  <tr>
   <td>
   <input type="radio" name="Verify[Address][Uploaded]" value="<?php print_r($questions['Address']['Uploaded'][1])?>" <?php if($questions['Address']['Uploaded'][1]==$document['Verify']['Address']['Uploaded']){echo " checked ";}?>> Yes 
   <input type="radio" name="Verify[Address][Uploaded]" value="<?php print_r($questions['Address']['Uploaded'][0])?>" <?php if($questions['Address']['Uploaded'][0]==$document['Verify']['Address']['Uploaded']){echo " checked ";}?>> No 
   </td>
  </tr>
  <tr>
   <td>
   Information Correct?
   </td>
  </tr>
  <tr>
   <td>
   <input type="radio" name="Verify[Address][Correct]" value="<?php print_r($questions['Address']['Correct'][1])?>" <?php if($questions['Address']['Correct'][1]==$document['Verify']['Address']['Correct']){echo " checked ";}?>> Yes 
   <input type="radio" name="Verify[Address][Correct]" value="<?php print_r($questions['Address']['Correct'][0])?>" <?php if($questions['Address']['Correct'][0]==$document['Verify']['Address']['Correct']){echo " checked ";}?>> No 
   </td>
  </tr>
 </table>
 </div>
</div>
<hr>
<h5 class="alert alert-success">Passport Information <span class="float-right">Score: <?=($document['Verify']['Passport']['Correct']+$document['Verify']['Passport']['Uploaded'])?> Total: <?=$document['Verify']['Score']?></span></h5>
<div class="row">
	<div class="col-sm-5">
  <table class="table">
		<tr>
			<td>Name: </td>
			<td><?=$document['details']['Passport']['firstname']?> <?=$document['details']['Passport']['middlename']?> <?=$document['details']['Passport']['lastname']?></td>
		</tr>  
		<tr>
			<td>Date of Birth: </td>
			<td><?=$document['details']['Passport']['dob']?></td>
		</tr>  
		<tr>
			<td>Address: </td>
			<td><?=$document['details']['Passport']['address']?> <?=$document['details']['Passport']['street']?></td>
		</tr>
		<tr>
			<td>City: </td>
			<td><?=$document['details']['Passport']['city']?></td>
		</tr>  
		<tr>
			<td>State: </td>
			<td><?=$document['details']['Passport']['state']?></td>
		</tr>  
		<tr>
			<td>Pin: </td>
			<td><?=$document['details']['Passport']['zip']?></td>
		</tr>  
		<tr>
			<td>Country: </td>
			<td><?=$document['details']['Passport']['country']?></td>
		</tr>  
		<tr>
			<td>Passport No: </td>
			<td><?=$document['details']['Passport']['no']?></td>
		</tr>  
		<tr>
			<td>Expiry: </td>
			<td><?=$document['details']['Passport']['expiry']?></td>
		</tr>  
		<tr>
			<td>Issued Country: </td>
			<td><?=$document['details']['Passport']['pass_country']?></td>
		</tr>  
  </table>
 </div>
 <div class="col-sm-4">
  <table class="table">
  <tr>
  	<td>Passport<br>
			<?php if($imagename_passport1==""){?>
				No Document Uploaded
			<?php }else{?>
				<a href="/documents/<?=$imagename_passport1?>" target="_blank"><img src="/documents/<?=$imagename_passport1?>" width="100%"></a>
			<?php } ?>
			</td>
  </tr>
  <tr>
  	<td>Passport<br>
			<?php if($imagename_passport2==""){?>
				No Document Uploaded
			<?php }else{?>
				<a href="/documents/<?=$imagename_passport2?>" target="_blank"><img src="/documents/<?=$imagename_passport2?>" width="100%"></a>
			<?php } ?>
			</td>
  </tr>
  </table>
 </div>
 <div class="col-sm-3">
 <table class="table">
  <tr>
   <td>
    Document Uploaded?
   </td>
  </tr>
  <tr>
   <td>
   <input type="radio" name="Verify[Passport][Uploaded]" value="<?php print_r($questions['Passport']['Uploaded'][1])?>" <?php if($questions['Passport']['Uploaded'][1]==$document['Verify']['Passport']['Uploaded']){echo " checked ";}?>> Yes 
   <input type="radio" name="Verify[Passport][Uploaded]" value="<?php print_r($questions['Passport']['Uploaded'][0])?>" <?php if($questions['Passport']['Uploaded'][0]==$document['Verify']['Passport']['Uploaded']){echo " checked ";}?>> No 
   </td>
  </tr>
  <tr>
   <td>
   Information Correct?
   </td>
  </tr>
  <tr>
   <td>
   <input type="radio" name="Verify[Passport][Correct]" value="<?php print_r($questions['Passport']['Correct'][1])?>" <?php if($questions['Passport']['Correct'][1]==$document['Verify']['Passport']['Correct']){echo " checked ";}?>> Yes 
   <input type="radio" name="Verify[Passport][Correct]" value="<?php print_r($questions['Passport']['Correct'][0])?>" <?php if($questions['Passport']['Correct'][0]==$document['Verify']['Passport']['Correct']){echo " checked ";}?>> No 
   </td>
  </tr>
 </table>
 </div>
</div>
<hr>
<h5 class="alert alert-success">Tax Information <span class="float-right">Score: <?=($document['Verify']['Tax']['Correct']+$document['Verify']['Tax']['Uploaded'])?> Total: <?=$document['Verify']['Score']?></span></h5>
<div class="row">
	<div class="col-sm-5">
  <table class="table">
		<tr>
			<td>Name: </td>
			<td><?=$document['details']['Tax']['firstname']?> <?=$document['details']['Tax']['middlename']?> <?=$document['details']['Tax']['lastname']?></td>
		</tr>  
		<tr>
			<td>Date of Birth: </td>
			<td><?=$document['details']['Tax']['dateofBirth']?></td>
		</tr>  
		<tr>
			<td>ID: </td>
			<td><?=$document['details']['Tax']['id']?></td>
		</tr>
  </table>
 </div>
 <div class="col-sm-4">
  <table class="table">
  <tr>
  <td>Tax<br>
			<?php if($imagename_tax1==""){?>
			No Document Uploaded
			<?php }else{?>
				<a href="/documents/<?=$imagename_tax1?>" target="_blank"><img src="/documents/<?=$imagename_tax1?>" width="100%"></a>
			<?php } ?>
			</td>
  </tr>
  </table>
 </div>
 <div class="col-sm-3">
 <table class="table">
  <tr>
   <td>
    Document Uploaded?
   </td>
  </tr>
  <tr>
   <td>
   <input type="radio" name="Verify[Tax][Uploaded]" value="<?php print_r($questions['Tax']['Uploaded'][1])?>" <?php if($questions['Tax']['Uploaded'][1]==$document['Verify']['Tax']['Uploaded']){echo " checked ";}?>> Yes 
   <input type="radio" name="Verify[Tax][Uploaded]" value="<?php print_r($questions['Tax']['Uploaded'][0])?>" <?php if($questions['Tax']['Uploaded'][0]==$document['Verify']['Tax']['Uploaded']){echo " checked ";}?>> No 
   </td>
  </tr>
  <tr>
   <td>
   Information Correct?
   </td>
  </tr>
  <tr>
   <td>
   <input type="radio" name="Verify[Tax][Correct]" value="<?php print_r($questions['Tax']['Correct'][1])?>" <?php if($questions['Tax']['Correct'][1]==$document['Verify']['Tax']['Correct']){echo " checked ";}?>> Yes 
   <input type="radio" name="Verify[Tax][Correct]" value="<?php print_r($questions['Tax']['Correct'][0])?>" <?php if($questions['Tax']['Correct'][0]==$document['Verify']['Tax']['Correct']){echo " checked ";}?>> No 
   </td>
  </tr>
 </table>
 </div>
</div>
<hr>
<h5 class="alert alert-success">Identity Information <span class="float-right">Score: <?=($document['Verify']['Identity']['Correct']+$document['Verify']['Identity']['Uploaded'])?> Total: <?=$document['Verify']['Score']?></span></h5>
<div class="row">
	<div class="col-sm-5">
  <table class="table">
		<tr>
			<td>Name: </td>
			<td><?=$document['details']['Identity']['firstname']?> <?=$document['details']['Identity']['middlename']?> <?=$document['details']['Identity']['lastname']?></td>
		</tr>  
		<tr>
			<td>Identity No: </td>
			<td><?=$document['details']['Identity']['no']?></td>
		</tr>  
  </table>
 </div>
 <div class="col-sm-4">
  <table class="table">
  <tr>
			<td>Identity<br>
			<?php if($imagename_identity1==""){?>
				No Document Uploaded
			<?php }else{?>
				<a href="/documents/<?=$imagename_identity1?>" target="_blank"><img src="/documents/<?=$imagename_identity1?>" width="100%"></a>
			<?php } ?>
			</td>
   </tr>
  <tr>
			<td>
			<?php if($imagename_identity2==""){?>
				No Document Uploaded
			<?php }else{?>
				<a href="/documents/<?=$imagename_identity2?>" target="_blank"><img src="/documents/<?=$imagename_identity2?>" width="100%"></a>
			<?php } ?>
			</td>
   </tr>  </table>
 </div>
 <div class="col-sm-3">
 <table class="table">
  <tr>
   <td>
    Document Uploaded?
   </td>
  </tr>
  <tr>
   <td>
   <input type="radio" name="Verify[Identity][Uploaded]" value="<?php print_r($questions['Identity']['Uploaded'][1])?>" <?php if($questions['Identity']['Uploaded'][1]==$document['Verify']['Identity']['Uploaded']){echo " checked ";}?>> Yes 
   <input type="radio" name="Verify[Identity][Uploaded]" value="<?php print_r($questions['Identity']['Uploaded'][0])?>" <?php if($questions['Identity']['Uploaded'][0]==$document['Verify']['Identity']['Uploaded']){echo " checked ";}?>> No 
   </td>
  </tr>
  <tr>
   <td>
   Information Correct?
   </td>
  </tr>
  <tr>
   <td>
   <input type="radio" name="Verify[Identity][Correct]" value="<?php print_r($questions['Identity']['Correct'][1])?>" <?php if($questions['Identity']['Correct'][1]==$document['Verify']['Identity']['Correct']){echo " checked ";}?>> Yes 
   <input type="radio" name="Verify[Identity][Correct]" value="<?php print_r($questions['Identity']['Correct'][0])?>" <?php if($questions['Identity']['Correct'][0]==$document['Verify']['Identity']['Correct']){echo " checked ";}?>> No 
   </td>
  </tr>
 </table>
 </div>
</div>
<hr>
<h5 class="alert alert-success">Driving License Information<span class="float-right">Score: <?=($document['Verify']['Driving']['Correct']+$document['Verify']['Driving']['Uploaded'])?> Total: <?=$document['Verify']['Score']?></span></h5>
<div class="row">
	<div class="col-sm-5">
  <table class="table">
		<tr>
			<td>Name: </td>
			<td><?=$document['details']['Driving']['firstname']?> <?=$document['details']['Driving']['middlename']?> <?=$document['details']['Driving']['lastname']?></td>
		</tr>  
		<tr>
			<td>Date of Birth: </td>
			<td><?=$document['details']['Driving']['dob']?></td>
		</tr>  
		<tr>
			<td>Address: </td>
			<td><?=$document['details']['Driving']['address']?> <?=$document['details']['Driving']['street']?></td>
		</tr>
		<tr>
			<td>City: </td>
			<td><?=$document['details']['Driving']['city']?></td>
		</tr>  
		<tr>
			<td>State: </td>
			<td><?=$document['details']['Driving']['state']?></td>
		</tr>  
		<tr>
			<td>Pin: </td>
			<td><?=$document['details']['Driving']['zip']?></td>
		</tr>  
		<tr>
			<td>Country: </td>
			<td><?=$document['details']['Driving']['country']?></td>
		</tr>  
		<tr>
			<td>Driving No: </td>
			<td><?=$document['details']['Driving']['no']?></td>
		</tr>  
		<tr>
			<td>Expiry: </td>
			<td><?=$document['details']['Driving']['expiry']?></td>
		</tr>  
		<tr>
			<td>Issued Country: </td>
			<td><?=$document['details']['Driving']['license_country']?></td>
		</tr>  
  </table>
 </div>
 <div class="col-sm-4">
  <table class="table">
			<td>Driving<br>
			<?php if($imagename_driving==""){?>
			No Document Uploaded
			<?php }else{?>
				<a href="/documents/<?=$imagename_driving?>" target="_blank"><img src="/documents/<?=$imagename_driving?>" width="100%"></a>
			<?php } ?>
			</td>
  </table>
 </div>
 <div class="col-sm-3">
 <table class="table">
  <tr>
   <td>
    Document Uploaded?
   </td>
  </tr>
  <tr>
   <td>
   <input type="radio" name="Verify[Driving][Uploaded]" value="<?php print_r($questions['Driving']['Uploaded'][1])?>" <?php if($questions['Driving']['Uploaded'][1]==$document['Verify']['Driving']['Uploaded']){echo " checked ";}?>> Yes 
   <input type="radio" name="Verify[Driving][Uploaded]" value="<?php print_r($questions['Driving']['Uploaded'][0])?>" <?php if($questions['Driving']['Uploaded'][0]==$document['Verify']['Driving']['Uploaded']){echo " checked ";}?>> No 
   </td>
  </tr>
  <tr>
   <td>
   Information Correct?
   </td>
  </tr>
  <tr>
   <td>
   <input type="radio" name="Verify[Driving][Correct]" value="<?php print_r($questions['Driving']['Correct'][1])?>" <?php if($questions['Driving']['Correct'][1]==$document['Verify']['Driving']['Correct']){echo " checked ";}?>> Yes 
   <input type="radio" name="Verify[Driving][Correct]" value="<?php print_r($questions['Driving']['Correct'][0])?>" <?php if($questions['Driving']['Correct'][0]==$document['Verify']['Driving']['Correct']){echo " checked ";}?>> No 
   </td>
  </tr>
 </table>
 </div>
</div>
<hr>
<h5 class="alert alert-success">Face + Identity Information<span class="float-right">Score: <?=($document['Verify']['Face']['Correct']+$document['Verify']['Face']['Uploaded'])?> Total: <?=$document['Verify']['Score']?></span></h5>
<div class="row">
	<div class="col-sm-5">
  <table class="table">
  </table>
 </div>
 <div class="col-sm-4">
  <table class="table">
  <tr>
<td>Passport/Face<br>
			<?php 
   if($imagename_hold_img==""){?>
			No Document Uploaded
			<?php }else{?>
				<a href="/documents/<?=$imagename_hold_img?>" target="_blank"><img src="/documents/<?=$imagename_hold_img?>" width="100%"></a>
			<?php } ?>
			</td>			  
  </tr>
  </table>
 </div>
 <div class="col-sm-3">
 <table class="table">
  <tr>
   <td>
    Document Uploaded?
   </td>
  </tr>
  <tr>
   <td>
   <input type="radio" name="Verify[Face][Uploaded]" value="<?php print_r($questions['Face']['Uploaded'][1])?>" <?php if($questions['Face']['Uploaded'][1]==$document['Verify']['Face']['Uploaded']){echo " checked ";}?>> Yes 
   <input type="radio" name="Verify[Face][Uploaded]" value="<?php print_r($questions['Face']['Uploaded'][0])?>" <?php if($questions['Face']['Uploaded'][0]==$document['Verify']['Face']['Uploaded']){echo " checked ";}?>> No 
   </td>
  </tr>
  <tr>
   <td>
   Information Correct?
   </td>
  </tr>
  <tr>
   <td>
   <input type="radio" name="Verify[Face][Correct]" value="<?php print_r($questions['Face']['Correct'][1])?>" <?php if($questions['Face']['Correct'][1]==$document['Verify']['Face']['Correct']){echo " checked ";}?>> Yes 
   <input type="radio" name="Verify[Face][Correct]" value="<?php print_r($questions['Face']['Correct'][0])?>" <?php if($questions['Face']['Correct'][0]==$document['Verify']['Face']['Correct']){echo " checked ";}?>> No 
   </td>
  </tr>
 </table>
 </div>
</div>
<hr>
		<input type="submit" value="Save" class="btn btn-primary ">
		<a href="/kyc/report/<?=$document['hash']?>" class="btn btn-success">Submit final report</a>
		<a href="/" class="btn btn-success">Close</a>
	</form>
	</div>
<hr>