<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function (string $extKey) {

        # ==================================================================
        # Register plugin for displaying pages and add flexform config
        # ==================================================================
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'RKW.rkw_pdf2content',
            'Pi1',
            'RKW PDF2Content Display Pages'
        );
    },
    'rkw_pdf2content'
);
