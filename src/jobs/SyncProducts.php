<?php
/**
 * Mailchimp for Craft Commerce
 *
 * @link      https://ethercreative.co.uk
 * @copyright Copyright (c) 2019 Ether Creative
 */

namespace ether\mc\jobs;

use craft\db\QueryAbortedException;
use craft\errors\SiteNotFoundException;
use craft\queue\BaseJob;
use craft\queue\QueueInterface;
use ether\mc\MailchimpCommerce;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\queue\Queue;

/**
 * Class SyncProducts
 *
 * @author  Ether Creative
 * @package ether\mc\jobs
 */
class SyncProducts extends BaseJob
{

	// Properties
	// =========================================================================

	public $productIds = [];
	public $productName = 'Products';

	// Methods
	// =========================================================================

	/**
	 * @param Queue|QueueInterface $queue The queue the job belongs to
	 *
	 * @throws Exception
	 * @throws QueryAbortedException
	 * @throws SiteNotFoundException
	 * @throws InvalidConfigException
	 */
	public function execute ($queue)
	{
		$products = MailchimpCommerce::$i->products;
		$i = 0;
		$total = count($this->productIds);

		foreach ($this->productIds as $id)
		{
			if (!$products->syncProductById($id))
				throw new QueryAbortedException('Failed to sync product');

			$this->setProgress($queue, $i++ / $total);
		}
	}

	protected function defaultDescription ()
	{
		return MailchimpCommerce::t('Syncing {name} to Mailchimp', [
			'name' => $this->productName,
		]);
	}

}
