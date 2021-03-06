<?php

namespace RKW\RkwPdf2content\Service;

use TYPO3\CMS\Core\FormProtection\Exception;
use TYPO3\CMS\Core\Utility\File\BasicFileUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Birger Stöckelmann <stoeckelmann@bergisch-media.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 *
 *
 * @package RKW_Pdf2Content
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class PdfService implements \TYPO3\CMS\Core\SingletonInterface
{
	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @return array
	 */
	public function getSettings()
    {
		return $this->settings;
	}

	/**
	 * @param array $settings
	 */
	public function setSettings($settings)
    {
		$this->settings = $settings;
	}

    /**
     * Logger
     *
     * @var \TYPO3\CMS\Core\Log\Logger
     */
    protected $logger;

	/**
	 * Parses the PDF with Apache PDF Box and returns the result as HTML DOM
	 * @param $pdfFile
	 * @return mixed|string
	 * @throws Exception
	 */
	public function parsePdf($pdfFile)
    {
		$resultDom = '';
		$pdfBoxPath = GeneralUtility::getFileAbsFileName($this->settings['pdfBoxPath']);

		// pdf path found?
		if (file_exists($pdfFile)) {

			if (file_exists($pdfBoxPath)) {

				$command = 'java -jar ' . $pdfBoxPath . ' ExtractText -html -console -ignoreBeads ' . $pdfFile;
				$resultDom = shell_exec($command);
				$resultDom = strip_tags($resultDom, '<p><b><i><u><div>');
				$resultDom = str_replace('<div style="page-break-before:always; page-break-after:always"><div>', '<span class="page_break"></span>', $resultDom);
				$resultDom = str_replace('</div></div>', '', $resultDom);

				if($resultDom == '') {
                    $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::ERROR, sprintf('PDF2HTML Ergebnis ist leer!'));
					throw new Exception(LocalizationUtility::translate('be.msg.pdfservice_pdf2html_empty', 'Rkwpdf2content'));
                }

			}
			else {
                $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::ERROR, sprintf('PDF BOX JAR Datei konnte nicht gefunden werden. Setup-Pfad ist: %s',  array($this->settings['pdfBoxPath'])));
				throw new Exception(LocalizationUtility::translate('be.msg.pdfservice_pdfbox_missing', 'Rkwpdf2content', array($this->settings['pdfBoxPath'])));
			}

		}
		else {
            $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::ERROR, sprintf('Upload- oder Kopier-Problem. PDF-Datei existiert nicht!'));
			throw new Exception(LocalizationUtility::translate('be.msg.pdfservice_pdffile_missing', 'Rkwpdf2content'));
		}

		return $resultDom;

	}



    /**
     * Returns logger instance
     *
     * @return \TYPO3\CMS\Core\Log\Logger
     */
    protected function getLogger()
    {

        if (!$this->logger instanceof \TYPO3\CMS\Core\Log\Logger) {
            $this->logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Log\\LogManager')->getLogger(__CLASS__);
        }

        return $this->logger;
        //===
    }

}