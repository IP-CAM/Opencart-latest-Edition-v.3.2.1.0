<?php
/**
 * Class Layout
 *
 * Can be called using $this->load->model('design/layout');
 *
 * @package Admin\Model\Design
 */
class ModelDesignLayout extends Model {
	/**
	 * Add Layout
	 *
	 * @param array<string, mixed> $data array of data
	 *
	 * @return int returns the primary key of the new layout record
	 *
	 * @example
	 *
	 * $layout_data = [
	 *     'name' => 'Layout Name'
	 * ];
	 *
	 * $this->load->model('design/layout');
	 *
	 * $layout_id = $this->model_design_layout->addLayout($layout_data);
	 */
	public function addLayout(array $data): int {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "layout` SET `name` = '" . $this->db->escape($data['name']) . "'");

		$layout_id = $this->db->getLastId();

		if (isset($data['layout_route'])) {
			foreach ($data['layout_route'] as $layout_route) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "layout_route` SET `layout_id` = '" . (int)$layout_id . "', `store_id` = '" . (int)$layout_route['store_id'] . "', `route` = '" . $this->db->escape($layout_route['route']) . "'");
			}
		}

		if (isset($data['layout_module'])) {
			foreach ($data['layout_module'] as $layout_module) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "layout_module` SET `layout_id` = '" . (int)$layout_id . "', `code` = '" . $this->db->escape($layout_module['code']) . "', `position` = '" . $this->db->escape($layout_module['position']) . "', `sort_order` = '" . (int)$layout_module['sort_order'] . "'");
			}
		}

		return $layout_id;
	}

	/**
	 * Edit Layout
	 *
	 * @param int                  $layout_id primary key of the layout record
	 * @param array<string, mixed> $data      array of data
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $layout_data = [
	 *     'name' => 'Layout Name'
	 * ];
	 *
	 * $this->load->model('design/layout');
	 *
	 * $this->model_design_layout->editLayout($layout_id, $layout_data);
	 */
	public function editLayout(int $layout_id, array $data): void {
		$this->db->query("UPDATE `" . DB_PREFIX . "layout` SET `name` = '" . $this->db->escape($data['name']) . "' WHERE `layout_id` = '" . (int)$layout_id . "'");

		$this->db->query("DELETE FROM `" . DB_PREFIX . "layout_route` WHERE `layout_id` = '" . (int)$layout_id . "'");

		if (isset($data['layout_route'])) {
			foreach ($data['layout_route'] as $layout_route) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "layout_route` SET `layout_id` = '" . (int)$layout_id . "', `store_id` = '" . (int)$layout_route['store_id'] . "', `route` = '" . $this->db->escape($layout_route['route']) . "'");
			}
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "layout_module` WHERE `layout_id` = '" . (int)$layout_id . "'");

		if (isset($data['layout_module'])) {
			foreach ($data['layout_module'] as $layout_module) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "layout_module` SET `layout_id` = '" . (int)$layout_id . "', `code` = '" . $this->db->escape($layout_module['code']) . "', `position` = '" . $this->db->escape($layout_module['position']) . "', `sort_order` = '" . (int)$layout_module['sort_order'] . "'");
			}
		}
	}

	/**
	 * Delete Layout
	 *
	 * @param int $layout_id primary key of the layout record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('design/layout');
	 *
	 * $this->model_design_layout->deleteLayout($layout_id);
	 */
	public function deleteLayout(int $layout_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "layout` WHERE `layout_id` = '" . (int)$layout_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "layout_route` WHERE `layout_id` = '" . (int)$layout_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "layout_module` WHERE `layout_id` = '" . (int)$layout_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "category_to_layout` WHERE `layout_id` = '" . (int)$layout_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_to_layout` WHERE `layout_id` = '" . (int)$layout_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "information_to_layout` WHERE `layout_id` = '" . (int)$layout_id . "'");
	}

	/**
	 * Get Layout
	 *
	 * @param int $layout_id primary key of the layout record
	 *
	 * @return array<string, mixed> layout record that has layout ID
	 *
	 * @example
	 *
	 * $this->load->model('design/layout');
	 *
	 * $layout_info = $this->model_design_layout->getLayout($layout_id);
	 */
	public function getLayout(int $layout_id): array {
		$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "layout` WHERE `layout_id` = '" . (int)$layout_id . "'");

		return $query->row;
	}

	/**
	 * Get Layouts
	 *
	 * @param array<string, mixed> $data array of filters
	 *
	 * @return array<int, array<string, mixed>> layout records
	 *
	 * @example
	 *
	 * $filter_data = [
	 *     'sort'  => 'name',
	 *     'order' => 'DESC',
	 *     'start' => 0,
	 *     'limit' => 10
	 * ];
	 *
	 * $this->load->model('design/layout');
	 *
	 * $results = $this->model_design_layout->getLayouts($filter_data);
	 */
	public function getLayouts(array $data = []): array {
		$sql = "SELECT * FROM `" . DB_PREFIX . "layout`";

		$sort_data = ['name'];

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY `name`";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	/**
	 * Get Routes
	 *
	 * @param int $layout_id primary key of the layout record
	 *
	 * @return array<int, array<string, mixed>> route records that have layout ID
	 *
	 * @example
	 *
	 * $this->load->model('design/layout');
	 *
	 * $layout_routes = $this->model_design_layout->getRoutes($layout_id);
	 */
	public function getRoutes(int $layout_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "layout_route` WHERE `layout_id` = '" . (int)$layout_id . "'");

		return $query->rows;
	}

	/**
	 * Get Modules
	 *
	 * @param int $layout_id primary key of the layout record
	 *
	 * @return array<int, array<string, mixed>> module records that have layout ID
	 *
	 * @example
	 *
	 * $this->load->model('design/layout');
	 *
	 * $layout_modules = $this->model_design_layout->getModules($layout_id);
	 */
	public function getModules(int $layout_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "layout_module` WHERE `layout_id` = '" . (int)$layout_id . "' ORDER BY `position` ASC, `sort_order` ASC");

		return $query->rows;
	}

	/**
	 * Get Total Layouts
	 *
	 * @return int total number of layout records
	 *
	 * @example
	 *
	 * $filter_data = [
	 *     'sort'  => 'name',
	 *     'order' => 'DESC',
	 *     'start' => 0,
	 *     'limit' => 10
	 * ];
	 *
	 * $this->load->model('design/layout');
	 *
	 * $layout_total = $this->model_design_layout->getTotalLayouts($filter_data);
	 */
	public function getTotalLayouts(): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "layout`");

		return (int)$query->row['total'];
	}
}
