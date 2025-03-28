<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "RKW_Pdf2Content".
 */

$EM_CONF[$_EXTKEY] = array(
    'title' => 'RKW PDF2Content',
    'description' => 'Extract text from PDFs and create TYPO3 sites with it!',
    'category' => 'backend',
    'version' => '10.4.1',
    'state' => 'stable',
    'uploadfolder' => false,
    'clearcacheonload' => true,
    'author' => 'Guido Wehner, Birger Stöckelmann, Maximilian Fäßler,Steffen Kroggel',
    'author_email' => 'wehner@bergisch-media.de, stoeckelmann@bergisch-media.de, maximilian@faesslerweb.de, developer@steffenkroggel.de',
    'author_company' => 'Bergisch Media GmbH',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-10.4.99',
            'core_extended' => '10.4.0-12.4.99',
            'rkw_basics' => '10.4.0-12.4.99',
        ],
        'conflicts' => [
            'bm_pdf2content' => ''
        ],
        'suggests' => [
        ],
    ],
    'module' => 'mod1'
);

