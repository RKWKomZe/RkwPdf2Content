<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Configure plugins
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'RKW.' . $_EXTKEY,
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
$GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'] .= ', tx_rkwpdf2content_is_import, tx_rkwpdf2content_is_import_sub';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['PagesFields']
    = \RKW\RkwPdf2content\Updates\PagesFieldsUpdater::class;
