<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "RKW_Pdf2Content".
 */

$EM_CONF[$_EXTKEY] = array(
    'title' => 'RKW PDF2Content',
    'description' => 'Extract text from PDFs and create TYPO3 sites with it!',
    'category' => 'backend',
    'version' => '7.6.0',
    'state' => 'beta',
    'uploadfolder' => false,
    'createDirs' => '',
    'clearcacheonload' => true,
    'author' => 'Guido Wehner, Birger Stöckelmann',
    'author_email' => 'wehner@bergisch-media.de, stoeckelmann@bergisch-media.de',
    'author_company' => 'Bergisch Media GmbH',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.0-8.7.99',
        ],
        'conflicts' => [
            'bm_pdf2content' => ''
        ],
        'suggests' => [
        ],
    ],
    'module' => 'mod1'
);

