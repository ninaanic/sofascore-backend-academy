<?php
$dsn = 'pgsql:host=localhost;port=5433;dbname=zadaca-2';
$connection = new PDO($dsn.';user=postgres;password=nina');
header('Content-Type: application/json');

if (!isset($_GET['page']) && !isset($_GET['slug'])) {
    $sql_get_sport = "SELECT slug, name FROM Sport";
    $result_get_sport = $connection->query($sql_get_sport);
    $result_get_sport = $result_get_sport->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result_get_sport, JSON_PRETTY_PRINT);

} 
elseif (isset($_GET['page']) && (!isset($_GET['slug']) || !isset($_GET['id']))) {
    http_response_code(404);
    echo "404 not found";
}  
elseif (isset($_GET['slug'])) { 
    $slug = $_GET['slug'];

    if ($_GET['page'] === 'sport') {

        $sql_get_tournament = "SELECT sport.name as sport_name, tournament.slug, tournament.name
                                FROM sport JOIN tournament 
                                ON sport.Id = tournament.sport_id
                                WHERE sport.slug LIKE '$slug'";
        $result_get_tournament = $connection->query($sql_get_tournament);

        $tournaments = array();
        while ($row = $result_get_tournament->fetch()) {
            $sport_name = $row["sport_name"];
            $tmp = [
                "slug" => $row["slug"], 
                "name" => $row["name"]
            ];
            array_push($tournaments, $tmp);
        };

        $data = array(
            "slug" => $slug,
            "name" => $sport_name,
            "tournaments" => $tournaments
        );

        echo json_encode($data, JSON_PRETTY_PRINT);
    } 
    if ($_GET['page'] === 'tournament') {
        $sql_get_event = "SELECT tournament.name as tournament_name, event.Id
                        FROM tournament JOIN event 
                        ON tournament.Id = event.tournament_id
                        WHERE tournament.slug LIKE '$slug'";
        $result_get_event = $connection->query($sql_get_event);

        $events = array();
        while ($row = $result_get_event->fetch()) {
            $tournament_name = $row["tournament_name"];
            $tmp = [
                "id" => $row["id"], 
            ];
            array_push($events, $tmp);
        };

        $data = array(
            "slug" => $slug,
            "name" => $tournament_name,
            "events" => $events
        );

        echo json_encode($data, JSON_PRETTY_PRINT);
    }
} elseif (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql_get_event_details = "SELECT *
                                FROM event 
                                WHERE event.Id = $id";
    $result_get_event_details = $connection->query($sql_get_event_details);
    $result_get_event_details = $result_get_event_details->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result_get_event_details, JSON_PRETTY_PRINT);

} 