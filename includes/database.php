<?php
require_once 'config.php';

class Database {
    private $connection;
    private static $instance = null;

    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]
            );
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Database query error: " . $e->getMessage() . " SQL: " . $sql . " Params: " . json_encode($params));
            throw new Exception("Database operation failed: " . $e->getMessage());
        }
    }

    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = str_repeat('?,', count($data) - 1) . '?';
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $this->query($sql, array_values($data));
        
        return $this->connection->lastInsertId();
    }

    public function update($table, $data, $where, $whereParams = []) {
        $setClause = [];
        $setParams = [];
        $paramIndex = 1;
        
        foreach (array_keys($data) as $column) {
            $setClause[] = "$column = ?";
            $setParams[] = $data[$column];
        }
        $setClause = implode(', ', $setClause);
        
        $sql = "UPDATE $table SET $setClause WHERE $where";
        $params = array_merge($setParams, $whereParams);
        
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM $table WHERE $where";
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    public function commit() {
        return $this->connection->commit();
    }

    public function rollback() {
        return $this->connection->rollback();
    }

    public function close() {
        $this->connection = null;
    }
}

// User Model
class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create($data) {
        $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        unset($data['password']);
        return $this->db->insert('users', $data);
    }

    public function findByEmail($email) {
        return $this->db->fetchOne("SELECT * FROM users WHERE email = ?", [$email]);
    }

    public function findByUsername($username) {
        return $this->db->fetchOne("SELECT * FROM users WHERE username = ?", [$username]);
    }

    public function findById($id) {
        return $this->db->fetchOne("SELECT * FROM users WHERE id = ?", [$id]);
    }

    public function update($id, $data) {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }
        return $this->db->update('users', $data, 'id = ?', [$id]);
    }

    public function delete($id) {
        return $this->db->delete('users', 'id = ?', [$id]);
    }

    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT * FROM users ORDER BY created_at DESC";
        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        return $this->db->fetchAll($sql);
    }

    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}

// Product Model
class Product {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create($data) {
        if (isset($data['colors']) && is_array($data['colors'])) {
            $data['colors'] = json_encode($data['colors']);
        }
        if (isset($data['sizes']) && is_array($data['sizes'])) {
            $data['sizes'] = json_encode($data['sizes']);
        }
        return $this->db->insert('products', $data);
    }

    public function findById($id) {
        return $this->db->fetchOne("SELECT * FROM products WHERE id = ?", [$id]);
    }

    public function update($id, $data) {
        if (isset($data['colors']) && is_array($data['colors'])) {
            $data['colors'] = json_encode($data['colors']);
        }
        if (isset($data['sizes']) && is_array($data['sizes'])) {
            $data['sizes'] = json_encode($data['sizes']);
        }
        return $this->db->update('products', $data, 'id = ?', [$id]);
    }

    public function delete($id) {
        return $this->db->delete('products', 'id = ?', [$id]);
    }

    public function getAll($filters = [], $limit = null, $offset = 0) {
        $sql = "SELECT * FROM products WHERE is_active = 1";
        $params = [];

        if (!empty($filters['category'])) {
            $sql .= " AND category = ?";
            $params[] = $filters['category'];
        }

        if (!empty($filters['sport_type'])) {
            $sql .= " AND sport_type = ?";
            $params[] = $filters['sport_type'];
        }

        if (!empty($filters['min_price'])) {
            $sql .= " AND price >= ?";
            $params[] = $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $sql .= " AND price <= ?";
            $params[] = $filters['max_price'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (name LIKE ? OR description LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sql .= " ORDER BY is_featured DESC, created_at DESC";

        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }

        return $this->db->fetchAll($sql, $params);
    }

    public function getAllForAdmin($filters = [], $limit = null, $offset = 0) {
        $sql = "SELECT * FROM products";
        $params = [];

        // Build WHERE clause for filters
        $whereConditions = [];
        
        if (!empty($filters['category'])) {
            $whereConditions[] = "category = ?";
            $params[] = $filters['category'];
        }

        if (!empty($filters['sport_type'])) {
            $whereConditions[] = "sport_type = ?";
            $params[] = $filters['sport_type'];
        }

        if (!empty($filters['min_price'])) {
            $whereConditions[] = "price >= ?";
            $params[] = $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $whereConditions[] = "price <= ?";
            $params[] = $filters['max_price'];
        }

        if (!empty($filters['search'])) {
            $whereConditions[] = "(name LIKE ? OR description LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(' AND ', $whereConditions);
        }

        $sql .= " ORDER BY is_featured DESC, created_at DESC";

        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }

        return $this->db->fetchAll($sql, $params);
    }

    public function getFeatured() {
        return $this->db->fetchAll("SELECT * FROM products WHERE is_featured = 1 AND is_active = 1 ORDER BY created_at DESC");
    }

    public function getCategories() {
        return $this->db->fetchAll("SELECT DISTINCT category FROM products WHERE is_active = 1 ORDER BY category");
    }

    public function getSportTypes() {
        return $this->db->fetchAll("SELECT DISTINCT sport_type FROM products WHERE is_active = 1 AND sport_type IS NOT NULL ORDER BY sport_type");
    }
}

// Order Model
class Order {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create($data) {
        $this->db->beginTransaction();
        try {
            $orderId = $this->db->insert('orders', $data);
            $this->db->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function findById($id) {
        return $this->db->fetchOne("SELECT * FROM orders WHERE id = ?", [$id]);
    }

    public function findByUser($userId) {
        return $this->db->fetchAll("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC", [$userId]);
    }

    public function update($id, $data) {
        return $this->db->update('orders', $data, 'id = ?', [$id]);
    }

    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT o.*, u.first_name, u.last_name, u.email FROM orders o 
                JOIN users u ON o.user_id = u.id 
                ORDER BY o.created_at DESC";
        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        return $this->db->fetchAll($sql);
    }

    public function getOrderItems($orderId) {
        return $this->db->fetchAll(
            "SELECT oi.*, p.name, p.image_url FROM order_items oi 
             JOIN products p ON oi.product_id = p.id 
             WHERE oi.order_id = ?",
            [$orderId]
        );
    }

    public function addOrderItem($data) {
        return $this->db->insert('order_items', $data);
    }
}

// Cart Model
class Cart {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function addItem($data) {
        // Check if item already exists in cart
        $existing = $this->db->fetchOne(
            "SELECT * FROM cart_items WHERE user_id = ? AND product_id = ? AND size = ? AND color = ?",
            [$data['user_id'], $data['product_id'], $data['size'], $data['color']]
        );

        if ($existing) {
            // Update quantity
            return $this->db->update('cart_items', 
                ['quantity' => $existing['quantity'] + $data['quantity']], 
                'id = ?', 
                [$existing['id']]
            );
        } else {
            // Add new item
            return $this->db->insert('cart_items', $data);
        }
    }

    public function getItems($userId) {
        return $this->db->fetchAll(
            "SELECT ci.*, p.name, p.price, p.image_url FROM cart_items ci 
             JOIN products p ON ci.product_id = p.id 
             WHERE ci.user_id = ?",
            [$userId]
        );
    }

    public function updateQuantity($id, $quantity) {
        return $this->db->update('cart_items', ['quantity' => $quantity], 'id = ?', [$id]);
    }

    public function removeItem($id) {
        return $this->db->delete('cart_items', 'id = ?', [$id]);
    }

    public function clearCart($userId) {
        return $this->db->delete('cart_items', 'user_id = ?', [$userId]);
    }
}
?> 