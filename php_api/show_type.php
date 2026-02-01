<?php
include 'condb.php';
header("Content-Type: application/json; charset=UTF-8");

try {
    $method = $_SERVER['REQUEST_METHOD'];

    // ================= GET =================
    if ($method === "GET") {
        $stmt = $conn->prepare("SELECT * FROM type ORDER BY type_id DESC");
        $stmt->execute();
        echo json_encode([
            "success" => true,
            "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ]);
    }

    // ================= POST =================
    elseif ($method === "POST") {

        $contentType = $_SERVER["CONTENT_TYPE"] ?? '';
        $data = (stripos($contentType, "application/json") !== false)
            ? json_decode(file_get_contents("php://input"), true)
            : $_POST;

        if (empty($data["type_name"])) {
            echo json_encode(["success" => false, "message" => "กรุณากรอกชื่อประเภท"]);
            exit;
        }

        $stmt = $conn->prepare(
            "INSERT INTO type (type_name) VALUES (:type_name)"
        );
        $stmt->bindParam(":type_name", $data["type_name"]);

        echo json_encode([
            "success" => $stmt->execute(),
            "message" => "เพิ่มข้อมูลเรียบร้อย"
        ]);
    }

    // ================= PUT =================
    elseif ($method === "PUT") {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data["type_id"]) || empty($data["type_name"])) {
            echo json_encode(["success" => false, "message" => "ข้อมูลไม่ครบ"]);
            exit;
        }

        $stmt = $conn->prepare(
            "UPDATE type SET type_name = :type_name WHERE type_id = :id"
        );
        $stmt->bindParam(":type_name", $data["type_name"]);
        $stmt->bindParam(":id", $data["type_id"], PDO::PARAM_INT);

        echo json_encode([
            "success" => $stmt->execute(),
            "message" => "แก้ไขข้อมูลเรียบร้อย"
        ]);
    }

    // ================= DELETE =================
    elseif ($method === "DELETE") {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data["type_id"])) {
            echo json_encode(["success" => false, "message" => "ไม่พบ type_id"]);
            exit;
        }

        $stmt = $conn->prepare(
            "DELETE FROM type WHERE type_id = :id"
        );
        $stmt->bindParam(":id", $data["type_id"], PDO::PARAM_INT);

        echo json_encode([
            "success" => $stmt->execute(),
            "message" => "ลบข้อมูลเรียบร้อย"
        ]);
    }

    else {
        echo json_encode(["success" => false, "message" => "Method ไม่ถูกต้อง"]);
    }

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

