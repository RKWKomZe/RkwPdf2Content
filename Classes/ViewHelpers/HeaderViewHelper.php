<?php

namespace BM\BmPdf2content\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Birger Stöckelmann <stoeckelmann@bergisch-media.de>
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

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Configuration\FrontendConfigurationManager;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 *
 * Allows rendering of defined tags (h1) for a given level
 *
 * @package BM_PDF2Content
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class HeaderViewHelper extends AbstractTagBasedViewHelper {

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     * @inject
     */
    protected $configurationManager;

    /**
     * Plugin Typoscript settings
     * @var array
     */
    protected $settings;

    /**
     * Overwritten by header mapping if set in Typoscript setup
     * @var string
     */
    protected $tagName = 'h1';

    /**
     * Default attributes init
     */
    public function initializeArguments() {
        $this->registerUniversalTagAttributes();
    }

    /**
     * Get the plugin settings from Configuration Manager
     */
    public function initialize() {
        parent::initialize();
        $this->settings = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS);
    }

    /**
     * Renders the header tag with given mapping
     * @param integer $level
     * @param bool $useMapping
     * @param integer $levelAddition
     * @return string
     */
    public function render($level, $useMapping = TRUE, $levelAddition = 0) {

        if (isset($this->settings['headerMapping'][$level]) && $useMapping == TRUE) {
            $this->tag->setTagName($this->settings['headerMapping'][$level]);
        } else if ($useMapping == FALSE) {
            $this->tag->setTagName('h' . $level);
            if ($levelAddition > 0) {
                $this->tag->setTagName('h' . ($level + $levelAddition));
            }
        }

        $this->tag->setContent($this->renderChildren());
        $this->tag->forceClosingTag(TRUE);

        return $this->tag->render();

    }

}