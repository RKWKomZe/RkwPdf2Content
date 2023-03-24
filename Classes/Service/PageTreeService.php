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

/**
 * Class PageTreeService
 *
 * @author Birger Stöckelmann <stoeckelmann@bergisch-media.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwPdf2Content
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later

 */
class PageTreeService
{

	/**
	 * @var array
	 */
	protected array $fePageTree = [];


	/**
	 * Gets the frontend page tree from starting point
     *
	 * @param int $pid
     * @return void
	 */
	public function initFePageTree(int $pid): void
    {

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
     *
	 * @return array
	 */
	public function getFePageTree(): array
    {
		return $this->fePageTree;
	}


	/**
	 * Build page tree (recursively)
     *
	 * @param int $pid
     * @param int $level
     * @return void
	 */
	private function processSubtree(int $pid, int $level): void
    {
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
