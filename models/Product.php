<?php
/**
 * Product.php — Model
 * --------------------
 * Handles database operations for shop / gallery products.
 *
 * MVC Layer : Model
 * DB Table  : products
 */

class Product
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Get products optionally filtered by category IDs.
     *
     * @param int[] $categoryIds
     * @return array
     */
    public function getFiltered(array $categoryIds = []): array
    {
        $validIds = [];
        foreach ($categoryIds as $id) {
            $id = (int) $id;
            if ($id > 0) {
                $validIds[] = $id;
            }
        }

        if (!empty($validIds)) {
            $placeholders = implode(',', array_fill(0, count($validIds), '?'));
            $sql = "SELECT product_id, category_id, product_name, description, price, stock_quantity,
                           COALESCE(image_url, '') AS image
                    FROM products
                    WHERE category_id IN ($placeholders)
                    ORDER BY product_id ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($validIds);
        } else {
            $sql = "SELECT product_id, category_id, product_name, description, price, stock_quantity,
                           COALESCE(image_url, '') AS image
                    FROM products
                    ORDER BY product_id ASC";
            $stmt = $this->db->query($sql);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find single product by ID.
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE product_id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
