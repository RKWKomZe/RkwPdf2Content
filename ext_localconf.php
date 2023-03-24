<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function($extKey)
    {

        // Configure plugins
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'RKW.' . $extKey,
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

        //=================================================================
        // Register Logger
        //=================================================================
        $GLOBALS['TYPO3_CONF_VARS']['LOG']['RKW']['RkwPdf2content']['writerConfiguration'] = array(

            // configuration for WARNING severity, including all
            // levels with higher severity (ERROR, CRITICAL, EMERGENCY)
            \TYPO3\CMS\Core\Log\LogLevel::DEBUG => array(

                // add a FileWriter
                'TYPO3\\CMS\\Core\\Log\\Writer\\FileWriter' => array(
                    // configuration for the writer
                    'logFile' => \TYPO3\CMS\Core\Core\Environment::getVarPath()  . '/log/tx_rkwpdf2content.log'
                )
            ),
        );

    },
    $_EXTKEY
);
