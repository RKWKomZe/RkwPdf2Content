<?php
namespace RKW\RkwPdf2content\Service;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\FormProtection\Exception;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class PdfService
 *
 * @author Birger Stöckelmann <stoeckelmann@bergisch-media.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwPdf2Content
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */
class PdfService implements \TYPO3\CMS\Core\SingletonInterface
{

	/**
	 * @var array
	 */
	protected array $settings = [];


    /**
     * Logger
     *
     * @var \TYPO3\CMS\Core\Log\Logger|null
     */
    protected ?Logger $logger = null;


	/**
	 * @return array
	 */
	public function getSettings(): array
    {
		return $this->settings;
	}


	/**
	 * @param array $settings
	 */
	public function setSettings(array $settings): void
    {
		$this->settings = $settings;
	}


	/**
	 * Parses the PDF with Apache PDF Box and returns the result as HTML DOM
	 *
     * @param string $pdfFile
	 * @return string
	 * @throws Exception
	 */
	public function parsePdf(string $pdfFile): string
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
                    $this->getLogger()->log(
                        \TYPO3\CMS\Core\Log\LogLevel::ERROR,
                        'PDF2HTML Ergebnis ist leer!'
                    );
					throw new Exception(
                        LocalizationUtility::translate(
                            'be.msg.pdfservice_pdf2html_empty',
                            'Rkwpdf2content'
                        )
                    );
                }
			}
			else {
                $this->getLogger()->log(
                    \TYPO3\CMS\Core\Log\LogLevel::ERROR,
                    sprintf(
                        'PDF BOX JAR Datei konnte nicht gefunden werden. Setup-Pfad ist: %s',
                        array($this->settings['pdfBoxPath'])
                    )
                );
				throw new Exception(
                    LocalizationUtility::translate(
                        'be.msg.pdfservice_pdfbox_missing',
                        'Rkwpdf2content',
                        array($this->settings['pdfBoxPath'])
                    )
                );
			}

		}
		else {
            $this->getLogger()->log(
                \TYPO3\CMS\Core\Log\LogLevel::ERROR,
                'Upload- oder Kopier-Problem. PDF-Datei existiert nicht!'
            );
			throw new Exception(
                LocalizationUtility::translate(
                    'be.msg.pdfservice_pdffile_missing',
                    'Rkwpdf2content'
                )
            );
		}

		return $resultDom;
	}


    /**
     * Returns logger instance
     *
     * @return \TYPO3\CMS\Core\Log\Logger
     */
    protected function getLogger(): Logger
    {

        if (!$this->logger instanceof \TYPO3\CMS\Core\Log\Logger) {
            $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        }

        return $this->logger;
    }

}
