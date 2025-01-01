<?php
/**
 * Class Laybyy Layout
 *
 * @package Catalog\Model\Extension\Module
 */
class ModelExtensionModuleLaybuyLayout extends Model {
	/**
	 * Get Status Label
	 *
	 * @param int $id
	 *
	 * @return string
	 */
	public function getStatusLabel(int $id): string {
		$statuses = $this->getTransactionStatuses();

		foreach ($statuses as $status) {
			if ($status['status_id'] == $id && $status['status_name'] != '') {
				return $status['status_name'];
				break;
			}
		}

		return '';
	}

	/**
	 * Get Transaction By Order ID
	 *
	 * @param int $order_id
	 *
	 * @return array<string, mixed>
	 */
	public function getTransactionByOrderId(int $order_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "laybuy_transaction` WHERE `order_id` = '" . (int)$order_id . "' ORDER BY `laybuy_ref_no` DESC LIMIT 1");

		return $query->row;
	}

	/**
	 * Get Transaction Statuses
	 *
	 * @return array<string, mixed>
	 */
	public function getTransactionStatuses(): array {
		$this->load->language('extension/payment/laybuy');

		return [
			[
				'status_id'   => 1,
				'status_name' => $this->language->get('text_status_1')
			],
			[
				'status_id'   => 5,
				'status_name' => $this->language->get('text_status_5')
			],
			[
				'status_id'   => 7,
				'status_name' => $this->language->get('text_status_7')
			],
			[
				'status_id'   => 50,
				'status_name' => $this->language->get('text_status_50')
			],
			[
				'status_id'   => 51,
				'status_name' => $this->language->get('text_status_51')
			]
		];
	}

	/**
	 * Is LayBuy Order
	 *
	 * @param int $order_id
	 *
	 * @return bool
	 */
	public function isLayBuyOrder(int $order_id): bool {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "laybuy_transaction` WHERE `order_id` = '" . (int)$order_id . "'");

		if ($query->num_rows) {
			return true;
		} else {
			return false;
		}
	}
}
