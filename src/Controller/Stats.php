<?php

declare(strict_types=1);

namespace SimpleSAML\Module\consentsimpleadmin\Controller;

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
class Statistics
{
    /**
     * Controller constructor.
     *
     * It initializes the global configuration and session for the controllers implemented here.
     *
     * @param \SimpleSAML\Configuration $config The configuration to use by the controllers.
     * @param \SimpleSAML\Session $session The session to use by the controllers.
     *
     * @throws \Exception
     */
    public function __construct(
        protected Configuration $config,
        protected Session $session
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
        $t = new Template($config, 'consentSimpleAdmin:consentstats.twig');
        $translator = $t->getTranslator();

        $t->data['stats'] = $stats;
        $t->data['total'] = $translator->t(
            'Consent storage contains %NO% entries.',
            ['%NO%' => $t->data['stats']['total']]
        );
        $t->data['statusers'] = $translator->t(
            '%NO% unique users have given consent.',
            ['%NO%' => $t->data['stats']['users']]
        );
        $t->data['statservices'] = $translator->t(
            'Consent is given to %NO% unique services.',
            ['%NO%' => $t->data['stats']['services']]
        );
        return $t;
    }
}
