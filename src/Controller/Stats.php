<?php

declare(strict_types=1);

namespace SimpleSAML\Module\consentSimpleAdmin\Controller;

use SimpleSAML\Configuration;
use SimpleSAML\Module\consent\Store;
use SimpleSAML\Session;
use SimpleSAML\Utils;
use SimpleSAML\XHTML\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller class for the consentsimpleadmin module.
 *
 * This class serves the different views available in the module.
 *
 * @package simplesamlphp/simplesamlphp-module-consentsimpleadmin
 */
class Stats
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
    public function stats(Request $request): Template
    {
        $authUtils = new Utils\Auth();
        $authUtils->requireAdmin();

        // Get config object
        $consentconfig = $this->config::getConfig('module_consentSimpleAdmin.php');

        // Parse consent config
        $consent_storage = Store::parseStoreConfig($consentconfig->getValue('store'));

        // Get all consents for user
        $stats = $consent_storage->getStatistics();

        // Init template
        $t = new Template($this->config, 'consentSimpleAdmin:consentstats.twig');
        $t->data['stats'] = $stats;

        return $t;
    }
}
