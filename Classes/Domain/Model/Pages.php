<?php
namespace RKW\RkwPdf2content\Domain\Model;

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
 * Class Pages
 *
 * @package RKW_Pdf2Content
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class Pages extends \RKW\RkwSearch\Domain\Model\Pages
{
    /**
     * uid
     * @var int
     * @validate NotEmpty
     */
    protected $uid;


    /**
     * pid
     * @var int
     * @validate NotEmpty
     */
    protected $pid;


    /**
     * sorting
     * @var int
     * @validate NotEmpty
     */
    protected $sorting;


    /**
     * title
     *
     * @var string
     */
    protected $title;


    /**
     * subtitle
     *
     * @var string
     */
    protected $subtitle;



    /**
     * pubdate
     *
     * @var integer
     */
    protected $pubDate;


	/**
	 * txRkwpdf2contentIsImport
	 *
	 * @var \integer
	 */
	protected $txRkwpdf2contentIsImport;


    /**
     * txRkwpdf2contentIsImportSub
     *
     * @var \integer
     */
    protected $txRkwpdf2contentIsImportSub;


    /**
     * Returns the pid
     *
     * @return int $pid
     */
    public function getPid() {
        return $this->pid;
        //===
    }


    /**
     * Returns the sorting
     *
     * @return int $sorting
     */
    public function getSorting() {
        return $this->sorting;
        //===
    }

    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle() {
        return $this->title;
        //===
    }

    /**
     * Returns the subtitle
     *
     * @return string $subtitle
     */
    public function getSubtitle() {
        return $this->subtitle;
        //===
    }

    /**
     * Returns the pubDate
     *
     * @return integer
     */
    public function getPubDate() {
        return $this->pubDate;
    }

    /**
     * Sets the pubDate
     *
     * @param integer $pubDate
     * @return void
     */
    public function setPubdate($pubDate) {
        $this->pubDate = $pubDate;
    }


    /**
     * Returns the txRkwpdf2contentIsImport
     *
     * @return \string txRkwpdf2contentIsImport
     */
    public function getRkwpdf2contentIsImport() {
        return $this->txRkwpdf2contentIsImport;
    }

    /**
     * Sets the txRkwpdf2contentEtrackerIsImport
     *
     * @param \string $txRkwpdf2contentIsImport
     * @return \string txRkwpdf2contentIsImport
     */
    public function settxRkwpdf2contentIsImport($txRkwpdf2contentIsImport) {
        $this->txRkwpdf2contentIsImport = $txRkwpdf2contentIsImport;
    }

    /**
     * Returns the txRkwpdf2contentIsImportSub
     *
     * @return \string txRkwpdf2contentIsImportSub
     */
    public function getRkwpdf2contentIsImportSub() {
        return $this->txRkwpdf2contentIsImportSub;
    }

    /**
     * Sets the txRkwpdf2contentIsImportSub
     *
     * @param \string $txRkwpdf2contentIsImportSub
     * @return \string txRkwpdf2contentIsImportSub
     */
    public function settxRkwpdf2contentIsImportSub($txRkwpdf2contentIsImportSub) {
        $this->txRkwpdf2contentIsImportSub = $txRkwpdf2contentIsImportSub;
    }


    /**
     * __construct
     */
    public function __construct() {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     *
     * @return void
     */
    protected function initStorageObjects() {

    }

}
?>