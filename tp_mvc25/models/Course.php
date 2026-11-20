<?php
require_once __DIR__ . "/DB.php";

class Course
{
    /** Seluruh mata kuliah beserta nama dosen pengampu. */
    public function getAll(): array
    {
        return DB::select(
            "SELECT c.id, c.code, c.name, c.credits, c.semester,
                    l.name AS lecturer_name
             FROM courses c
             LEFT JOIN lecturers l ON l.id = c.lecturer_id
             ORDER BY c.id"
        );
    }

    public function getById(int $id): ?array
    {
        $rows = DB::select("SELECT * FROM courses WHERE id = ?", "i", [$id]);
        return $rows[0] ?? null;
    }

    public function create(array $d): void
    {
        DB::execute(
            "INSERT INTO courses (code, name, credits, semester, lecturer_id) VALUES (?, ?, ?, ?, ?)",
            "ssiii",
            [$d["code"], $d["name"], $d["credits"], $d["semester"], $d["lecturer_id"]]
        );
    }

    public function update(int $id, array $d): void
    {
        DB::execute(
            "UPDATE courses SET code = ?, name = ?, credits = ?, semester = ?, lecturer_id = ? WHERE id = ?",
            "ssiiii",
            [$d["code"], $d["name"], $d["credits"], $d["semester"], $d["lecturer_id"], $id]
        );
    }

    public function delete(int $id): void
    {
        DB::execute("DELETE FROM courses WHERE id = ?", "i", [$id]);
    }
}
