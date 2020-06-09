<?php

if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$extensionName = \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase('BM.' . $_EXTKEY);
$pluginSignature = strtolower($extensionName) . '_pi1';

# ---------------------------------------------------------------------------
# Register plugin for displaying pages and add flexform config
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'BM.' . $_EXTKEY,
	'Pi1',
	'PDF2Content Display Pages'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';

ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform.xml');

# ---------------------------------------------------------------------------
# Add static typoscript template files
ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'PDF2Content');

# ---------------------------------------------------------------------------
# Register backend module for editing pdfs
if (TYPO3_MODE === 'BE') {
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'BM.' . $_EXTKEY,
		'tools',
		'mod1',
		'',
		array(
			'BackendModule' => 'index,render',
		),
		array(
			'access' => 'user,group',
			'icon' => 'EXT:' . $_EXTKEY . '/ext_icon.gif',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_mod1.xlf',
		)
	);
}

# ---------------------------------------------------------------------------
# Add ajax handler for backend module upload
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerAjaxHandler (
	'bm_pdf2content_mod1::processPdf',
	'BM\\BmPdf2content\\Controller\\BackendModuleController->processPdfAjax'
);
?>