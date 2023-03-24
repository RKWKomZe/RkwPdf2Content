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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\DataHandling\DataHandler;

define('KEY_CHILDREN', 'nodes');
define('KEY_ELEMENT_TYPE_CHAPTER', 'chapter');

// define type in json structure for tt_content text elements
define('KEY_ELEMENT_TYPE_TEXT', 'element');
// define other types here...

/**
 * Class RecordCreationService
 *
 * @author Birger Stöckelmann <stoeckelmann@bergisch-media.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwPdf2Content
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */
class RecordCreationService implements \TYPO3\CMS\Core\SingletonInterface
{

	/**
	 * @var array
	 */
	protected array $pageData = [];


	/**
	 * @var array
	 */
	protected array $pageDataMap = [];


	/**
	 * @var array
	 */
	protected array $elementsData = [];


	/**
	 * @var array
	 */
	protected array $elementsDataMap = [];


	/**
	 * @var \TYPO3\CMS\Core\DataHandling\DataHandler
	 * @TYPO3\CMS\Extbase\Annotation\Inject
	 */
	private DataHandler $tce;


	/**
	 * @var array
	 */
	private array $settings = [];


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
	 * Init method
     *
	 * @param string $json
	 * @param int $targetPageId
	 * @param string $firstPageTitle
     * @return void
	 */
	public function init(string $json, int $targetPageId, string $firstPageTitle): void
    {
		$this->initFirstPage($targetPageId, $firstPageTitle);
		$this->processData(
            json_decode($json, true),
            $this->pageData[0]['uid'],
            $this->pageData[0]['subpages']
        );
		$this->buildPageDataMap($this->pageData);
		$this->buildElementsDataMap($this->elementsData);
	}


	/**
	 * Main function - creates pages and elements
	 * @throws \TYPO3\CMS\Core\Error\Exception
	 */
	public function createRecords()
    {
		if (!is_array($this->pageDataMap) || count($this->pageDataMap) == 0) {
			throw new \TYPO3\CMS\Core\Error\Exception('no page data found!');
		}

		$recordDataMap = array_merge($this->pageDataMap, $this->elementsDataMap);

		$this->tce->stripslashes_values = 0;
		$this->tce->start($recordDataMap, array());
		$this->tce->process_datamap();

		// update the page tree
		BackendUtility::setUpdateSignal('updatePageTree');
	}


	/**
	 * Build content elements data map for process_datamap
	 *
     * @return void
	 */
	protected function buildElementsDataMap(): void
    {
		$rdata = array_reverse($this->elementsData);

		foreach ($rdata as $elementData) {

			// process text
			if(isset($elementData['text'])) {
				$text = str_replace('<br/>', '<br />', $elementData['text']);
			}

			// create a unique NEW id for content element
			$id = uniqid('NEW');
			switch ($elementData['type']) {

				// prepare text element data
				case(KEY_ELEMENT_TYPE_TEXT):
					$this->elementsDataMap['tt_content'][$id]['colPos'] = $this->settings['colpos'];
					$this->elementsDataMap['tt_content'][$id]['header'] = $elementData['title'];
					$this->elementsDataMap['tt_content'][$id]['bodytext'] = $text;
					$this->elementsDataMap['tt_content'][$id]['sectionIndex'] = 1;
					$this->elementsDataMap['tt_content'][$id]['pid'] = $elementData['pid'];
					break;

				// prepare other tt_content elements here...

			}
		}

		//die(var_dump($this->elementsDataMap));
	}


	/**
	 * Build page data map for process_datamap (recursively)
	 *
     * @param array $dataArray
     * @return void
	 */
	protected function buildPageDataMap(array $dataArray): void
    {
		foreach ($dataArray as $pageDataArray) {
			$this->pageDataMap['pages'][$pageDataArray['uid']] = array();
			$this->pageDataMap['pages'][$pageDataArray['uid']]['title'] = $pageDataArray['title'];
			$this->pageDataMap['pages'][$pageDataArray['uid']]['pid'] = $pageDataArray['pid'];
			$this->pageDataMap['pages'][$pageDataArray['uid']]['hidden'] = $pageDataArray['hidden'];
			$this->pageDataMap['pages'][$pageDataArray['uid']]['disabled'] = $pageDataArray['disabled'];
            $this->pageDataMap['pages'][$pageDataArray['uid']]['tx_rkwpdf2content_is_import'] = $pageDataArray['tx_rkwpdf2content_is_import'];
            $this->pageDataMap['pages'][$pageDataArray['uid']]['tx_rkwpdf2content_is_import_sub'] = $pageDataArray['tx_rkwpdf2content_is_import_sub'];
            if (is_array($pageDataArray['subpages']) && count($pageDataArray['subpages']) > 0) {
				$this->buildPageDataMap($pageDataArray['subpages']);
			}
		}
	}


	/**
	 * Creates the first page entry (parent page for pdf chapters)
     *
     * @param int $targetPageId
     * @param string $firstPageTitle
     * @return void
	 */
	protected function initFirstPage(int $targetPageId, string $firstPageTitle): void
    {
		$this->pageData[] = [
            'uid' => uniqid('NEW'),
            'title' => $firstPageTitle,
            'pid' => $targetPageId,
            'hidden' => 1,
            'disabled' => 0,
            'tx_rkwpdf2content_is_import' => 1,
            'tx_rkwpdf2content_is_import_sub' => 0,
            'subpages' => []
        ];
	}



	/**
	 * Creates flat pages and elements data (recursively)
	 * @param array $data
	 * @param string $parentId (maybe a string if new!)
	 * @param array $parentArray
     * @return void
	 */
	protected function processData(array $data, string $parentId, array &$parentArray = []): void
    {
		foreach ($data as $dataSet) {

			// chapter (pages) creation
			if ($dataSet['type'] == KEY_ELEMENT_TYPE_CHAPTER) {
				$currentId = uniqid('NEW');
				$tmpArray = array(
					'uid' => $currentId,
					'title' => $dataSet['title'],
					'pid' => $parentId,
					'hidden' => 0,
					'disabled' => 0,
                    'tx_rkwpdf2content_is_import' => 1,
                    'tx_rkwpdf2content_is_import_sub' => 1

				);
				// elements?
				if (is_array($dataSet[KEY_CHILDREN]) && count($dataSet[KEY_CHILDREN]) > 0) {
					$tmpArray['subpages'] = array();
					$this->processData($dataSet[KEY_CHILDREN], $currentId, $tmpArray['subpages']);
				}
				array_unshift($parentArray, $tmpArray);
			}

			else { // content element

				$this->elementsData[] = array(
					'uid' => uniqid('NEW'),
					'type' => $dataSet['type'],
					'title' => $dataSet['title'],
					'text' => $dataSet['text'],
					'pid' => $parentId
					// add other json properties for content elements here...
				);

			}
		}
	}
}
