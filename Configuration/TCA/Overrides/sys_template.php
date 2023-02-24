<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
ExtensionManagementUtility::addStaticFile('rkw_pdf2content', 'Configuration/TypoScript', 'RKW PDF2Content');
