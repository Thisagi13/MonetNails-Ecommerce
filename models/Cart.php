<?php
/**
 * Cart.php — Model
 * Handles all cart_items database operations.
 */

class Cart
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /** Add item to cart (or increment quantity if already exists) */
    public function addItem(?int $userId, string $sessionKey, int $productId, int $quantity = 1, array $options = []): bool
    {
        $shape           = !empty($options['shape']) ? $options['shape'] : 'Medium Almond';
        $finish          = !empty($options['finish']) ? $options['finish'] : 'Glossy Gel';
        $sizePreset      = !empty($options['size_preset']) ? $options['size_preset'] : 'M (Medium)';
        $customSizing    = !empty($options['custom_sizing_code']) ? $options['custom_sizing_code'] : null;
        $specialFeatures = !empty($options['special_features']) ? $options['special_features'] : null;

        // Check if existing item exists for this user / session
        if ($userId !== null) {
            $stmt = $this->db->prepare("SELECT cart_id, quantity FROM cart_items WHERE user_id = ? AND product_id = ? LIMIT 1");
            $stmt->execute([$userId, $productId]);
        } else {
            $stmt = $this->db->prepare("SELECT cart_id, quantity FROM cart_items WHERE session_key = ? AND product_id = ? LIMIT 1");
            $stmt->execute([$sessionKey, $productId]);
        }
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $newQty = (int)$existing['quantity'] + $quantity;
            return $this->updateQuantity((int)$existing['cart_id'], $newQty, $userId, $sessionKey);
        }

        $sql = "INSERT INTO cart_items (user_id, session_key, product_id, quantity, shape, finish, size_preset, custom_sizing_code, special_features) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $userId,
            $sessionKey,
            $productId,
            $quantity,
            $shape,
            $finish,
            $sizePreset,
            $customSizing,
            $specialFeatures
        ]);
    }

    /**
     * Get all cart items (joined with products) for a guest or logged-in user.
     */
    public function getItems(?int $userId, string $sessionKey): array
    {
        if ($userId !== null) {
            $sql = "SELECT c.cart_id, c.quantity, c.shape, c.finish, c.size_preset,
                           c.custom_sizing_code, c.special_features,
                           p.product_id, p.product_name, p.price,
                           COALESCE(p.image_url, '') AS image_url
                    FROM cart_items c
                    JOIN products p ON p.product_id = c.product_id
                    WHERE c.user_id = ?
                    ORDER BY c.added_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
        } else {
            $sql = "SELECT c.cart_id, c.quantity, c.shape, c.finish, c.size_preset,
                           c.custom_sizing_code, c.special_features,
                           p.product_id, p.product_name, p.price,
                           COALESCE(p.image_url, '') AS image_url
                    FROM cart_items c
                    JOIN products p ON p.product_id = c.product_id
                    WHERE c.session_key = ?
                    ORDER BY c.added_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$sessionKey]);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Remove a cart item by cart_id */
    public function removeItem(int $cartId, ?int $userId, string $sessionKey): bool
    {
        if ($userId !== null) {
            $sql = "DELETE FROM cart_items WHERE cart_id = ? AND user_id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$cartId, $userId]);
        } else {
            $sql = "DELETE FROM cart_items WHERE cart_id = ? AND session_key = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$cartId, $sessionKey]);
        }
    }

    /** Update quantity of a cart item */
    public function updateQuantity(int $cartId, int $qty, ?int $userId, string $sessionKey): bool
    {
        if ($qty < 1) {
            return $this->removeItem($cartId, $userId, $sessionKey);
        }
        if ($userId !== null) {
            $sql = "UPDATE cart_items SET quantity = ? WHERE cart_id = ? AND user_id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$qty, $cartId, $userId]);
        } else {
            $sql = "UPDATE cart_items SET quantity = ? WHERE cart_id = ? AND session_key = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$qty, $cartId, $sessionKey]);
        }
    }
}

