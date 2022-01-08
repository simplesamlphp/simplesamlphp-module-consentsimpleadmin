<?php

declare(strict_types=1);

use SimpleSAML\Locale\Translate;
use SimpleSAML\Module;
use SimpleSAML\XHTML\Template;

/**
 * Hook to add the consentSimpleAdmin module to the config page.
 *
 * @param \SimpleSAML\XHTML\Template &$template The template that we should alter in this hook.
 */
function consentSimpleAdmin_hook_configpage(Template &$template)
{
    $template->data['links'][] = [
        'href' => Module::getModuleURL('consentSimpleAdmin/consentAdmin.php'),
        'text' => Translate::noop('Consent withdrawal'),
    ];
    $template->data['links'][] = [
        'href' => Module::getModuleURL('consentSimpleAdmin/consentStats.php'),
        'text' => Translate::noop('Consent Storage Statistics'),
    ];
    $template->getLocalization()->addModuleDomain('consentSimpleAdmin');
}
