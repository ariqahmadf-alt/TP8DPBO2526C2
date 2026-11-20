<?php
require_once __DIR__ . "/DB.php";

class Department
{
    /** Seluruh program studi beserta jumlah dosen di dalamnya. */
    public function getAll(): array
    {
        return DB::select(
            "SELECT d.id, d.code, d.name, COUNT(l.id) AS lecturer_count
             FROM departments d
             LEFT JOIN lecturers l ON l.department_id = d.id
             GROUP BY d.id, d.code, d.name
             ORDER BY d.id"
        );
    }

    /** Versi ringan untuk opsi dropdown. */
    public function getOptions(): array
    {
        return DB::select("SELECT id, code, name FROM departments ORDER BY name");
    }

    public function getById(int $id): ?array
    {
        $rows = DB::select("SELECT * FROM departments WHERE id = ?", "i", [$id]);
        return $rows[0] ?? null;
    }

    public function create(array $d): void
    {
        DB::execute("INSERT INTO departments (code, name) VALUES (?, ?)", "ss", [$d["code"], $d["name"]]);
    }

    public function update(int $id, array $d): void
    {
        DB::execute("UPDATE departments SET code = ?, name = ? WHERE id = ?", "ssi", [$d["code"], $d["name"], $id]);
    }

    public function delete(int $id): void
    {
        DB::execute("DELETE FROM departments WHERE id = ?", "i", [$id]);
    }
}
