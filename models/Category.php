<?php
/**
 * Category.php — Model
 * ---------------------
 * Handles database operations for product categories.
 *
 * MVC Layer : Model
 * DB Table  : categories
 */

class Category
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Retrieve all categories along with their respective product counts.
     */
    public function getAllWithProductCounts(): array
    {
        $sql = "SELECT c.category_id, c.category_name,
                       COUNT(p.product_id) AS product_count
                FROM categories c
                LEFT JOIN products p ON p.category_id = c.category_id
                GROUP BY c.category_id, c.category_name
                ORDER BY c.category_id ASC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
