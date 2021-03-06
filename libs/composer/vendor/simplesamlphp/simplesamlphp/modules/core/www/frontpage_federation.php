<?php



// Load SimpleSAMLphp, configuration
$config = SimpleSAML_Configuration::getInstance();
$session = SimpleSAML_Session::getSessionFromRequest();

// Check if valid local session exists.
if ($config->getBoolean('admin.protectindexpage', false)) {
    SimpleSAML\Utils\Auth::requireAdmin();
}
$loginurl = SimpleSAML\Utils\Auth::getAdminLoginURL();
$isadmin = SimpleSAML\Utils\Auth::isAdmin();




	
	
	
$links = array();
$links_welcome = array();
$links_config = array();
$links_auth = array();
$links_federation = array();

if ($config->getBoolean('idpdisco.enableremember', false)) {
    $links_federation[] = array(
        'href' => 'cleardiscochoices.php',
        'text' => '{core:frontpage:link_cleardiscochoices}',
    );
}


$links_federation[] = array(
    'href' => \SimpleSAML\Utils\HTTP::getBaseURL() . 'admin/metadata-converter.php',
    'text' => '{core:frontpage:link_xmlconvert}',
);




$allLinks = array(
    'links' => &$links,
    'welcome' => &$links_welcome,
    'config' => &$links_config,
    'auth' => &$links_auth,
    'federation' => &$links_federation,
);
SimpleSAML\Module::callHooks('frontpage', $allLinks);


$metadataHosted = array();
SimpleSAML\Module::callHooks('metadata_hosted', $metadataHosted);









$metadata = SimpleSAML_Metadata_MetaDataStorageHandler::getMetadataHandler();

$metaentries = array('hosted' => $metadataHosted, 'remote' => array() );


if ($isadmin) {
    $metaentries['remote']['saml20-idp-remote'] = $metadata->getList('saml20-idp-remote');
    $metaentries['remote']['shib13-idp-remote'] = $metadata->getList('shib13-idp-remote');
}

if ($config->getBoolean('enable.saml20-idp', false) === true) {
    try {
        $metaentries['hosted']['saml20-idp'] = $metadata->getMetaDataCurrent('saml20-idp-hosted');
        $metaentries['hosted']['saml20-idp']['metadata-url'] =
            $config->getBasePath().'saml2/idp/metadata.php?output=xhtml';
        if ($isadmin)
            $metaentries['remote']['saml20-sp-remote'] = $metadata->getList('saml20-sp-remote');
    } catch(Exception $e) {
        \SimpleSAML\Logger::error('Federation: Error loading saml20-idp: '.$e->getMessage());
    }
}
if ($config->getBoolean('enable.shib13-idp', false) === true) {
    try {
        $metaentries['hosted']['shib13-idp'] = $metadata->getMetaDataCurrent('shib13-idp-hosted');
        $metaentries['hosted']['shib13-idp']['metadata-url'] =
            $config->getBasePath().'shib13/idp/metadata.php?output=xhtml';
        if ($isadmin)
            $metaentries['remote']['shib13-sp-remote'] = $metadata->getList('shib13-sp-remote');
    } catch(Exception $e) {
        \SimpleSAML\Logger::error('Federation: Error loading shib13-idp: ' . $e->getMessage());
    }
}
if ($config->getBoolean('enable.adfs-idp', false) === true) {
    try {
        $metaentries['hosted']['adfs-idp'] = $metadata->getMetaDataCurrent('adfs-idp-hosted');
        $metaentries['hosted']['adfs-idp']['metadata-url'] = \SimpleSAML\Module::getModuleURL(
            'adfs/idp/metadata.php',
            array('output' => 'xhtml')
        );
        if ($isadmin)
            $metaentries['remote']['adfs-sp-remote'] = $metadata->getList('adfs-sp-remote');
    } catch(Exception $e) {
        SimpleSAML\Logger::error('Federation: Error loading adfs-idp: ' . $e->getMessage());
    }
}

foreach ($metaentries['remote'] as $key => $value) {
    if (empty($value)) {
        unset($metaentries['remote'][$key]);
    }
}

$t = new SimpleSAML_XHTML_Template($config, 'core:frontpage_federation.tpl.php');
$translator = $t->getTranslator();

$language = $translator->getLanguage()->getLanguage();
$defaultLanguage = $config->getString('language.default', 'en');

$translators = array(
    'name' => 'name_translated',
    'descr' => 'descr_translated',
    'OrganizationDisplayName' => 'organizationdisplayname_translated',
);

foreach ($metaentries['hosted'] as $index => $entity) {
    foreach ($translators as $old => $new) {
        if (isset($entity[$old][$language])) {
            $metaentries['hosted'][$index][$new] = $entity[$old][$language];
        } elseif (isset($entity[$old][$defaultLanguage])) {
            $metaentries['hosted'][$index][$new] = $entity[$old][$defaultLanguage];
        } elseif (isset($metaentries['hosted'][$index][$old])) {
            $metaentries['hosted'][$index][$new] = $metaentries['hosted'][$index][$old];
        }
    }
}
foreach ($metaentries['remote'] as $key => $set) {
    foreach ($set as $entityid => $entity) {
        foreach ($translators as $old => $new) {
            if (isset($entity[$old][$language])) {
                $metaentries['remote'][$key][$entityid][$new] = $entity[$old][$language];
            } elseif (isset($entity[$old][$defaultLanguage])) {
                $metaentries['remote'][$key][$entityid][$new] = $entity[$old][$defaultLanguage];
            } elseif (isset($metaentries['remote'][$key][$entityid][$old])) {
                $metaentries['remote'][$key][$entityid][$new] = $metaentries['remote'][$key][$entityid][$old];
            }
        }
    }
}

# look up translated string
$mtype = array(
    'saml20-sp-remote' => $translator->noop('{admin:metadata_saml20-sp}'),
    'saml20-sp-hosted' => $translator->noop('{admin:metadata_saml20-sp}'),
    'saml20-idp-remote' => $translator->noop('{admin:metadata_saml20-idp}'),
    'saml20-idp-hosted' => $translator->noop('{admin:metadata_saml20-idp}'),
    'shib13-sp-remote' => $translator->noop('{admin:metadata_shib13-sp}'),
    'shib13-sp-hosted' => $translator->noop('{admin:metadata_shib13-sp}'),
    'shib13-idp-remote' => $translator->noop('{admin:metadata_shib13-idp}'),
    'shib13-idp-hosted' => $translator->noop('{admin:metadata_shib13-idp}'),
    'adfs-sp-remote' => $translator->noop('{admin:metadata_adfs-sp}'),
    'adfs-sp-hosted' => $translator->noop('{admin:metadata_adfs-sp}'),
    'adfs-idp-remote' => $translator->noop('{admin:metadata_adfs-idp}'),
    'adfs-idp-hosted' => $translator->noop('{admin:metadata_adfs-idp}'),
);

$t->data['pageid'] = 'frontpage_federation';
$t->data['isadmin'] = $isadmin;
$t->data['loginurl'] = $loginurl;


$t->data['links'] = $links;
$t->data['links_welcome'] = $links_welcome;
$t->data['links_config'] = $links_config;
$t->data['links_auth'] = $links_auth;
$t->data['links_federation'] = $links_federation;


$t->data['metaentries'] = $metaentries;
$t->data['mtype'] = $mtype;


$t->show();

