<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tools
[END_COT_EXT]
==================== */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('plug', 'authbyuser');
cot_block($usr['isadmin']);

$t = new XTemplate(cot_tplfile('authbyuser', 'plug', true));

require_once cot_incfile('auth');
require_once cot_langfile('users', 'module');

$a = cot_import('a', 'G', 'TXT');
$id = cot_import('id', 'P', 'INT');

if ($a == 'auth' && $id > 0)
{
  $row = ($id > 0) ? $db->query("SELECT * FROM $db_users WHERE user_id=".$id)->fetch() : array();

  if($row['user_id'] > 0)
  {
		//cot_setcookie($sys['site_id'], '', time()-63072000, $cfg['cookiepath'], $cfg['cookiedomain'], $sys['secure'], true);

	  //session_unset();
	  //session_destroy();

		$rusername = $row['user_name'];

		$ruserid = $row['user_id'];
		$token = cot_unique(16);

		$sid = hash_hmac('sha256', $rmdpass . $row['user_sidtime'], $cfg['secret_key']);

		if (empty($row['user_sid']) || $row['user_sid'] != $sid
			|| $row['user_sidtime'] + $cfg['cookielifetime'] < $sys['now'])
		{
			// Generate new session identifier
			$sid = hash_hmac('sha256', $rmdpass . $sys['now'], $cfg['secret_key']);
			$update_sid = ", user_sid = " . $db->quote($sid) . ", user_sidtime = " . $sys['now'];
		}
		else
		{
			$update_sid = '';
		}

		if($validating)
		{
			$update_lostpass = ', user_lostpass='.$db->quote(md5(microtime()));
		}
		else
		{
			$update_lostpass = '';
		}

		$db->query("UPDATE $db_users SET user_token = '$token' $update_lostpass $update_sid WHERE user_id={$row['user_id']}");

		$sid = hash_hmac('sha1', $sid, $cfg['secret_key']);

		$u = base64_encode($ruserid.':'.$sid);

		cot_setcookie($sys['site_id'], $u, time()+$cfg['cookielifetime'], $cfg['cookiepath'], $cfg['cookiedomain'], $sys['secure'], true);
		unset($_SESSION[$sys['site_id']]);

    cot_redirect(cot_url('index', '', '', true));
  } else {
	 cot_redirect(cot_url('admin', 'm=other&p=authbyuser', '', true));
  }
}

require_once cot_incfile('forms');

$urrs = array();
$urrs_json = array();
$urrsql = $db->query("SELECT * FROM $db_users")->fetchAll();
foreach($urrsql as $row) {
  $urrs[$row['user_id']] = $row['user_name'].' - '.$row['user_email'];
  $urrs_json[] = array(
    'id' => $row['user_id'],
    'name' => $row['user_name'],
    'email' => $row['user_email']
  );
}
$urrs_json = json_encode($urrs_json);

$t->parse('MAIN');

$adminmain = $t->text('MAIN');
