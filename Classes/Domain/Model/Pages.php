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
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwPdf2Content
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class Pages extends \Madj2k\CoreExtended\Domain\Model\Pages
{

    /**
     * @var bool
     */
    protected bool $txRkwpdf2contentIsImport = false;


    /**
     * @var bool
     */
    protected bool $txRkwpdf2contentIsImportSub = false;


    /**
     * Returns the txRkwpdf2contentIsImport
     *
     * @return bool
     */
    public function getRkwpdf2contentIsImport(): bool
    {
        return $this->txRkwpdf2contentIsImport;
    }


    /**
     * Sets the txRkwpdf2contentEtrackerIsImport
     *
     * @param bool $txRkwpdf2contentIsImport
     */
    public function setTxRkwpdf2contentIsImport(bool $txRkwpdf2contentIsImport) {
        $this->txRkwpdf2contentIsImport = $txRkwpdf2contentIsImport;
    }


    /**
     * Returns the txRkwpdf2contentIsImportSub
     *
     * @return bool
     */
    public function getRkwpdf2contentIsImportSub(): bool
    {
        return $this->txRkwpdf2contentIsImportSub;
    }


    /**
     * Sets the txRkwpdf2contentIsImportSub
     *
     * @param bool $txRkwpdf2contentIsImportSub
     * @return void
     */
    public function setTxRkwpdf2contentIsImportSub(bool $txRkwpdf2contentIsImportSub): void
    {
        $this->txRkwpdf2contentIsImportSub = $txRkwpdf2contentIsImportSub;
    }

}
