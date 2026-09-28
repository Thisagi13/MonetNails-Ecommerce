<?php
/**
 * ProductController.php
 * Handles Product Details view and cart insertions
 */

require_once 'models/Product.php';
require_once 'models/Cart.php';

class ProductController
{
    private PDO $db;
    private Product $productModel;
    private Cart $cartModel;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->productModel = new Product($db);
        $this->cartModel = new Cart($db);
    }

    public function index()
    {
        // 1. Identify product
        $productId = isset($_GET['id']) ? (int)$_GET['id'] : 1;
        if ($productId < 1) {
            $productId = 1;
        }

        // Try to fetch from DB
        $product = $this->productModel->findById($productId);

        // Fallback default if not found
        if (!$product) {
            $product = [
                "product_id"     => 1,
                "product_name"   => "Signature Blossom Press-On Set",
                "price"          => 1000,
                "description"    => "Indulge in the delicate artistry of our Signature Blossom set. Each nail is meticulously handcrafted with a soft pink base, accented by elegant bow charms and pearl details. Perfect for adding a touch of quiet luxury to your everyday look.",
                "material_info"  => "Salon-grade gel",
                "includes_info"  => "24 nails, adhesive, prep kit",
                "shipping_info"  => "Free standard shipping on orders over $50. Express shipping available at checkout.",
                "image_url"      => "assets/images/product/node-5.png"
            ];
        }

        // 2. Handle Add to Cart / Buy Now
        $added = false;
        $error = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action !== 'add_to_cart' && $action !== 'buy_now') {
                $error = "Unknown action. Please try again.";
            } else {
                $userId = $_SESSION['user_id'] ?? null;
                $sessionKey = ($userId === null) ? session_id() : "";

                if (empty($sessionKey) && $userId === null) {
                    session_start();
                    $sessionKey = session_id();
                }

                if ($this->cartModel->addItem($userId, $sessionKey, $product['product_id'], 1)) {
                    if ($action === 'buy_now') {
                        header("Location: Checkout.php");
                        exit;
                    }
                    $added = true;
                } else {
                    $error = "Sorry, we could not add this item. Please try again.";
                }
            }
        }

        // Helper for view
        $priceText = "Rs. " . number_format((float)$product['price'], 0, ".", "");
        
        // Make sure image path is retrieved cleanly from database
        $rawImage = !empty($product['image_url']) ? $product['image_url'] : "assets/images/gallery/signature-blossom.png";
        if (strpos($rawImage, 'images/') === 0) {
             $rawImage = str_replace('images/', 'assets/images/gallery/', $rawImage);
        }
        if (strpos($rawImage, '/') === 0 || strpos($rawImage, 'http://') === 0 || strpos($rawImage, 'https://') === 0) {
            $productImage = $rawImage;
        } else {
            $productImage = '/MonetNails-Ecommerce2/MonetNails-Ecommerce/' . ltrim($rawImage, '/');
        }

        $data = [
            'product' => $product,
            'added' => $added,
            'error' => $error,
            'priceText' => $priceText,
            'productImage' => $productImage
        ];

        // 3. Render View based on auth
        if (isset($_SESSION['user_id'])) {
            require 'views/product/registered.php';
        } else {
            require 'views/product/unregistered.php';
        }
    }
}
