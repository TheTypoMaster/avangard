<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage sale
 * @copyright 2001-2012 Bitrix
 */
namespace Bitrix\Sale;

use Bitrix\Main\Entity;

class ProductTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'b_catalog_product';
	}

	public static function getMap()
	{
		// Get weight factor
		$siteId = '';
		$weight_koef = 0;
		if (class_exists('\CBaseSaleReportHelper'))
		{
			if (\CBaseSaleReportHelper::isInitialized())
			{
				$siteId = \CBaseSaleReportHelper::getDefaultSiteId();
				if ($siteId !== null)
				{
					$weight_koef = intval(\CBaseSaleReportHelper::getDefaultSiteWeightDivider());
				}
			}
		}
		if ($weight_koef <= 0) $weight_koef = 1;

		// Get site currency
		$site_currency = \COption::GetOptionString('sale', 'default_currency', null, ($siteId !== null) ? $siteId : false);

		global $DB, $DBType;

		$fieldsMap = array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true
			),
			/*'IBLOCK_ID' => array(
				'data_type' => 'integer'
			),*/
			'TIMESTAMP_X' => array(
				'data_type' => 'integer'
			),
			'DATE_UPDATED' => array(
				'data_type' => 'datetime',
				'expression' => array(
					$DB->DatetimeToDateFunction('%s'), 'TIMESTAMP_X',
				)
			),
			'QUANTITY' => array(
				'data_type' => 'float'
			),
			'IBLOCK' => array(
				'data_type' => 'Bitrix\Iblock\Element',
				'reference' => array('=this.ID' => 'ref.ID')
			),
			'NAME' => array(
				'data_type' => 'string',
				'expression' => array(
					'%s', 'IBLOCK.NAME'
				)
			),
			'ACTIVE' => array(
				'data_type' => 'boolean',
				'expression' => array(
					'%s', 'IBLOCK.ACTIVE'
				),
				'values' => array('N','Y')
			),
			'WEIGHT' => array(
				'data_type' => 'float'
			),
			'WEIGHT_IN_SITE_UNITS' => array(
				'data_type' => 'float',
				'expression' => array(
					'%s / '.$DB->ForSql($weight_koef), 'WEIGHT'
				)
			),
			'PRICE' => array(
				'data_type' => 'float',
				'expression' => array(
					'(SELECT b_catalog_price.PRICE FROM b_catalog_price
						LEFT JOIN b_catalog_group ON b_catalog_group.ID = b_catalog_price.CATALOG_GROUP_ID
					WHERE
						b_catalog_price.PRODUCT_ID = %s
						AND
						b_catalog_group.base = \'Y\'
						AND
						( b_catalog_price.quantity_from <= 1 OR b_catalog_price.quantity_from IS NULL )
						AND
						( b_catalog_price.quantity_to >= 1 OR b_catalog_price.quantity_to IS NULL))', 'ID'
				)
			),
			'CURRENCY' => array(
				'data_type' => 'string',
				'expression' => array(
					'(SELECT b_catalog_price.CURRENCY FROM b_catalog_price
						LEFT JOIN b_catalog_group ON b_catalog_group.ID = b_catalog_price.CATALOG_GROUP_ID
					WHERE
						b_catalog_price.PRODUCT_ID = %s
						AND
						b_catalog_group.base = \'Y\'
						AND
						( b_catalog_price.quantity_from <= 1 OR b_catalog_price.quantity_from IS NULL )
						AND
						( b_catalog_price.quantity_to >= 1 OR b_catalog_price.quantity_to IS NULL))', 'ID'
				)
			),
			'SUMMARY_PRICE' => array(
				'data_type' => 'float',
				'expression' => array(
					'%s * %s', 'QUANTITY', 'PRICE'
				),
			),



			'CURRENT_CURRENCY_RATE' => array(
				'data_type' => 'float',
				'expression' => array(
					$DBType === 'oracle'
					? '(SELECT r FROM (SELECT b_catalog_currency.CURRENCY c, b_catalog_product.ID i, (CASE WHEN b_catalog_currency_rate.RATE IS NOT NULL THEN b_catalog_currency_rate.RATE ELSE b_catalog_currency.AMOUNT END) r
					FROM b_catalog_product INNER JOIN b_catalog_currency ON 1=1
						LEFT JOIN b_catalog_currency_rate ON (b_catalog_currency.CURRENCY = b_catalog_currency_rate.CURRENCY AND b_catalog_currency_rate.DATE_RATE <= '.$DB->DatetimeToDateFunction('b_catalog_product.TIMESTAMP_X').')
					ORDER BY DATE_RATE DESC) WHERE i = %s AND c = %s AND ROWNUM = 1)'
					: '('.$DB->TopSql('SELECT (CASE WHEN b_catalog_currency_rate.RATE IS NOT NULL THEN b_catalog_currency_rate.RATE ELSE b_catalog_currency.AMOUNT END)
					FROM b_catalog_product INNER JOIN b_catalog_currency ON 1=1
						LEFT JOIN b_catalog_currency_rate ON (b_catalog_currency.CURRENCY = b_catalog_currency_rate.CURRENCY AND b_catalog_currency_rate.DATE_RATE <= '.$DB->DatetimeToDateFunction('b_catalog_product.TIMESTAMP_X').')
					WHERE b_catalog_product.ID = %s AND b_catalog_currency.CURRENCY = %s
					ORDER BY DATE_RATE DESC', 1).')', 'ID', 'CURRENCY'
				)
			),
			'CURRENT_CURRENCY_RATE_CNT' => array(
				'data_type' => 'float',
				'expression' => array(
					$DBType === 'oracle'
						? '(SELECT r FROM (SELECT b_catalog_currency.CURRENCY c, b_catalog_product.ID i, (CASE WHEN b_catalog_currency_rate.RATE_CNT IS NOT NULL THEN b_catalog_currency_rate.RATE_CNT ELSE b_catalog_currency.AMOUNT_CNT END) r
					FROM b_catalog_product INNER JOIN b_catalog_currency ON 1=1
						LEFT JOIN b_catalog_currency_rate ON (b_catalog_currency.CURRENCY = b_catalog_currency_rate.CURRENCY AND b_catalog_currency_rate.DATE_RATE <= '.$DB->DatetimeToDateFunction('b_catalog_product.TIMESTAMP_X').')
					ORDER BY DATE_RATE DESC) WHERE i = %s AND c = %s AND ROWNUM = 1)'
						: '('.$DB->TopSql('SELECT (CASE WHEN b_catalog_currency_rate.RATE_CNT IS NOT NULL THEN b_catalog_currency_rate.RATE_CNT ELSE b_catalog_currency.AMOUNT_CNT END)
					FROM b_catalog_product INNER JOIN b_catalog_currency ON 1=1
						LEFT JOIN b_catalog_currency_rate ON (b_catalog_currency.CURRENCY = b_catalog_currency_rate.CURRENCY AND b_catalog_currency_rate.DATE_RATE <= '.$DB->DatetimeToDateFunction('b_catalog_product.TIMESTAMP_X').')
					WHERE b_catalog_product.ID = %s AND b_catalog_currency.CURRENCY = %s
					ORDER BY DATE_RATE DESC', 1).')', 'ID', 'CURRENCY'
				)
			),

			'CURRENT_SITE_CURRENCY_RATE' => array(
				'data_type' => 'float',
				'expression' => array(
					$DBType === 'oracle'
						? '(SELECT r FROM (SELECT b_catalog_product.ID i, (CASE WHEN b_catalog_currency_rate.RATE IS NOT NULL THEN b_catalog_currency_rate.RATE ELSE b_catalog_currency.AMOUNT END) r
					FROM b_catalog_product INNER JOIN b_catalog_currency ON 1=1
						LEFT JOIN b_catalog_currency_rate ON (b_catalog_currency.CURRENCY = b_catalog_currency_rate.CURRENCY AND b_catalog_currency_rate.DATE_RATE <= '.$DB->DatetimeToDateFunction('b_catalog_product.TIMESTAMP_X').')
					WHERE b_catalog_currency.CURRENCY = \''.$DB->ForSql($site_currency).'\'
					ORDER BY DATE_RATE DESC) WHERE i = %s AND ROWNUM = 1)'
						: '('.$DB->TopSql('SELECT (CASE WHEN b_catalog_currency_rate.RATE IS NOT NULL THEN b_catalog_currency_rate.RATE ELSE b_catalog_currency.AMOUNT END)
					FROM b_catalog_product INNER JOIN b_catalog_currency ON 1=1
						LEFT JOIN b_catalog_currency_rate ON (b_catalog_currency.CURRENCY = b_catalog_currency_rate.CURRENCY AND b_catalog_currency_rate.DATE_RATE <= '.$DB->DatetimeToDateFunction('b_catalog_product.TIMESTAMP_X').')
					WHERE b_catalog_product.ID = %s AND b_catalog_currency.CURRENCY = \''.$DB->ForSql($site_currency).'\'
					ORDER BY DATE_RATE DESC', 1).')', 'ID'
				)
			),

			'CURRENT_SITE_CURRENCY_RATE_CNT' => array(
				'data_type' => 'float',
				'expression' => array(
					$DBType === 'oracle'
						? '(SELECT r FROM (SELECT b_catalog_product.ID i, (CASE WHEN b_catalog_currency_rate.RATE_CNT IS NOT NULL THEN b_catalog_currency_rate.RATE_CNT ELSE b_catalog_currency.AMOUNT_CNT END) r
					FROM b_catalog_product INNER JOIN b_catalog_currency ON 1=1
						LEFT JOIN b_catalog_currency_rate ON (b_catalog_currency.CURRENCY = b_catalog_currency_rate.CURRENCY AND b_catalog_currency_rate.DATE_RATE <= '.$DB->DatetimeToDateFunction('b_catalog_product.TIMESTAMP_X').')
					WHERE b_catalog_currency.CURRENCY = \''.$DB->ForSql($site_currency).'\'
					ORDER BY DATE_RATE DESC) WHERE i = %s AND ROWNUM = 1)'
						: '('.$DB->TopSql('SELECT (CASE WHEN b_catalog_currency_rate.RATE_CNT IS NOT NULL THEN b_catalog_currency_rate.RATE_CNT ELSE b_catalog_currency.AMOUNT_CNT END)
					FROM b_catalog_product INNER JOIN b_catalog_currency ON 1=1
						LEFT JOIN b_catalog_currency_rate ON (b_catalog_currency.CURRENCY = b_catalog_currency_rate.CURRENCY AND b_catalog_currency_rate.DATE_RATE <= '.$DB->DatetimeToDateFunction('b_catalog_product.TIMESTAMP_X').')
					WHERE b_catalog_product.ID = %s AND b_catalog_currency.CURRENCY = \''.$DB->ForSql($site_currency).'\'
					ORDER BY DATE_RATE DESC', 1).')', 'ID'
				)
			),



			'PRICE_IN_SITE_CURRENCY' => array(
				'data_type' => 'float',
				'expression' => array(
					'%s * (%s * %s / %s / %s)',
					'PRICE', 'CURRENT_CURRENCY_RATE', 'CURRENT_SITE_CURRENCY_RATE_CNT', 'CURRENT_SITE_CURRENCY_RATE', 'CURRENT_CURRENCY_RATE_CNT'
				)
			),

			'SUMMARY_PRICE_IN_SITE_CURRENCY' => array(
				'data_type' => 'float',
				'expression' => array(
					'%s * (%s * %s / %s / %s)',
					'SUMMARY_PRICE', 'CURRENT_CURRENCY_RATE', 'CURRENT_SITE_CURRENCY_RATE_CNT', 'CURRENT_SITE_CURRENCY_RATE', 'CURRENT_CURRENCY_RATE_CNT'
				)
			),

			'VIEWS_IN_PERIOD_BY_SHOP' => array(
				'data_type' => 'integer',
				'expression' => array(
					'(SELECT  SUM(1) FROM b_catalog_product, b_sale_viewed_product WHERE %s = b_sale_viewed_product.PRODUCT_ID
					AND b_catalog_product.ID = b_sale_viewed_product.PRODUCT_ID
					AND b_sale_viewed_product.DATE_VISIT %%RT_TIME_INTERVAL%% AND b_sale_viewed_product.LID %%RT_SITE_FILTER%%)', 'ID'
				)
			),

			'ORDERS_IN_PERIOD_BY_SHOP' => array(
				'data_type' => 'integer',
				'expression' => array(
					'(SELECT  COUNT(DISTINCT b_sale_order.ID)
					FROM b_catalog_product
						INNER JOIN b_sale_basket ON b_catalog_product.ID = b_sale_basket.PRODUCT_ID
						INNER JOIN b_sale_order ON b_sale_basket.ORDER_ID = b_sale_order.ID
					WHERE
							b_catalog_product.ID = %s
						AND b_sale_order.PAYED = \'Y\'
						AND b_sale_order.DATE_INSERT %%RT_TIME_INTERVAL%%
						AND b_sale_basket.LID %%RT_SITE_FILTER%%)', 'ID'
				)
			),
			'SALED_PRODUCTS_IN_PERIOD_BY_SHOP' => array(
				'data_type' => 'integer',
				'expression' => array(
					'(SELECT  SUM(b_sale_basket.QUANTITY)
					FROM b_catalog_product
						INNER JOIN b_sale_basket ON b_catalog_product.ID = b_sale_basket.PRODUCT_ID
						INNER JOIN b_sale_order ON b_sale_basket.ORDER_ID = b_sale_order.ID
					WHERE
							b_catalog_product.ID = %s
						AND b_sale_order.PAYED = \'Y\'
						AND b_sale_order.DATE_INSERT %%RT_TIME_INTERVAL%%
						AND b_sale_basket.LID %%RT_SITE_FILTER%%)', 'ID'
				)
			),
			'CONVERSION' => array(
				'data_type' => 'float',
				'expression' => array(
					'100 * CASE WHEN %s IS NULL OR %s = 0 THEN NULL ELSE %s / %s END',
					'VIEWS_IN_PERIOD_BY_SHOP', 'VIEWS_IN_PERIOD_BY_SHOP', 'ORDERS_IN_PERIOD_BY_SHOP', 'VIEWS_IN_PERIOD_BY_SHOP'
				)
			)
		);

		return $fieldsMap;
	}
}
