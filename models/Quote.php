<?php
class Quote {
    private $conn;
    private $table = 'quotes';

    public $id;
    public $quote;
    public $author_id;
    public $category_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = 'SELECT 
                    q.id,
                    q.quote,
                    a.author,
                    c.category
                  FROM ' . $this->table . ' q
                  LEFT JOIN authors a ON q.author_id = a.id
                  LEFT JOIN categories c ON q.category_id = c.id
                  ORDER BY q.id';

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function read_single() {
        $query = 'SELECT 
                    q.id,
                    q.quote,
                    a.author,
                    c.category
                  FROM ' . $this->table . ' q
                  LEFT JOIN authors a ON q.author_id = a.id
                  LEFT JOIN categories c ON q.category_id = c.id
                  WHERE q.id = :id
                  LIMIT 1';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        return $stmt;
    }

    public function read_filtered() {
        $query = 'SELECT 
                    q.id,
                    q.quote,
                    a.author,
                    c.category
                  FROM ' . $this->table . ' q
                  LEFT JOIN authors a ON q.author_id = a.id
                  LEFT JOIN categories c ON q.category_id = c.id
                  WHERE 1=1';

        if (!empty($this->author_id)) {
            $query .= ' AND q.author_id = :author_id';
        }

        if (!empty($this->category_id)) {
            $query .= ' AND q.category_id = :category_id';
        }

        $query .= ' ORDER BY q.id';

        $stmt = $this->conn->prepare($query);

        if (!empty($this->author_id)) {
            $stmt->bindParam(':author_id', $this->author_id);
        }

        if (!empty($this->category_id)) {
            $stmt->bindParam(':category_id', $this->category_id);
        }

        $stmt->execute();

        return $stmt;
    }

    public function authorExists() {
        $query = 'SELECT id FROM authors WHERE id = :author_id LIMIT 1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':author_id', $this->author_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function categoryExists() {
        $query = 'SELECT id FROM categories WHERE id = :category_id LIMIT 1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function create() {
    $query = 'INSERT INTO ' . $this->table . ' (quote, author_id, category_id)
              VALUES (:quote, :author_id, :category_id)';

    $stmt = $this->conn->prepare($query);

    $this->quote = htmlspecialchars(strip_tags($this->quote));
    $this->author_id = htmlspecialchars(strip_tags($this->author_id));
    $this->category_id = htmlspecialchars(strip_tags($this->category_id));

    $stmt->bindParam(':quote', $this->quote);
    $stmt->bindParam(':author_id', $this->author_id);
    $stmt->bindParam(':category_id', $this->category_id);

    return $stmt->execute();
}

public function update() {
    $query = 'UPDATE ' . $this->table . '
              SET quote = :quote,
                  author_id = :author_id,
                  category_id = :category_id
              WHERE id = :id';

    $stmt = $this->conn->prepare($query);

    $this->id = htmlspecialchars(strip_tags($this->id));
    $this->quote = htmlspecialchars(strip_tags($this->quote));
    $this->author_id = htmlspecialchars(strip_tags($this->author_id));
    $this->category_id = htmlspecialchars(strip_tags($this->category_id));

    $stmt->bindParam(':id', $this->id);
    $stmt->bindParam(':quote', $this->quote);
    $stmt->bindParam(':author_id', $this->author_id);
    $stmt->bindParam(':category_id', $this->category_id);

    return $stmt->execute();
}

public function quoteExists() {
    $query = 'SELECT id FROM ' . $this->table . ' WHERE id = :id LIMIT 1';
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $this->id);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}

public function delete() {
    $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';

    $stmt = $this->conn->prepare($query);

    $this->id = htmlspecialchars(strip_tags($this->id));
    $stmt->bindParam(':id', $this->id);

    return $stmt->execute();
}
}
?>