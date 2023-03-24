<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$tempColumnsPages = array(

    'tx_rkwpdf2content_is_import' => array (
        'exclude' => 0,
        // 'displayCond' => 'FIELD:tx_rkwpdf2content_is_import_sub:=:0',
        'label' => 'LLL:EXT:rkw_pdf2content/Resources/Private/Language/locallang_db.xlf:tx_rkwpdf2content_domain_model_pages.tx_rkwpdf2content_is_import',
        'config' => array (
            'type' => 'check',
            'default' => 0,
            'items' => array(
                '1' => array(
                    '0' => 'LLL:EXT:rkw_pdf2content/Resources/Private/Language/locallang_db.xlf:tx_rkwpdf2content_domain_model_pages.tx_rkwpdf2content_is_import.I.enable'
                )
            )
        ),
        'onChange' => 'reload'
    ),
    'tx_rkwpdf2content_is_import_sub' => array (
        'exclude' => 1,
        'displayCond' => 'FIELD:tx_rkwpdf2content_is_import:=:1',
        // 'label' => 'LLL:EXT:rkw_pdf2content/Resources/Private/Language/locallang_db.xlf:tx_rkwpdf2content_domain_model_pages.tx_rkwpdf2content_is_import_sub',
        'config' => array (
            'type' => 'check',
            'default' => 0,
            'items' => array(
                '1' => array(
                    '0' => 'LLL:EXT:rkw_pdf2content/Resources/Private/Language/locallang_db.xlf:tx_rkwpdf2content_domain_model_pages.tx_rkwpdf2content_is_import_sub.I.enable'
                )
            )
        ),
        'onChange' => 'reload'
    ),
);

// Add TCA - nothing else
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages',$tempColumnsPages);

// Add field to the existing palette
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('pages', 'abstract','--linebreak--,tx_rkwpdf2content_is_import,tx_rkwpdf2content_is_import_sub','after:abstract');
