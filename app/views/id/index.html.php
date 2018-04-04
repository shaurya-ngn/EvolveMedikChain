<br>
<h2>Get KYC Info for client</h2>
<form class="form container" method="post">
<div class="form-group row">
  <label for="email" class="col-sm-2 col-form-label">Client Email</label>
  <div class="col-sm-10">
    <input type="text" class="form-control" id="email" name="email" placeholder="email@example.com" value="<?=$email ?>">
  </div>
  <label for="name" class="col-sm-2 col-form-label">Company Name</label>
  <div class="col-sm-10">
    <input type="text" class="form-control" id="name" name="name" placeholder="ABC & Company" value="<?=$company ?>">
  </div>
  <label for="companyemail" class="col-sm-2 col-form-label">Company Email</label>
  <div class="col-sm-10">
    <input type="text" class="form-control" id="companyemail" name="companyemail" placeholder="name@company.com" value="<?=$company_email ?>">
  </div>
  
  <label for="companyphone" class="col-sm-2 col-form-label">Company Phone</label>
  <div class="col-sm-10">
    <input type="text" class="form-control" id="companyphone" name="companyphone" placeholder="+91 12345 67890" value="<?=$company_phone ?>">
  </div>
  </div>
  <div class="form-group row">
    <label for="inputPassword" class="col-sm-2 col-form-label">KYC ID</label>
    <div class="col-sm-3">
      <input type="kyc1" class="form-control" id="kyc1" name="kyc1" placeholder="123456" value="<?=$kyc1 ?>">
    </div>_
    <div class="col-sm-3">
      <input type="kyc2" class="form-control" id="kyc2" name="kyc2" placeholder="098765" value="<?=$kyc2 ?>">
    </div>_
    <div class="col-sm-3">
      <input type="kyc3" class="form-control" id="kyc3" name="kyc3" placeholder="654321" value="<?=$kyc3 ?>">
    </div>
  </div>
  <button type="submit" class="btn btn-primary mb-2">Get KYC Info</button>
</form>
<div class="container">
<?php if(count($document)>0){?>
<table class="table">

 <?php foreach ($document as $key=>$v){?>
 <tr>
  <th><?=$key?>  </th>
  <td><?=$v?>  </td>
 </tr>
 <?php }?>
</table>
<?php }?>
<?php if(count($shared)>0){?>
<?php  foreach($shared as $sh){ ?>

<?php print_r('<hr>');?>
<h3>
<?php print_r($sh);?>
</h3>
<table class="table">
<?php foreach($toShare as $key){?>
<?php  if($key[$sh]){?>
<?php   foreach($key[$sh] as $x=>$y){?>
<tr>
<th>
<?php    print_r(strtoupper($x));?>
</th>
<td>
<?php    print_r($y);?>
</td>
<?php   }?>
</tr>
<?php  }?>

<?php }?>
</table>

<?php }?>
<?php }?>