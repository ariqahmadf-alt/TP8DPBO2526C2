<?php
require_once __DIR__ . "/DB.php";

class Lecturer
{
    /** Seluruh dosen beserta nama prodi dan jumlah mata kuliah yang diampu. */
    public function getAll(): array
    {
        return DB::select(
            "SELECT l.id, l.name, l.nidn, l.phone, l.join_date,
                    d.name AS department_name,
                    COUNT(c.id) AS course_count
             FROM lecturers l
             LEFT JOIN departments d ON d.id = l.department_id
             LEFT JOIN courses c ON c.lecturer_id = l.id
             GROUP BY l.id, l.name, l.nidn, l.phone, l.join_date, d.name
             ORDER BY l.id"
        );
    }

    /** Versi ringan untuk opsi dropdown. */
    public function getOptions(): array
    {
        return DB::select("SELECT id, name FROM lecturers ORDER BY name");
    }

    public function getById(int $id): ?array
    {
        $rows = DB::select("SELECT * FROM lecturers WHERE id = ?", "i", [$id]);
        return $rows[0] ?? null;
    }

    public function create(array $d): void
    {
        DB::execute(
            "INSERT INTO lecturers (name, nidn, phone, join_date, department_id) VALUES (?, ?, ?, ?, ?)",
            "ssssi",
            [$d["name"], $d["nidn"], $d["phone"], $d["join_date"], $d["department_id"]]
        );
    }

    public function update(int $id, array $d): void
    {
        DB::execute(
            "UPDATE lecturers SET name = ?, nidn = ?, phone = ?, join_date = ?, department_id = ? WHERE id = ?",
            "ssssii",
            [$d["name"], $d["nidn"], $d["phone"], $d["join_date"], $d["department_id"], $id]
        );
    }

    public function delete(int $id): void
    {
        DB::execute("DELETE FROM lecturers WHERE id = ?", "i", [$id]);
    }
}
