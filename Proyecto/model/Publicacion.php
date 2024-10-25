<?php
require_once 'Db.php';
class Publicacion {
    private $connection;

    public function __construct() {
        $this->getConnection();
    }

    public function getConnection() {
        $dbObj = new Db();
        $this->connection = $dbObj->connection;
    }

    //Obtener las últimas 5 publicaciones (preguntas y respuestas)
    public function getPubliaciones() {
        $sql = "
            (SELECT 'pregunta' as tipo, p.titulo, p.texto, p.fecha_hora, u.foto_perfil 
             FROM Preguntas p
             JOIN Usuarios u ON p.id_usuario = u.id)
            UNION
            (SELECT 'respuesta' as tipo, pr.titulo, r.texto, r.fecha_hora, u.foto_perfil 
             FROM Respuestas r
             JOIN Preguntas pr ON r.id_pregunta = pr.id
             JOIN Usuarios u ON r.id_usuario = u.id)
            ORDER BY fecha_hora DESC
            LIMIT 5;


        ";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Devolver los resultados como array asociativo
    }


    public function getPublicacionesByTemaId($tema_id) {
        $sql = "SELECT * FROM Preguntas WHERE id_tema = :tema_id";  // Filtrar preguntas por el tema
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':tema_id', $tema_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalPublicaciones() {
        $sql = "SELECT 
                (SELECT COUNT(*) FROM Preguntas) + (SELECT COUNT(*) FROM Respuestas) as total";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function buscarPublicaciones($termino) {
        $conn = $this->connection;  // Usa la conexión establecida en el constructor

        // Prepara la consulta para buscar en la vista
        $stmt = $conn->prepare("
        SELECT 
           *
        FROM 
            vw_publicaciones
        WHERE 
            pregunta_titulo LIKE :termino
    ");
        $stmt->execute(['termino' => '%' . $termino . '%']);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Devuelve los resultados
    }











}

