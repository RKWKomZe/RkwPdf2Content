<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$extensionName = \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase('RKW.rkw_pdf2content');
$pluginSignature = strtolower($extensionName) . '_pi1';

# ---------------------------------------------------------------------------
# Register plugin for displaying pages and add flexform config
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'RKW.rkw_pdf2content',
    'Pi1',
    'RKW PDF2Content Display Pages'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';

ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:rkw_pdf2content/Configuration/FlexForms/flexform.xml');


