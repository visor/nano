<?php

include_once './lib/include.php';
include_once './lib/nano.php';
include_once DOCUMENT_ROOT . '/lib/booking.redirect.php';

/**
 * Если запрос произошёл к адресу вида:
 * http://<URL-организации>.komsindrom.ru/
 * перенаправляем запрос на страницу этой организации вида:
 * http://komsindrom.ru/<URL-города>/<ID-организации>/
 *
 * {{
 */
$hostParts = explode('.', $_SERVER['HTTP_HOST']);

if (count($hostParts) == 3 && $hostParts[0] != 'www' && $hostParts[0] != 'beta') {
	$org = KomSindrom::db()->getRow('SELECT * FROM orgs WHERE subdomain LIKE ' . Nano::db()->quote($hostParts[0]), DB_FETCHMODE_ASSOC);
	if ($org['org_id'] > 0) {
		header('Location: http://' . SITE_URL . SEO::tool()->url->getFor($org['org_id']));
	} else {
		header('Location: http://' . SITE_URL . $_SERVER['REQUEST_URI']);
	}
	exit();
}
/* }} */

Nano::dispatcher()->setCustom(new KomSindromDispatcher());
Nano::run();