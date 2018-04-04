<?php
use app\extensions\action\Functions;
			$function = new Functions();
?><div style="margin:20px">
<br>
<strong>Network status: </strong>We are in sync with EvolveChain network using <a href="/blockchain/peer"><strong><?=$getconnectioncount?></strong></a> connections!
<hr>
<h2><a href="/blockchain/blocks"><?=$getblockcount?> Blocks</a></h2>
Generated <?=$function->toFriendlyTime((time()-$getblock['time']));?> mins ago at <?=gmdate('Y-m-d H:i:s',$getblock['time'])?>. 
The above block had difficulty level of <?=$getblock['difficulty']?>.<br>
Hash: <code><?=$getblock['hash']?></code><br>
Version: <code><?=$getinfo['version']?></code><br>
Protocol Version: <code><?=$getinfo['protocolversion']?></code><br>
Wallet Version: <code><?=$getinfo['walletversion']?></code><br>
Balance: <code><?=$getinfo['balance']?></code><br>
Blocks: <code><?=$getinfo['blocks']?></code><br>
Connections: <code><?=$getinfo['connections']?></code><br>
Server: <code><?=$_SERVER["SERVER_ADDR"]?></code><br>
Remote: <code><?=$_SERVER["REMOTE_ADDR"]?></code><br>
</div>