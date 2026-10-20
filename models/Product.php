<?php
/**
 * Product.php — Model
 * --------------------
 * Handles database operations for shop / gallery products.
 * Extended with full CRUD for admin product management.
 *
 * MVC Layer : Model
 * DB Table  : products, categories
 */

class Product
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // =========================================================================
    // ADMIN — Statistics
    // =========================================================================

    /** Total active products */
    public function countActive(): int
    {
        return (int)$this->db->query(
            "SELECT COUNT(*) FROM products WHERE is_active = 1"
        )->fetchColumn();
    }

    /** Count products with stock 1–5 (low stock) */
    public function countLowStock(): int
    {
        return (int)$this->db->query(
            "SELECT COUNT(*) FROM products WHERE is_active = 1 AND stock_quantity > 0 AND stock_quantity <= 5"
        )->fetchColumn();
    }

    /** Count out-of-stock products */
    public function countOutOfStock(): int
    {
        return (int)$this->db->query(
            "SELECT COUNT(*) FROM products WHERE is_active = 1 AND stock_quantity = 0"
        )->fetchColumn();
    }

    /** Total inventory retail value (price * stock) */
    public function inventoryValue(): float
    {
        return (float)$this->db->query(
            "SELECT IFNULL(SUM(price * stock_quantity), 0) FROM products WHERE is_active = 1"
        )->fetchColumn();
    }

    // =========================================================================
    // ADMIN — List / Search / Filter / Paginate
    // =========================================================================

    /**
     * Fetch paginated, filtered product list for admin.
     *
     * @param string $search     Search term (product_name, sku)
     * @param int    $categoryId 0 = all categories
     * @param string $stockFilter 'all' | 'low' | 'out' | 'in'
     * @param string $statusFilter 'all' | 'active' | 'inactive'
     * @param int    $page       1-based page number
     * @param int    $perPage    Rows per page
     * @return array ['rows' => [...], 'total' => int]
     */
    public function adminList(
        string $search = '',
        int    $categoryId = 0,
        string $stockFilter = 'all',
        string $statusFilter = 'all',
        int    $page = 1,
        int    $perPage = 10
    ): array {
        $where  = ['1=1'];
        $params = [];

        if ($search !== '') {
            $where[]  = '(p.product_name LIKE ? OR p.sku LIKE ?)';
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        if ($categoryId > 0) {
            $where[]  = 'p.category_id = ?';
            $params[] = $categoryId;
        }

        switch ($stockFilter) {
            case 'low':
                $where[] = 'p.stock_quantity > 0 AND p.stock_quantity <= 5';
                break;
            case 'out':
                $where[] = 'p.stock_quantity = 0';
                break;
            case 'in':
                $where[] = 'p.stock_quantity > 5';
                break;
        }

        switch ($statusFilter) {
            case 'active':
                $where[] = 'p.is_active = 1';
                break;
            case 'inactive':
                $where[] = 'p.is_active = 0';
                break;
        }

        $whereClause = implode(' AND ', $where);

        // Total count
        $countSql  = "SELECT COUNT(*) FROM products p LEFT JOIN categories c ON p.category_id = c.category_id WHERE $whereClause";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        // Paginated rows
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT p.product_id, p.sku, p.product_name, p.price,
                       p.stock_quantity, p.image_url, p.shape, p.finish, p.is_active,
                       p.category_id, p.description, p.material_info, p.includes_info, p.shipping_info,
                       c.category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.category_id
                WHERE $whereClause
                ORDER BY p.product_id ASC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $paramIdx = 1;
        foreach ($params as $val) {
            $stmt->bindValue($paramIdx++, $val);
        }
        $stmt->bindValue($paramIdx++, (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue($paramIdx++, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'rows'  => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
        ];
    }

    // =========================================================================
    // ADMIN — CRUD
    // =========================================================================

    /** Create a new product. Returns the new product_id. */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO products
                (category_id, sku, product_name, description, price,
                 stock_quantity, image_url, shape, finish,
                 material_info, includes_info, shipping_info, is_active)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            (int)($data['category_id']   ?? 1),
            $data['sku']                  ?? null,
            $data['product_name']         ?? '',
            $data['description']          ?? null,
            (float)($data['price']        ?? 0),
            (int)($data['stock_quantity'] ?? 0),
            $data['image_url']            ?? null,
            $data['shape']               ?? null,
            $data['finish']              ?? null,
            $data['material_info']       ?? null,
            $data['includes_info']       ?? null,
            $data['shipping_info']       ?? null,
            isset($data['is_active']) ? (int)$data['is_active'] : 1,
        ]);
        $newId = (int)$this->db->lastInsertId();
        // Auto-set SKU if not provided
        if (empty($data['sku'])) {
            $this->db->prepare("UPDATE products SET sku = ? WHERE product_id = ?")->execute(
                ['#MN-' . str_pad((string)$newId, 3, '0', STR_PAD_LEFT), $newId]
            );
        }
        return $newId;
    }

    /** Update an existing product. */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE products SET
                category_id    = ?,
                sku            = ?,
                product_name   = ?,
                description    = ?,
                price          = ?,
                stock_quantity = ?,
                image_url      = ?,
                shape          = ?,
                finish         = ?,
                material_info  = ?,
                includes_info  = ?,
                shipping_info  = ?,
                is_active      = ?
             WHERE product_id = ?"
        );
        return $stmt->execute([
            (int)($data['category_id']   ?? 1),
            $data['sku']                  ?? null,
            $data['product_name']         ?? '',
            $data['description']          ?? null,
            (float)($data['price']        ?? 0),
            (int)($data['stock_quantity'] ?? 0),
            $data['image_url']            ?? null,
            $data['shape']               ?? null,
            $data['finish']              ?? null,
            $data['material_info']       ?? null,
            $data['includes_info']       ?? null,
            $data['shipping_info']       ?? null,
            isset($data['is_active']) ? (int)$data['is_active'] : 1,
            $id,
        ]);
    }

    /** Soft-delete: set is_active = 0 */
    public function softDelete(int $id): bool
    {
        return $this->db->prepare("UPDATE products SET is_active = 0 WHERE product_id = ?")
                        ->execute([$id]);
    }

    /** Hard delete (use with caution; FK checks will prevent if referenced in order_items) */
    public function hardDelete(int $id): bool
    {
        return $this->db->prepare("DELETE FROM products WHERE product_id = ?")
                        ->execute([$id]);
    }

    /** Adjust stock quantity by a delta (+N or -N). Clamps to 0 minimum. */
    public function adjustStock(int $id, int $delta): bool
    {
        return $this->db->prepare(
            "UPDATE products SET stock_quantity = GREATEST(0, stock_quantity + ?) WHERE product_id = ?"
        )->execute([$delta, $id]);
    }

    /** Set stock quantity directly. */
    public function setStock(int $id, int $qty): bool
    {
        $qty = max(0, $qty);
        return $this->db->prepare(
            "UPDATE products SET stock_quantity = ? WHERE product_id = ?"
        )->execute([$qty, $id]);
    }

    // =========================================================================
    // STOREFRONT (existing methods)
    // =========================================================================

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
                    WHERE is_active = 1 AND category_id IN ($placeholders)
                    ORDER BY product_id ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($validIds);
        } else {
            $sql = "SELECT product_id, category_id, product_name, description, price, stock_quantity,
                           COALESCE(image_url, '') AS image
                    FROM products
                    WHERE is_active = 1
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
