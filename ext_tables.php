<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function($extKey)
    {

        # ---------------------------------------------------------------------------
        # Register backend module for editing pdfs
        if (TYPO3_MODE === 'BE') {
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
                'RKW.' . $extKey,
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

    },
    'rkw_pdf2content'
);
