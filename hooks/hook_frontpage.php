<?php

/**
 * Hook to add the simple consenet admin module to the frontpage.
 *
 * @param array &$links  The links on the frontpage, split into sections.
 * @return void
 */
function consentSimpleAdmin_hook_frontpage(array &$links)
{
    assert(array_key_exists("links", $links));

    $links['config'][] = [
        'href' => \SimpleSAML\Module::getModuleURL('consentSimpleAdmin/consentAdmin.php'),
        'text' => '{consentSimpleAdmin:consentsimpleadmin:header}',
    ];
    $links['config'][] = [
        'href' => \SimpleSAML\Module::getModuleURL('consentSimpleAdmin/consentStats.php'),
        'text' => '{consentSimpleAdmin:consentsimpleadmin:headerstats}',
    ];
}
