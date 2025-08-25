<?php

declare(strict_types=1);

namespace SimpleSAML\Module\consentSimpleAdmin\Controller;

use Exception;
use SimpleSAML\Auth;
use SimpleSAML\Configuration;
use SimpleSAML\Logger;
use SimpleSAML\Metadata\MetaDataStorageHandler;
use SimpleSAML\Module\consent\Auth\Process\Consent;
use SimpleSAML\Module\consent\Store;
use SimpleSAML\Session;
use SimpleSAML\XHTML\Template;
use Symfony\Component\HttpFoundation\Request;

use function array_key_exists;
use function count;
use function sprintf;

/**
 * Controller class for the consentsimpleadmin module.
 *
 * This class serves the different views available in the module.
 *
 * @package simplesamlphp/simplesamlphp-module-consentsimpleadmin
 */
class Admin
{
    /**
     * Controller constructor.
     *
     * It initializes the global configuration and session for the controllers implemented here.
     *
     * @param \SimpleSAML\Configuration $config The configuration to use by the controllers.
     * @param \SimpleSAML\Session $session The session to use by the controllers.
     */
    public function __construct(
        protected Configuration $config,
        protected Session $session,
    ) {
    }



    /**
     * @param \Symfony\Component\HttpFoundation\Request $request The current request.
     *
     * @return \SimpleSAML\XHTML\Template
     */
    public function admin(Request $request): Template
    {
        $consentconfig = Configuration::getConfig('module_consentSimpleAdmin.php');

        $as = $consentconfig->getValue('auth');
        $as = new Auth\Simple($as);
        $as->requireAuth();

        // Get all attributes
        $attributes = $as->getAttributes();

        // Get user ID
        $userid_attributename = $consentconfig->getOptionalValue('userid', 'eduPersonPrincipalName');

        if (empty($attributes[$userid_attributename])) {
            throw new Exception(sprintf(
                'Could not generate useridentifier for storing consent. Attribute [%s] was not available.',
                $userid_attributename,
            ));
        }

        $userid = $attributes[$userid_attributename][0];

        // Get metadata storage handler
        $metadata = MetaDataStorageHandler::getMetadataHandler();

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

        Logger::debug('consentAdmin: IdP is [' . $idp_entityid . ']');

        $source = $idp_metadata['metadata-set'] . '|' . $idp_entityid;

        // Parse consent config
        $consent_storage = Store::parseStoreConfig($consentconfig->getValue('store'));

        // Calc correct user ID hash
        $hashed_user_id = Consent::getHashedUserID($userid, $source);

        // Check if button with withdraw all consent was clicked
        if (array_key_exists('withdraw', $_REQUEST)) {
            Logger::info(sprintf(
                'consentAdmin: UserID [%s] has requested to withdraw all consents given...',
                $hashed_user_id,
            ));

            $consent_storage->deleteAllConsents($hashed_user_id);
        }

        // Get all consents for user
        $user_consent_list = $consent_storage->getConsents($hashed_user_id);

        $consentServices = [];
        foreach ($user_consent_list as $c) {
            $consentServices[$c[1]] = 1;
        }

        Logger::debug(sprintf(
            'consentAdmin: no of consents [%d] no of services [%d]',
            count($user_consent_list),
            count($consentServices),
        ));

        // Init template
        $t = new Template($this->config, 'consentSimpleAdmin:consentadmin.twig');
        $translator = $t->getTranslator();

        $t->data['consentServices'] = count($consentServices);
        $t->data['consents'] = count($user_consent_list);

        return $t;
    }
}
