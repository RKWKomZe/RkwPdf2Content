<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Configure plugins
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'BM.' . $_EXTKEY,
    'Pi1',
    array(
        'DisplayPages' => 'list, importParentPage'
    ),
    # not cached actions
    array(
        'DisplayPages' => 'list'
    )
);


// Add rootline-Fields
$TYPO3_CONF_VARS['FE']['addRootLineFields'] .= ', tx_bmpdf2content_is_import, tx_bmpdf2content_is_import_sub';
