<?php
class Category {
    private $conn;
    private $table = 'categories';

    public $id;
    public $category;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = 'SELECT id, category FROM ' . $this->table . ' ORDER BY id';

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function read_single() {
        $query = 'SELECT id, category FROM ' . $this->table . ' WHERE id = :id LIMIT 1';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        return $stmt;
    }

    public function create() {
    $query = 'INSERT INTO ' . $this->table . ' (category)
              VALUES (:category)
              RETURNING id';

    $stmt = $this->conn->prepare($query);

    $this->category = htmlspecialchars(strip_tags($this->category));
    $stmt->bindParam(':category', $this->category);

    if ($stmt->execute()) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->id = $row['id'];
        return true;
    }

    return false;
}

    public function update() {
        $query = 'UPDATE ' . $this->table . '
                  SET category = :category
                  WHERE id = :id';

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->category = htmlspecialchars(strip_tags($this->category));

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':category', $this->category);

        return $stmt->execute();
    }

    public function delete() {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function categoryExists() {
        $query = 'SELECT id FROM ' . $this->table . ' WHERE id = :id LIMIT 1';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
?>