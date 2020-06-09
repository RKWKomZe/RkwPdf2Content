<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$tempColumnsPages = array(

    'tx_bmpdf2content_is_import' => array (
        'exclude' => 0,
        'displayCond' => 'FIELD:tx_bmpdf2content_is_import_sub:=:0',
        'label' => 'LLL:EXT:bm_pdf2content/Resources/Private/Language/locallang_db.xlf:tx_bmpdf2content_domain_model_pages.tx_bmpdf2content_is_import',
        'config' => array (
            'type' => 'check',
            'default' => 0,
            'items' => array(
                '1' => array(
                    '0' => 'LLL:EXT:bm_pdf2content/Resources/Private/Language/locallang_db.xlf:tx_bmpdf2content_domain_model_pages.tx_bmpdf2content_is_import.I.enable'
                )
            )
        )
    ),
    'tx_bmpdf2content_is_import_sub' => array (
        'exclude' => 0,
        'label' => 'LLL:EXT:bm_pdf2content/Resources/Private/Language/locallang_db.xlf:tx_bmpdf2content_domain_model_pages.tx_bmpdf2content_is_import_sub',
        'config' => array (
            'type' => 'check',
            'default' => 0,
        )
    ),
);


// Add TCA - nothing else
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages',$tempColumnsPages);

// Add field to the existing palette
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('pages', 'tx_rkwbasics_extended', 'tx_bmpdf2content_is_import,');
