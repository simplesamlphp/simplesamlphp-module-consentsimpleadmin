<?php

/**
 * consentSimpleAdmin - Simple Consent administration module
 *
 * This module is a simplification of the danish consent administration module.
 *
 * @package SimpleSAMLphp
 */

// Get config object
$config = \SimpleSAML\Configuration::getInstance();
$consentconfig = \SimpleSAML\Configuration::getConfig('module_consentSimpleAdmin.php');

$as = $consentconfig->getValue('auth');
$as = new \SimpleSAML\Auth\Simple($as);
$as->requireAuth();

// Get all attributes
$attributes = $as->getAttributes();


// Get user ID
$userid_attributename = $consentconfig->getValue('userid', 'eduPersonPrincipalName');
if (empty($attributes[$userid_attributename])) {
    throw new \Exception('Could not generate useridentifier for storing consent. Attribute [' .
        $userid_attributename . '] was not available.');
}

$userid = $attributes[$userid_attributename][0];

// Get metadata storage handler
$metadata = \SimpleSAML\Metadata\MetaDataStorageHandler::getMetadataHandler();

// Get IdP id and metadata
$idp_entityid = $as->getAuthData('saml:sp:IdP');
if ($idp_entityid !== null) {
    // From a remote idp (as bridge)
    $idp_metadata = $metadata->getMetaData($idp_entityid, 'saml20-idp-remote');
} else {
    // from the local idp
    $idp_entityid = $metadata->getMetaDataCurrentEntityID('saml20-idp-hosted');
    $idp_metadata = $metadata->getMetaData($idp_entityid, 'saml20-idp-hosted');
}

\SimpleSAML\Logger::debug('consentAdmin: IdP is [' . $idp_entityid . ']');

$source = $idp_metadata['metadata-set'] . '|' . $idp_entityid;

// Parse consent config
$consent_storage = \SimpleSAML\Module\consent\Store::parseStoreConfig($consentconfig->getValue('store'));

// Calc correct user ID hash
$hashed_user_id = \SimpleSAML\Module\consent\Auth\Process\Consent::getHashedUserID($userid, $source);


// Check if button with withdraw all consent was clicked
if (array_key_exists('withdraw', $_REQUEST)) {
    \SimpleSAML\Logger::info(
        'consentAdmin: UserID [' . $hashed_user_id . '] has requested to withdraw all consents given...'
    );

    $consent_storage->deleteAllConsents($hashed_user_id);
}


// Get all consents for user
$user_consent_list = $consent_storage->getConsents($hashed_user_id);

$consentServices = array();
foreach ($user_consent_list as $c) {
    $consentServices[$c[1]] = 1;
}

\SimpleSAML\Logger::debug(
    'consentAdmin: no of consents [' . count($user_consent_list) . '] no of services [' . count($consentServices) . ']'
);

// Init template
$t = new \SimpleSAML\XHTML\Template($config, 'consentSimpleAdmin:consentadmin.twig');

$t->data['consentServices'] = count($consentServices);
$t->data['consents'] = count($user_consent_list);
$t->data['granted'] = $t->getTranslator()->t('{consentSimpleAdmin:consentsimpleadmin:granted}', [
        '%NO%' => (string)$this->data['consents'],
        '%OF%' => (string)$this->data['consentServices'],
]);

$t->send();
