<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "bm_pdf2content".
 */

$EM_CONF[$_EXTKEY] = array(
    'title' => 'PDF2Content',
    'description' => 'Extract text from PDFs and create TYPO3 sites with it!',
    'category' => 'backend',
    'version' => '0.1.0',
    'state' => 'beta',
    'uploadfolder' => false,
    'createDirs' => '',
    'clearcacheonload' => true,
    'author' => 'Guido Wehner, Birger Stöckelmann',
    'author_email' => 'wehner@bergisch-media.de, stoeckelmann@bergisch-media.de',
    'author_company' => 'Bergisch Media GmbH',
    'constraints' =>
        array(
            'depends' =>
                array(
                    'typo3' => '6.2.0-7.6.*',
                    'extbase' => '6.2.0-7.6.*',
                    'fluid' => '6.2.0-7.6.*',
                ),
            'conflicts' =>
                array(),
            'suggests' =>
                array(),
        ),
    'module' => 'mod1'
);

