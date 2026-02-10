<?php
include 'con_student.php';
header("Content-Type: application/json; charset=UTF-8");

try {
    $method = $_SERVER['REQUEST_METHOD'];

    // ===== GET =====
    if ($method === "GET") {
        $stmt = $conn->prepare("SELECT * FROM student ORDER BY student_id DESC");
        $stmt->execute();
        echo json_encode([
            "success" => true,
            "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ]);
    }

    // ===== POST =====
    elseif ($method === "POST") {
        $contentType = $_SERVER["CONTENT_TYPE"] ?? '';

        if (stripos($contentType, "application/json") !== false) {
            $data = json_decode(file_get_contents("php://input"), true);
        } else {
            $data = $_POST;
        }

        if (
            empty($data["first_name"]) ||
            empty($data["last_name"]) ||
            empty($data["phone"]) ||
            empty($data["email"])
        ) {
            echo json_encode(["success" => false, "message" => "กรุณากรอกข้อมูลให้ครบ"]);
            exit;
        }

        $stmt = $conn->prepare(
            "INSERT INTO student (first_name, last_name, phone, email)
             VALUES (:first_name, :last_name, :phone, :email)"
        );

        $stmt->execute([
            ":first_name" => $data["first_name"],
            ":last_name"  => $data["last_name"],
            ":phone"      => $data["phone"],
            ":email"      => $data["email"]
        ]);

        echo json_encode(["success" => true, "message" => "เพิ่มข้อมูลเรียบร้อย"]);
    }

    // ===== PUT =====
    elseif ($method === "PUT") {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data["student_id"])) {
            echo json_encode(["success" => false, "message" => "ไม่พบ student_id"]);
            exit;
        }

        $stmt = $conn->prepare(
            "UPDATE student
             SET first_name = :first_name,
                 last_name  = :last_name,
                 phone      = :phone,
                 email      = :email
             WHERE student_id = :id"
        );

        $stmt->execute([
            ":first_name" => $data["first_name"],
            ":last_name"  => $data["last_name"],
            ":phone"      => $data["phone"],
            ":email"      => $data["email"],
            ":id"         => $data["student_id"]
        ]);

        echo json_encode(["success" => true, "message" => "แก้ไขข้อมูลเรียบร้อย"]);
    }

    // ===== DELETE =====
    elseif ($method === "DELETE") {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data["student_id"])) {
            echo json_encode(["success" => false, "message" => "ไม่พบ student_id"]);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM student WHERE student_id = :id");
        $stmt->execute([":id" => $data["student_id"]]);

        echo json_encode(["success" => true, "message" => "ลบข้อมูลเรียบร้อย"]);
    }

    else {
        echo json_encode(["success" => false, "message" => "Method ไม่ถูกต้อง"]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
