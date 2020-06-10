<?php

if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

# ---------------------------------------------------------------------------
# Register backend module for editing pdfs
if (TYPO3_MODE === 'BE') {
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'RKW.' . $_EXTKEY,
		'tools',
		'mod1',
		'',
		array(
			'BackendModule' => 'index,render',
		),
		array(
			'access' => 'user,group',
			'icon' => 'EXT:rkw_pdf2content/ext_icon.gif',
			'labels' => 'LLL:EXT:rkw_pdf2content/Resources/Private/Language/locallang_mod1.xlf',
		)
	);
}


$currentVersion = \TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version);
if ($currentVersion < 8000000) {
    # ---------------------------------------------------------------------------
    # Add ajax handler for backend module upload
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerAjaxHandler(
        'rkw_pdf2content_mod1::processPdf',
        'RKW\\RkwPdf2content\\Controller\\BackendModuleController->processPdfAjax'
    );
} else {
    // TAKE A LOOK HERE: /rkw_pdf2content/Configuration/Backend/AjaxRoutes.php
}

?>