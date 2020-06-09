<?php

namespace BM\BmPdf2content\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;
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

/**
 *
 *
 * @package BM_PDF2Content
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class PageTreeService {

	/**
	 * @var array
	 */
	protected $fePageTree;

	/**
	 * Gets the frontend page tree from starting point
	 * @param integer $pid
	 */
	public function initFePageTree($pid) {

		$page = $GLOBALS['TSFE']->sys_page->getPage($pid);

		if ($page['uid']) {
			$this->fePageTree[] = array(
				'title' => $page['title'],
				'uid' => $page['uid'],
				'level' => 0
			);
			$this->processSubtree($pid, 1);
		}
	}

	/**
	 * Gets the fe page tree
	 * @return array
	 */
	public function getFePageTree() {
		return $this->fePageTree;
	}

	/**
	 * Build page tree (recursively)
	 * @param $pid
	 */
	private function processSubtree($pid, $level) {
		$menu = $GLOBALS['TSFE']->sys_page->getMenu($pid, 'uid, title', 'sorting');
		if (count($menu) > 0) {
			foreach ($menu as $menuItem) {
				$menuItem['level'] = $level;
				$this->fePageTree[] = $menuItem;
				$this->processSubtree($menuItem['uid'], $level + 1);
			}
		}
	}


}