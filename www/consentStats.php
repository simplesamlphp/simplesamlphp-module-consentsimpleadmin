<?php

/**
 * consentSimpleAdmin - Simple Consent administration module
 *
 * shows statistics.
 *
 * @author Andreas Åkre Solberg <andreas.solberg@uninett.no>
 * @package SimpleSAMLphp
 */

\SimpleSAML\Utils\Auth::requireAdmin();

// Get config object
$config = \SimpleSAML\Configuration::getInstance();
$consentconfig = \SimpleSAML\Configuration::getConfig('module_consentSimpleAdmin.php');


// Parse consent config
$consent_storage = \SimpleSAML\Module\consent\Store::parseStoreConfig($consentconfig->getValue('store'));

// Get all consents for user
$stats = $consent_storage->getStatistics();

// Init template
$t = new \SimpleSAML\XHTML\Template($config, 'consentSimpleAdmin:consentstats.twig');
$translator = $t->getTranslator();

$t->data['stats'] = $stats;
$t->data['total'] = $translator->t(
    '{consentSimpleAdmin:consentsimpleadmin:stattotal}',
    ['%NO%' => $t->data['stats']['total']]
);
$t->data['statusers'] = $translator->t(
    '{consentSimpleAdmin:consentsimpleadmin:statusers}',
    ['%NO%' => $t->data['stats']['users']]
);
$t->data['statservices'] = $translator->t(
    '{consentSimpleAdmin:consentsimpleadmin:statservices}',
    ['%NO%' => $t->data['stats']['services']]
);

$t->send();
