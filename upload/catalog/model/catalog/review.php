<?php
/**
 * Class Review
 *
 * Can be called using $this->load->model('catalog/review');
 *
 * @package Catalog\Model\Catalog
 */
class ModelCatalogReview extends Model {
	/**
	 * Add Review
	 *
	 * @param int                  $product_id primary key of the product record
	 * @param array<string, mixed> $data       array of data
	 *
	 * @throws \Exception
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('catalog/product');
	 *
	 * $this->model_catalog_review->addReview($product_id, $data);
	 */
	public function addReview(int $product_id, array $data): void {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "review` SET `author` = '" . $this->db->escape($data['name']) . "', `customer_id` = '" . (int)$this->customer->getId() . "', `product_id` = '" . (int)$product_id . "', `text` = '" . $this->db->escape($data['text']) . "', `rating` = '" . (int)$data['rating'] . "', `date_added` = NOW(), `date_modified` = NOW()");

		$review_id = $this->db->getLastId();

		if (in_array('review', (array)$this->config->get('config_mail_alert'))) {
			$this->load->language('mail/review');

			// Products
			$this->load->model('catalog/product');

			$product_info = $this->model_catalog_product->getProduct($product_id);

			$subject = sprintf($this->language->get('text_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));

			$message = $this->language->get('text_waiting') . "\n";
			$message .= sprintf($this->language->get('text_product'), html_entity_decode($product_info['name'], ENT_QUOTES, 'UTF-8')) . "\n";
			$message .= sprintf($this->language->get('text_reviewer'), html_entity_decode($data['name'], ENT_QUOTES, 'UTF-8')) . "\n";
			$message .= sprintf($this->language->get('text_rating'), $data['rating']) . "\n";
			$message .= $this->language->get('text_review') . "\n";
			$message .= html_entity_decode($data['text'], ENT_QUOTES, 'UTF-8') . "\n\n";

			if ($this->config->get('config_mail_engine')) {
				$mail_option = [
					'parameter'     => $this->config->get('config_mail_parameter'),
					'smtp_hostname' => $this->config->get('config_mail_smtp_hostname'),
					'smtp_username' => $this->config->get('config_mail_smtp_username'),
					'smtp_password' => html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8'),
					'smtp_port'     => $this->config->get('config_mail_smtp_port'),
					'smtp_timeout'  => $this->config->get('config_mail_smtp_timeout')
				];

				$mail = new \Mail($this->config->get('config_mail_engine'), $mail_option);

				$mail->setTo($this->config->get('config_email'));
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
				$mail->setSubject($subject);
				$mail->setText($message);
				$mail->send();

				// Send to additional alert emails
				$emails = explode(',', (string)$this->config->get('config_mail_alert_email'));

				foreach ($emails as $email) {
					if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
						$mail->setTo($email);
						$mail->send();
					}
				}
			}
		}
	}

	/**
	 * Get Reviews By Product ID
	 *
	 * @param int $product_id primary key of the product record
	 * @param int $start
	 * @param int $limit
	 *
	 * @return array<int, array<string, mixed>> review records that have product ID
	 *
	 * @example
	 *
	 * $this->load->model('catalog/product');
	 *
	 * $reviews = $this->model_catalog_product->getReviewsByProductId($product_id, $start, $limit);
	 */
	public function getReviewsByProductId(int $product_id, int $start = 0, int $limit = 20): array {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 20;
		}

		$query = $this->db->query("SELECT `r`.`review_id`, `r`.`author`, `r`.`rating`, `r`.`text`, `p`.`product_id`, `pd`.`name`, `p`.`price`, `p`.`image`, `r`.`date_added` FROM `" . DB_PREFIX . "review` `r` LEFT JOIN `" . DB_PREFIX . "product` `p` ON (`r`.`product_id` = `p`.`product_id`) LEFT JOIN `" . DB_PREFIX . "product_description` `pd` ON (`p`.`product_id` = `pd`.`product_id`) WHERE `p`.`product_id` = '" . (int)$product_id . "' AND `p`.`date_available` <= NOW() AND `p`.`status` = '1' AND `r`.`status` = '1' AND `pd`.`language_id` = '" . (int)$this->config->get('config_language_id') . "' ORDER BY `r`.`date_added` DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	/**
	 * Get Total Reviews By Product ID
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return int total number of review records that have product ID
	 *
	 * @example
	 *
	 * $this->load->model('catalog/product');
	 *
	 * $review_total = $this->model_catalog_review->getTotalReviewsByProductId($product_id);
	 */
	public function getTotalReviewsByProductId(int $product_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "review` `r` LEFT JOIN `" . DB_PREFIX . "product` `p` ON (`r`.`product_id` = `p`.`product_id`) LEFT JOIN `" . DB_PREFIX . "product_description` `pd` ON (`p`.`product_id` = `pd`.`product_id`) WHERE `p`.`product_id` = '" . (int)$product_id . "' AND `p`.`date_available` <= NOW() AND `p`.`status` = '1' AND `r`.`status` = '1' AND `pd`.`language_id` = '" . (int)$this->config->get('config_language_id') . "'");

		return (int)$query->row['total'];
	}
}
