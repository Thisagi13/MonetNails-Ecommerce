<?php
/**
 * AboutController.php — About Us Page Controller
 * Monet Nails MVC Application
 *
 * Serves the public About Us page. No auth guard —
 * both guests and registered customers can view this page.
 * The unified header adjusts automatically based on session.
 *
 * Related views:
 *   views/about/about.php
 */

class AboutController
{
    public function __construct(?PDO $pdo = null)
    {
        // No models needed for a static content page
    }

    /**
     * Display the About Us page.
     */
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Navigation state for the common header
        $activeNav = 'about';

        // Render view
        require __DIR__ . '/../views/about/about.php';
    }
}
