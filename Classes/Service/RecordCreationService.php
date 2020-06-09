<?php

namespace BM\BmPdf2content\Service;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

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

define('KEY_CHILDREN', 'nodes');
define('KEY_ELEMENT_TYPE_CHAPTER', 'chapter');

// define type in json structure for tt_content text elements
define('KEY_ELEMENT_TYPE_TEXT', 'element');
// define other types here...

/**
 *
 * @package BM_Pdf2Content
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 2 or later
 *
 */
class RecordCreationService implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * @var array
	 */
	protected $pageData = array();

	/**
	 * @var array
	 */
	protected $pageDataMap = array();

	/**
	 * @var array
	 */
	protected $elementsData = array();

	/**
	 * @var array
	 */
	protected $elementsDataMap = array();

	/**
	 * @var \TYPO3\CMS\Core\DataHandling\DataHandler
	 * @inject
	 */
	private $tce;

	/**
	 * @var array
	 */
	private $settings;

	/**
	 * @return array
	 */
	public function getSettings() {
		return $this->settings;
	}

	/**
	 * @param array $settings
	 */
	public function setSettings($settings) {
		$this->settings = $settings;
	}

	/**
	 * Init method
	 * @param string $json
	 * @param integer $targetPageId
	 * @param string $firstPageTitle
	 */
	public function init($json, $targetPageId, $firstPageTitle) {
		$this->initFirstPage($targetPageId, $firstPageTitle);
		$this->processData(json_decode($json, TRUE), $this->pageData[0]['uid'], $this->pageData[0]['subpages']);
		$this->buildPageDataMap($this->pageData);
		$this->buildElementsDataMap($this->elementsData);
	}

	/**
	 * Main function - creates pages and elements
	 * @throws \TYPO3\CMS\Core\Error\Exception
	 */
	public function createRecords() {

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

	// ----------------------------------------------------------------------

	/**
	 * Build content elements data map for process_datamap
	 *
	 */
	protected function buildElementsDataMap() {

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
	 * @param array $dataArray
	 */
	protected function buildPageDataMap($dataArray) {
		foreach ($dataArray as $pageDataArray) {
			$this->pageDataMap['pages'][$pageDataArray['uid']] = array();
			$this->pageDataMap['pages'][$pageDataArray['uid']]['title'] = $pageDataArray['title'];
			$this->pageDataMap['pages'][$pageDataArray['uid']]['pid'] = $pageDataArray['pid'];
			$this->pageDataMap['pages'][$pageDataArray['uid']]['hidden'] = $pageDataArray['hidden'];
			$this->pageDataMap['pages'][$pageDataArray['uid']]['disabled'] = $pageDataArray['disabled'];
            $this->pageDataMap['pages'][$pageDataArray['uid']]['tx_bmpdf2content_is_import'] = $pageDataArray['tx_bmpdf2content_is_import'];
            $this->pageDataMap['pages'][$pageDataArray['uid']]['tx_bmpdf2content_is_import_sub'] = $pageDataArray['tx_bmpdf2content_is_import_sub'];
            if (is_array($pageDataArray['subpages']) && count($pageDataArray['subpages']) > 0) {
				$this->buildPageDataMap($pageDataArray['subpages']);
			}
		}
	}

	/**
	 * Creates the first page entry (parent page for pdf chapters)
	 * @param string $firstPageTitle
	 * @param integer $targetPageId
	 */
	protected function initFirstPage($targetPageId, $firstPageTitle) {
		array_push($this->pageData, array(
			'uid' => uniqid('NEW'),
			'title' => $firstPageTitle,
			'pid' => $targetPageId,
			'hidden' => 1,
			'disabled' => 0,
            'tx_bmpdf2content_is_import' => 1,
            'tx_bmpdf2content_is_import_sub' => 0,
			'subpages' => array()
		));
	}

	/**
	 * Creates flat pages and elements data (recursively)
	 * @param array $data
	 * @param integer $parentId
	 * @param array $parentArray
	 */
	protected function processData($data, $parentId, &$parentArray = array()) {

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
                    'tx_bmpdf2content_is_import' => 1,
                    'tx_bmpdf2content_is_import_sub' => 1

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