<?php
use lithium\g11n\Message;
extract(Message::aliases());

?><h3><?=substr($compact['data']['kyc_id'],0,6)?> is your email code for EvolveChain Mobile App</h3>
<p>
email: <?=$compact['data']['email']?><br>
Date and time: <?=gmdate('Y-m-d H:i:s',time())?>
</p>