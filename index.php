<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

include 'DbConnect.php';
$objDb = new DbConnect;
$conn = $objDb->connect();

$method = $_SERVER['REQUEST_METHOD'];
switch($method){
    case "GET": 
        $path = explode('/', $_SERVER['REQUEST_URI']);
        error_log("🧩 REQUEST_URI: " . $_SERVER['REQUEST_URI']);
        error_log("🧩 PATH: " . print_r($path, true));
        // 🔹 Si la ruta es /visitado/{user_id}/{atraccion_id}
        if (isset($path[2]) && $path[2] === "visitado") {
            $user_id = $path[3] ?? null;
            $atraccion_id = $path[4] ?? null;

            // Asegúrate de que los dos valores estén presentes
            if ($user_id && $atraccion_id) {
                $sql = "SELECT * FROM visited WHERE usuario_id = :usuario_id AND ciudad_id  = :ciudad_id";
                $stmt = $conn->prepare($sql);
                // ✅ Use the same variable names you extracted
                $stmt->bindParam(':usuario_id', $user_id);
                $stmt->bindParam(':ciudad_id', $atraccion_id);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $response = ['visited' => true];
                } else {
                    $response = ['visited' => false];
                }
            } else {
                $response = ['error' => 'Faltan parámetros user_id o ciudad_id'];
            }
            echo json_encode($response);
            break;
        }

        // 🔹 Buscar atracciones por nombre parcial
        else if (isset($path[2]) && $path[2] === "atracciones" && isset($path[3]) && $path[3] === "search" && isset($path[4])) {
            $texto = "%" . urldecode($path[4]) . "%";

            $sql = "SELECT atraccion_id, atraccion_nombre 
                    FROM lugares_turisticos
                    WHERE atraccion_nombre LIKE :texto
                    LIMIT 10";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':texto', $texto);
            $stmt->execute();
            $atracciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($atracciones) {
                echo json_encode(["status" => 1, "atracciones" => $atracciones]);
            } else {
                echo json_encode(["status" => 0, "atracciones" => []]);
            }

            break;
        }

        else if (isset($path[2]) && $path[2] === "provincia" && isset($path[3])) {
            $provincia_id = intval($path[3]);

            $sql = "SELECT id, nombre, calificacion_general, calificacion_comida, calificacion_transporte, calificacion_hoteles, tipo_turismo 
                    FROM provincias 
                    WHERE id = :id 
                    LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $provincia_id);
            $stmt->execute();
            $provincia = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($provincia) {
                echo json_encode(["status" => 1, "provincia" => $provincia]);
            } else {
                echo json_encode(["status" => 0, "message" => "Provincia no encontrada"]);
            }

            break;
        }


        // ✅ Get a single attraction by province (first one or featured)
        else if (isset($path[2]) && $path[2] === "atracciones" && isset($path[3]) && $path[3] === "oneByProvincia" && isset($path[4])) {
            $provincia_id = intval($path[4]);

            $sql = "SELECT * FROM lugares_turisticos WHERE provincia_id = :provincia_id LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':provincia_id', $provincia_id);
            $stmt->execute();

            $atraccion = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($atraccion) {
                echo json_encode(["status" => 1, "atraccion" => $atraccion]);
            } else {
                echo json_encode(["status" => 0, "message" => "No se encontró atracción"]);
            }

            break;
        }

        // ✅ Get all attractions by province
        else if (isset($path[2]) && $path[2] === "atracciones" && isset($path[3]) && $path[3] === "byProvincia" && isset($path[4])) {
            $provincia_id = intval($path[4]);

            $sql = "SELECT atraccion_id, atraccion_nombre, descripcion, atraccion, transporte, comida, image_url, provincia_id
                    FROM lugares_turisticos
                    WHERE provincia_id = :provincia_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':provincia_id', $provincia_id);
            $stmt->execute();

            $atracciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($atracciones && count($atracciones) > 0) {
                echo json_encode(["status" => 1, "atracciones" => $atracciones]);
            } else {
                echo json_encode(["status" => 0, "atracciones" => []]);
            }

            break;
        }

        // 🔹 Obtener reseñas de una atracción específica
        else if (isset($path[2]) && $path[2] === "resenas" && isset($path[3])) {
            $atraccion_id = intval($path[3]);

            $sql = "SELECT r.id, r.reseña, r.estrellas, r.fecha, u.name AS user_nombre
                    FROM reseñas r
                    JOIN users u ON u.id = r.usuario_id
                    WHERE r.atraccion_id = :atraccion_id
                    ORDER BY r.fecha DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':atraccion_id', $atraccion_id);
            $stmt->execute();
            $reseñas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($reseñas);
            return;
        }
        // 🔹 Obtener atracciones visitadas por un usuario
        else if (isset($path[2]) && $path[2] === "visitadas" && isset($path[3])) {
            $usuario_id = intval($path[3]);

            $sql = "SELECT DISTINCT l.atraccion_id AS atraccion_id, l.atraccion_nombre, l.image_url
                    FROM visited v
                    JOIN lugares_turisticos l ON l.atraccion_id = v.ciudad_id
                    WHERE v.usuario_id = :usuario_id";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            $visitadas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(["status" => 1, "visitadas" => $visitadas]);
            return;
        }


        // 🔹 Si la ruta es /users o /users/{id}
        else if (isset($path[2]) && $path[2] === "users") {
            $sql = "SELECT * FROM users";
            if (isset($path[3]) && is_numeric($path[3])) {
                $sql .= " WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id', $path[3]);
                $stmt->execute();
                $users = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            echo json_encode($users);
            return;
        }
        
    case "POST":
        $uri = urldecode($_SERVER['REQUEST_URI']);
        $path = explode('/', $uri);
        $input = json_decode(file_get_contents("php://input"), true);
        $pregunta = trim($input['pregunta'] ?? '');
        $rawInput = file_get_contents("php://input");
        error_log("📩 RAW INPUT: " . $rawInput);
        error_log("🧩 REQUEST_URI: " . $_SERVER['REQUEST_URI']);
        error_log("🧠 METHOD: " . $_SERVER['REQUEST_METHOD']);

        if (isset($path[3]) && $path[3] === "recomendar") {

            error_log("✅ Entrando a la ruta /recomendar");
            error_log("🧠 Pregunta del usuario: " . $pregunta);

            // 1️⃣ Obtener todos los lugares ANTES de usarlos
            $sql = "SELECT atraccion_nombre, descripcion, provincia_id FROM lugares_turisticos";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $lugares = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$lugares || count($lugares) === 0) {
                error_log("⚠️ No hay lugares disponibles en la base de datos");
                echo json_encode([
                    "status" => 0,
                    "message" => "No hay lugares disponibles"
                ]);
                exit;
            }

            error_log("📦 Lugares cargados: " . count($lugares));

            // 2️⃣ Construir lista de lugares
            $listaLugares = "";
            foreach ($lugares as $lugar) {
                $listaLugares .= "- Nombre: {$lugar['atraccion_nombre']}\n  Descripción: {$lugar['descripcion']}\n\n";
            }

            // 3️⃣ Preparar prompt
            $messages = [
                [
                    "role" => "system",
                    "content" => "Eres un recomendador de lugares turísticos en Perú. 
                    Se te dará una lista de lugares con nombre, descripción y ubicación.
                    Devuelve SOLO el nombre EXACTO de uno de esos lugares que más se relacione
                    con lo que el usuario pide. No inventes nombres ni des explicaciones."
                ],
                [
                    "role" => "user",
                    "content" => "Pregunta del usuario: $pregunta\n\nLugares disponibles:\n$listaLugares"
                ]
            ];

            // 4️⃣ Llamada a la API de OpenAI
            $apiKey = "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";

            $ch = curl_init("https://api.openai.com/v1/chat/completions");
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "Authorization: Bearer $apiKey"
                ],
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode([
                    "model" => "gpt-4o-mini",
                    "messages" => $messages,
                    "temperature" => 0.4
                ])
            ]);

            $response = curl_exec($ch);
            curl_close($ch);

            // 5️⃣ Guardar la respuesta para debug
            $debugFile = __DIR__ . "/debug_gpt_response.txt";
            file_put_contents($debugFile, $response);
            error_log("🪵 Debug file saved at: " . $debugFile);

            // 6️⃣ Procesar respuesta
            $result = json_decode($response, true);
            $respuestaModelo = trim($result['choices'][0]['message']['content'] ?? '');
            error_log("🌍 GPT recomendó: " . $respuestaModelo);

            // 7️⃣ Buscar coincidencia exacta en BD
            $found = null;
            foreach ($lugares as $lugar) {
                if (strcasecmp(trim($lugar['atraccion_nombre']), trim($respuestaModelo)) === 0) {
                    $found = $lugar;
                    break;
                }
            }

            // 8️⃣ Responder al frontend
            if ($found) {
                echo json_encode([
                    "status" => 1,
                    "message" => "Recomendación generada",
                    "recomendacion" => $found
                ]);
            } else {
                echo json_encode([
                    "status" => 0,
                    "message" => "Atracción no encontrada",
                    "gpt_result" => $respuestaModelo
                ]);
            }

            exit;
        }

        else if (isset($path[2]) && $path[2] === "resenas") {
            $usuario_id = $input['usuario_id'] ?? null;
            $atraccion_id = $input['atraccion_id'] ?? null;
            $texto = trim($input['reseña'] ?? ''); // mantiene el nombre del JSON
            $estrellas = intval($input['estrellas'] ?? 0);

            if (!$usuario_id || !$atraccion_id || !$texto) {
                echo json_encode(["error" => "Datos incompletos"]);
                break;
            }

            $sql = "INSERT INTO reseñas (usuario_id, atraccion_id, reseña, estrellas, fecha)
                    VALUES (:usuario_id, :atraccion_id, :texto, :estrellas, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->bindParam(':atraccion_id', $atraccion_id);
            $stmt->bindParam(':texto', $texto);
            $stmt->bindParam(':estrellas', $estrellas);

            if ($stmt->execute()) {
                $newId = $conn->lastInsertId();
                echo json_encode([
                    "id" => $newId,
                    "usuario_id" => $usuario_id,
                    "atraccion_id" => $atraccion_id,
                    "reseña" => $texto,
                    "estrellas" => $estrellas,
                    "fecha" => date('Y-m-d H:i:s')
                ]);
            } else {
                echo json_encode(["error" => "No se pudo guardar la reseña"]);
            }

            break;
        }


        else if (isset($path[2]) && $path[2] === "visitado") {
            $data = json_decode(file_get_contents("php://input"));
            $user_id = $data->user_id;
            $atraccion_id = $data->atraccion_id;

            try {
                $sql = "INSERT INTO visited (usuario_id, ciudad_id) VALUES (:usuario_id, :ciudad_id)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':usuario_id', $user_id);
                $stmt->bindParam(':ciudad_id', $atraccion_id);
                $stmt->execute();

                $response = ['status' => 1, 'message' => 'Marcado como visitado'];
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $response = ['status' => 0, 'message' => 'Ya estaba marcado como visitado'];
                } else {
                    $response = ['status' => 0, 'message' => $e->getMessage()];
                }
            }
            echo json_encode($response);
            break;
        }

        else if (isset($path[3]) && $path[3] === "busqueda") {
            $atraccion = json_decode(file_get_contents('php://input'));
            $sql = "SELECT * FROM lugares_turisticos WHERE atraccion_nombre = :atraccion_nombre";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':atraccion_nombre', $atraccion->atraccion_nombre);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $response = ['status' => 1, 'message' => 'Atracción encontrada', 'atraccion' => $result];
            } else {
                $response = ['status' => 0, 'message' => 'Atracción no encontrada'];
            }
            echo json_encode($response);
            break;
        }

        else if (isset($path[3]) && $path[3] === "login") {
            $user = json_decode(file_get_contents('php://input'));
            $sql = "SELECT * FROM users WHERE email = :email";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':email', $user->email);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result && password_verify($user->contrasena, $result['contrasena'])) {
                $response = ['status' => 1, 'message' => 'Login successful', 'user' => $result];
                
            } else {
                $response = ['status' => 0, 'message' => 'Invalid credentials'];
            }
            echo json_encode($response);
            break;
        }
        else {
            $user = json_decode( file_get_contents('php://input') );
            $required = ['name', 'apellido', 'email', 'contrasena', 'pais'];

            foreach ($required as $field) {
                if (!isset($user->$field) || trim($user->$field) === '') {
                    $response = ['status' => 0, 'message' => "El campo $field es obligatorio."];
                    echo json_encode($response);
                    exit;
                }
            }
            $hashedPassword = password_hash($user->contrasena, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users(id, name, apellido, email, contrasena, pais) VALUES(null, :name, :apellido, :email, :contrasena, :pais)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':name', $user->name);
            $stmt->bindParam(':apellido', $user->apellido);
            $stmt->bindParam(':email', $user->email);
            $stmt->bindParam(':contrasena', $hashedPassword);
            $stmt->bindParam(':pais', $user->pais);
            try {
        if($stmt->execute()){
                $response = ['status' => 1, 'message' => 'Record created successfully.'];
                }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                // error de email duplicado
                $response = ['status' => 0, 'message' => 'El email ya está registrado.'];
            } else {
                $response = ['status' => 0, 'message' => 'Error: '.$e->getMessage()];
            }
        }

        echo json_encode($response);
        break;
        }
    
        echo json_encode($response);
        break;

    case "PUT":
        $user = json_decode( file_get_contents('php://input') );
        $sql = "UPDATE users SET name = :name, email =:email, apellido =:apellido, contrasena =:contrasena, pais =:pais WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $user->id);
        $stmt->bindParam(':name', $user->name);
        $stmt->bindParam(':apellido', $user->apellido);
        $stmt->bindParam(':email', $user->email);
        $stmt->bindParam(':contrasena', $user->contrasena);
        $stmt->bindParam(':pais', $user->pais);
        
        if($stmt->execute()){
            $response = ['status' => 1, 'message' => 'Record updated successfully.'];
        }
        else {
            $response = ['status' => 0, 'message' => 'Failed to update a record.'];
        }
        echo json_encode($response);
        break;

        case "DELETE": 
            $path = explode('/', $_SERVER['REQUEST_URI']);
            if (isset($path[2]) && $path[2] === 'visitado') {
                $usuario_id = $path[3] ?? null;
                $atraccion_id = $path[4] ?? null;

                if ($usuario_id && $atraccion_id) {
                    $sql = "DELETE FROM visited WHERE usuario_id = :usuario_id AND ciudad_id = :ciudad_id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':usuario_id', $usuario_id);
                    $stmt->bindParam(':ciudad_id', $atraccion_id);
                    $stmt->execute();

                    if ($stmt->rowCount() > 0) {
                        $response = ['status' => 1, 'message' => 'Visitado eliminado correctamente'];
                    } else {
                        $response = ['status' => 0, 'message' => 'No se encontró el registro para eliminar'];
                    }
                } else {
                    $response = ['status' => 0, 'message' => 'Faltan parámetros'];
                }

                echo json_encode($response);
                exit;
            }
            $sql = "DELETE FROM users WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $path[3]);
                
            if($stmt->execute()){
                $response = ['status' => 1, 'message' => 'Record deleted successfully.'];
            }
            else {
                $response = ['status' => 0, 'message' => 'Failed to delete a record.'];
            }
            echo json_encode($response);
            break;

}