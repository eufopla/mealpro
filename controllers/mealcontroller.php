<?php

class MealController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    // GET /meals — liste tous les repas
    public function index(): void
    {
        $stmt = $this->db->query("SELECT * FROM meals ORDER BY created_at DESC");
        $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->json($meals);
    }

    // GET /meals/{id} — un seul repas
    public function show(int $id): void
    {
        $stmt = $this->db->prepare("SELECT * FROM meals WHERE id = ?");
        $stmt->execute([$id]);
        $meal = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$meal) {
            $this->json(['error' => 'Meal not found'], 404);
            return;
        }

        $this->json($meal);
    }

    // POST /meals — créer un repas
    public function store(): void
    {
        $data = $this->getBody();

        if (empty($data['name'])) {
            $this->json(['error' => 'Name is required'], 422);
            return;
        }

        $stmt = $this->db->prepare("
            INSERT INTO meals (name, description, calories, created_at)
            VALUES (?, ?, ?, NOW())
        ");

        $stmt->execute([
            $data['name'],
            $data['description'] ?? null,
            $data['calories']    ?? null,
        ]);

        $meal = [
            'id'          => (int) $this->db->lastInsertId(),
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'calories'    => $data['calories']    ?? null,
        ];

        $this->json($meal, 201);
    }

    // PUT /meals/{id} — mettre à jour un repas
    public function update(int $id): void
    {
        $data = $this->getBody();

        $stmt = $this->db->prepare("SELECT id FROM meals WHERE id = ?");
        $stmt->execute([$id]);

        if (!$stmt->fetch()) {
            $this->json(['error' => 'Meal not found'], 404);
            return;
        }

        $stmt = $this->db->prepare("
            UPDATE meals
            SET name = ?, description = ?, calories = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $data['name']        ?? null,
            $data['description'] ?? null,
            $data['calories']    ?? null,
            $id,
        ]);

        $this->json(['message' => 'Meal updated']);
    }

    // DELETE /meals/{id} — supprimer un repas
    public function destroy(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM meals WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) {
            $this->json(['error' => 'Meal not found'], 404);
            return;
        }

        $this->json(['message' => 'Meal deleted']);
    }

    // --- Helpers ---

    private function getBody(): array
    {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }

    private function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}