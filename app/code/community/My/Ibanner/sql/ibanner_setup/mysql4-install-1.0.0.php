<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    My
 * @package     My_Ibanner
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS `{$this->getTable('ibanner/banner')}`;
CREATE TABLE `{$this->getTable('ibanner/banner')}` (
  `banner_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NULL DEFAULT '',
  `position` varchar(128) NULL DEFAULT '',
  `sort_order` smallint(5) NULL DEFAULT 0,
  `is_active` tinyint(1) NULL DEFAULT '1',
  PRIMARY KEY (`banner_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='My Ibanner' ;

-- DROP TABLE IF EXISTS `{$this->getTable('ibanner/banner_image')}`;
CREATE TABLE `{$this->getTable('ibanner/banner_image')}` (
  `image_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `position` smallint(5) DEFAULT '0',
  `disabled` tinyint(1) DEFAULT '1',
  `banner_id` smallint(6) DEFAULT '0',
  PRIMARY KEY (`image_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='My Ibanner Image' ;

-- DROP TABLE IF EXISTS `{$this->getTable('ibanner/banner_category')}`;
CREATE TABLE `{$this->getTable('ibanner/banner_category')}` (
  `banner_id` smallint(6) NOT NULL,
  `category_id` smallint(6) NOT NULL,
  PRIMARY KEY (`banner_id`,`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='My Ibanner Category' ;

-- DROP TABLE IF EXISTS `{$this->getTable('ibanner/banner_page')}`;
CREATE TABLE `{$this->getTable('ibanner/banner_page')}` (
  `banner_id` smallint(6) NOT NULL,
  `page_id` smallint(6) NOT NULL,
  PRIMARY KEY (`banner_id`,`page_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='My Ibanner Page' ;

-- DROP TABLE IF EXISTS `{$this->getTable('ibanner/banner_store')}`;
CREATE TABLE `{$this->getTable('ibanner/banner_store')}` (
  `banner_id` smallint(6) NOT NULL,
  `store_id` smallint(6) NOT NULL,
  PRIMARY KEY (`banner_id`,`store_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='My Ibanner Store' ;

    ");

$installer->endSetup();