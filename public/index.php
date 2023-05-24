<?php

use App\Models\DB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$app->get('/', function (Request $request, Response $response, $args) {
   $response->getBody()->write("Welcome to API CRUD SLIM!");
   return $response;
});

//*****************************************************************************************/
// For example, the table 'books' was used with the columns 'title', 'content' and 'author'
//*****************************************************************************************/
$app->get('/books/all', function (Request $request, Response $response) {
   $sql = "SELECT * FROM books";

   try {
      $db = new DB();
      $conn = $db->connect();
      $stmt = $conn->query($sql);
      $books = $stmt->fetchAll(PDO::FETCH_OBJ);
      $db = null;

      $response->getBody()->write(json_encode($books));
      return $response
         ->withHeader('content-type', 'application/json')
         ->withStatus(200);
   } catch (PDOException $e) {
      $error = array(
         "message" => $e->getMessage()
      );

      $response->getBody()->write(json_encode($error));
      return $response
         ->withHeader('content-type', 'application/json')
         ->withStatus(500);
   }
});

$app->get('/books/{id}', function (Request $request, Response $response, array $args) {
   $id = $args["id"];
   $sql = "SELECT * FROM books WHERE id = $id";

   try {
      $db = new DB();
      $conn = $db->connect();
      $stmt = $conn->query($sql);
      $books = $stmt->fetchAll(PDO::FETCH_OBJ);
      $db = null;

      $response->getBody()->write(json_encode($books));
      return $response
         ->withHeader('content-type', 'application/json')
         ->withStatus(200);
   } catch (PDOException $e) {
      $error = array(
         "message" => $e->getMessage()
      );

      $response->getBody()->write(json_encode($error));
      return $response
         ->withHeader('content-type', 'application/json')
         ->withStatus(500);
   }
});

$app->post('/books/add', function (Request $request, Response $response, array $args) {
   $data = $request->getParsedBody();
   $title = $data["title"];
   $content = $data["content"];
   $author = $data["author"];
   $sql = "INSERT INTO books (title, content, author) VALUES (:title, :content, :author)";

   try {
      $db = new DB();
      $conn = $db->connect();
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':title', $title);
      $stmt->bindParam(':content', $content);
      $stmt->bindParam(':author', $author);

      $result = $stmt->execute();

      $db = null;
      $response->getBody()->write(json_encode($result));
      return $response
         ->withHeader('content-type', 'application/json')
         ->withStatus(200);
   } catch (PDOException $e) {
      $error = array(
         "message" => $e->getMessage()
      );

      $response->getBody()->write(json_encode($error));
      return $response
         ->withHeader('content-type', 'application/json')
         ->withStatus(500);
   }
});

$app->put('/books/update/{id}', function (Request $request, Response $response, array $args) {
      $id = $request->getAttribute('id');
      $data = $request->getParsedBody();
      $title = $data["title"];
      $content = $data["content"];
      $author = $data["author"];
      $sql = "UPDATE books SET title = :title, content = :content, author = :author WHERE id = $id";

      try {
         $db = new DB();
         $conn = $db->connect();
         $stmt = $conn->prepare($sql);
         $stmt->bindParam(':title', $title);
         $stmt->bindParam(':content', $content);
         $stmt->bindParam(':author', $author);

         $result = $stmt->execute();

         $db = null;
         $response->getBody()->write(json_encode($result));
         return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
      } catch (PDOException $e) {
         $error = array(
            "message" => $e->getMessage()
         );

         $response->getBody()->write(json_encode($error));
         return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
      }
   }
);

$app->delete('/books/delete/{id}', function (Request $request, Response $response, array $args) {
   $id = $args["id"];
   $sql = "DELETE FROM books WHERE id = $id";

   try {
      $db = new DB();
      $conn = $db->connect();

      $stmt = $conn->prepare($sql);
      $result = $stmt->execute();

      $db = null;
      $response->getBody()->write(json_encode($result));
      return $response
         ->withHeader('content-type', 'application/json')
         ->withStatus(200);
   } catch (PDOException $e) {
      $error = array(
         "message" => $e->getMessage()
      );

      $response->getBody()->write(json_encode($error));
      return $response
         ->withHeader('content-type', 'application/json')
         ->withStatus(500);
   }
});

$app->run();
