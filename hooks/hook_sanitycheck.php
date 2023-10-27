<?php

declare(strict_types=1);

use Exception;
use SimpleSAML\Assert\Assert;
use SimpleSAML\Configuration;
use SimpleSAML\Module\consent\Store;

use function is_callable;

/**
 * @param array &$hookinfo  hookinfo
 */
function consentSimpleAdmin_hook_sanitycheck(array &$hookinfo): void
{
    Assert::keyExists($hookinfo, 'errors');
    Assert::keyExists($hookinfo, 'info');

    try {
        $consentconfig = Configuration::getConfig('module_consentSimpleAdmin.php');

        // Parse consent config
        $consent_storage = Store::parseStoreConfig($consentconfig->getValue('store'));

        if (!is_callable([$consent_storage, 'selftest'])) {
            // Doesn't support a selftest
            return;
        }
        $testres = $consent_storage->selftest();
        if ($testres) {
            $hookinfo['info'][] = '[consentSimpleAdmin] Consent Storage selftest OK.';
        } else {
            $hookinfo['errors'][] = '[consentSimpleAdmin] Consent Storage selftest failed.';
        }
    } catch (Exception $e) {
        $hookinfo['errors'][] = '[consentSimpleAdmin] Error connecting to storage: ' . $e->getMessage();
    }
}
